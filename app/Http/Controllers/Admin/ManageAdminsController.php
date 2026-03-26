<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ManageAdminsController extends Controller
{
    public function index()
    {
        $admins = Admin::with('roles', 'mobiles')->get();

        return view('Admin.Admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();

        return view('Admin.Admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            'mobiles' => 'array',
            'roles' => 'array',
        ]);

        $admin = Admin::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'address' => $request->address,
            'gender' => $request->gender ?? 'male',
            'age' => $request->age,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('mobiles')) {
            foreach ($request->mobiles as $number) {
                if (! empty($number)) {
                    AdminMobile::create(['admin_id' => $admin->id, 'mobile_number' => $number]);
                }
            }
        }

        if ($request->has('roles')) {
            $admin->assignRole($request->roles);
        }

        return redirect()->route('admin.manage_admins.index')->with('success', __('Admin created successfully.'));
    }

    public function show(Admin $manage_admin)
    {
        $admin = $manage_admin;
        $admin->load('mobiles', 'roles');

        return view('Admin.Admins.show', compact('admin'));
    }

    public function edit(Admin $manage_admin)
    {
        $admin = $manage_admin;
        $admin->load('mobiles', 'roles');
        $roles = Role::where('guard_name', 'admin')->get();

        return view('Admin.Admins.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, Admin $manage_admin)
    {
        $admin = $manage_admin;
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,'.$admin->id,
            'mobiles' => 'array',
            'roles' => 'array',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'title' => $request->title,
            'address' => $request->address,
            'gender' => $request->gender ?? 'male',
            'age' => $request->age,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        // Update Mobiles Logic
        if ($request->has('mobiles')) {
            $admin->mobiles()->delete();
            foreach ($request->mobiles as $number) {
                if (! empty($number)) {
                    AdminMobile::create(['admin_id' => $admin->id, 'mobile_number' => $number]);
                }
            }
        }

        if ($request->has('roles')) {
            $admin->syncRoles($request->roles);
        } else {
            $admin->syncRoles([]);
        }

        return redirect()->route('admin.manage_admins.index')->with('success', __('Admin updated successfully.'));
    }

    public function destroy(Admin $manage_admin)
    {
        if (auth()->guard('admin')->id() === $manage_admin->id) {
            return redirect()->back()->with('error', __('Cannot delete own account.'));
        }
        $manage_admin->delete();

        return redirect()->route('admin.manage_admins.index')->with('success', __('Admin deleted successfully.'));
    }
}
