<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Module;
use Validator;
use DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rolesFilter = Role::get();
        if($request->roleFilter != null){
            $search =$request->roleFilter;
            $roles = Role::where('name','like','%'.$search.'%')->orderBy('id','DESC')->paginate(20);
        } else{
            $roles = Role::paginate(20);
        }
        return view('admin.role.index')->with(compact('roles','rolesFilter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules = Module::orderBy('id','DESC')->get();
        return view('admin.role.create')->with(compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //print_r($request->all());die();
          $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $role = new Role([
            'name' => $request->get('name')
        ]);
        $role->save();
        foreach ($request->moduleid as $key => $value) {
            $role_permission = new RolePermission([
                'role_id' => $role->id,
                'module_id' => $value,
                'pview' => isset($request->view[$key]) ? 1 : 0,
                'pcreate' => isset($request->create[$key]) ? 1 : 0,
                'pedit' => isset($request->edit[$key]) ? 1 : 0,
                'pdelete' => isset($request->delete[$key]) ? 1 : 0,
            ]);
            $role_permission->save();
        }
        return redirect(route('role.index'))->with('success', 'Role is successfully updated');
        // return redirect()->route('role.index')->with('success', 'Role has been added.');
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
        $role = Role::with('permission')->findOrFail($id);
        $role->permission = json_decode($role->permission, true);
        // print_r(json_decode($role->permission, true));die;
        $modules = Module::orderBy('id','DESC')->get();
        return view('admin.role.edit', compact('role', 'modules'));
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
        $role = Role::whereId($id)->first();
        $role->name=  $request->get('name');
        $role->permission()->delete();
        $role->save();
        foreach ($request->moduleid as $key => $value) {
            $role_permission = new RolePermission([
                'role_id' => $role->id,
                'module_id' => $value,
                'pview' => isset($request->view[$key]) ? 1 : 0,
                'pcreate' => isset($request->create[$key]) ? 1 : 0,
                'pedit' => isset($request->edit[$key]) ? 1 : 0,
                'pdelete' => isset($request->delete[$key]) ? 1 : 0,
            ]);
            $role_permission->save();
        }
        return redirect(route('role.index'))->with('success', 'Role is successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function role_delete(Request $request)
    {
        $dept = Role::find($request->id);
        $permissions = RolePermission::where('role_id',$request->id)->get();
        foreach($permissions as $perm){
            $perm->delete();
        }
        $dept->delete();
        return redirect()->back()->with('error',"Role Deleted Successfully!");

        // return response()->json(['success'=> 'role record deleted']);
    }
    public function filter(Request $request){
        $validator = Validator::make($request->all(), [
            'bulkaction' => 'not_in:00',
            'roleFilter' => 'not_in:00',
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        } else{
            $ids = explode(',',$request->getIds);
            $roles = Role::whereIn('id',$ids)->get();
            if($request->bulkaction =='delete'){
                foreach($roles as $role){
                    $role->delete();
                }
                return redirect()->back()->with('error',"Role Deleted Successfully!");
            } else{
                return redirect()->back()->with('error',"Something Went Wrong!");
            }
        }
    }
}
