<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use Validator;

class ModuleController extends Controller
{
    public function index(){
        $modules = Module::latest()->get();
        return view('admin.module.index',compact('modules'));
    }

    public function store(Request $request){
        $validators =Validator::make($request->all(),[
            'name' => 'required',
            'route' => 'required',
            'icon' => 'required',
        ]);
        if($validators->fails()){
            return response()->json([
                'errors' => $validators->errors()
            ], 422);
        } else{
            Module::create([
                'name' => $request->name,
                'route' => $request->route,
                'icon' => $request->icon,
                'icon_type' => $request->icon_type,
                'parent_id' => ($request->parent_id != null)? $request->parent_id : 0,
                'sorting' => $request->sort,
                'is_group_title' => 0,
                'color' => null,
                'is_active' => 1,
            ]);
            return response()->json(['success' => 'Module added successfully!']);
        }
    }

    public function edit($slug,$id){
        $module = Module::find($id);
        $modules = Module::latest()->get();
        return view('admin.module.edit',compact('module','modules'));
    }

    public function update($slug,Request $request,$id){
        $validators =Validator::make($request->all(),[
            'name' => 'required',
            'route' => 'required',
            'icon' => 'required',
        ]);
        if($validators->fails()){
            return response()->json([
                'errors' => $validators->errors()
            ], 422);
        } else{
            $module = Module::find($id);
            if(!$module){
                return response()->json(['error' => 'Module not found!'], 404);
            }
            // Update the module
            $module->update([
                'name' => $request->name,
                'route' => $request->route,
                'icon' => $request->icon,
                'parent_id' => ($request->parent_id != null)? $request->parent_id : 0,
                'sorting' => $request->sort,
            ]);
            // Return success response
            return redirect(admin_route('module.index'))->with('success', 'Module updated successfully!');
        }
    }

    public function destroy($id){
        $modules = Module::find($id);
        if(!$modules){
            return response()->json(['error' => 'Module not found!'], 404);
        }
        $modules->delete();
        Module::where('parent_id', $id)->delete();
        return response()->json(['success' => 'Module deleted successfully!']);
    }
}
