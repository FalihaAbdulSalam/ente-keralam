<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Articleattachments;
use App\Models\ArticleattachmentsLang;
use App\Models\ArticleLang;
use App\Models\Articletype;
use App\Models\Banner;
use App\Models\Bannercategory;
use App\Models\BannerLang;
use App\Models\BOD;
use App\Models\BODLang;
use App\Models\Cityzenchapter;
use App\Models\Component;
use App\Models\EoDB;
use App\Models\FacitiesAtPort;
use App\Models\FacitiesAtPortLang;
use App\Models\footer;
use App\Models\FooterCategory;
use App\Models\FooterLang;
use App\Models\Gallery;
use App\Models\Galleryattachments;
use App\Models\GalleryattachmentsLang;
use App\Models\GalleryCategory;
use App\Models\GalleryLang;
use App\Models\GalleryOtherType;
use App\Models\Gallerytype;
use App\Models\InternalComplaintsCommittee;
use App\Models\Language;
use App\Models\Logo;
use App\Models\LogoLang;
use App\Models\Logotype;
use App\Models\Mainmenu;
use App\Models\MainmenuLang;
use App\Models\MediaCategory;
use App\Models\MediaCategoryLang;
use App\Models\Menulinktype;
use App\Models\Modelmaster;
use App\Models\Pattanrajysabha;
use App\Models\Portservice;
use App\Models\PortserviceLang;
use App\Models\Publicservice;
use App\Models\RTI;
use App\Models\Tabcontents;
use App\Models\TabcontentsLang;
use App\Models\Tabcontentstype;
use App\Models\Socialmedia;
use App\Models\Sponsor;
use App\Models\SponsorLang;
use App\Models\Submenu;
use App\Models\SubmenuLang;
use App\Models\Timeline;
use App\Models\TimelineLang;
use App\Models\Transparencyplan;
use App\Models\UserPermission;
use Auth;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use Illuminate\Validation\Rule;
class SiteadminController extends Controller
{
    //
    //Mainmenu

