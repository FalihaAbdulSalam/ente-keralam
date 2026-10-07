<?php

namespace App\Services;

use App\Models\EmailRecipient;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\Extra\SpoofCheckValidation;
use Egulias\EmailValidator\Validation\FilterEmailValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class CampaignRecipientImporter
{
    public function import(?string $path = null, int $batchSize = 500, ?string $source = null): array
    {
        $path = $path ?? config('campaign.recipient_csv_path', storage_path('app/campaign/iffk.csv'));
        $source = $source ?? config('campaign.default_source', 'iffk');
        $allowedCategories = $this->normalizeCategories(
            config('campaign.allowed_categories', ['DELEGATE'])
        );
        $validationStyles = config('campaign.email_validation', ['rfc', 'dns']);

        if (!File::exists($path)) {
            throw new RuntimeException("CSV file not found at {$path}");
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException("Unable to open CSV file at {$path}");
        }

        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            throw new RuntimeException('CSV file has no header row.');
        }

        $headers[0] = Str::of($headers[0])->replace("\xEF\xBB\xBF", '')->toString();
        $headers = array_map(
            fn (string $header) => Str::of($header)->lower()->snake()->toString(),
            $headers
        );

        $headerMap = [
            'sl_no' => 'source_sl_no',
            'name' => 'name',
            'applicant_name' => 'name',
            'mobile' => 'mobile',
            'mobile_number' => 'mobile',
            'whatsapp' => 'whatsapp',
            'email' => 'email',
            'category' => 'category',
        ];
        $hasCategoryColumn = in_array('category', $headers, true);

        $rows = [];
        $processed = 0;
        $skipped = 0;
        $upserted = 0;
        $now = now();
        $validator = new EmailValidator();
        $validation = $this->buildValidation($validationStyles);

        while (($data = fgetcsv($handle)) !== false) {
            $processed++;
            $row = [];

            foreach ($headers as $index => $header) {
                $key = $headerMap[$header] ?? $header;
                $row[$key] = $data[$index] ?? null;
            }

            $category = trim((string) ($row['category'] ?? ''));
            if ($hasCategoryColumn && !empty($allowedCategories)) {
                $normalizedCategory = Str::upper($category);
                if (!in_array($normalizedCategory, $allowedCategories, true)) {
                    $skipped++;
                    continue;
                }
            }

            $email = Str::lower(trim((string) ($row['email'] ?? '')));
            if ($email === '' || !$validator->isValid($email, $validation)) {
                $skipped++;
                continue;
            }

            $rows[] = [
                'source_sl_no' => !empty($row['source_sl_no']) ? (int) $row['source_sl_no'] : null,
                'name' => trim((string) ($row['name'] ?? '')),
                'mobile' => trim((string) ($row['mobile'] ?? '')),
                'whatsapp' => trim((string) ($row['whatsapp'] ?? '')),
                'email' => $email,
                'category' => $category,
                'source' => $source,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= $batchSize) {
                $this->upsertBatch($rows);
                $upserted += count($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            $this->upsertBatch($rows);
            $upserted += count($rows);
        }

        fclose($handle);

        return [
            'processed' => $processed,
            'upserted' => $upserted,
            'skipped' => $skipped,
        ];
    }

    private function upsertBatch(array $rows): void
    {
        EmailRecipient::upsert(
            $rows,
            ['email'],
            ['source_sl_no', 'name', 'mobile', 'whatsapp', 'category', 'source', 'updated_at']
        );
    }

    private function normalizeCategories(array $categories): array
    {
        $normalized = [];

        foreach ($categories as $category) {
            $value = Str::upper(trim((string) $category));
            if ($value !== '') {
                $normalized[] = $value;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function buildValidation(array $styles)
    {
        $validators = [];
        $map = [
            'rfc' => RFCValidation::class,
            'strict' => NoRFCWarningsValidation::class,
            'dns' => DNSCheckValidation::class,
            'spoof' => SpoofCheckValidation::class,
            'filter' => FilterEmailValidation::class,
        ];

        foreach ($styles as $style) {
            $style = Str::lower(trim((string) $style));
            if ($style === 'filter_unicode') {
                $validators[] = FilterEmailValidation::unicode();
                continue;
            }

            if (isset($map[$style])) {
                $validators[] = new $map[$style]();
            }
        }

        if (empty($validators)) {
            $validators[] = new RFCValidation();
        }

        return count($validators) === 1
            ? $validators[0]
            : new MultipleValidationWithAnd($validators);
    }
}
