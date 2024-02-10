<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function authenticated(Request $request, $user)
    {

        $errors = [$this->username() => trans('auth.failed')];
        $username = $this->username();
        $credentials = request()->only($username, 'password');
        // dd($credentials);
        if ((auth()->user()->position != strtolower($credentials[$username])) && $credentials[$username] != strtolower($credentials[$username])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'invalid credentials!');
        } else {
        }
        if ($user->hasRole('superadmin')) {
            return redirect()->route('superadmindashboard');
        }
        if ($user->hasRole('admin')) {
            return redirect()->route('admindashboard');
        }
        if ($user->hasRole('user')) {
            return redirect()->route('userdashboard');
        }
    }
}
