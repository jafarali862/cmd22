<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
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
    // protected $redirectTo = '/home';


    protected function redirectTo()
    {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return '/home'; // admin dashboard
    }

    if ($user->role === 'user') {
        return '/home2'; // user dashboard
    }

    return '/'; // default fallback
    }

    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

// public function logout(Request $request)
//     {
//         Auth::logout();

//         // Redirect to login page (or wherever you want)
//         return redirect('/login');
//     }


}
