<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Validator;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        if(Auth::check()){
            return redirect()->route('dashboard');
        } else{
            return view('auth.login');
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        } else{
            $remember = $request->has('remember');
            $user = User::where('email', $request->email)->first();
            if ($user && $user->status != 1) {
                return back()->withErrors(['email' => 'Your account is not active.']);
            } else{
                if ($user && Auth::attempt(['email' => $request->email, 'password' => $request->password],$remember)) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Login successful',
                        'redirect' => route('dashboard')
                    ]);
                } else{
                    return response()->json([
                        'status' => 'fail',
                        'message' => 'Login failed',
                    ]);

                }
            }

        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
