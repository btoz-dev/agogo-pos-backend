<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    public function field(Request $request)
    {
        $email = $this->username();

        return filter_var($request->get($email), FILTER_VALIDATE_EMAIL) ? $email : 'username';
    }


    public function login(Request $request)
    {
        $get_role = User::role(['admin', 'manager'])
            ->where('username', $request->username)->count();
        // return $get_role;
        
        if ($get_role > 0) {
            # code...

        $this->validate($request, [
            // 'email' => 'required|email',
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        
        
        if (auth()->attempt(['username' => $request->username, 'password' => $request->password, 'status' => 1])) {
            return redirect()->intended('home');
        }
        return redirect()->back()->with(['error' => 'Username / Password Salah']);
        }
        return redirect()->back()->with(['error' => 'Hanya Untuk Admin & Manager']);

    }

     /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    // protected function validateLogin(Request $request)
    // {
    //     $field = $this->field($request);
    //     $message  = [

    //         "{$this->username()}.exist" => 'akun belum terdaftar / tervalidasi'

    //     ];
    //     $this->validate($request, [
    //         $this->username() => "required|string|exists:users,{$field}",
    //         'password' => 'required|string',
    //     ], $message);

        
    // }

     /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $field = $this->field($request);
        return [
            $field => $request->get($this->username()),
            'password' => $request->get('password')
        ];
        // return $request->only($this->username(), 'password');
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect(\URL::previous());
      }
}
