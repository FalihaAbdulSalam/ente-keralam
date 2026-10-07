<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminMenu;
use Illuminate\Support\Facades\Auth;

class PledgeAPIController extends Controller
{
    
    public function getPledge($id)
    {
        $pledge = DB::table('tbl_pledge')
            ->where('pledge_id', $id)
            ->where('pledge_status', 1)
            ->first();

        if (!$pledge) {
            return response()->json([
                'result' => false,
                'message' => 'Pledge not found or inactive.'
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Pledge retrieved successfully.',
            'data' => [
                'pledge_id' => $pledge->pledge_id,
                'title' => $pledge->pledge_title,
                'description' => $pledge->pledge_description,
                'content' => $pledge->pledge_content,
                'score' => $pledge->pledge_score,
                'start_date' => $pledge->pledge_startDate,
                'end_date' => $pledge->pledge_endDate,
                'banner'=>$pledge->banner,
                'poster'=>$pledge->poster,
                'status' => $pledge->pledge_status,
            ]
        ], 200);
    }
     /*public function getPledgeactive()
    {
        $pledge = DB::table('tbl_pledge')
            ->where('pledge_status', 1)
            ->first();

        if (!$pledge) {
            return response()->json([
                'result' => false,
                'message' => 'Pledge not found or inactive.'
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => 'Pledge retrieved successfully.',
            'data' => [
                'pledge_id' => $pledge->pledge_id,
                'title' => $pledge->pledge_title,
                'description' => $pledge->pledge_description,
                'content' => $pledge->pledge_content,
                'score' => $pledge->pledge_score,
                'start_date' => $pledge->pledge_startDate,
                'end_date' => $pledge->pledge_endDate,
                'banner'=>$pledge->banner,
                'poster'=>$pledge->poster,
                'status' => $pledge->pledge_status,
            ]
        ], 200);
    }
    */
    public function getPledgeactive()
    {
    $pledges = DB::select('tbl_pledge')
        ->where('pledge_status', 1)
        ->get();

    if ($pledges->isEmpty()) {
        return response()->json([
            'result'  => false,
            'message' => 'No active pledges found.'
        ], 404);
    }

    return response()->json([
        'result'  => true,
        'message' => 'Active pledges retrieved successfully.',
        'data'    => $pledges->map(function ($pledge) {
            return [
                'pledge_id'   => $pledge->pledge_id,
                'title'       => $pledge->pledge_title,
                'description' => $pledge->pledge_description,
                'content'     => $pledge->pledge_content,
                'score'       => $pledge->pledge_score,
                'start_date'  => $pledge->pledge_startDate,
                'end_date'    => $pledge->pledge_endDate,
                'banner'      => $pledge->banner,
                'poster'      => $pledge->poster,
                'status'      => $pledge->pledge_status,
            ];
        }),
    ], 200);
    }

}
