<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view('login');
    }

    public function superDashboard()
    {
        return view('dashboard.superDashboard');
    }
    public function electricalDashboard()
    {
        return view('dashboard.electricalDashboard');
    }
    public function electronicsDashboard()
    {
        return view('dashboard.electronicsDashboard');
    }
    public function form()
    {
        return view('admin.form');
    }

    public function logout()
    {
        // Auth::guard('admin')->logout();
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged Out Successfully.');
    }




    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::guard('admin')->user();

            if ($user->role == 'Super Admin') {
                return redirect()->route('superadmin.dashboard');
            } elseif ($user->role == 'Electrical') {
                return redirect()->route('electrical.dashboard');
            } elseif ($user->role == 'Electronics') {
                return redirect()->route('electronics.dashboard');
            } else {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Unauthorized user. Access Denied.');
            }
        }

        return redirect()->route('login')->with('error', 'Email & Password are incorrect.');

        // $user = new User();
        // $user->email = $request->email;
        // $user->password = $request->password;
        // $user->save();
        // return redirect()->back()->with('success', 'User Created');
    }

    public function redirectToDashboard()
    {
        if (Auth::user()->role === 'Admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif (Auth::user()->role === 'Electrical') {
            return redirect()->route('electrical.dashboard');
        } elseif (Auth::user()->role === 'Electronics') {
            return redirect()->route('electronics.dashboard');
        }

        return redirect()->route('login'); // Fallback for other roles or superadmin
    }


    // public function loadregister(Request $request)
    // {
    //     return view('admin.register');
    // }
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|email',
    //         'role' => 'required|string',
    //         'password' => 'required',
    //     ]);
    //     $user = new User();
    //     $user->name = $request->input('name');
    //     $user->email = $request->input('email');
    //     $user->password = Hash::make($request->input('password'));
    //     $user->role = $request->input('role');
    //     $user->save();
    //     return redirect()->route('admin.login')->with('success', 'User Registered successfully.');
    // }
}
