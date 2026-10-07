<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CkeditorUploadController extends Controller
{
    public function upload(Request $request)
    {

        
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time().'_'.$file->getClientOriginalName();
            $res=$file->move(public_path('uploads/ckeditor'), $filename);
            // dd($res);
            return response()->json([
            'uploaded' => true,
            'url' => asset('uploads/ckeditor/'.$filename)
        ]);
            // return response()->json([
            //     'url' => asset('uploads/ckeditor/'.$filename)
            // ]);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }
}
