<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSettingModel;
use File;

class GeneralSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $general_settings =GeneralSettingModel::first();
        if(isset($general_settings)){
             $socials = json_decode($general_settings->social_media);
             return view('admin.general_settings.index',compact('general_settings','socials'));
        }
        return view('admin.general_settings.index',compact('general_settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
     return view('admin.general_settings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $logo = null;
        $whitelogo = null;
        $favicon = null;
        $vission_image =null;
        $mission_image = null;
        if($request->Hasfile('logo')){
            $file = $request->file('logo');
            $logo = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$logo);
        }
        if($request->Hasfile('whitelogo')){
            $file = $request->file('whitelogo');
            $whitelogo = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$whitelogo);
        }
        if($request->Hasfile('favicon')){
            $file = $request->file('favicon');
            $favicon = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$favicon);
        }
        if($request->Hasfile('vission_image')){
            $file = $request->file('vission_image');
            $vission_image = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$vission_image);
        }
        if($request->Hasfile('mission_image')){
            $file = $request->file('mission_image');
            $mission_image = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$mission_image);
        }
        $socialmedia=[];
        $jsData= null;
        foreach($request->social_icon as $key => $social){
            $socialmedia[$key]['social_icon'] =  $soical_icon = $social;
            $socialmedia[$key]['social_link'] =$request->social_link[$key];
            $jsData= json_encode($socialmedia);
        }
        GeneralSettingModel::create([
            "store_id" => 1,
            "logo" => $logo,
            "white_logo" => $whitelogo,
            "favicon" => $favicon,
            "social_media" => $jsData,
            "phone_number" => $request->phone_number,
            "phone_number_optional" => $request->phone_number_Optional,
            "email" => $request->email_address,
            "email_optional" => $request->email_Optional,
            "address" => $request->address,
        ]);
        return redirect(admin_route('general_settings.index'))->with('success',"General Setting Stored Successfully!");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $general_info =GeneralSettingModel::find($id);
        return view('admin.general_settings.edit')->with(compact('general_info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $general_settings =GeneralSettingModel::find($id);
        $currentLogo =$general_settings->logo;
        $currentWhiteLogo =$general_settings->white_logo;
        $currentFavicon =$general_settings->favicon;
        $currentVission =$general_settings->vission_image;
        $currentMission =$general_settings->mission_image;
        $logo = null;
        $whitelogo = null;
        $favicon = null;
        $vission_image = null;
        $mission_image = null;
        if($request->Hasfile('logo')){
            $file = $request->file('logo');
            $logo = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$logo);
        }
        if($request->Hasfile('whitelogo')){
            $file = $request->file('whitelogo');
            $whitelogo = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$whitelogo);
        }
        if($request->Hasfile('favicon')){
            $file = $request->file('favicon');
            $favicon = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$favicon);
        }
        if($request->Hasfile('vission_image')){
            $file = $request->file('vission_image');
            $vission_image = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$vission_image);
        }
        if($request->Hasfile('mission_image')){
            $file = $request->file('mission_image');
            $mission_image = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
            $file->move(public_path()."/admin/uploads/generalsetting",$mission_image);
        }
        $socialmedia=[];
        $jsData= null;
        foreach($request->social_icon as $key => $social){
            $socialmedia[$key]['social_icon'] =  $soical_icon = $social;
            $socialmedia[$key]['social_link'] =$request->social_link[$key];
            $jsData= json_encode($socialmedia);
        }
        $general_settings->update([
            "logo" => ($logo)? $logo : $currentLogo,
            "white_logo" => ($whitelogo)? $whitelogo : $currentWhiteLogo,
            "favicon" => ($favicon)? $favicon : $currentFavicon,
            "social_media" => $jsData,
            "phone_number" => $request->phone_number,
            "phone_number_optional" => $request->phone_number_Optional,
            "email" => $request->email_address,
            "email_optional" => $request->email_Optional,
            "address" => $request->address,
            "vission" => $request->vission,
            "mission" => $request->mission,
            "vission_image" => ($vission_image)? $vission_image : $currentVission,
            "mission_image" => ($mission_image)? $mission_image : $currentMission,
        ]);
        if($logo){
            File::delete(public_path('admin/uploads/generalsetting/'.$currentLogo));
        }
        if($whitelogo){
            File::delete(public_path('admin/uploads/generalsetting/'.$currentWhiteLogo));
        }
        if($favicon){
            File::delete(public_path('admin/uploads/generalsetting/'.$currentFavicon));
        }
        if($vission_image){
            File::delete(public_path('admin/uploads/generalsetting/'.$currentVission));
        }
        if($mission_image){
            File::delete(public_path('admin/uploads/generalsetting/'.$currentMission));
        }
        return redirect(admin_route('general_settings.index'))->with('success','General Settings Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
