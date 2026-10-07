<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\SectorDetail;
use App\Models\Article;


class SectorwiseController extends Controller
{

     private function uploadfileUrl($path)
    {
       
        $cleanPath = str_replace('public/', '', $path);
    
       
        return $path ? asset($cleanPath) : null;
    }
    public function sectorShow(Request $request){
        //  $sectorDetail->icon = $this->fileUrl($sectorDetail->icon);
        $slug = request()->segment(2);
        $sectorDetail=SectorDetail::where('link',$slug)->first();// $sectorDetail
        $artdetail=Article::where('sector_details_id',$sectorDetail->id)
        ->where('entitle','!=','')->get()
        ->map(function ($item) {
            $item->poster = $this->uploadfileUrl('/'.$item->poster);
           
            return $item;
        })
        ;

        // dd($artdetail);
            
            return response()->json([
                'status' => true,
                'data' => $artdetail
            ]);
    }
    
    private function fileUrl($path)
    {
        if (!$path) {
            return null;
        }

        // If using Storage::disk('public')
        return Storage::url($path);
    }

}