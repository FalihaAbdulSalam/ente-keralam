<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS using Mobile Seva Gateway
     * 
     * @param string $mobile Mobile number (10 digits)
     * @param string $message SMS message content
     * @param string $context Context for logging (e.g., 'otp_login', 'registration_complete')
     * @param string|null $templateId DLT template ID (required)
     * @return bool Success status
     */
    public static function send(string $mobile, string $message, string $context = 'general', ?string $templateId = null): bool
    {
        try {
            // Get configuration
            $username = config('services.sms.username');
            $password = config('services.sms.password');
            $senderId = config('services.sms.sender_id');
            $deptSecureKey = config('services.sms.dept_secure_key');
            
            // Validate configuration
            if (!$username || !$password || !$senderId || !$deptSecureKey) {
                Log::error('Mobile Seva SMS configuration missing', [
                    'context' => $context,
                ]);
                return false;
            }
            
            // Clean mobile number
            $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
            
            // Validate mobile number (10 digits)
            if (strlen($cleanMobile) !== 10) {
                Log::error('Invalid mobile number', [
                    'mobile' => $mobile,
                    'cleaned' => $cleanMobile,
                    'context' => $context,
                ]);
                return false;
            }
            
            // Determine service type based on context
            $serviceType = self::getServiceType($context);
            
            // Send SMS based on service type
            if ($serviceType === 'otpmsg' || $serviceType === 'unicodeotpmsg') {
                return self::sendOtpSms($username, $password, $senderId, $message, $cleanMobile, $deptSecureKey, $templateId, $context);
            } else {
                return self::sendSingleSms($username, $password, $senderId, $message, $cleanMobile, $deptSecureKey, $templateId, $context);
            }
            
        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'mobile' => $mobile,
                'context' => $context,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
    
    /**
     * Determine SMS service type based on context
     */
    private static function getServiceType(string $context): string
    {
        // OTP related contexts
        if (strpos($context, 'otp') !== false) {
            return 'otpmsg';
        }
        
        // Default to single message
        return 'singlemsg';
    }
    
    /**
     * Send single SMS via Mobile Seva
     */
    private static function sendSingleSms(
        string $username,
        string $password,
        string $senderId,
        string $message,
        string $mobile,
        string $deptSecureKey,
        ?string $templateId,
        string $context
    ): bool {
        $encryptedPassword = sha1(trim($password));
        $key = hash('sha512', trim($username) . trim($senderId) . trim($message) . trim($deptSecureKey));
        
        $data = [
            'username' => trim($username),
            'password' => trim($encryptedPassword),
            'senderid' => trim($senderId),
            'content' => trim($message),
            'smsservicetype' => 'singlemsg',
            'mobileno' => trim($mobile),
            'key' => trim($key),
        ];
        
        // Add template ID if provided
        if ($templateId) {
            $data['templateid'] = trim($templateId);
        }
        
        return self::sendToMobileSeva($data, $mobile, $context);
    }
    
    /**
     * Send OTP SMS via Mobile Seva
     */
    private static function sendOtpSms(
        string $username,
        string $password,
        string $senderId,
        string $message,
        string $mobile,
        string $deptSecureKey,
        ?string $templateId,
        string $context
    ): bool {
        $encryptedPassword = sha1(trim($password));
        $key = hash('sha512', trim($username) . trim($senderId) . trim($message) . trim($deptSecureKey));
        
        $data = [
            'username' => trim($username),
            'password' => trim($encryptedPassword),
            'senderid' => trim($senderId),
            'content' => trim($message),
            'smsservicetype' => 'otpmsg',
            'mobileno' => trim($mobile),
            'key' => trim($key),
        ];
        
        // Add template ID if provided
        if ($templateId) {
            $data['templateid'] = trim($templateId);
        }
        
        return self::sendToMobileSeva($data, $mobile, $context);
    }
    
    /**
     * Send HTTP request to Mobile Seva gateway
     */
    private static function sendToMobileSeva(array $data, string $mobile, string $context): bool
    {
        $url = 'https://msdgweb.mgov.gov.in/esms/sendsmsrequestDLT';
        
        try {
            $response = Http::asForm()
                ->withOptions([
                    'verify' => false, // SSL verification disabled as per original code
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2, // Force TLS 1.2
                ])
                ->post($url, $data);
            
            $responseBody = $response->body();
            
            // Log the response
            Log::info('Mobile Seva SMS sent', [
                'to' => $mobile,
                'context' => $context,
                'service_type' => $data['smsservicetype'],
                'status' => $response->status(),
                'response' => $responseBody,
            ]);
            
            // Check if request was successful
            if ($response->successful()) {
                // Mobile Seva returns text response, check for success indicators
                if (stripos($responseBody, 'success') !== false || 
                    stripos($responseBody, 'sent') !== false ||
                    is_numeric($responseBody)) { // Sometimes returns message ID
                    return true;
                }
            }
            
            Log::warning('Mobile Seva SMS response unclear', [
                'mobile' => $mobile,
                'context' => $context,
                'response' => $responseBody,
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Mobile Seva SMS API call failed', [
                'mobile' => $mobile,
                'context' => $context,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
    
    /**
     * Send bulk SMS to multiple numbers
     * 
     * @param array $mobiles Array of mobile numbers
     * @param string $message SMS message content
     * @param string $context Context for logging
     * @param string|null $templateId DLT template ID (required)
     * @return bool Success status
     */
    public static function sendBulk(array $mobiles, string $message, string $context = 'bulk', ?string $templateId = null): bool
    {
        try {
            $username = config('services.sms.username');
            $password = config('services.sms.password');
            $senderId = config('services.sms.sender_id');
            $deptSecureKey = config('services.sms.dept_secure_key');
            
            if (!$username || !$password || !$senderId || !$deptSecureKey) {
                Log::error('Mobile Seva SMS configuration missing');
                return false;
            }
            
            // Clean and validate mobile numbers
            $cleanMobiles = array_map(function($mobile) {
                return preg_replace('/[^0-9]/', '', $mobile);
            }, $mobiles);
            
            $cleanMobiles = array_filter($cleanMobiles, function($mobile) {
                return strlen($mobile) === 10;
            });
            
            if (empty($cleanMobiles)) {
                Log::error('No valid mobile numbers for bulk SMS', [
                    'context' => $context,
                ]);
                return false;
            }
            
            $mobileNos = implode(',', $cleanMobiles);
            
            $encryptedPassword = sha1(trim($password));
            $key = hash('sha512', trim($username) . trim($senderId) . trim($message) . trim($deptSecureKey));
            
            $data = [
                'username' => trim($username),
                'password' => trim($encryptedPassword),
                'senderid' => trim($senderId),
                'content' => trim($message),
                'smsservicetype' => 'bulkmsg',
                'bulkmobno' => trim($mobileNos),
                'key' => trim($key),
            ];
            
            if ($templateId) {
                $data['templateid'] = trim($templateId);
            }
            
            return self::sendToMobileSeva($data, 'bulk:' . count($cleanMobiles), $context);
            
        } catch (\Exception $e) {
            Log::error('Bulk SMS sending failed', [
                'context' => $context,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
