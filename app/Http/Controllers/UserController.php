<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use DB;
use File;
use Image;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'DESC')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function create()
    {
        $role = Role::orderBy('name', 'ASC')->get();
        return view('users.create', compact('role'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|max:100',
            'name' => 'string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string|exists:roles,name',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg'

        ]);        

        try {
            $photo = 'profile.png';
            if ($request->hasFile('photo')) {
                $photo = $this->saveFile($request->name, $request->file('photo'));
            }

            $user = User::firstOrCreate([
                'email' => $request->email
            ], [
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'status' => true,
                'name'  => $request->name,
                'photo' => $photo,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with(['error' => $e->getMessage()]);
        }

        

        $user->assignRole($request->role);
        return redirect(route('users.index'))->with(['success' => 'User: <strong>' . $user->username . '</strong> Ditambahkan']);
    }

    private function saveFile($name, $photo)
    {
        $images = str_slug($name) . time() . '.' . $photo->getClientOriginalExtension();
        $path = public_path('uploads/profile');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        } 
        Image::make($photo)->save($path . '/' . $images);
        return $images;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // $user = User::findOrFail($id);
        // return redirect(route('users.index'))->with(['success' => 'User: <strong>' . $user->username . '</strong> Diperbaharui']);
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:100',
            'email' => 'required|email|exists:users,email',
            'password' => 'nullable|min:6',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        $user = User::findOrFail($id);
        $photo = $user->photo;

            if ($request->hasFile('photo')) {
                !empty($photo) ? File::delete(public_path('uploads/profile/' . $photo)):null;
                $photo = $this->saveFile($user->name, $request->file('photo'));
            }
        $password = !empty($request->password) ? bcrypt($request->password):$user->password;
        $user->update([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => $password,
            'photo' => $photo
        ]);
        return redirect(route('users.index'))->with(['success' => 'User: <strong>' . $user->username . '</strong> Diperbaharui']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with(['success' => 'User: <strong>' . $user->username . '</strong> Dihapus']);
    }

    public function roles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all()->pluck('name');
        return view('users.roles', compact('user', 'roles'));
    }

    public function setRole(Request $request, $id)
    {
        $this->validate($request, [
            'role' => 'required'
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles($request->role);
        return redirect()->back()->with(['success' => 'Role Sudah Di Set']);
    }

    public function rolePermission(Request $request)
    {
        
        $role = $request->get('role');
        $permissions = null;
        $hasPermission = null;

        $roles = Role::all()->pluck('name');

        if (!empty($role)) {
            $getRole = Role::findByName($role);
            $hasPermission = DB::table('role_has_permissions')
                ->select('permissions.name')
                ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->where('role_id', $getRole->id)->get()->pluck('name')->all();
            $permissions = Permission::all()->pluck('name');
        }
        return view('users.role_permission', compact('roles', 'permissions', 'hasPermission'));
    }

    public function addPermission(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:permissions'
        ]);

        $permission = Permission::firstOrCreate([
            'name' => $request->name
        ]);
        return redirect()->back();
    }

    public function setRolePermission(Request $request, $role)
    {
        $role = Role::findByName($role);
        $role->syncPermissions($request->permission);
        return redirect()->back()->with(['success' => 'Permission to Role Saved!']);
    }
}