    public function mainmenu_list(Request $request,$encid=null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Main Menu', 'message' => 'List Main Menu', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('mainmenu_list')->id;
        $head = 'Main Menu List';
        //Data and data
        $itemIds = explode(',', \Auth::user()->item_id);
        $componentData = Mainmenu::with(['mainmenu_langs' => function () {}])
        ->orderBy('updated_at', 'desc')->get();
        $componentData = Mainmenu::with(['mainmenu_langs' => function () {}])
        ->where('user_id',\Auth::user()->id)
        ->orWhereHas('mainmenu_langs',function($q) use($itemIds){
            $q->whereIn('id',$itemIds);
        })
        ->orderBy('order_num', 'asc')
        ->orderBy('updated_at', 'desc')->get();
        // $componentData=Banner::with(['banner_langs'=>function(){

        // }])->orderBy('order_num','asc')->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();

        // dd($componentData);
        if($encid!=''){
            $id=\Crypt::decrypt($encid);
            $componentData = Mainmenu::with(['mainmenu_langs' => function () {}])
            ->where('user_id',\Auth::user()->id)
            ->orWhereIn('id', $itemIds)
            ->orWhereHas('mainmenu_langs',function($q) use($itemIds){
                $q->whereIn('id',$itemIds);
            })
            ->orderBy('updated_at', 'desc')->get();

        }
        $userrole=Auth::user()->usertypes[0]->userrolename;
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Mainmenu");
        return view('siteadmin.mainmenu.mainmenu_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function mainmenu_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Main Menu', 'message' => 'List Main Menu', 'status' => 0, 'link' => $userrole.'/mainmenu_list'],
                2 => ['title' => 'Add Main Menu', 'message' => 'Add Main Menu', 'status' => 0],
            ];
            $head = 'Add Main Menu';
            $editF = 'A';
            $result = [];
            $article = [];
            $menulinkname = '';
        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Main Menu', 'message' => 'List Main Menu', 'status' => 0, 'link' => $userrole.'/mainmenu_list'],
                2 => ['title' => 'Edit Main Menu', 'message' => 'Edit Main Menu', 'status' => 0],
            ];
            $head = 'Edit Main Menu';
            $editF = 'E';
            $result = Mainmenu::with(['mainmenu_langs' => function ($query) {}])->where('id', $id)->first();
            $article = Article::with(['article_langs' => function ($query) {}])->where('status_id', 1)->orderBy('updated_at', 'asc')->get();
            $menulinkname = Menulinktype::select('name')->where('status_id', 1)->where('id', $result->menulinktype_id)->first();
        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('mainmenu_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $lang = Language::where('status_id', 1)->get();
        if(\Auth::user()->superuser==1){
            $menulinktype = Menulinktype::where('status_id', 1)->get();
        }else{
            // dd($editF);
            $menulinktype=app('App\Http\Controllers\CommonfunctionController')->types_allowed(12,'Mainmenu','Menulinktype','menulinktype_id');
            if($editF=='E'){
                $menulinktype=Menulinktype::select('name')->where('status_id', 1)->where('id', $result->menulinktype_id)->get();
            }
        }
       


        return view('siteadmin.mainmenu.mainmenu_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'menulinktype', 'lang', 'article', 'menulinkname', 'usertype'));
    }

    public function articledropdown(Request $request)
    {
        $article = Article::with(['article_langs' => function ($query) {}])->where('status_id', 1)->orderBy('updated_at', 'asc')->get();

        return response()->json(['article' => $article]);
    }

    public function mainmenu_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'menulinktype_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'linkvalval' => app('App\Http\Controllers\CommonfunctionController')->linktypeval($request->menulinktype_id),
            'iconclass' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            // dd($request->menulinktype_id);

            if (\Crypt::decrypt($request->menulinktype_id) == 3) {//file
                if (isset($request->linkvalval)) {
                    $imname = 'mainmenu'.date('yyyy:mm:dd:hh:mm:ss').$request->linkvalval->extension();
                    $path = $request->file('linkvalval')->storeAs('uploads/mainmenu', $imname, 'myfile');
                    $dataarr = new Mainmenu([

                        'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                        'linkvalval' => $imname,
                        'icon_class' => $request->iconclass,
                        'user_id' => Auth::user()->id,
                    ]);
                } else {
                    $dataarr = new Mainmenu([

                        'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),

                        'icon_class' => $request->iconclass,
                        'user_id' => Auth::user()->id,
                    ]);
                }
            } else {
                $dataarr = new Mainmenu([

                    'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                    'linkvalval' => $request->linkvalval,
                    'icon_class' => $request->iconclass,
                    'user_id' => Auth::user()->id,
                ]);

            }

            $res = $dataarr->save();
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = MainmenuLang::where('title', $request->title[1])->where('lang_id', 1)->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    $datarrlang = new MainmenuLang([
                        'title' => $request->title[$lan->id],
                        'mainmenu_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                        // $res = true;
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            // dd($res);
            if ($res) {
                $success = 'Main Menu Added!';

                return redirect($userrole.'/mainmenu_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function mainmenu_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'menulinktype_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'linkvalval' => app('App\Http\Controllers\CommonfunctionController')->linktypeval(\Crypt::decrypt($request->menulinktype_id)),
            'iconclass' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);
            if (\Crypt::decrypt($request->menulinktype_id) == 3) {//file
                if (isset($request->linkvalval)) {
                    $imname = 'mainmenu'.date('yyyy:mm:dd:hh:mm:ss').$request->linkvalval->extension();
                    $path = $request->file('linkvalval')->storeAs('uploads/mainmenu', $imname, 'myfile');
                    $dataarr = [

                        'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                        'linkvalval' => $imname,
                        'icon_class' => $request->iconclass,
                        'user_id' => Auth::user()->id,
                    ];
                } else {
                    $dataarr = [

                        'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),

                        'icon_class' => $request->iconclass,
                        'user_id' => Auth::user()->id,
                    ];
                }
            } else {
                $linkvalval = $request->linkvalval;
                $dataarr = [

                    'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                    'linkvalval' => $linkvalval,
                    'icon_class' => $request->iconclass,
                    'user_id' => Auth::user()->id,
                ];
            }

            $res = Mainmenu::where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = MainmenuLang::where('mainmenu_id', '!=', $id)->where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    $datarrlang = [
                        'title' => $request->title[$lan->id],
                        'lang_id' => $lan->id,
                    ];
                    if ($chkrws == 0) {

                        $reslan = MainmenuLang::where('mainmenu_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'Main Menu Updated!';

                return redirect($userrole.'/mainmenu_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function mainmenu_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Mainmenu::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Mainmenu::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function mainmenu_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        if (\Schema::hasTable('mainmenu_langs')) {
            $reslang = MainmenuLang::where('mainmenu_id', $id)->delete();
            $res = Mainmenu::where('id', $id)->delete();
        } else {
            $res = Mainmenu::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }

    }

    //Submenu

    public function submenu_list(Request $request,$encid=null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Submenu', 'message' => 'List Submenu', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('submenu_list')->id;
        $head = 'Submenu List';
        //Data and data
        $itemIds = explode(',', \Auth::user()->item_id);
        $componentData = Submenu::with(['submenu_langs' => function () {}])->with(['mainmenus' => function () {}])
        ->where('user_id',\Auth::user()->id)
        ->orWhereHas('submenu_langs',function($q) use($itemIds){
            if(\Auth::user()->id!=2){
                $q->whereIn('id',$itemIds);
            }
            
        })
        ->orderBy('order_num', 'asc')
        ->orderBy('updated_at', 'desc')->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();

        // dd($component);
        if($encid!=''){
            $id=\Crypt::decrypt($encid);
            $componentData = Submenu::with(['submenu_langs' => function () {}])->with(['mainmenus' => function () {}])
            ->where('user_id',\Auth::user()->id)
            ->orWhereIn('id', $itemIds)
            // ->where('id',$id)
            ->orWhereHas('submenu_langs',function($q) use($itemIds){
                $q->whereIn('id',$itemIds);
            })
            ->orderBy('updated_at', 'desc')->get();
        }
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Submenu");
        $userrole=Auth::user()->usertypes[0]->userrolename;
        return view('siteadmin.submenu.submenu_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function submenu_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Submenu', 'message' => 'List Submenu', 'status' => 0, 'link' => $userrole.'/submenu_list'],
                2 => ['title' => 'Add Submenu', 'message' => 'Add Submenu', 'status' => 0],
            ];
            $head = 'Add Submenu';
            $editF = 'A';
            $result = [];
            $article = [];
            $menulinkname = '';
            $mainmenu = Mainmenu::with(['mainmenu_langs' => function () {}])->where('status_id', 1)->get();
        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Submenu', 'message' => 'List Submenu', 'status' => 0, 'link' => $userrole.'/submenu_list'],
                2 => ['title' => 'Edit Submenu', 'message' => 'Edit Submenu', 'status' => 0],
            ];
            $head = 'Edit Submenu';
            $editF = 'E';
            $result = Submenu::with(['submenu_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($result);
            $article = Article::with(['article_langs' => function ($query) {}])->where('status_id', 1)->orderBy('updated_at', 'asc')->get();
            $mainmenu = Mainmenu::with(['mainmenu_langs' => function () {}])->where('status_id', 1)->get();
            $menulinkname = Menulinktype::select('name')->where('status_id', 1)->where('id', $result->menulinktype_id)->first();
        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('submenu_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $lang = Language::where('status_id', 1)->get();
        $menulinktype = Menulinktype::where('status_id', 1)->get();

        $services = [];
        $schemes = [];
        $tabcontent_types = Tabcontentstype::with(['tabcontentstype_langs' => function ($q) {}])->get();
        if(\Auth::user()->superuser==1){
            $menulinktype = Menulinktype::where('status_id', 1)->get();
        }else{
            $menulinktype=app('App\Http\Controllers\CommonfunctionController')->types_allowed(13,'Submenu','Menulinktype','menulinktype_id');

        }
        return view('siteadmin.submenu.submenu_add', compact('services',
            'schemes', 'component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'menulinktype', 'lang', 'article', 'menulinkname', 'mainmenu', 'usertype','tabcontent_types'));
    }

    public function submenu_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd($request->menulinktype_id);
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'menulinktype_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'parent_menu_id' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonly(),
            //  'linkvalval'=>app('App\Http\Controllers\CommonfunctionController')->linktypeval(\Crypt::decrypt($request->menulinktype_id)),
            'iconclass' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'linkSerschevalval' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            if (isset($request->linkSerschevalval) && ! empty($request->linkSerschevalval)) {

                $linkSerschevalval = $request->linkSerschevalval;
            } else {
                $linkSerschevalval = 1;
            }
            if (empty($request->linkvalval)) {
                $linkvalval = 0;
            } else {
                $linkvalval = $request->linkvalval;
            }
            if (isset($request->tabid) && ! empty($request->tabid)) {
                $tabid = \Crypt::decrypt($request->tabid);
            } else {
                $tabid = 0;
            }

            if (\Crypt::decrypt($request->menulinktype_id) == 3) {//file
                if (isset($request->linkvalval)) {
                    $imname = 'submenu'.date('yyyy:mm:dd:hh:mm:ss').'.'.$request->linkvalval->extension();
                    $path = $request->file('linkvalval')->storeAs('uploads/submenu/', $imname, 'myfile');
                    // dd($imname);
                    $dataarr = new Submenu([

                        'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                        'parent_menu_id' => $request->parent_menu_id,
                        'linkvalval' => $imname,
                        'tabid' => $tabid,
                        'icon_class' => $request->iconclass,
                        'linkSerschevalval' => $linkSerschevalval,
                        'user_id' => Auth::user()->id,
                    ]);
                } else {
                    $dataarr = new Submenu([

                        'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                        'parent_menu_id' => $request->parent_menu_id,
                        'linkSerschevalval' => $linkSerschevalval,
                        'tabid' => $tabid,
                        'icon_class' => $request->iconclass,
                        'user_id' => Auth::user()->id,
                    ]);
                }
            } else {
                $dataarr = new Submenu([

                    'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                    'parent_menu_id' => $request->parent_menu_id,
                    'linkvalval' => $linkvalval,
                    'linkSerschevalval' => $linkSerschevalval,
                    'tabid' => $tabid,
                    'icon_class' => $request->iconclass,
                    'user_id' => Auth::user()->id,
                ]);

            }

            $res = $dataarr->save();
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = SubmenuLang::where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    $datarrlang = new SubmenuLang([
                        'title' => $request->title[$lan->id] ?? '',
                        'submenu_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'Submenu Added!';

                return redirect($userrole.'/submenu_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function submenu_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd();
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'menulinktype_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'parent_menu_id' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonly(),
            'linkvalval' => app('App\Http\Controllers\CommonfunctionController')->linktypeval(\Crypt::decrypt($request->menulinktype_id) ?? $request->menulinktype_id ),
            'iconclass' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'linkSerschevalval' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),

            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);
            if (isset($request->linkSerschevalval) && ! empty($request->linkSerschevalval)) {

                $linkSerschevalval = $request->linkSerschevalval;
            } else {
                $linkSerschevalval = 1;
            }
            if (isset($request->tabid) && ! empty($request->tabid)) {
                $tabid = \Crypt::decrypt($request->tabid);
            } else {
                $tabid = 0;
            }
            if (\Crypt::decrypt($request->menulinktype_id) == 3) {//file
                if (isset($request->linkvalval)) {
                    $imname = 'submenu'.date('yyyy:mm:dd:hh:mm:ss').'.'.$request->linkvalval->extension();
                    $path = $request->file('linkvalval')->storeAs('uploads/submenu', $imname, 'myfile');
                    $dataarr = [

                        'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                        'parent_menu_id' => $request->parent_menu_id,
                        'linkvalval' => $imname,
                        'linkSerschevalval' => $linkSerschevalval,
                        'tabid' => $tabid,
                        'icon_class' => $request->iconclass,
                        'user_id' => Auth::user()->id,
                    ];
                } else {
                    $dataarr = [

                        'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                        'parent_menu_id' => $request->parent_menu_id,
                        'linkSerschevalval' => $linkSerschevalval,
                        'tabid' => $tabid,
                        'icon_class' => $request->iconclass,
                        'user_id' => Auth::user()->id,
                    ];
                }
            } else {
                $linkvalval = $request->linkvalval;
                $dataarr = [

                    'menulinktype_id' => \Crypt::decrypt($request->menulinktype_id),
                    'parent_menu_id' => $request->parent_menu_id,
                    'linkSerschevalval' => $linkSerschevalval,
                    'tabid' => $tabid,
                    'linkvalval' => $linkvalval,
                    'icon_class' => $request->iconclass,
                    'user_id' => Auth::user()->id,
                ];
            }
            // dd($dataarr);
            $res = Submenu::where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = SubmenuLang::where('submenu_id', '!=', $id)->where('title', $request->title[$lan->id])->exists() ? 1 : 0;
                    $datarrlang = [
                        'title' => $request->title[$lan->id] ?? '',
                        'lang_id' => $lan->id,
                    ];
                    if ($chkrws == 0) {

                        $reslan = SubmenuLang::where('submenu_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'Submenu Updated!';

                return redirect($userrole.'/submenu_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function submenu_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Submenu::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        } else {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Submenu::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function submenu_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);

        if (\Schema::hasTable('submenu_langs')) {
            $reslang = SubmenuLang::where('submenu_id', $id)->delete();
            $res = Submenu::where('id', $id)->delete();
        } else {
            $res = Submenu::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }

    }

    //Banner
    public function banner_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Banner', 'message' => 'List Banner', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('banner_list')->id;
        // dd($linactive);
        $head = 'Banner List';
        //Data and data
        $componentid=Modelmaster::where('name',"Banner")->first();
        // $componentData = Banner::with(['banner_langs' => function () {}])
        // // ->where('id')
        
        // ->orderBy('updated_at', 'desc')->get();
       $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Banner");
       $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
       $userrole=Auth::user()->usertypes[0]->userrolename;
       return view('siteadmin.banner.banner_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function banner_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Banner', 'message' => 'List Banner', 'status' => 0, 'link' => $userrole.'/banner_list'],
                2 => ['title' => 'Add Banner', 'message' => 'Add Banner', 'status' => 0],
            ];
            $head = 'Add Banner';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Banner', 'message' => 'List Banner', 'status' => 0, 'link' => $userrole.'/banner_list'],
                2 => ['title' => 'Edit Banner', 'message' => 'Edit Banner', 'status' => 0],
            ];
            $head = 'Edit Banner';
            $editF = 'E';
            $result = Banner::where('id', $id)->first();
        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('banner_list')->id.'a';
        $lang = Language::where('status_id', 1)->get();
        $bannercategory = Bannercategory::where('status_id', 1)->get();
        $article = Article::with(['article_langs' => function ($query) {}])->where('status_id', 1)->get();

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);

        if(\Auth::user()->superuser==1){
            $bannercategory = Bannercategory::where('status_id', 1)->get();
        }else{
            // dd($editF);
            $bannercategory=app('App\Http\Controllers\CommonfunctionController')->types_allowed(17,'Banner','Bannercategory','bannercategories_id');
            if($editF=='E'){
                $bannercategory=Bannercategory::where('status_id', 1)->where('id', $result->bannercategories_id)->get();
            }
        }

        return view('siteadmin.banner.banner_add', compact('article', 'component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'bannercategory', 'usertype'));
    }

    public function banner_save(Request $request)
    {
        $titles = $request->input('title', []);
        $rules = [];
        if(isset($request->hidden_val)&&!empty($request->hidden_val)){
            $id = \Crypt::decrypt($request->hidden_val);
        }else{
            $id = 0;
        }
        
        foreach ($titles as $lang_id => $title) {
            $rules["title.$lang_id"] = [
                'nullable',
                Rule::unique('banner_langs', 'title')
                    ->where(function ($query) use ($lang_id,$id) {
                        return $query
                        // ->where('banners_id', '!=', $id)
                        ->where('lang_id', $lang_id);
                    }),
            ];
        }
        
        $validated = $request->validate($rules);
        $titles = $request->input('title', []);

        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();
        
        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'bannercategories_id'=>'required'
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {

            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }
            if (isset($request->inner_banner_id)) {
                $inner_banner_id = 1;
            } else {
                $inner_banner_id = 0;
            }
            
            if (! empty($request->artcleid)) {
                $artcleid = $request->artcleid;
            } else {
                $artcleid = 0;
            }
            if (isset($request->polygon_poster)) {
                $imname = 'bannerpolygon'.date('yyyy:mm:dd:hh:mm:ss').$request->polygon_poster->extension();
                $path = $request->file('polygon_poster')->storeAs('uploads/bannerpolygon', $imname, 'myfile');
                $dataarr_art = new Banner([
                    'bannercategories_id' => \Crypt::decrypt($request->bannercategories_id),
                    'homepage_status' => $homepage_status,
                    'polygon_poster' => $imname,
                    'inner_banner_id'=>$inner_banner_id,
                    'article_id' => $artcleid,
                    'user_id' => Auth::user()->id,
                ]);
            } else {
                $dataarr_art = new Banner([
                    'bannercategories_id' => \Crypt::decrypt($request->bannercategories_id),
                    'homepage_status' => $homepage_status,
                    'article_id' => $artcleid,
                    'inner_banner_id'=>$inner_banner_id,
                    'user_id' => Auth::user()->id,
                ]);
            }

            $res_art = $dataarr_art->save();
            $imname ='';
            $path ='';
            $video_flag = 0;
            if ($res_art) {
                $lang = Language::where('status_id', 1)->get();
                foreach ($lang as $lan) {
                    if(isset($request->poster[$lan->id])){
                        $imname = 'banner'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                        $path = $request->file('poster')[$lan->id]->storeAs('uploads/banner', $imname, 'myfile');
                       
                        if ($request->poster[$lan->id]->extension() == 'mp4') {
                            $video_flag = 1;
                            Banner::where('id', $dataarr_art->id)->update(['video_flag' => $video_flag]);
                        }
                    }
                   
                    // $img = \Image::make($path)->resize(1905, 862);
                    // $img->save($path);
                    
                    $dataarr = new BannerLang([
                        'title' => $request->title[$lan->id] ?? '',
                        'subtitle' => $request->description[$lan->id] ?? '', 
                        'poster' => $imname,
                        'banners_id' => $dataarr_art->id,
                        'lang_id' => $lan->id,
                    ]);
                    $chkrws = BannerLang::where('title', $request->title[1])->where('lang_id', 1)->where('lang_id', $lan->id)->exists() ? 1 : 0;

                    if ($chkrws == 0) {

                        $res = $dataarr->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($chkrws == 0) {

                $res = $dataarr->save();
            } else {
                $res = false;
                $msg = 'This Name is already existing';
            }
            if ($res) {
                $success = 'Banner Added!';

                return redirect($userrole.'/banner_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function banner_update(Request $request)
    {
        $titles = $request->input('title', []);
        $rules = [];
        if(isset($request->hidden_val)&&!empty($request->hidden_val)){
            $id = \Crypt::decrypt($request->hidden_val);
        }else{
            $id = 0;
        }
        
        foreach ($titles as $lang_id => $title) {
            $rules["title.$lang_id"] = [
                'nullable',
                Rule::unique('banner_langs', 'title')
                    ->where(function ($query) use ($lang_id,$id) {
                        return $query
                        ->where('banners_id', '!=', $id)
                        ->where('lang_id', $lang_id);
                    }),
            ];
        }
        
        $validated = $request->validate($rules);
        $titles = $request->input('title', []);

        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();
        
        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'bannercategories_id'=>'required',
            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);
            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }
            if (isset($request->inner_banner_id)) {
                $inner_banner_id = 1;
            } else {
                $inner_banner_id = 0;
            }
            
            if (! empty($request->artcleid)) {
                $artcleid = $request->artcleid;
            } else {
                $artcleid = 0;
            }
            $imname ='';
            $path ='';
            if (isset($request->polygon_poster)) {
                if(isset($request->poster[$lan->id])){
                    $imname = 'bannerpolygon'.date('yyyy:mm:dd:hh:mm:ss').$request->polygon_poster->extension();
                    $path = $request->file('polygon_poster')->storeAs('uploads/bannerpolygon', $imname, 'myfile');
                }
                $dataarr_art = [
                    'bannercategories_id' => \Crypt::decrypt($request->bannercategories_id),
                    'homepage_status' => $homepage_status,
                    'polygon_poster' => $imname,
                    'article_id' => $artcleid,
                    'user_id' => Auth::user()->id,
                    'inner_banner_id'=>$inner_banner_id,
                ];
            } else {
                $dataarr_art = [
                    'bannercategories_id' => \Crypt::decrypt($request->bannercategories_id),
                    'homepage_status' => $homepage_status,
                    'article_id' => $artcleid,
                    'user_id' => Auth::user()->id,
                    'inner_banner_id'=>$inner_banner_id,
                ];
            }

            $res_art = Banner::where('id', $id)->update($dataarr_art);
            if ($res_art) {
                $lang = Language::where('status_id', 1)->get();
                foreach ($lang as $lan) {
                    if (isset($request->poster)) {

                        $imname = 'banner'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                        $path = $request->file('poster')[$lan->id]->storeAs('uploads/banner', $imname, 'myfile');
                        $video_flag = 0;
                        if ($request->poster[$lan->id]->extension() == 'mp4') {
                            $video_flag = 1;
                            Banner::where('id', $id)->update(['video_flag' => $video_flag]);
                        }
                        //    dd($path);
                        // $img = \Image::make($path)->resize(1905, 862);
                        // $image = Image::make('path_to_your_image.jpg');

                        // Remove EXIF metadata
                        // dd( $img->exif([]));
                        // $img->exif([]);

                        // Save the modified image
                        // $img->save($path);
                        $dataarr = [
                            'title' => $request->title[$lan->id] ?? '',
                            'subtitle' => $request->description[$lan->id] ?? '',
                            'poster' => $imname,
                            'banners_id' => $id,
                            'lang_id' => $lan->id,
                        ];
                    } else {
                        $dataarr = [
                            'title' => $request->title[$lan->id] ?? '',
                            'subtitle' => $request->description[$lan->id] ?? '',

                            'banners_id' => $id,
                            'lang_id' => $lan->id,
                        ];
                    }
                    $chkrws = BannerLang::where('banners_id', '!=', $id)->where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;

                    if ($chkrws == 0) {

                        $res = BannerLang::where('banners_id', $id)->where('lang_id', $lan->id)->update($dataarr);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'Banner Updated!';

                return redirect($userrole.'/banner_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function banner_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Banner::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Banner::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function banner_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);

        if (\Schema::hasTable('portservices_langs')) {
            $reslang = BannerLang::where('banners_id', $id)->delete();
            $res = Banner::where('id', $id)->delete();
        } else {
            $res = Banner::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    public function orderchange_list_fun(Request $request, $encid = null)
    {
        // $catid = \Crypt::decrypt($encid);
        // dd($catid);
        try {
            // dd();

            $catid = \Crypt::decrypt($encid);
            // dd($catid);
            $id = \Crypt::decrypt($request->id);
            if ($catid == 1) {
                //banner
                $res = Banner::where('id', '=', $id)->update(['order_num' => $request->val]);
            } elseif ($catid == 2) {
                //Mainmenu
                $res = Mainmenu::where('id', '=', $id)->update(['order_num' => $request->val]);

            } elseif ($catid == 3) {
                //submenu
                $res = Submenu::where('id', '=', $id)->update(['order_num' => $request->val]);

            } elseif ($catid == 4) {
                //Article
                $res = Article::where('id', '=', $id)->update(['order_num' => $request->val]);

            } elseif ($catid == 5) {
                //Gallery
                $res = Gallery::where('id', '=', $id)->update(['order_num' => $request->val]);

            } elseif ($catid == 6) {
                //schemes
                $res = Scheme::where('id', '=', $id)->update(['order_num' => $request->val]);

            } elseif ($catid == 7) {
                //schemes
                $res = Portservice::where('id', '=', $id)->update(['order_num' => $request->val]);

            } elseif ($catid == 8) {
                //schemes
                $res = BOD::where('id', '=', $id)->update(['order_num' => $request->val]);

            }
            // return response()->json(['html' => $res,'flag'=>$res]);
            // dd($res);
            //
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            return response()->json(['html' => $exception, 'flag' => 3]);
        } catch (\Throwable $exception) {
            return response()->json(['html' => $exception, 'flag' => 4]);
        } catch (\Illuminate\Database\QueryException $exception) {

            return response()->json(['html' => $exception, 'flag' => 5]);
        }
        // dd($res);
        if ($res) {
            $success = 'Order Changed!';

            return response()->json(['html' => $success, 'flag' => 1]);
        } else {
            $error = 'Error in Order Change';

            return response()->json(['html' => $error, 'flag' => 2]);
        }
    }

    //Article Type
    public function articletype_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Article Type', 'message' => 'List Article Type', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('articletype_list')->id;
        // dd($linactive);
        $head = 'Article Type List';
        //Data and data
        $componentData = Articletype::get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();

        // dd($component);
        $userrole=Auth::user()->usertypes[0]->userrolename;
        return view('masteradmin.articletype.articletype_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function articletype_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Article Type', 'message' => 'List Article Type', 'status' => 0, 'link' => $userrole.'/articletype_list'],
                2 => ['title' => 'Add Article Type', 'message' => 'Add Article Type', 'status' => 0],
            ];
            $head = 'Add Article Type';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Article Type', 'message' => 'List Article Type', 'status' => 0, 'link' => $userrole.'/articletype_list'],
                2 => ['title' => 'Edit Article Type', 'message' => 'Edit Article Type', 'status' => 0],
            ];
            $head = 'Edit Article Type';
            $editF = 'E';
            $result = Articletype::where('id', $id)->first();
        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('articletype_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);

        return view('masteradmin.articletype.articletype_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'usertype'));
    }

    public function articletype_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'name' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $dataarr = new Articletype([
                'name' => $request->name,
                'user_id' => Auth::user()->id,
            ]);
            $chkrws = Articletype::where('name', $request->name)->exists() ? 1 : 0;

            if ($chkrws == 0) {

                $res = $dataarr->save();
            } else {
                $res = false;
                $msg = 'This Name is already existing';
            }
            if ($res) {
                $success = 'Article Type Added!';

                return redirect($userrole.'/articletype_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function articletype_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'name' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);
            $dataarr = [
                'name' => $request->name,
                'user_id' => Auth::user()->id,
            ];
            $chkrws = Articletype::where('id', '!=', $id)->where('name', $request->name)->exists() ? 1 : 0;

            if ($chkrws == 0) {

                $res = Articletype::where('id', $id)->update($dataarr);
            } else {
                $res = false;
                $msg = 'This Name is already existing';
            }
            if ($res) {
                $success = 'Article Type Updated!';

                return redirect($userrole.'/articletype_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function articletype_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Articletype::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Articletype::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function articletype_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $res = Articletype::where('id', $id)->delete();
        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    //Article
    public function article_list(Request $request, $encid=null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());

        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Article Type', 'message' => 'List Article Type', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('article_list')->id;
        $head = 'Article List';
        //Data and data
        $itemIds =[];
       
        $itemIds = explode(',', \Auth::user()->item_id);
        $componentData = Article::with(['article_langs' => function ($query) {}])
        // ->where('user_id',\Auth::user()->id)
        // ->orWhereIn('id', $itemIds)
        ->whereHas('article_langs',function($q) use($itemIds){
            if(\Auth::user()->id!=2){
              $q->where('title','!=','')->where('user_id',\Auth::user()->id);
                if(!empty(\Auth::user()->component_permission_id)&& !empty($itemIds)) {
                    $q ->orWhereIn('id', $itemIds);
                }
       
            }
           
        })
        ->orderBy('updated_at', 'desc')->get();
       

        // dd($componentData);
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();

        // dd($encid);
        if($encid!=''){
            $id=\Crypt::decrypt($encid);
            $componentData = Article::with(['article_langs' => function ($query) {}])
            ->where('user_id',\Auth::user()->id)
            
            
            ->orderBy('updated_at', 'desc')
            ->get();

        $userrole=Auth::user()->usertypes[0]->userrolename;}
        $userrole=Auth::user()->usertypes[0]->userrolename;
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Article");
        return view('siteadmin.article.article_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }
    public function article_deleter_poser(Request $request, $encid = null){

        $id = \Crypt::decrypt($encid);

        $res = Article::find($id);
        $redel=false;
        // ****
        $relang = $res->article_langs()->get();
        foreach ($relang as $galLang) {

            $galLangimg = public_path('uploads/article/').$galLang->poster;
            if (file_exists($galLangimg)) {
                @unlink($galLangimg);

            }
            $poster=['poster'=>''];
            $redel=ArticleLang::where('id',$galLang->id)->update($poster);
        }


        if ($redel) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }

        //  $id=\Crypt::decrypt($encid);
        //  $res=Article::where('id',$id)->delete();
        //  if($res){
        //     $success="Deleted Successfully";
        //     return back()->with(['success' => $success]);
        //  }else{
        //     $success="Error in delete";
        //     return back()->with(['error' => $success]);
        //  }
    }
    public function article_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Article', 'message' => 'List Article', 'status' => 0, 'link' => $userrole.'/article_list'],
                2 => ['title' => 'Add Article', 'message' => 'Add Article', 'status' => 0],
            ];
            $head = 'Add Article';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Article', 'message' => 'List Article', 'status' => 0, 'link' => $userrole.'/article_list'],
                2 => ['title' => 'Edit Article', 'message' => 'Edit Article', 'status' => 0],
            ];
            $head = 'Edit Article';
            $editF = 'E';
            $result = Article::with(['article_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($id);
        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('article_list')->id.'a';
        if(!empty(\Auth::user()->sub_cat_id)){

            $subcatid = array_filter(explode(',', \Auth::user()->sub_cat_id));


            $media_categories_id = Articletype::
          
        where('status_id', 1)->get();
            // $subcatid=explode(',',\Auth::user()->sub_cat_id);

            $articletype = Articletype::where('status_id', 1)->whereIn('id',$subcatid)->get();

        }else{
            $articletype = Articletype::where('status_id', 1)->get();

        }
        $lang = Language::where('status_id', 1)->get();
        $schemes = Portservice::with(['portservices_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();
        $services = Publicservice::with(['publicservice_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();
        $submenu = Submenu::with(['submenu_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();
        $mainmenu = Mainmenu::with(['mainmenu_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();
        //Data
        $pattanrajyasabhas = Pattanrajysabha::where('status_id', 1)->get();
        $eodb = EoDB::where('status_id', 1)->get();
        $rti = RTI::where('status_id', 1)->get();
        $citizenchapter = Cityzenchapter::where('status_id', 1)->get();
        $transparencyplan = Transparencyplan::where('status_id', 1)->get();
        $icc = InternalComplaintsCommittee::where('status_id', 1)->get();

        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);

        if(\Auth::user()->superuser==1){
            $articletype = Articletype::where('status_id', 1)->get();
        }else{
            $articletype=app('App\Http\Controllers\CommonfunctionController')->types_allowed(11,'Article','Articletype','articletypes_id');
            if($editF=='E'){
                $articletype=Articletype::where('status_id', 1)->where('id', $result->articletypes_id)->get();
            }
        }

        return view('siteadmin.article.article_add', compact('schemes', 'services', 'component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'articletype', 'usertype', 'submenu', 'mainmenu',
            'pattanrajyasabhas',
            'eodb',
            'rti',
            'citizenchapter',
            'transparencyplan',
            'icc'
        ));
    }

    public function article_save(Request $request)
    {
        $input = $request->all(); // or your custom array

        $titles = $request->input('title', []);
        $rules = [];
        
        foreach ($titles as $lang_id => $title) {
            $rules["title.$lang_id"] = [
                'nullable',
                Rule::unique('article_langs', 'title')
                    ->where(function ($query) use ($lang_id) {
                        return $query->where('lang_id', $lang_id);
                    }),
            ];
        }
        
        $validated = $request->validate($rules);
        $titles = $request->input('title', []);

        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();
        
        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd($request->dispschemeser);
        if (empty($request->dispschemeser)) {
            $validator = \Validator::make($request->all(), [
                'title' => 'array|required|array|min:1',
                'title.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'description' => 'array|required|array|min:1',
                'description.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'poster.*' => app('App\Http\Controllers\CommonfunctionController')->getposterValArticleposter(),
                'articletypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'dispschemeser' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),
                'homepage_status' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),

            ]);
        } else {
            $validator = \Validator::make($request->all(), [
                'title' => 'array|required|array|min:1',
                'title.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'description' => 'array|required|array|min:1',
                'description.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'poster.*' => app('App\Http\Controllers\CommonfunctionController')->getposterValArticleposter(),
                'articletypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'dispschemeser' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'homepage_status' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),

            ]);
        }

        if ($validator->fails()) {
            // // dd($validator->errors());
            return back()->withInput()->withErrors($validator->errors());
        }

        try {

            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }

            $arttypeid = \Crypt::decrypt($request->articletypes_id);
            if ($arttypeid == 3) {
                //schemes
                $dataarr_art = new Article([
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'schemes_id' => $request->dispschemeser,
                ]);

            } elseif ($arttypeid == 13) {
                //services
                $dataarr_art = new Article([
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                ]);

            } elseif ($arttypeid == 16) {
                //submenu
                $dataarr_art = new Article([
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                ]);

            } elseif ($arttypeid == 21) {
                //mainmenu
                $dataarr_art = new Article([
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                ]);

            } elseif ($arttypeid == 22) {
                //pattanrajyasabha
                $dataarr_art = new Article([
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                ]);

            } elseif ($arttypeid == 44) {
                //eodb
                $dataarr_art = new Article([
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                ]);

            } elseif ($arttypeid == 45) {
                //rti
                $dataarr_art = new Article([
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                ]);

            } else {
                $dataarr_art = new Article([
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => 0,
                ]);
            }
            // dd($dataarr_art);
            $res_art = $dataarr_art->save();
            // dd($dataarr_art);
            if ($res_art) {

                $lang = Language::where('status_id', 1)->get();
                foreach ($lang as $lan) {
                    if (empty($request->poster[$lan->id])) {
                        $imname = '';
                        // return back()->withInput()->with('error',$lan->name.' Poster needed');
                    } else {
                        $imname = 'article'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                        $path = $request->file('poster')[$lan->id]->storeAs('uploads/article', $imname, 'myfile');
                    }

                    $dataarr = new ArticleLang([
                        'title' => $request->title[$lan->id] ?? '',

                        'description' => $request->description[$lan->id] ?? '',
                        'poster' => $imname,
                        'article_id' => $dataarr_art->id,
                        'lang_id' => $lan->id,
                    ]);
                    $chkrws = ArticleLang::where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;

                    if ($chkrws == 0) {

                        $res = $dataarr->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            // dd($res);
            if ($res) {
                $success = 'Article Added!';
                $article_id = $dataarr_art->id;
                //    dd($dataarr_art->id);
                $breadcrumb = [
                    0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                    1 => ['title' => 'List Article', 'message' => 'List Article', 'status' => 0, 'link' => $userrole.'/article_list'],
                    2 => ['title' => 'Article Upload', 'message' => 'Article Upload', 'status' => 0],
                ];
                $head = 'Article Upload';
                $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
                $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
                $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('article_list')->id.'a';
                $articletype = Articletype::where('status_id', 1)->get();
                $lang = Language::where('status_id', 1)->get();
                $editF = 'A';
                //Data
                $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
                $artdet = Article::whereId($article_id)->first();
                // dd($article_id.$artdet);
                $artalbum = Articleattachments::with(['articleattachments_langs' => function ($query) {}])->where('article_id', $article_id)->where('status_id', 1)->get();
                $artalbumcnt = count($artalbum);

                // dd($artalbumcnt);
                return view('siteadmin.article.article_upload', compact(['article_id', 'breadcrumbarr', 'linactive', 'articletype', 'lang', 'component', 'head', 'editF', 'artdet', 'artalbum', 'artalbumcnt'], 'usertype'));
                // return redirect($userrole.'/article_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            // $error=$e;
            return back()->withInput()->with('error', $e);
        }
    }

    public function article_update(Request $request)
    {
       

        $titles = $request->input('title', []);
        $rules = [];
        if(isset($request->hidden_val)&&!empty($request->hidden_val)){
            $id = \Crypt::decrypt($request->hidden_val);
        }else{
            $id = 0;
        }
        
        foreach ($titles as $lang_id => $title) {
            $rules["title.$lang_id"] = [
                'nullable',
                Rule::unique('article_langs', 'title')
                    ->where(function ($query) use ($lang_id,$id) {
                        return $query
                        ->where('article_id', '!=', $id)
                        ->where('lang_id', $lang_id);
                    }),
            ];
        }
        
        $validated = $request->validate($rules);
        $titles = $request->input('title', []);

        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();
        
        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd($request->all());
        if (empty($request->dispschemeser)) {
            $validator = \Validator::make($request->all(), [
                'title' => 'array|required|array|min:1',
                'title.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'description' => 'array|required|array|min:1',
                'description.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'poster.*' => app('App\Http\Controllers\CommonfunctionController')->getposterValArticleposter(),
                'articletypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'dispschemeser' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),
                'homepage_status' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),

                'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            ]);
        } else {
            $validator = \Validator::make($request->all(), [
                'title' => 'array|required|array|min:1',
                'title.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'description' => 'array|required|array|min:1',
                'description.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'poster.*' => app('App\Http\Controllers\CommonfunctionController')->getposterValArticleposter(),
                'articletypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'dispschemeser' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'homepage_status' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),

                'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            ]);
        }
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);
            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }
            $arttypeid = \Crypt::decrypt($request->articletypes_id);
            if ($arttypeid == 3) {
                //schemes
                $dataarr = [
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'schemes_id' => $request->dispschemeser,
                    'scervices_id' => 0,
                ];
            } elseif ($arttypeid == 13) {
                //services
                $dataarr = [
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                    'schemes_id' => 0,
                ];
            } elseif ($arttypeid == 16) {
                //submenu
                $dataarr = [
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                    'schemes_id' => 0,
                ];
            } elseif ($arttypeid == 21) {
                //mainmenu
                $dataarr = [
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                    'schemes_id' => 0,
                ];
            } elseif ($arttypeid == 22) {
                //pattansabha
                $dataarr = [
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                    'schemes_id' => 0,
                ];
            } elseif ($arttypeid == 44) {//eodb
                $dataarr = [
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                    'schemes_id' => 0,
                ];
            } elseif ($arttypeid == 45) {
                //rti
                $dataarr = [
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                    'schemes_id' => 0,
                ];
            } else {
                $dataarr = [
                    'articletypes_id' => \Crypt::decrypt($request->articletypes_id),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                    'scervices_id' => $request->dispschemeser,
                    'schemes_id' => 0,
                ];
            }

            // dd($dataarr);

            $res_art = Article::where('id', $id)->update($dataarr);

            $i = 0;
            if ($res_art) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    if (isset($request->poster[$lan->id])) {
                        $imname = 'article'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                        $path = $request->file('poster')[$lan->id]->storeAs('uploads/article', $imname, 'myfile');
                        $dataarrq = [
                            'title' => $request->title[$lan->id] ?? '',
                            'description' => $request->description[$lan->id] ?? '',
                            'poster' => $imname,
                            'lang_id' => $lan->id,
                        ];
                    } else {
                        $dataarrq = [
                            'title' => $request->title[$lan->id] ?? '',
                            'description' => $request->description[$lan->id] ?? '',
                            'lang_id' => $lan->id,
                        ];
                    }

                    $chkrws = ArticleLang::where('article_id', '!=', $id)->where('title', $request->title[$lan->id])->exists() ? 1 : 0;

                    if ($chkrws == 0) {

                        $res = ArticleLang::where('article_id', $id)->where('lang_id', $lan->id)->update($dataarrq);

                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                    $i++;
                }
            }

            if ($res) {
                // $success = "Article Updated!";
                // return redirect($userrole.'/article_list')->with(['success' => $success]);
                $article_id = $id;
                $breadcrumb = [
                    0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                    1 => ['title' => 'List Article', 'message' => 'List Article', 'status' => 0, 'link' => $userrole.'/article_list'],
                    2 => ['title' => 'Article Upload', 'message' => 'Article Upload', 'status' => 0],
                ];
                $head = 'Article Upload';
                $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
                $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
                $linactive = $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('article_list')->id.'a';
                $articletype = Articletype::where('status_id', 1)->get();
                $lang = Language::where('status_id', 1)->get();
                $editF = 'A';
                //Data
                $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
                $artdet = Article::whereId($article_id)->first();
                $artalbum_res = Articleattachments::with(['articleattachments_langs' => function ($query) {}])
                ->where('article_id', $article_id)
                // ->where('id',19)
               
                    ->where('status_id', 1);
                if($artdet->articletypes_id=560){
                    $artalbum_res->where('user_id',\Auth::user()->id);
                }
                $artalbum= $artalbum_res->get();
               
                $artalbumcnt = count($artalbum);

                // dd($artalbum);
                return view('siteadmin.article.article_upload', compact(['article_id', 'breadcrumbarr', 'linactive', 'articletype', 'lang', 'component', 'head', 'editF', 'artdet', 'artalbum', 'artalbumcnt'], 'usertype'));
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function article_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Article::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Article::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function article_delete(Request $request, $encid = null)
    {

        $id = \Crypt::decrypt($encid);

        $res = Article::find($id);

        // ****
        $relang = $res->article_langs()->get();
        foreach ($relang as $galLang) {
            $galLangimg = public_path('uploads/article/').$galLang->poster;
            if (file_exists($galLangimg)) {
                @unlink($galLangimg);

            }
        }
        $res->article_langs()->delete();

        $galalbumattachment = Articleattachments::where('article_id', $id)->select('id')->get();

        foreach ($galalbumattachment as $attch) {
            $artalbumattachmentlang = ArticleattachmentsLang::where('articleattachments_id', $attch->id)->get();
            foreach ($artalbumattachmentlang as $attchlang) {
                $artalbumimg = public_path('uploads/article_attachments/').$attchlang->file;
                if (file_exists($artalbumimg)) {
                    @unlink($artalbumimg);

                }
                ArticleattachmentsLang::where('id', $attchlang->id)->delete();
            }
        }

        $res->articleattachments()->delete();

        $res->delete();

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }

        //  $id=\Crypt::decrypt($encid);
        //  $res=Article::where('id',$id)->delete();
        //  if($res){
        //     $success="Deleted Successfully";
        //     return back()->with(['success' => $success]);
        //  }else{
        //     $success="Error in delete";
        //     return back()->with(['error' => $success]);
        //  }
    }

    public function articleattachmentsstore(Request $request, $encid)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd($encid);

        $id = \Crypt::decrypt($encid);

        $validator = \Validator::make(
            $request->all(),
            [
                //'file' => 'required|mimes:pdf,doc,docx,odt,jpeg,png,jpg,gif,svg|max:5000000|dimensions:max_width=500,max_height=500',
                'file' => app('App\Http\Controllers\CommonfunctionController')->articleattachment(),
                'lang_id' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonly(),

            ],
            [
                'file.required' => 'File is required. ',
                'file.mimes' => 'Invalid image format.',
                'file.max' => 'Max size of 5MB.',
                //'file.dimensions' => 'Image resolution does not meet the requirement. Size of the image should be 500 x 500 (w x h). ',

            ]
        );

        if ($validator->fails()) {
            // dd($validator->errors());

            return back()->withInput()->withErrors($validator->errors());
        }
        try {

            $artdet = Article::where('id', $id)->first();
            // dd('trues');

            $formdata = new Articleattachments([

                'article_id' => $id,
                'status_id' => 1,
                'user_id' => Auth::user()->id,

            ]);
            $resatt = $formdata->save();

            $lang = Language::where('status_id', 1)->get();

            $files = $request->file;

            $imageName = 'articleattachment'.$request->lang_id.time().rand().'.'.$files->extension();
            $size = $files->getSize();
            $files->move(public_path('uploads/articleattachments/'), $imageName);

            $dataarr = new ArticleattachmentsLang([
                'articleattachments_id' => $formdata->id,
                'alt' => 'articleattachment'.$request->lang_id,
                'file' => $imageName,
                'size' => 1,
                'title' => $request->description,
                'description' => $request->description,
                'lang_id' => $request->lang_id,
            ]);
            // dd($dataarr);
            $resattLang = $dataarr->save();
            // dd($resattLang);
            if ($resattLang) {
                $success = 'Article attachment uploaded!';

                // dd($success);
                return redirect($userrole.'/article_list')->withSuccess($success);
            } else {
                $error = 'Not Uploaded';

                return redirect($userrole.'/article_list')->with('error', $error);
            }

        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);

            return back()->withInput()->withErrors($exception);
        } catch (\Throwable $exception) {

            return back()->withInput()->withErrors($exception);
        } catch (\Illuminate\Database\QueryException $exception) {

            return back()->withInput()->withErrors($exception);
        }
    }

    public function articleattachmentslist(Request $request, $encid)
    {
        // dd();
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $id = \Crypt::decrypt($encid);
        $article_id = $id;
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Article', 'message' => 'List Article', 'status' => 0, 'link' => $userrole.'/article_list'],
            2 => ['title' => 'Article Upload', 'message' => 'Article Upload', 'status' => 0],
        ];
        $head = 'Article Upload';
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('article_list')->id.'a';
        $articletype = Articletype::where('status_id', 1)->get();
        $lang = Language::where('status_id', 1)->get();
        $editF = 'A';
        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        $artdet = Article::whereId($article_id)->first();
        $artalbum_res = Articleattachments::with(['articleattachments_langs' => function ($query) {}])
        ->where('article_id', $article_id)
        // ->where('id',19)
       
            ->where('status_id', 1);
        if($artdet->articletypes_id=560){
            $artalbum_res->where('user_id',\Auth::user()->id);
        }
        $artalbum= $artalbum_res->get();
        $artalbumcnt = count($artalbum);

        // dd($artalbum);
        return view('siteadmin.article.article_upload', compact(['article_id', 'breadcrumbarr', 'linactive', 'articletype', 'lang', 'component', 'head', 'editF', 'artdet', 'artalbum', 'artalbumcnt'], 'usertype'));
    }

    public function articleattachmentsdel(Request $request, $id)
    {
        if ($request->ajax()) {

            $artalbum = Articleattachments::whereId($id)->select('id')->first();
            $artalbumattachment = ArticleattachmentsLang::where('articleattachments_id', $artalbum->id)->get();

            foreach ($artalbumattachment as $attch) {
                $artalbumimg = public_path('uploads/articleattachments/').$attch->file;
                if (file_exists($artalbumimg)) {
                    @unlink($artalbumimg);

                }
                ArticleattachmentsLang::where('id', $id)->delete();
            }
            Articleattachments::where('id', $attch->id)->delete();

            return response()->json(['success' => 'Data Updated successfully.']);
        }
    }

    //Gallery
    public function gallery_list(Request $request,$encid=null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Gallery', 'message' => 'List Gallery', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('gallery_list')->id;
        $head = 'Gallery List';
        //Data and data
        $componentData = Gallery::with(['gallery_langs' => function ($query) {}])->orderBy('updated_at', 'desc')->get();
        // dd($componentData);
        $itemIds = explode(',', \Auth::user()->item_id);
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();

        // dd($component);
        if($encid!=''){
            $id=\Crypt::decrypt($encid);
            $componentData = Gallery::with(['gallery_langs' => function ($query) {}])
            ->where('user_id',\Auth::user()->id)
            ->orWhereHas('gallery_langs',function($q) use($itemIds){
                $q->whereIn('id',$itemIds);
            })
            ->orderBy('updated_at', 'desc')->get();

        }
        $userrole=Auth::user()->usertypes[0]->userrolename;
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Gallery");

        return view('siteadmin.gallery.gallery_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function gallery_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Gallery', 'message' => 'List Gallery', 'status' => 0, 'link' => $userrole.'/gallery_list'],
                2 => ['title' => 'Add Gallery', 'message' => 'Add Gallery', 'status' => 0],
            ];
            $head = 'Add Gallery';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Gallery', 'message' => 'List Gallery', 'status' => 0, 'link' => $userrole.'/gallery_list'],
                2 => ['title' => 'Edit Gallery', 'message' => 'Edit Gallery', 'status' => 0],
            ];
            $head = 'Edit Gallery';
            $editF = 'E';
            $result = Gallery::with(['gallery_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($id);
        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('gallery_list')->id.'a';
        $gallerytype = Gallerytype::with(['gallerytype_langs' => function () {}])->where('status_id', 1)->get();
        $lang = Language::where('status_id', 1)->get();

        $articletype = Articletype::where('status_id', 1)->get();
        // dd($articletype);
        $lang = Language::where('status_id', 1)->get();
        $schemes = [];
        $services = [];
        // dd($services);
        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
       
        //    dd($gallerycategory);
        $othercatdatares = GalleryOtherType::with(['gallery_other_type_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();

        if(\Auth::user()->superuser==1){
            $gallerycategory = GalleryCategory::where('status_id', 1)->get();
        }else{
            // $gallerycategory=app('App\Http\Controllers\CommonfunctionController')->types_allowed(14,'Gallery','GalleryCategory','articletypes_id');
            $gallerycategory = GalleryCategory::where('status_id', 1)->get();
        }
        // dd($othercatdatares);
        return view('siteadmin.gallery.gallery_add', compact('articletype', 'schemes', 'services', 'component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'gallerytype', 'gallerycategory', 'usertype', 'othercatdatares'));
    }

    public function gallery_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd($request->all());
        if(empty($request->gallerycategory_id)){
            return back()->withInput()->with('error', 'Gallery Category is needed');
        }
        if(empty($request->articletypes_id)){
            return back()->withInput()->with('error', 'Gallery Type is needed');
        }
        $art_id = \Crypt::decrypt($request->articletypes_id);
        // dd($art_id);
        if ($art_id == 14) {
            $validator = \Validator::make($request->all(), [
                'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'description.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'poster.*' => app('App\Http\Controllers\CommonfunctionController')->galleryposter(),
                'articletypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'dispschemeser' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'videolink' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'homepage_status' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),

            ]);
        } else {
            $validator = \Validator::make($request->all(), [
                'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'description.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'poster.*' => app('App\Http\Controllers\CommonfunctionController')->galleryposter(),
                'articletypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'dispschemeser' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),
                'videolink' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'homepage_status' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),

            ]);
        }

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {

            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }

            $arttypeid = \Crypt::decrypt($request->articletypes_id);

            $dataarr_art = new Gallery([
                'gallerytype_id' => \Crypt::decrypt($request->articletypes_id),
                'date' => date('Y-m-d'),
                'gallarycategory_id' => \Crypt::decrypt($request->gallerycategory_id),
                'homepage_status' => $homepage_status,
                'user_id' => Auth::user()->id,
                'videolink' => $request->videolink,
                'schemes_id' => 0,
            ]);

            $res_art = $dataarr_art->save();
            if ($res_art) {
                $lang = Language::where('status_id', 1)->get();
                foreach ($lang as $lan) {
                    // if(isset)
                    if(!empty($request->poster[$lan->id])){
                        $imname = 'gallery'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                        $path = $request->file('poster')[$lan->id]->storeAs('uploads/gallery', $imname, 'myfile');
                    }
                    // $img = \Image::make($path)->resize(417.99, 380);
                    // $img->save($path);
                    if(!empty($request->title[$lan->id])){
                        $dataarr = new GalleryLang([
                            'title' => $request->title[$lan->id] ?? '',
                            'description' => $request->description[$lan->id] ?? '',
                            'poster' => $imname ?? '',
                            'gallery_id' => $dataarr_art->id,
                            'lang_id' => $lan->id,
                        ]);
                        $chkrws = GalleryLang::where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;

                        if ($chkrws == 0) {
                            if(!empty($request->title[$lan->id])){
                                $res = $dataarr->save();
                            }
                            $res=true;
                            
                        } else {
                            $res = false;
                            $msg = 'This Name is already existing';
                        }
                    }
                    
                    
                }
            }

            if ($res) {
                $success = 'Gallery Added!';
                $gallery_id = $dataarr_art->id;
                $breadcrumb = [
                    0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                    1 => ['title' => 'List Gallery', 'message' => 'List Gallery', 'status' => 0, 'link' => $userrole.'/gallery_list'],
                    2 => ['title' => 'Gallery Upload', 'message' => 'Gallery Upload', 'status' => 0],
                ];
                $head = 'Gallery Upload';
                $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
                $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
                $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('gallery_list')->id.'a';
                $articletype = Gallerytype::where('status_id', 1)->get();
                $lang = Language::where('status_id', 1)->get();
                $editF = 'A';
                //Data
                $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
                $artdet = Gallery::whereId($gallery_id)->first();
                $artalbum = Galleryattachments::with(['galleryattachments_langs' => function ($query) {}])->where('gallery_id', $gallery_id)->where('status_id', 1)->get();
                $artalbumcnt = count($artalbum);

                return view('siteadmin.gallery.gallery_upload', compact(['gallery_id', 'breadcrumbarr', 'linactive', 'articletype', 'lang', 'component', 'head', 'editF', 'artdet', 'artalbum', 'artalbumcnt'], 'usertype'));
                // return redirect($userrole.'/gallery_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function gallery_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd($request->dispschemeser);
        if(empty($request->gallerycategory_id)){
            return back()->withInput()->with('error', 'Gallery Category is needed');
        }
        if(empty($request->articletypes_id)){
            return back()->withInput()->with('error', 'Gallery Type is needed');
        }
        $art_id = \Crypt::decrypt($request->articletypes_id);
        if ($art_id == 14) {
            $validator = \Validator::make($request->all(), [
                'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'description.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'poster.*' => app('App\Http\Controllers\CommonfunctionController')->galleryposter(),
                'articletypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'dispschemeser' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'videolink' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'homepage_status' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),

            ]);
        } else {
            $validator = \Validator::make($request->all(), [
                'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'description.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
                'poster.*' => app('App\Http\Controllers\CommonfunctionController')->galleryposter(),
                'articletypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                'dispschemeser' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
                'videolink' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),

                'homepage_status' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonlyNotreq(),

            ]);
        }
        // dd($validator->fails());
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);
            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }

            $arttypeid = \Crypt::decrypt($request->articletypes_id);
            // dd($arttypeid);
            // if($arttypeid==3){
            //     //schemes

            $dataarr = [
                'gallerytype_id' => \Crypt::decrypt($request->articletypes_id),
                'date' => date('Y-m-d'),
                'gallarycategory_id' => \Crypt::decrypt($request->gallerycategory_id),
                'homepage_status' => $homepage_status,
                'videolink' => $request->videolink,
                'user_id' => Auth::user()->id,
                'schemes_id' => $request->dispschemeser,
            ];

           

            $res_art = Gallery::where('id', $id)->update($dataarr);

            $i = 0;
            if ($res_art) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    if (isset($request->poster[$lan->id])) {
                        $imname = 'gallery'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                        $path = $request->file('poster')[$lan->id]->storeAs('uploads/gallery', $imname, 'myfile');
                        // $img = \Image::make($path)->resize(417.99, 380);
                        // $img->save($path);
                        if(!empty($request->title[$lan->id])){
                        $dataarrq = [
                            'title' => $request->title[$lan->id],
                            'description' => $request->description[$lan->id],
                            'poster' => $imname,
                            'lang_id' => $lan->id,
                        ];
                    }
                    } else {
                        if(!empty($request->title[$lan->id])){
                        $dataarrq = [
                            'title' => $request->title[$lan->id],
                            'description' => $request->description[$lan->id],
                            'lang_id' => $lan->id,
                        ];
                    }
                    }

                    $chkrws = GalleryLang::where('gallery_id', '!=', $id)->where('title', $request->title[$lan->id])->exists() ? 1 : 0;

                    if ($chkrws == 0) {
                        if(!empty($request->title[$lan->id])){
                        $res = GalleryLang::where('gallery_id', $id)->where('lang_id', $lan->id)->update($dataarrq);
                        }
                        else{
                            $res = true;
                        }
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                    $i++;
                }
            }

            if ($res) {
                // $success = "Article Updated!";
                // return redirect($userrole.'/gallery_list')->with(['success' => $success]);
                $gallery_id = $id;
                $breadcrumb = [
                    0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                    1 => ['title' => 'List Gallery', 'message' => 'List Gallery', 'status' => 0, 'link' => $userrole.'/gallery_list'],
                    2 => ['title' => 'Gallery Upload', 'message' => 'Gallery Upload', 'status' => 0],
                ];
                $head = 'Gallery Upload';
                $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
                $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
                $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('gallery_list')->id.'a';
                $articletype = Articletype::where('status_id', 1)->get();
                $lang = Language::where('status_id', 1)->get();
                $editF = 'A';
                //Data
                $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
                $artdet = Gallery::whereId($gallery_id)->first();

                $artalbum = Galleryattachments::with(['galleryattachments_langs' => function ($query) {}])->where('gallery_id', $gallery_id)->where('status_id', 1)->get();

                $artalbumcnt = count($artalbum);

                // dd($artalbum);
                return view('siteadmin.gallery.gallery_upload', compact(['gallery_id', 'breadcrumbarr', 'linactive', 'articletype', 'lang', 'component', 'head', 'editF', 'artdet', 'artalbum', 'artalbumcnt'], 'usertype'));
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function gallery_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Gallery::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Gallery::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function gallery_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);

        $res = Gallery::find($id);

        // ****
        $relang = $res->gallery_langs()->get();
        foreach ($relang as $galLang) {
            $galLangimg = public_path('uploads/gallery/').$galLang->poster;
            if (file_exists($galLangimg)) {
                @unlink($galLangimg);

            }
        }
        $res->gallery_langs()->delete();

        $galalbumattachment = Galleryattachments::where('gallery_id', $id)->select('id')->get();

        foreach ($galalbumattachment as $attch) {
            $artalbumattachmentlang = GalleryattachmentsLang::where('galleryattachments_id', $attch->id)->get();
            foreach ($artalbumattachmentlang as $attchlang) {
                $artalbumimg = public_path('uploads/gallery_attachments/').$attchlang->file;
                if (file_exists($artalbumimg)) {
                    @unlink($artalbumimg);

                }
                GalleryattachmentsLang::where('id', $attchlang->id)->delete();
            }
        }

        $res->galleryattachments()->delete();

        // ****
        //  $artalbum = Galleryattachments::whereId($id)->select('id')->first();
        // $artalbumattachment=GalleryattachmentsLang::where('articleattachments_id',$artalbum->id)->get();

        // foreach($artalbumattachment as $attch){
        //     $artalbumimg = public_path('uploads/gallery_attachments/') . $attch->file;
        //     if (file_exists($artalbumimg)) {
        //         @unlink($artalbumimg);

        //     }
        //     GalleryattachmentsLang::where('id',$id)->delete();
        // }
        // ***

        // $galattch=$res->galleryattachments()->get();
        // foreach($galattch as $attch){
        //     $reattlang=GalleryattachmentsLang::where('galleryattachments_id',$attch->id)->delete();

        // }
        // $galattch=$res->galleryattachments()->delete();
        $res->delete();

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    public function gallery_attachmentsstore(Request $request, $encid)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd($request->all());
        $validator = \Validator::make(
            $request->all(),
            [
                // 'encid'=>app('App\Http\Controllers\CommonfunctionController')->langtitle(),
                //'file' => 'required|mimes:pdf,doc,docx,odt,jpeg,png,jpg,gif,svg|max:5000000|dimensions:max_width=500,max_height=500',
                'file' => app('App\Http\Controllers\CommonfunctionController')->galleryattachment(),
                'lang_id' => app('App\Http\Controllers\CommonfunctionController')->getdigitsonly(),

            ],
            [
                'file.required' => 'File is required. ',
                'file.mimes' => 'Invalid image format.',
                'file.max' => 'Max size of 5MB.',
                //'file.dimensions' => 'Image resolution does not meet the requirement. Size of the image should be 500 x 500 (w x h). ',

            ]
        );

        if ($validator->fails()) {
            // dd($validator->errors());

            return back()->withInput()->withErrors($validator->errors());
        }
        try {
            $id = \Crypt::decrypt($encid);
            $artdet = Gallery::where('id', $id)->first();
            // dd('trues');

            $formdata = new Galleryattachments([

                'gallery_id' => $id,
                'status_id' => 1,
                'user_id' => Auth::user()->id,

            ]);
            $resatt = $formdata->save();

            $lang = Language::where('status_id', 1)->get();

            $files = $request->file;

            $imageName = 'gallery_attachment'.$request->lang_id.time().rand().'.'.$files->extension();
            $size = $files->getSize();
            $files->move(public_path('uploads/gallery_attachments/'), $imageName);

            $dataarr = new GalleryattachmentsLang([
                'galleryattachments_id' => $formdata->id,
                'alt' => 'gallery_attachment'.$request->lang_id,
                'file' => $imageName,
                'size' => 1,
                'title' => 'gallery_attachment'.$request->lang_id,
                'description' => 'gallery_attachment'.$request->lang_id,
                'lang_id' => $request->lang_id,
            ]);
            // dd($dataarr);
            $resattLang = $dataarr->save();
            if ($resattLang) {
                $success = 'Gallery attachment uploaded!';

                // dd($success);
                return redirect($userrole.'/gallery_list')->withSuccess($success);
            } else {
                $error = 'Not Uploaded';

                return redirect($userrole.'/gallery_list')->with('error', $error);
            }
            if ($resattLang) {
                $success = "Gallery attachment uploaded!";
                return back()->withSuccess($success);
            } else {
                $error = $msg;
                return back()->withInput()->withErrors($error);
            }

        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);

            dd($exception);

            return back()->withInput()->withErrors($exception);
        } catch (\Throwable $exception) {

            dd($exception);

            return back()->withInput()->withErrors($exception);
        } catch (\Illuminate\Database\QueryException $exception) {
            dd($exception);

            return back()->withInput()->withErrors($exception);
        }
    }

    public function gallery_attachmentslist(Request $request, $encid)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $id = \Crypt::decrypt($encid);
        $gallery_id = $id;
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Gallery', 'message' => 'List Gallery', 'status' => 0, 'link' => $userrole.'/gallery_list'],
            2 => ['title' => 'Gallery Upload', 'message' => 'Gallery Upload', 'status' => 0],
        ];
        $head = 'Gallery Upload';
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('gallery_list')->id.'a';
        $articletype = Articletype::where('status_id', 1)->get();
        $lang = Language::where('status_id', 1)->get();
        $editF = 'A';
        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        $artdet = Gallery::whereId($gallery_id)->first();
        $artalbum = Galleryattachments::with(['galleryattachments_langs' => function ($query) {}])->where('gallery_id', $gallery_id)
        // ->where('id',19)
            ->where('status_id', 1)->get();
        $artalbumcnt = count($artalbum);

        // dd($artalbum);
        return view('siteadmin.gallery.gallery_upload', compact(['gallery_id', 'breadcrumbarr', 'linactive', 'articletype', 'lang', 'component', 'head', 'editF', 'artdet', 'artalbum', 'artalbumcnt'], 'usertype'));
    }

    public function gallery_attachmentsdel(Request $request, $id)
    {
        if ($request->ajax()) {

            $artalbum = Galleryattachments::whereId($id)->select('id')->first();
            $artalbumattachment = GalleryattachmentsLang::where('galleryattachments_id', $artalbum->id)->get();

            foreach ($artalbumattachment as $attch) {
                $artalbumimg = public_path('uploads/gallery_attachments/').$attch->file;
                if (file_exists($artalbumimg)) {
                    @unlink($artalbumimg);

                }
                GalleryattachmentsLang::where('id', $id)->delete();
            }
            Galleryattachments::where('id', $attch->id)->delete();

            return response()->json(['success' => 'Data Updated successfully.']);
        }
    }

    //Logo
    public function logo_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Logo', 'message' => 'List Logo', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('logo_list')->id;
        // dd($linactive);
        $head = 'Logo List';
        //Data and data
        $componentData = Logo::with(['logo_langs' => function () {}])->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Logo");

        // dd($component);
$userrole=Auth::user()->usertypes[0]->userrolename;
        return view('siteadmin.logo.logo_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function logo_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Logo', 'message' => 'List Logo', 'status' => 0, 'link' => $userrole.'/logo_list'],
                2 => ['title' => 'Add Logo', 'message' => 'Add Logo', 'status' => 0],
            ];
            $head = 'Add Logo';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Logo', 'message' => 'List Logo', 'status' => 0, 'link' => $userrole.'/logo_list'],
                2 => ['title' => 'Edit Logo', 'message' => 'Edit Logo', 'status' => 0],
            ];
            $head = 'Edit Logo';
            $editF = 'E';
            $result = Logo::with(['logo_langs' => function () {}])->where('id', $id)->first();
            // dd($result);
        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('logo_list')->id.'a';
        $lang = Language::where('status_id', 1)->get();
        $logotyperes = Logotype::where('status_id', 1)->get();
        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);

        return view('siteadmin.logo.logo_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'logotyperes', 'usertype'));
    }

    public function logo_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'logotypes_id' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'poster.*' => app('App\Http\Controllers\CommonfunctionController')->logoimg(\Crypt::decrypt($request->logotypes_id) ?? ''),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }
            $dataarr_art = new Logo([
                'logotypes_id' => \Crypt::decrypt($request->logotypes_id),
                'homepage_status' => $homepage_status,
                'user_id' => Auth::user()->id,
            ]);
            $res_art = $dataarr_art->save();
            if ($res_art) {
                $lang = Language::where('status_id', 1)->get();
                foreach ($lang as $lan) {
                    $imname = 'logo'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                    $path = $request->file('poster')[$lan->id]->storeAs('uploads/logo', $imname, 'myfile');
                    $dataarr = new LogoLang([
                        'title' => $request->title[$lan->id],
                        'description' => $request->description[$lan->id],
                        'poster' => $imname,
                        'logos_id' => $dataarr_art->id,
                        'lang_id' => $lan->id,
                    ]);
                    $chkrws = LogoLang::where('title', $request->title[1])->where('lang_id', 1)->where('lang_id', $lan->id)->exists() ? 1 : 0;

                    if ($chkrws == 0) {

                        $res = $dataarr->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($chkrws == 0) {

                $res = $dataarr->save();
            } else {
                $res = false;
                $msg = 'This Name is already existing';
            }
            if ($res) {
                $success = 'Logo Added!';

                return redirect($userrole.'/logo_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function logo_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);
            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }
            $dataarr_art = [
                'logotypes_id' => \Crypt::decrypt($request->logotypes_id),
                // 'homepage_status'=>$homepage_status,
                'user_id' => Auth::user()->id,
            ];
            $res_art = Logo::where('id', $id)->update($dataarr_art);
            if ($res_art) {
                $lang = Language::where('status_id', 1)->get();
                foreach ($lang as $lan) {
                    if (isset($request->poster)) {

                        $imname = 'logo'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                        $path = $request->file('poster')[$lan->id]->storeAs('uploads/logo', $imname, 'myfile');
                        $dataarr = [
                            'title' => $request->title[$lan->id],
                            'description' => $request->description[$lan->id],
                            'poster' => $imname,
                            'logos_id' => $id,
                            'lang_id' => $lan->id,
                        ];
                    } else {
                        $dataarr = [
                            'title' => $request->title[$lan->id],
                            'description' => $request->description[$lan->id],

                            'logos_id' => $id,
                            'lang_id' => $lan->id,
                        ];
                    }
                    $chkrws = LogoLang::where('logos_id', '!=', $id)->where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;

                    if ($chkrws == 0) {

                        $res = LogoLang::where('logos_id', $id)->where('lang_id', $lan->id)->update($dataarr);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'Logo Updated!';

                return redirect($userrole.'/logo_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function logo_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Logo::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Logo::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function logo_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);

        if (\Schema::hasTable('logos_langs')) {
            $reslang = LogoLang::where('logos_id', $id)->delete();
            $res = Logo::where('id', $id)->delete();
        } else {
            $res = Logo::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    //footer

    public function footer_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Footer', 'message' => 'List Footer', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('footer_list')->id;
        $head = 'Footer List';
        //Data and data
        $componentData = footer::with(['footer_langs' => function () {}])->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("footer");

        // dd($component);
        // dd($componentData);
$userrole=Auth::user()->usertypes[0]->userrolename;
        return view('siteadmin.footer.footer_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function footer_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Footer', 'message' => 'List Footer', 'status' => 0, 'link' => $userrole.'/footer_list'],
                2 => ['title' => 'Add Footer', 'message' => 'Add Footer', 'status' => 0],
            ];
            $head = 'Add Footer';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Footer', 'message' => 'List Footer', 'status' => 0, 'link' => $userrole.'/footer_list'],
                2 => ['title' => 'Edit Footer', 'message' => 'Edit Footer', 'status' => 0],
            ];
            $head = 'Edit Footer';
            $editF = 'E';
            $result = footer::with(['footer_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($result);

        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('footer_list')->id.'a';
        $footer_category = FooterCategory::where('status_id', 1)->get();
        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $lang = Language::where('status_id', 1)->get();

        return view('siteadmin.footer.footer_add', compact('footer_category', 'component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'usertype'));
    }

    public function footer_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->CKeditlangtitle(),
            'subtitle.*' => app('App\Http\Controllers\CommonfunctionController')->CKeditlangtitle(),
            'icon_class' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
            'link' => app('App\Http\Controllers\CommonfunctionController')->linknotreq(),
            'footer_categories_id' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),

        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            // dd($request->menulinktype_id);

            if (isset($request->poster)) {
                $imname = 'footer'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/footer', $imname, 'myfile');

                $dataarr = new Footer([
                    'link' => $request->link,
                    'icon_class' => $request->icon_class,

                    'footer_categories_id' => \Crypt::decrypt($request->footer_categories_id),
                    'poster' => $imname,
                    'status_id' => 1,
                    'user_id' => Auth::user()->id,
                ]);

            } else {
                $dataarr = new Footer([
                    'link' => $request->link,
                    'icon_class' => $request->icon_class,

                    'footer_categories_id' => \Crypt::decrypt($request->footer_categories_id),

                    'status_id' => 1,
                    'user_id' => Auth::user()->id,
                ]);
            }

            $res = $dataarr->save();
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = FooterLang::where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    $datarrlang = new FooterLang([
                        'title' => $request->title[$lan->id],
                        'subtitle' => $request->subtitle[$lan->id],

                        'footers_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'Footer Added!';

                return redirect($userrole.'/footer_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function footer_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'subtitle.*' => app('App\Http\Controllers\CommonfunctionController')->CKeditlangtitle(),
            'icon_class' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
            'link' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
            'footer_categories_id' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);

            if (isset($request->poster)) {
                $imname = 'footer'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/footer', $imname, 'myfile');

                $dataarr = [

                    'link' => $request->link,
                    'icon_class' => $request->icon_class,

                    'footer_categories_id' => \Crypt::decrypt($request->footer_categories_id),
                    'poster' => $imname,
                    'user_id' => Auth::user()->id,
                ];

            } else {

                $dataarr = [

                    'link' => $request->link,
                    'icon_class' => $request->icon_class,

                    'footer_categories_id' => \Crypt::decrypt($request->footer_categories_id),
                    'user_id' => Auth::user()->id,
                ];
            }
            $res = footer::where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = FooterLang::where('footers_id', '!=', $id)->where('title', $request->title[$lan->id])->exists() ? 1 : 0;
                    $datarrlang = [
                        'title' => $request->title[$lan->id],
                        'subtitle' => $request->subtitle[$lan->id],

                        'lang_id' => $lan->id,
                    ];
                    if ($chkrws == 0) {

                        $reslan = FooterLang::where('footers_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'Footer Updated!';

                return redirect($userrole.'/footer_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function footer_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = footer::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = footer::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function footer_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        if (\Schema::hasTable('footer_langs')) {
            $reslang = Footerlang::where('footers_id', $id)->delete();
            $res = footer::where('id', $id)->delete();
        } else {
            $res = footer::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    //Portservices

    public function portservices_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Portservice', 'message' => 'List Portservice', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('portservices_list')->id;
        $head = 'Portservice List';
        //Data and data
        $componentData = Portservice::with(['portservices_langs' => function () {}])
        ->orderBy('order_num', 'asc')
        // ->orderBy('updated_at', 'desc')
        ->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Portservice");
        // dd($component);
        $userrole=Auth::user()->usertypes[0]->userrolename;
                return view('siteadmin.service.portservices_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function portservices_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Portservice', 'message' => 'List Portservice', 'status' => 0, 'link' => $userrole.'/portservices_list'],
                2 => ['title' => 'Add Portservice', 'message' => 'Add Portservice', 'status' => 0],
            ];
            $head = 'Add Portservice';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Portservice', 'message' => 'List Portservice', 'status' => 0, 'link' => $userrole.'/portservices_list'],
                2 => ['title' => 'Edit Portservice', 'message' => 'Edit Portservice', 'status' => 0],
            ];
            $head = 'Edit Portservice';
            $editF = 'E';
            $result = Portservice::with(['portservices_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($result);

        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('portservices_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $lang = Language::where('status_id', 1)->get();

        return view('siteadmin.service.portservices_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'usertype'));
    }

    public function portservices_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),

            'link' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            // dd($request->menulinktype_id);
            if (isset($request->poster)) {
                $imname = 'service'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/service', $imname, 'myfile');
                // $img = \Image::make($path)->resize(302, 272);
                // $img->save($path);
                $dataarr = new Portservice([
                    'icon_class' => $request->iconclass,
                    'color_class' => $request->colorclass,
                    'poster' => $imname,
                    'status_id' => 1,
                    'user_id' => Auth::user()->id,
                    'link' => $request->link,
                ]);
            } else {
                $dataarr = new Portservice([

                    'icon_class' => $request->iconclass,
                    'color_class' => $request->colorclass,
                    'status_id' => 1,
                    'user_id' => Auth::user()->id,
                    'link' => $request->link,
                ]);
            }

            $res = $dataarr->save();
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = PortserviceLang::where('name', $request->name[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    $datarrlang = new PortserviceLang([
                        'name' => $request->name[$lan->id] ?? '',
                        'description' => $request->description[$lan->id] ?? '',
                        'portservices_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'Portservice Added!';

                return redirect($userrole.'/portservices_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function portservices_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),

            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
            'link' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);

            if (isset($request->poster)) {
                $imname = 'service'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/service', $imname, 'myfile');
                // $img = \Image::make($path)->resize(302, 272);
                // $img->save($path);
                $dataarr = [

                    'icon_class' => $request->iconclass,
                    'color_class' => $request->colorclass,
                    'poster' => $imname,
                    'link' => $request->link,

                    'user_id' => Auth::user()->id,
                ];
            } else {
                $dataarr = [

                    'icon_class' => $request->iconclass,
                    'color_class' => $request->colorclass,

                    'link' => $request->link,

                    'user_id' => Auth::user()->id,
                ];
            }

            $res = Portservice::where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = PortserviceLang::where('portservices_id', '!=', $id)->where('name', $request->name[$lan->id])->exists() ? 1 : 0;
                    $datarrlang = [
                        'name' => $request->name[$lan->id]  ?? '',
                        'description' => $request->description[$lan->id] ?? '',
                        'lang_id' => $lan->id,
                    ];
                    $chkrws=0;
                    if ($chkrws == 0) {

                        $reslan = PortserviceLang::where('portservices_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'Portservice Updated!';

                return redirect($userrole.'/portservices_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function portservices_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Portservice::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Portservice::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function portservices_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        if (\Schema::hasTable('portservices_langs')) {
            $reslang = PortserviceLang::where('services_id', $id)->delete();
            $res = Portservice::where('id', $id)->delete();
        } else {
            $res = Portservice::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    //BOD
    public function bod_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        // dd($userrole);
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'Ministers List', 'message' => 'Ministers List', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('bod_list')->id;
        // dd($linactive);
        $head = 'Ministers List';
        //Data and data
        $itemIds = explode(',', \Auth::user()->item_id);
        $componentData = BOD::with(['bod_langs' => function () {}])
        ->orWhereHas('bod_langs',function($q) use($itemIds){
            $q->whereIn('id',$itemIds);
        })
        ->orderBy('updated_at', 'desc')->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("BOD");

        // dd($component);
$userrole=Auth::user()->usertypes[0]->userrolename;
        return view('siteadmin.bod.bod_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function bod_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'Ministers List', 'message' => 'Ministers List', 'status' => 0, 'link' => $userrole.'/bod_list'],
                2 => ['title' => 'Ministers Add', 'message' => 'Ministers Add', 'status' => 0],
            ];
            $head = 'Ministers Add';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'Ministers List', 'message' => 'Ministers List', 'status' => 0, 'link' => $userrole.'/bod_list'],
                2 => ['title' => 'Edit Minister', 'message' => 'Edit Minister', 'status' => 0],
            ];
            $head = 'Edit Minister';
            $editF = 'E';
            $result = BOD::where('id', $id)->first();
        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('bod_list')->id.'a';
        $lang = Language::where('status_id', 1)->get();
        $socialmediaicon = Socialmedia::where('status_id', 1)->get();
        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();

        // dd($component);
        // dd($result);
        return view('siteadmin.bod.bod_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'socialmediaicon', 'usertype'));
    }

    public function bod_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'contact.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'jobtitle.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2_desc(),

            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }
            // dd($request->socialmediaicon);

            if (isset($request->poster)) {
                $imname = 'bod'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/bod/', $imname, 'myfile');
                $img = \Image::make($path)->resize(169, 170);
                $img->save($path);
                $dataarr_art = new BOD([
                    'poster' => $imname,
                    'socialmedia_id' => implode(',', array_keys($request->socialmediaicon)),
                    'linkvalval' => implode(',', $request->socialmediaicon),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                ]);
            } else {
                $dataarr_art = new BOD([
                    'socialmedia_id' => implode(',', array_keys($request->socialmediaicon)),
                    'linkvalval' => implode(',', $request->socialmediaicon),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                ]);
            }

            // dd( $dataarr_art);
            $res_art = $dataarr_art->save();
            if ($res_art) {
                // dd($dataarr_art->id);
                $lang = Language::where('status_id', 1)->get();
                foreach ($lang as $lan) {
                    // $imname='bod'.$lan->id.date("yyyy:mm:dd:hh:mm:ss").$request->poster[$lan->id]->extension();
                    // $path=$request->file('poster')[$lan->id]->storeAs('uploads/bod',$imname,'myfile');
                    $dataarr = new BODLang([
                        'title' => $request->title[$lan->id] ?? '',
                        'description' => $request->description[$lan->id] ?? '',
                        'contact' => $request->contact[$lan->id] ?? '',
                        'jobtitle' => $request->jobtitle[$lan->id] ?? '',
                        // 'poster'=>$imname,
                        'b_o_d_s_id' => $dataarr_art->id,
                        'lang_id' => $lan->id,
                    ]);
                    $chkrws = BODLang::where('title', $request->title[1])->where('lang_id', 1)->where('lang_id', $lan->id)->exists() ? 1 : 0;

                    if ($chkrws == 0) {

                        $res = $dataarr->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($chkrws == 0) {

                $res = $dataarr->save();
            } else {
                $res = false;
                $msg = 'This Name is already existing';
            }
            if ($res) {
                $success = 'BOD Added!';

                return redirect($userrole.'/bod_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function bod_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'contact.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'jobtitle.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2_desc(),

            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }
        // dd($request->all());

        try {
            $id = \Crypt::decrypt($request->hidden_val);
            if (isset($request->homepage_status)) {
                $homepage_status = 1;
            } else {
                $homepage_status = 0;
            }
            if (isset($request->poster)) {
                $imname = 'bod'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/bod/', $imname, 'myfile');

                $img = \Image::make($path)->resize(169, 170);
                $img->save($path);
                $dataarr_art = [
                    'poster' => $imname,
                    'socialmedia_id' => implode(',', array_keys($request->socialmediaicon)),
                    'linkvalval' => implode(',', $request->socialmediaicon),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                ];
            } else {
                $dataarr_art = [
                    'socialmedia_id' => implode(',', array_keys($request->socialmediaicon)),
                    'linkvalval' => implode(',', $request->socialmediaicon),
                    'homepage_status' => $homepage_status,
                    'user_id' => Auth::user()->id,
                ];
            }

            $res_art = BOD::where('id', $id)->update($dataarr_art);
            if ($res_art) {
                $lang = Language::where('status_id', 1)->get();
                foreach ($lang as $lan) {
                    if (isset($request->poster)) {

                        // $imname='bod'.$lan->id.date("yyyy:mm:dd:hh:mm:ss").$request->poster[$lan->id]->extension();
                        // $path=$request->file('poster')[$lan->id]->storeAs('uploads/bod',$imname,'myfile');
                        $dataarr = [
                            'title' => $request->title[$lan->id] ?? '',
                            'description' => $request->description[$lan->id] ?? '',
                            'contact' => htmlspecialchars($request->contact[$lan->id], ENT_QUOTES, 'UTF-8'),
                            'jobtitle' => htmlspecialchars($request->jobtitle[$lan->id], ENT_QUOTES, 'UTF-8'),
                            // 'poster'=>$imname,
                            'b_o_d_s_id' => $id,
                            'lang_id' => $lan->id,
                        ];
                    } else {
                        $dataarr = [
                            'title' => $request->title[$lan->id],
                            'description' => $request->description[$lan->id],
                            'contact' => htmlspecialchars($request->contact[$lan->id], ENT_QUOTES, 'UTF-8'),
                            'jobtitle' => htmlspecialchars($request->jobtitle[$lan->id], ENT_QUOTES, 'UTF-8'),
                            'b_o_d_s_id' => $id,
                            'lang_id' => $lan->id,
                        ];
                    }
                    $chkrws = BODLang::where('b_o_d_s_id', '!=', $id)->where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;

                    if ($chkrws == 0) {

                        $res = BODLang::where('b_o_d_s_id', $id)->where('lang_id', $lan->id)->update($dataarr);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'BOD Updated!';

                return redirect($userrole.'/bod_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function bod_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = BOD::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = BOD::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function bod_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);

        if (\Schema::hasTable('service_langs')) {
            $reslang = BODLang::where('b_o_d_s_id', $id)->delete();
            $res = BOD::where('id', $id)->delete();
        } else {
            $res = BOD::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }
    //mediacategory

    public function mediacategory_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List MediaCategory', 'message' => 'List MediaCategory', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('mediacategory_list')->id;
        $head = 'MediaCategory List';
        //Data and data
        $itemIds = explode(',', \Auth::user()->item_id);
        $componentData = MediaCategory::with(['media_category_langs' => function () {}])
        ->orWhereHas('media_category_langs',function($q) use($itemIds){
            $q->whereIn('id',$itemIds);
        })
        ->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("MediaCategory");
        // dd($component);
        // dd($componentData);
        $userrole=Auth::user()->usertypes[0]->userrolename;
        return view('siteadmin.mediacategory.mediacategory_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function mediacategory_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List MediaCategory', 'message' => 'List MediaCategory', 'status' => 0, 'link' => $userrole.'/mediacategory_list'],
                2 => ['title' => 'Add MediaCategory', 'message' => 'Add MediaCategory', 'status' => 0],
            ];
            $head = 'Add MediaCategory';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List MediaCategory', 'message' => 'List MediaCategory', 'status' => 0, 'link' => $userrole.'/mediacategory_list'],
                2 => ['title' => 'Edit MediaCategory', 'message' => 'Edit MediaCategory', 'status' => 0],
            ];
            $head = 'Edit MediaCategory';
            $editF = 'E';
            $result = MediaCategory::with(['media_category_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($result);

        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('mediacategory_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $lang = Language::where('status_id', 1)->get();

        return view('siteadmin.mediacategory.mediacategory_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'usertype'));
    }

    public function mediacategory_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            // dd($request->menulinktype_id);

            $dataarr = new MediaCategory([
                'link' => $request->link,
                'icon_class' => $request->icon_class,

                'status_id' => 1,
                'user_id' => Auth::user()->id,
            ]);

            $res = $dataarr->save();
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = MediaCategoryLang::where('name', $request->name[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    $datarrlang = new MediaCategoryLang([
                        'name' => $request->name[$lan->id],
                        'media_categories_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'MediaCategory Added!';

                $usertype = '';

                if (Auth::user()->usertypes_id == 1) {
                    $usertype = 'admin';
                } elseif (Auth::user()->usertypes_id == 2) {
                    $usertype = 'siteadmin';
                } elseif ((Auth::user()->usertypes_id == 5) || (Auth::user()->usertypes_id == 7)) {
                    $usertype = 'media';
                }

                return redirect($usertype.'/mediacategory_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function mediacategory_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);

            $dataarr = [

                'link' => $request->link,
                'icon_class' => $request->icon_class,

                'user_id' => Auth::user()->id,
            ];

            $res = MediaCategory::where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = MediaCategoryLang::where('media_categories_id', '!=', $id)->where('name', $request->name[$lan->id])->exists() ? 1 : 0;
                    $datarrlang = [
                        'name' => $request->name[$lan->id],
                        'lang_id' => $lan->id,
                    ];
                    if ($chkrws == 0) {

                        $reslan = MediaCategoryLang::where('media_categories_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'MediaCategory Updated!';
                $usertype = '';

                if (Auth::user()->usertypes_id == 1) {
                    $usertype = 'admin';
                } elseif (Auth::user()->usertypes_id == 2) {
                    $usertype = 'siteadmin';
                } elseif ((Auth::user()->usertypes_id == 5) || (Auth::user()->usertypes_id == 7)) {
                    $usertype = 'media';
                }

                return redirect($usertype.'/mediacategory_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function mediacategory_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = MediaCategory::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = MediaCategory::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function mediacategory_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        if (\Schema::hasTable('service_langs')) {
            $reslang = MediaCategoryLang::where('media_categories_id', $id)->delete();
            $res = MediaCategory::where('id', $id)->delete();
        } else {
            $res = MediaCategory::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    //Sponsor
    public function sponsor_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Sponsor', 'message' => 'List Sponsor', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('sponsor_list')->id;
        $head = 'Sponsor List';
        //Data and data
        $itemIds = explode(',', \Auth::user()->item_id);

        $componentData = Sponsor::with(['sponsor_langs' => function () {}])
        ->orWhereHas('sponsor_langs',function($q) use($itemIds){
            $q->whereIn('id',$itemIds);
        })
        ->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();

        // dd($component);
        // dd($componentData);
        $userrole=Auth::user()->usertypes[0]->userrolename;
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Sponsor");

        return view('siteadmin.sponsor.sponsor_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function sponsor_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Sponsor', 'message' => 'List Sponsor', 'status' => 0, 'link' => $userrole.'/sponsor_list'],
                2 => ['title' => 'Add Sponsor', 'message' => 'Add Sponsor', 'status' => 0],
            ];
            $head = 'Add Sponsor';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Sponsor', 'message' => 'List Sponsor', 'status' => 0, 'link' => $userrole.'/sponsor_list'],
                2 => ['title' => 'Edit Sponsor', 'message' => 'Edit Sponsor', 'status' => 0],
            ];
            $head = 'Edit Sponsor';
            $editF = 'E';
            $result = Sponsor::with(['sponsor_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($result);

        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('sponsor_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $lang = Language::where('status_id', 1)->get();

        return view('siteadmin.sponsor.sponsor_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'usertype'));
    }

    public function sponsor_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),

        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            // dd($request->menulinktype_id);

            $dataarr = new Sponsor([
                'link' => $request->link,
                // 'icon_class'=>$request->icon_class,

                'status_id' => 1,
                'user_id' => Auth::user()->id,
            ]);

            $res = $dataarr->save();
            if ($res) {

                $lang = Language::where('status_id', 1)->get();
                $imname ='';
                $path ='';
                foreach ($lang as $lan) {
                    $chkrws = SponsorLang::where('title', $request->name[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    if(!empty($request->poster[$lan->id])){
                        $imname = 'sponsor'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                        $path = $request->file('poster')[$lan->id]->storeAs('uploads/sponsor', $imname, 'myfile');
    
                    }else{
                        $imname ='';
                        $path =''; 
                    }
                   
                    $datarrlang = new SponsorLang([
                        'title' => $request->name[$lan->id] ?? '',
                        'poster' => $imname,

                        'sponsors_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'Sponsor Added!';

                return redirect($userrole.'/sponsor_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function sponsor_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),

        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);

            $dataarr = [

                // 'news_cat_id'=>$id,
                'link' => $request->link,
                // 'icon_class'=>$request->icon_class,

                'user_id' => Auth::user()->id,
            ];

            $res = Sponsor::where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = SponsorLang::where('sponsors_id', '!=', $id)->where('title', $request->name[$lan->id])->exists() ? 1 : 0;
                    if (isset($request->file('poster')[$lan->id])) {
                        if(!empty($request->poster[$lan->id])){
                            $imname = 'sponsor'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').$request->poster[$lan->id]->extension();
                            $path = $request->file('poster')[$lan->id]->storeAs('uploads/sponsor', $imname, 'myfile');
        
                        }else{
                            $imname ='';
                            $path =''; 
                        }
                        $datarrlang = [
                            'title' => $request->name[$lan->id] ?? '',
                            'poster' => $imname,
                            'lang_id' => $lan->id,
                        ];
                    } else {
                      
                        $datarrlang = [
                            'title' => $request->name[$lan->id],
                            'lang_id' => $lan->id,
                        ];
                    }

                    if ($chkrws == 0) {

                        $reslan = SponsorLang::where('sponsors_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'Sponsor Updated!';

                return redirect($userrole.'/sponsor_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function sponsor_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Sponsor::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Sponsor::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function sponsor_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        if (\Schema::hasTable('sponsor_langs')) {
            $reslang = SponsorLang::where('sponsors_id', $id)->delete();
            $res = Sponsor::where('id', $id)->delete();
        } else {
            $res = Sponsor::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    //tabcontents 23 Sep 2024
    public function tabcontents_list(Request $request,$encid=null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Tabcontents', 'message' => 'List Tabcontents', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('tabcontents_list')->id;
        $head = 'Tabcontents List';
        //Data and data
        $itemIds = explode(',', \Auth::user()->item_id);
        $subcatitemIds = explode(',', \Auth::user()->sub_cat_id);
        // dd(\Auth::user()->id);
        
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Tabcontents");
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        
        $userrole=Auth::user()->usertypes[0]->userrolename;
        return view('siteadmin.tabcontents.tabcontents_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function tabcontents_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Tabcontents', 'message' => 'List Tabcontents', 'status' => 0, 'link' => $userrole.'/tabcontents_list'],
                2 => ['title' => 'Add Tabcontents', 'message' => 'Add Tabcontents', 'status' => 0],
            ];
            $head = 'Add Tabcontents';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Tabcontents', 'message' => 'List Tabcontents', 'status' => 0, 'link' => $userrole.'/tabcontents_list'],
                2 => ['title' => 'Edit Tabcontents', 'message' => 'Edit Tabcontents', 'status' => 0],
            ];
            $head = 'Edit Tabcontents';
            $editF = 'E';
            $result = Tabcontents::with(['tabcontentstypes'=>function($q){}])->with(['tabcontents_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($result);

        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('tabcontents_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $itemIds = explode(',', \Auth::user()->item_id);
        $subcatitemIds = explode(',', \Auth::user()->sub_cat_id);
       
       $lang=Language::get();
       $quertabcont=app('App\Http\Controllers\CommonfunctionController')->types_allowed(55,'Tabcontents','Tabcontentstype','tab_content_types_id');
       if($editF=='E'){
                $quertabcont=Tabcontentstype::where('status_id', 1)->where('id', $result->tab_content_types_id)->get();
            }
       $ships_in_port_types=$quertabcont;
        $tabcontent_types=$quertabcont;
        // ->whereIn('id',$itemIds)



        
        return view('siteadmin.tabcontents.tabcontents_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'usertype', 'ships_in_port_types','tabcontent_types'));
    }

    public function tabcontents_save(Request $request)
    {
        $titles = $request->input('title', []);
        $rules = [];
        if(isset($request->hidden_val)&&!empty($request->hidden_val)){
            $id = \Crypt::decrypt($request->hidden_val);
        }else{
            $id = 0;
        }
        
        foreach ($titles as $lang_id => $title) {
            $rules["title.$lang_id"] = [
                'nullable',
                Rule::unique('tabcontents_langs', 'title')
                    ->where(function ($query) use ($lang_id,$id) {
                        return $query
                        ->where('ships_in_port_details_id', '!=', $id)
                        ->where('lang_id', $lang_id);
                    }),
            ];
        }
        
        $validated = $request->validate($rules);
        // dd($validated);
        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();
        
        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
        $titles = $request->input('title', []);

        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();

        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'descrption.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
           
            'homepage_status'=>'required',
            'tab_content_types_id'=>'required',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            // dd($request->menulinktype_id);

            $dataarr = new Tabcontents([
                'doc' => $request->doc,
                'dos' => $request->dos,
                'homepage_status'=> $request->homepage_status,
                // 'icon_class'=>$request->icon_class,
                'tab_content_types_id' => \Crypt::decrypt($request->tab_content_types_id),

                'status_id' => 1,
                'user_id' => Auth::user()->id,
            ]);

            $res = $dataarr->save();
            if ($res) {

                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = TabcontentsLang::where('title', $request->title[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    if (! empty($request->file[$lan->id])) {
                        $imname = 'tabcontents'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').'.'.$request->file[$lan->id]->extension();
                        $path = $request->file('file')[$lan->id]->storeAs('uploads/tabcontents/', $imname, 'myfile');
                        if ($request->file[$lan->id]->extension() != 'pdf') {
                            $arrayexcel = ['xls', 'ods', 'xlsx'];
                            // dd($path);
                            $urlpath = asset('uploads/tabcontents/'.$imname);
                            if (in_array($request->file[$lan->id]->extension(), $arrayexcel)) {

                                $spreadsheet = IOFactory::load($path);

                                // Create PDF writer
                                // Register MPDF as the PDF renderer
                                IOFactory::registerWriter('Pdf', Mpdf::class);
                                $pdfWriter = IOFactory::createWriter($spreadsheet, 'Pdf');
                                // Create PDF writer object for the spreadsheet
                                //   $pdfWriter = new Mpdf($spreadsheet);
                                // Save PDF to a temporary file
                                // Save PDF to a temporary file
                                $pdfname = 'ships_in_port_details_idpdf'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').'.'.'pdf';
                                $pdfFilePath = public_path('uploads/tabcontents/'.$pdfname);
                                $pdfWriter->save($pdfFilePath);
                            } else {
                                $imname = 'tabcontents'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').'.'.$request->file[$lan->id]->extension();
                                $pathf = $request->file('file')[$lan->id]->storeAs('uploads/tabcontents/', $imname, 'myfile');
                                $pdfname = $imname;
                            }
                        } else {
                            $pdfname = $imname;
                        }
                    } else {
                        $pdfname = '';
                    }

                    $datarrlang = new TabcontentsLang([
                        'title' => $request->title[$lan->id] ?? '',
                        'description' => $request->description[$lan->id] ?? '',

                        'file' => $pdfname,
                        'ships_in_port_types_id'=>0,
                        'ships_in_port_details_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'Tabcontents Added!';

                return redirect($userrole.'/tabcontents_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function tabcontents_update(Request $request)
    {
        $titles = $request->input('title', []);
        $rules = [];
        if(isset($request->hidden_val)&&!empty($request->hidden_val)){
            $id = \Crypt::decrypt($request->hidden_val);
        }else{
            $id = 0;
        }
        
        foreach ($titles as $lang_id => $title) {
            $rules["title.$lang_id"] = [
                'nullable',
                Rule::unique('tabcontents_langs', 'title')
                    ->where(function ($query) use ($lang_id,$id) {
                        return $query
                        ->where('ships_in_port_details_id', '!=', $id)
                        ->where('lang_id', $lang_id);
                    }),
            ];
        }
        
        $validated = $request->validate($rules);
        // dd($validated);
        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();
        
        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
        $titles = $request->input('title', []);

        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();

        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'title.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'hidden_val' => app('App\Http\Controllers\CommonfunctionController')->langtitle(),
            'descrption.*' => app('App\Http\Controllers\CommonfunctionController')->NotCKeditlangtitle(),
            'homepage_status'=>'required',
            'tab_content_types_id'=>'required',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);

            $dataarr = [

                // 'news_cat_id'=>$id,
                'doc' => $request->doc,
                'dos' => $request->dos,
                // 'icon_class'=>$request->icon_class,
'homepage_status'=> $request->homepage_status,
                // 'icon_class'=>$request->icon_class,
                'tab_content_types_id' => \Crypt::decrypt($request->tab_content_types_id),

                'user_id' => Auth::user()->id,
            ];

            $res = Tabcontents::with(['tabcontentstypes'=>function($q){}])->where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = TabcontentsLang::where('ships_in_port_details_id', '!=', $id)->where('title', $request->title[$lan->id])->exists() ? 1 : 0;
                    if (isset($request->file('file')[$lan->id])&&!empty($request->file('file')[$lan->id])) {
                       
                        $imname = 'tabcontents'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').'.'.$request->file[$lan->id]->extension();
                        $path = $request->file('file')[$lan->id]->storeAs('uploads/tabcontents/', $imname, 'myfile');
                        
                        // dd($path);
                        $urlpath = asset('uploads/tabcontents/'.$imname);

                        $arrayexcel = ['xls', 'ods', 'xlx'];
                        // dd($path);
                        $urlpath = asset('uploads/tabcontents/'.$imname);
                        // if ($request->file[$lan->id]->extension() != 'pdf') {
                                // if (in_array($request->file[$lan->id]->extension(), $arrayexcel)) {

                                //     $spreadsheet = IOFactory::load($path);

                                //     // Create PDF writer
                                //     // Register MPDF as the PDF renderer
                                //     IOFactory::registerWriter('Pdf', Mpdf::class);

                                //     // Create PDF writer object for the spreadsheet
                                //     $pdfWriter = new Mpdf($spreadsheet);
                                //     // Save PDF to a temporary file
                                //     // Save PDF to a temporary file
                                //     $pdfname = 'ships_in_port_details_idpdf'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').'.'.'pdf';
                                //     $pdfFilePath = public_path('uploads/tabcontents/'.$pdfname);
                                //     $pdfWriter->save($pdfFilePath);
                                // } else {
                                //     $imname = 'tabcontents'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').'.'.$request->file[$lan->id]->extension();
                                //     $pathf = $request->file('file')[$lan->id]->storeAs('uploads/tabcontents/', $imname, 'myfile');
                                //     $pdfname = $imname;
                                //     // dd( $pathf );
                                // }
                                // if(isset($request->file[$lan->id])&&!empty($request->file[$lan->id])){
                                //     $imname = 'tabcontents'.$lan->id.date('yyyy:mm:dd:hh:mm:ss').'.'.$request->file[$lan->id]->extension();
                                //     $pathf = $request->file('file')[$lan->id]->storeAs('uploads/tabcontents/', $imname, 'myfile');
                                //     $pdfname = $imname;
                                // }
                                $pdfname = $imname;
                            $datarrlang = [
                                'title' => $request->title[$lan->id] ?? '',
                                // 'tab_content_types_id' => \Crypt::decrypt($request->ships_in_port_types[$lan->id]),
                                'description' => $request->description[$lan->id] ?? '',

                                'file' => $pdfname,
                                'lang_id' => $lan->id,
                            ];
                    } else {
                        // dd(true);
                        $datarrlang = [
                            'title' => $request->title[$lan->id] ?? '',
                            // 'tab_content_types_id' => \Crypt::decrypt($request->ships_in_port_types[$lan->id]),
                            'description' => $request->description[$lan->id] ?? '',

                            'lang_id' => $lan->id,
                        ];
                    }

                    if ($chkrws == 0) {

                        $reslan = TabcontentsLang::where('ships_in_port_details_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'Tabcontents Updated!';

                return redirect($userrole.'/tabcontents_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function tabcontents_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Tabcontents::with(['tabcontentstypes'=>function($q){}])->where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Tabcontents::with(['tabcontentstypes'=>function($q){}])->where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function tabcontents_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        if (\Schema::hasTable('tabcontents_langs')) {
            $reslang = TabcontentsLang::where('ships_in_port_details_id', $id)->delete();
            $res = Tabcontents::with(['tabcontentstypes'=>function($q){}])->where('id', $id)->delete();
        } else {
            $res = Tabcontents::with(['tabcontentstypes'=>function($q){}])->where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }

    public function dispschemservice(Request $request, $encid)
    {
        if ($request->ajax()) {
            $id = \Crypt::decrypt($encid);
            $datares=[];
            if ($id == 3) {//portservice
                //portservice
                $datares = Portservice::with(['portservices_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();
            } elseif ($id == 13) {//public service
                //public service
                $datares = Publicservice::with(['publicservice_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();
            } elseif ($id == 16) {//Submenu
                //Submenu
                $datares = Submenu::with(['submenu_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();
            } elseif ($id == 21) {//Mainmenu
                //Mainmenu
                $datares = Mainmenu::with(['mainmenu_langs' => function () {}])->where('status_id', 1)->orderBy('updated_at', 'desc')->get();
            } elseif ($id == 22) {//pattan rajyasabha
                //rajyasabha
                $datares = Pattanrajysabha::where('status_id', 1)->get();
            } elseif ($id == 44) {//Eodb
                //Eodb
                $datares = EoDB::where('status_id', 1)->get();
            } elseif ($id == 45) {//RTI
                //RTI
                $datares = RTI::where('status_id', 1)->get();
            } elseif ($id == 46) {//Citizen chapter
                //Citizen chapter
                $datares = Cityzenchapter::where('status_id', 1)->get();
            } elseif ($id == 47) {//transparency
                //transparency
                $datares = Transparencyplan::where('status_id', 1)->get();
            } elseif ($id == 49) {//InternalComplaintsCommittee
                //InternalComplaintsCommittee
                $datares = InternalComplaintsCommittee::where('status_id', 1)->get();
            }

            return response()->json(['result' => $datares, 'id' => $id]);

        }
    }
    //Portservices

    public function facities_at_ports_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List FacitiesAtPort', 'message' => 'List FacitiesAtPort', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('facities_at_ports_list')->id;
        $head = 'FacitiesAtPort List';
        //Data and data
        $itemIds = explode(',', \Auth::user()->item_id);
        $componentData = FacitiesAtPort::with(['facities_at_ports_langs' => function () {}])
        ->orWhereHas('facities_at_ports_langs',function($q) use($itemIds){
            if(\Auth::user()->id!=2){
                $q->whereIn('id',$itemIds);
            }
            
        })
        ->orderBy('updated_at', 'desc')->get();
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
// dd($componentData );
$componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("FacitiesAtPort");
        // dd($component);
$userrole=Auth::user()->usertypes[0]->userrolename;
        return view('siteadmin.facities_at_ports.facities_at_ports_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function facities_at_ports_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List FacitiesAtPort', 'message' => 'List FacitiesAtPort', 'status' => 0, 'link' => $userrole.'/facities_at_ports_list'],
                2 => ['title' => 'Add FacitiesAtPort', 'message' => 'Add FacitiesAtPort', 'status' => 0],
            ];
            $head = 'Add FacitiesAtPort';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List FacitiesAtPort', 'message' => 'List FacitiesAtPort', 'status' => 0, 'link' => $userrole.'/facities_at_ports_list'],
                2 => ['title' => 'Edit FacitiesAtPort', 'message' => 'Edit FacitiesAtPort', 'status' => 0],
            ];
            $head = 'Edit FacitiesAtPort';
            $editF = 'E';
            $result = FacitiesAtPort::with(['facities_at_ports_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($result);

        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('facities_at_ports_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $lang = Language::where('status_id', 1)->get();

        return view('siteadmin.facities_at_ports.facities_at_ports_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'usertype'));
    }

    public function facities_at_ports_save(Request $request)
    {
        $titles = $request->input('name', []);
        $rules = [];
        if(isset($request->hidden_val)&&!empty($request->hidden_val)){
            $id = \Crypt::decrypt($request->hidden_val);
        }else{
            $id = 0;
        }
        
        foreach ($titles as $lang_id => $title) {
            $rules["name.$lang_id"] = [
                'nullable',
                Rule::unique('facities_at_port_langs', 'name')
                    ->where(function ($query) use ($lang_id,$id) {
                        return $query
                        // ->where('tenders_id', '!=', $id)
                        ->where('lang_id', $lang_id);
                    }),
            ];
        }
        
        $validated = $request->validate($rules);
        // dd($validated);
        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();
        
        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
        $titles = $request->input('name', []);

        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();

        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),

            'link' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            // dd($request->menulinktype_id);
            if (isset($request->poster)) {
                $imname = 'service'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/service', $imname, 'myfile');
                // $img = \Image::make($path)->resize(302, 272);
                // $img->save($path);
                $dataarr = new FacitiesAtPort([
                    'icon_class' => $request->iconclass,
                    'color_class' => $request->colorclass,
                    'poster' => $imname,
                    'status_id' => 1,
                    'user_id' => Auth::user()->id,
                    'link' => $request->link,
                ]);
            } else {
                $dataarr = new FacitiesAtPort([

                    'icon_class' => $request->iconclass,
                    'color_class' => $request->colorclass,
                    'status_id' => 1,
                    'user_id' => Auth::user()->id,
                    'link' => $request->link,
                ]);
            }

            $res = $dataarr->save();
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = FacitiesAtPortLang::where('name', $request->name[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    $datarrlang = new FacitiesAtPortLang([
                        'name' => $request->name[$lan->id] ?? '',
                        'description' => $request->description[$lan->id] ?? '',
                        'facities_at_ports_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'FacitiesAtPort Added!';

                return redirect($userrole.'/facities_at_ports_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function facities_at_ports_update(Request $request)
    {
        $titles = $request->input('name', []);
        $rules = [];
        if(isset($request->hidden_val)&&!empty($request->hidden_val)){
            $id = \Crypt::decrypt($request->hidden_val);
        }else{
            $id = 0;
        }
        
        foreach ($titles as $lang_id => $title) {
            $rules["name.$lang_id"] = [
                'nullable',
                Rule::unique('facities_at_port_langs', 'name')
                    ->where(function ($query) use ($lang_id,$id) {
                        return $query
                        ->where('facities_at_ports_id', '!=', $id)
                        ->where('lang_id', $lang_id);
                    }),
            ];
        }
        
        $validated = $request->validate($rules);
        // dd($validated);
        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();
        
        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
        $titles = $request->input('name', []);

        $nonEmptyTitleExists = collect($titles)->filter(function ($val) {
            return trim($val) !== '';
        })->isNotEmpty();

        if (!$nonEmptyTitleExists) {
            return back()
                ->withErrors(['title' => 'At least one title must be filled.'])
                ->withInput();
        }
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),

            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
            'link' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);

            if (isset($request->poster)) {
                $imname = 'service'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/service', $imname, 'myfile');
                // $img = \Image::make($path)->resize(302, 272);
                // $img->save($path);
                $dataarr = [

                    'icon_class' => $request->iconclass,
                    'color_class' => $request->colorclass,
                    'poster' => $imname,
                    'link' => $request->link,

                    'user_id' => Auth::user()->id,
                ];
            } else {
                $dataarr = [

                    'icon_class' => $request->iconclass,
                    'color_class' => $request->colorclass,

                    'link' => $request->link,

                    'user_id' => Auth::user()->id,
                ];
            }

            $res = FacitiesAtPort::where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = FacitiesAtPortLang::where('facities_at_ports_id', '!=', $id)->where('name', $request->name[$lan->id])->exists() ? 1 : 0;
                    $datarrlang = [
                        'name' => $request->name[$lan->id] ?? '',
                        'description' => $request->description[$lan->id] ?? '',
                        'lang_id' => $lan->id,
                    ];
                    if ($chkrws == 0) {

                        $reslan = FacitiesAtPortLang::where('facities_at_ports_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'FacitiesAtPort Updated!';

                return redirect($userrole.'/facities_at_ports_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function facities_at_ports_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = FacitiesAtPort::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = FacitiesAtPort::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function facities_at_ports_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        if (\Schema::hasTable('facities_at_ports_langs')) {
            $reslang = FacitiesAtPortLang::where('facities_at_ports_id', $id)->delete();
            $res = FacitiesAtPort::where('id', $id)->delete();
        } else {
            $res = FacitiesAtPort::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }
    //Timelines

    public function timeline_list(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        $breadcrumb = [
            0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
            1 => ['title' => 'List Timeline', 'message' => 'List Timeline', 'status' => 0],
        ];
        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('timeline_list')->id;
        $head = 'Timeline List';
        //Data and data
        $itemIds = explode(',', \Auth::user()->item_id);
        $componentData = Timeline::with(['timeline_langs' => function () {}])
        // ->orWhereHas('timeline_langs',function($q) use($itemIds){
        //     $q->whereIn('id',$itemIds);
        // })
        ->where(function ($query) use ($itemIds) {
            $query->where('user_id', \Auth::user()->id)
                  ->orWhereIn('id', $itemIds)
                  ->orWhereHas('timeline_langs', function($q) use ($itemIds) {
                      $q->whereIn('id', $itemIds);
                  });
        })
        ->orderBy('year', 'asc')->get();
        $componentData=app('App\Http\Controllers\CommonfunctionController')->getItemlist("Timeline");
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();

        // dd($component);
$userrole=Auth::user()->usertypes[0]->userrolename;
        return view('siteadmin.timeline.timeline_list', compact('userrole','component', 'breadcrumbarr', 'linactive', 'head', 'componentData', 'usertype'));
    }

    public function timeline_add(Request $request, $encid = null)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        //common
        // dd($request->all());
        if (isset($encid)) {
            $id = \Crypt::decrypt($encid);
        } else {
            $id = '';
        }
        if (empty($id)) {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Timeline', 'message' => 'List Timeline', 'status' => 0, 'link' => $userrole.'/timeline_list'],
                2 => ['title' => 'Add Timeline', 'message' => 'Add Timeline', 'status' => 0],
            ];
            $head = 'Add Timeline';
            $editF = 'A';
            $result = [];

        } else {
            $breadcrumb = [
                0 => ['title' => 'Dashboard', 'message' => 'Dashboard', 'status' => 0, 'link' => 'dashboard'],
                1 => ['title' => 'List Timeline', 'message' => 'List Timeline', 'status' => 0, 'link' => $userrole.'/timeline_list'],
                2 => ['title' => 'Edit Timeline', 'message' => 'Edit Timeline', 'status' => 0],
            ];
            $head = 'Edit Timeline';
            $editF = 'E';
            $result = Timeline::with(['timeline_langs' => function ($query) {}])->where('id', $id)->first();
            // dd($result);

        }

        $breadcrumbarr = app('App\Http\Controllers\CommonfunctionController')->bread_crump_maker($breadcrumb);
        $usertype = app('\App\Http\Controllers\CommonfunctionController')->usertypelink();
        $linactive = app('App\Http\Controllers\CommonfunctionController')->sidebarmenuformname('timeline_list')->id.'a';

        //Data
        $component = app('App\Http\Controllers\CommonfunctionController')->sidebarmenu();
        // dd($component);
        $lang = Language::where('status_id', 1)->get();

        return view('siteadmin.timeline.timeline_add', compact('component', 'breadcrumbarr', 'linactive', 'head', 'editF', 'result', 'lang', 'usertype'));
    }

    public function timeline_save(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;

        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),

            'link' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            // dd($request->menulinktype_id);
            if (isset($request->poster)) {
                $imname = 'timeline'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/timeline', $imname, 'myfile');
                // $img = \Image::make($path)->resize(302, 272);
                // $img->save($path);
                $dataarr = new Timeline([
                    // 'icon_class'=>$request->iconclass,
                    // 'color_class'=>$request->colorclass,
                    'poster' => $imname,
                    'status_id' => 1,
                    'user_id' => Auth::user()->id,
                    'year' => \Crypt::decrypt($request->year),
                    'link' => $request->link,
                ]);
            } else {
                $dataarr = new Timeline([

                    // 'icon_class'=>$request->iconclass,
                    // 'color_class'=>$request->colorclass,
                    'status_id' => 1,
                    'year' => \Crypt::decrypt($request->year),

                    'user_id' => Auth::user()->id,
                    'link' => $request->link,
                ]);
            }

            $res = $dataarr->save();
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = TimelineLang::where('title', $request->name[$lan->id])->where('lang_id', $lan->id)->exists() ? 1 : 0;
                    $datarrlang = new TimelineLang([
                        'title' => $request->name[$lan->id] ?? '',
                        'description' => $request->description[$lan->id] ?? '',
                        'timelines_id' => $dataarr->id,
                        'lang_id' => $lan->id,
                    ]);
                    if ($chkrws == 0) {

                        $reslan = $datarrlang->save();
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }

            if ($res) {
                $success = 'Timeline Added!';

                return redirect($userrole.'/timeline_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function timeline_update(Request $request)
    {
         if(Auth::user()->usertypes_id==3){
            $userrole='media';
        }else if(Auth::user()->usertypes_id==2){
            $userrole='siteadmin';
        }else if(Auth::user()->usertypes_id==5){
            $userrole='tenderadmin';
        }
        $userrole=$userrole=Auth::user()->usertypes[0]->userrolename;
        $validator = \Validator::make($request->all(), [
            'name.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),
            'description.*' => app('App\Http\Controllers\CommonfunctionController')->langtitlenotreq2(),

            'poster' => app('App\Http\Controllers\CommonfunctionController')->getposterVal(),
            'link' => app('App\Http\Controllers\CommonfunctionController')->langtitleNotreq(),
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }

        try {
            $id = \Crypt::decrypt($request->hidden_val);

            if (isset($request->poster)) {
                $imname = 'timeline'.date('yyyy:mm:dd:hh:mm:ss').$request->poster->extension();
                $path = $request->file('poster')->storeAs('uploads/timeline', $imname, 'myfile');
                // $img = \Image::make($path)->resize(302, 272);
                // $img->save($path);
                $dataarr = [

                    // 'icon_class'=>$request->iconclass,
                    // 'color_class'=>$request->colorclass,
                    'poster' => $imname,
                    'link' => $request->link,
                    'year' => \Crypt::decrypt($request->year),

                    'user_id' => Auth::user()->id,
                ];
            } else {
                $dataarr = [

                    // 'icon_class'=>$request->iconclass,
                    // 'color_class'=>$request->colorclass,

                    'link' => $request->link,
                    'year' => \Crypt::decrypt($request->year),

                    'user_id' => Auth::user()->id,
                ];
            }

            $res = Timeline::where('id', $id)->update($dataarr);
            if ($res) {
                $lang = Language::where('status_id', 1)->get();

                foreach ($lang as $lan) {
                    $chkrws = TimelineLang::where('timelines_id', '!=', $id)->where('title', $request->name[$lan->id])->exists() ? 1 : 0;
                    $datarrlang = [
                        'title' => $request->name[$lan->id] ?? '',
                        'description' => $request->description[$lan->id] ?? '',
                        'lang_id' => $lan->id,
                    ];
                    if ($chkrws == 0) {

                        $reslan = TimelineLang::where('timelines_id', $id)->where('lang_id', $lan->id)->update($datarrlang);
                    } else {
                        $res = false;
                        $msg = 'This Name is already existing';
                    }
                }
            }
            if ($res) {
                $success = 'Timeline Updated!';

                return redirect($userrole.'/timeline_list')->with(['success' => $success]);
            } else {
                $error = $msg;

                return back()->withInput()->withErrors($error);
            }
        } catch (\Exception $e) {return back()->withInput()->withErrors((string) $e);
            dd($e);

            return back()->withInput()->with('error', $e);
        }
    }

    public function timeline_status(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        $currentStatus = Timeline::where('id', $id)->first();
        if ($currentStatus->status_id == 1) {
            $newStatus = 2; //active to inactive
            $success = 'Status changed to Inctive';
        } elseif ($currentStatus->status_id == 2) {
            $newStatus = 1; //inactive to active
            $success = 'Status changed to Active';
        }
        $res = Timeline::where('id', $id)->update(['status_id' => $newStatus]);
        if ($res) {
            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in Status change';

            return back()->with(['error' => $success]);
        }
    }

    public function timeline_delete(Request $request, $encid = null)
    {
        $id = \Crypt::decrypt($encid);
        if (\Schema::hasTable('timeline_langs')) {
            $reslang = TimelineLang::where('timelines_id', $id)->delete();
            $res = Timeline::where('id', $id)->delete();
        } else {
            $res = Timeline::where('id', $id)->delete();
        }

        if ($res) {
            $success = 'Deleted Successfully';

            return back()->with(['success' => $success]);
        } else {
            $success = 'Error in delete';

            return back()->with(['error' => $success]);
        }
    }
}
