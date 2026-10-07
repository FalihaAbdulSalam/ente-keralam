<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\SectorDetail;
use App\Models\ArticleType;


use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Search keyword is required.',
            ], 422);
        }

        $results = [];

        foreach (config('globalsearch') as $model => $fields) {

            $query = $model::query();

            foreach ($fields as $fieldDef) {

                // Check if "relation:column"
                if (str_contains($fieldDef, ':')) {

                    [$relation, $column] = explode(':', $fieldDef);

                    $query->orWhereHas($relation, function ($q) use ($column, $keyword) {
                        $q->where($column, 'like', "%{$keyword}%");
                    });

                } else {
                    // Direct column search
                    $query->orWhere($fieldDef, 'like', "%{$keyword}%");
                }
            }

            // Limit results for performance
            $results[class_basename($model)] = $query->take(10)->get();
        }

        return response()->json([
            'success' => true,
            'keyword' => $keyword,
            'results' => $results,
        ]);
    }
    public function sectorwise(Request $request)
    {
            $keyword = $request->input('keyword');
            $sectorId = $request->input('sector_id');

            if (!$keyword) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search keyword is required.',
                ], 422);
            }

            $results = [];

            $configarr = [
                Article::class => [
                    'entitle',
                    'maltitle',
                    'endescription',
                    'maldescription',
                    'encontent',
                    'malcontent',

                    // RELATION FIELDS
                    'type:entitle',
                    'type:maltitle',
                    'sector:entitle',
                ],

                ArticleType::class => ['entitle', 'maltitle'],
                SectorDetail::class => ['entitle'],
            ];

            foreach ($configarr as $model => $fields) {

                $query = $model::query();

                /**
                 * -------------------------------------------------
                 * APPLY CONDITIONAL SECTOR FILTER
                 * -------------------------------------------------
                 */
                $modelName = class_basename($model);

                if ($sectorId) {

                    if ($modelName === 'SectorDetail') {
                        // Filter sector master
                        $query->where('id', $sectorId);
                    }

                    if ($modelName === 'Article') {
                        // Filter articles belonging to selected sector
                        $query->where('sector_details_id', $sectorId);
                    }
                }

                /**
                 * -------------------------------------------------
                 * APPLY SEARCH CONDITIONS
                 * -------------------------------------------------
                 */
                $query->where(function ($q) use ($fields, $keyword) {

                    foreach ($fields as $fieldDef) {

                        if (str_contains($fieldDef, ':')) {
                            // relation:column
                            [$relation, $column] = explode(':', $fieldDef);

                            $q->orWhereHas($relation, function ($relQ) use ($column, $keyword) {
                                $relQ->where($column, 'like', "%{$keyword}%");
                            });

                        } else {
                            // direct field search
                            $q->orWhere($fieldDef, 'like', "%{$keyword}%");
                        }
                    }
                });

                /**
                 * -------------------------------------------------
                 * FETCH RESULTS
                 * -------------------------------------------------
                 */
                $results[$modelName] = $query->take(10)->get();
            }

            return response()->json([
                'success' => true,
                'keyword' => $keyword,
                'sector_id' => $sectorId,
                'results' => $results,
            ]);

    }
}
