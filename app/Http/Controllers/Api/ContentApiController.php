<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleType;
use App\Models\SectorDetail;
use App\Models\Creativethoughts;
use App\Models\Banner;
use App\Models\Footer;
use App\Models\Faq;

class ContentApiController extends Controller
{
    // Helper: Convert file path to full URL
    private function fileUrl($path)
    {
       
        $cleanPath = str_replace('public/', '', $path);
    
       
        return $path ? asset($cleanPath) : null;
    }

     private function uploadfileUrl($path)
    {
       
        $cleanPath = str_replace('public/', '', $path);
    
       
        return $path ? asset($cleanPath) : null;
    }
    //uploads
    /*---------------------------------
     | ARTICLES
     ---------------------------------*/
    public function articles()
    {
        $articles = Article::select(
            'id',
            'articletype_id',
            'sector_details_id',
            'entitle',
            'maltitle',
            'endescription',
            'maldescription',
            'encontent',
            'malcontent',
            'poster',
            'banner',
            'status',
            'created_at',
            'updated_at'
        )
        ->with(['type:id,entitle,maltitle', 'sector:id,entitle,maltitle','sector.counters:id,sector_id,entitle,icon,numeric_type_icon,counter_number'])
        ->orderByDesc('id')
        ->where('poster','!=','')
        ->get()
        ->map(function ($item) {
            $item->poster = $this->fileUrl($item->poster);
            $item->banner = $this->fileUrl($item->banner);
            return $item;
        });

        return response()->json([
            'status' => true,
            'data' => $articles
        ]);
    }

    public function articleShow($id)
    {
        $article = Article::with(['type:id,entitle,maltitle', 'sector:id,entitle,maltitle','sector.counters:id,sector_id,entitle,icon,numeric_type_icon,counter_number'])
            ->findOrFail($id);

            // dd($article);

        $article->poster = $this->fileUrl($article->poster);
        $article->banner = $this->fileUrl($article->banner);

        return response()->json([
            'status' => true,
            'data' => $article
        ]);
    }

    /*---------------------------------
     | ARTICLE TYPES
     ---------------------------------*/
    public function articleTypes()
    {
        $types = ArticleType::select('id', 'entitle', 'maltitle', 'status', 'created_at', 'updated_at')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $types
        ]);
    }

    public function articleTypeShow($id)
    {
        $type = ArticleType::select('id', 'entitle', 'maltitle', 'status', 'created_at', 'updated_at')
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $type
        ]);
    }

    /*---------------------------------
     | SECTOR DETAILS
     ---------------------------------*/
    public function sectors()
    {
        $sectors = SectorDetail::select(
            'id',
            'entitle',
            'maltitle',
            'endescription',
            'maldescription',
            'icon',
            'status',
            'created_at',
            'updated_at'
        )
        ->with('counters:id,sector_id,entitle,icon,numeric_type_icon,counter_number')
        ->orderBy('id')
        ->get()
        ->map(function ($item) {
            $item->icon = $this->fileUrl($item->icon);
            return $item;
        });

        // dd($sectors);
        return response()->json([
            'status' => true,
            'data' => $sectors
        ]);
    }

    public function sectorShow($id)
    {
        $sector = SectorDetail::with('counters:id,sector_id,entitle,icon,numeric_type_icon,counter_number')->findOrFail($id);
        $sector->icon = $this->fileUrl($sector->icon);

        // dd($sector);
        return response()->json([
            'status' => true,
            'data' => $sector
        ]);
    }
    //creativethought
    public function creativethought()
    {
        $creativethought = Creativethoughts::select(
            'id',
            'entitle',
            'maltitle',            
            'poster',
            'status',
            'created_at',
            'updated_at'
        )
        
        ->orderBy('id')
        ->get()
        ->map(function ($item) {
            $item->poster = $this->uploadfileUrl('uploads/'.$item->poster);
           
            return $item;
        });
        
        // dd($sectors);
        return response()->json([
            'status' => true,
            'data' => $creativethought
        ]);
    }
    //banners
    public function banners()
    {
        $banners = Banner::select(
            'id',
            'entitle',
            'maltitle',            
            'poster',
            'status',
            'created_at',
            'updated_at'
        )
        
        ->orderBy('id')
        ->get()
        ->map(function ($item) {
            $item->poster = $this->uploadfileUrl('uploads/'.$item->poster);
            //  dd($this->uploadfileUrl($item->poster));
            return $item;
        });

        // dd($sectors);
        return response()->json([
            'status' => true,
            'data' => $banners
        ]);
    }
    //footers
     public function footers()
    {
        $footers = Footer::select(
            'id',
            'entitle',
            'maltitle',            
            'link_text',
            'status',
            'created_at',
            'updated_at'
        )
        
        ->orderBy('id')
        ->where('status',1)
        ->get()
        ->map(function ($item) {
            $item->poster = $this->uploadfileUrl($item->poster);
            return $item;
        });

        // dd($sectors);
        return response()->json([
            'status' => true,
            'data' => $footers
        ]);
    }
    //faq

     public function faq()
    {
        $faq = Faq::select(
            'id',
             'enquestion',
            'enanswer',
            'malquestion',
            'malanswer',              
           
            'status',
            'created_at',
            'updated_at'
        )
        
        ->orderBy('id')
        ->get()
        ;

        // dd($sectors);
        return response()->json([
            'status' => true,
            'data' => $faq
        ]);
    }

}
