<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\ServiceUser;
use App\Rules\MatchOldPassword;
use Hash;
use File;
use Validator;
use Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('permission:users,pview')->only('index', 'show');
        $this->middleware('permission:users,pcreate')->only('create', 'store');
        $this->middleware('permission:users,pedit')->only('edit', 'update');
        $this->middleware('permission:users,pdelete')->only('destroy');
    }

    public function index(Request $request)
    {
        if($request->search != ''){
            $users = User::with('role')
                    ->orWhere('email',$request->search)
                    ->orWhere('name','LIKE','%'.$request->search.'%')
                    ->paginate(20);
        } else{
                $users = User::with('role')->where('role_id','!=',2)
                ->paginate(20);
        }
        return view('admin.user.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::get();
        return view('admin.user.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // dd($request->all());
        $validators = Validator::make($request->all(), [
            'name' => 'required',
            'role_id' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'retype_password' => 'required|same:password|min:8',
            'user_image' => 'image|mimes:png,jpeg,jpg',
        ]);

        if ($validators->fails()) {
            return redirect()->back()->withErrors($validators->errors());
        } else {
            $fileName = null;
            if ($request->hasFile('user_image')) {
                $file = $request->file('user_image');
                $fileName = md5($file->getClientOriginalName()) . "_" . date('d-m-y') . "_" . time() . "." . $file->getClientOriginalExtension();
                $file->move(public_path() . "/admin/uploads/user_images", $fileName);
            }
            $user = User::create([
                // 'store_id' => 1,
                'role_id' => $request->role_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'user_image' => $fileName,
                'password' => Hash::make($request->password),
                'status' => $request->status,

            ]);
            return redirect(route('user.index'))->with('success', 'User created successfully');
            // return redirect()->route('user.index')->with('success', 'User created successfully');
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::with('role')->find($id);
        $roles = Role::get();
        return view('admin.user.edit',compact('user','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required',
            'user_image' => 'image|mimes:png,jpeg,jpg',
        ]);

        if($validators->fails()){
            return redirect()->back()->withErrors($validators->errors());
        } else{
            $user = User::find($id);
            $currentImage = $user->user_image;
            $fileName = null;
            if($request->Hasfile('user_image')){
                $file = $request->file('user_image');
                $fileName = md5($file->getClientOriginalName())."_".date('d-m-y')."_".time().".".$file->getClientOriginalExtension();
                $file->move(public_path()."/admin/uploads/user_images",$fileName);
            }
            $user->update([
                // 'store_id' => 1,
                'role_id' => $request->role_id,
                'name' => $request->name,
                'user_image' => ($fileName)? $fileName: $currentImage,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'status' => $request->status,
            ]);
            if($fileName){
                File::delete(public_path('/admin/uploads/user_images/' . $currentImage ));
            }
            return redirect(route('user.index'))->with('success', 'User created successfully');
            // return redirect()->route('user.index')->with('success','User updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function user_delete(Request $request)
    {
        $user = User::with('role')->find($request->id);
        $currentImage = $user->user_image;
        File::delete(public_path('/admin/uploads/user_images/' . $currentImage ));
        $user->delete();
        return redirect()->back()->with('success','User deleted');
    }
    public function user_status(Request $request){
        $student_status = User::find($request->id);
        $newStatus=($student_status->status==0)? 1 : 0 ;
        $student_status->update(['status' => $newStatus]);
        return response()->json(['success' => $newStatus]);
    }
    public function setting_view(){
        return view('admin.change_password');
    }

    public function passwordChange(Request $request){
        $validator = Validator::make($request->all(),[
            'old_password' => ['required', new MatchOldPassword],
            'password' => 'required|min:8',
            'retype_password' => 'required|same:password|min:8',
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        } else{
            User::find(auth()->user()->id)->update(['password'=> Hash::make($request->retype_password)]);
            Session::flush();
            Auth::logout();
            return redirect()->route('login')->with('success','Password changed successfully!');
        }
    }
}
