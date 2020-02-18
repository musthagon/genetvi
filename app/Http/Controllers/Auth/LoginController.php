<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Curso;
use App\CursoParticipante;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Auth\Events\Registered;

use App\Traits\CommonFunctionsGenetvi; 

class LoginController extends Controller
{
    use CommonFunctionsGenetvi;
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

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $response = $this->cvucv_autenticacion($request);

        if($this->hasError($response)){
            return back()->withErrors([$this->username() => $response[$this->getErrorStatus()]]);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        //Si está registrado en el CVUCV, usaremos su token para puder usar los servicios $response['token']
        if (isset($response['error'])){
            return back()->withErrors([$this->username() => $response['error']]);
        }elseif (!isset($response['token'])){
            return back()->withErrors([$this->username() => 'Error inesperado. (cod=001) ']);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // Si no puedo logearme con esos datos
        // Puede siginificar dos cosas: Que la clave la cambio en el otro sistema, o simplemente no esta registrado en nuestro sistema
        
        //1. Consultamos si el username existe
        $obj_user = User::where($this->username(),$request->{($this->username())})->first();

        if($obj_user != null){
            //MOVER AL MODELOO
            $obj_user->password = bcrypt($request->password);
            $obj_user->save();

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
        }

        //2. O si no, Lo registramos...
        $new_profile = $this->cvucv_get_profile($request->cvucv_username,'username');
        
        if (empty($new_profile)){
            return back()->withErrors([$this->username() => 'Error inesperado. (cod=002) ']);
        }

        $params = [
            '_token'            => $request->_token,
            $this->username()   => $new_profile['username'],
            'cvucv_id'          => $new_profile['id'],
            'cvucv_lastname'    => $new_profile['lastname'],
            'cvucv_firstname'   => $new_profile['firstname'],
            'cvucv_suspended'   => $new_profile['suspended'],
            'email'             => $new_profile['email'],
            'name'              => $new_profile['firstname'],
            'password'          => $request->password,
            'avatar'            => $new_profile['profileimageurl']
        ];

        event(new Registered($user = User::create($params)));

        $this->guard()->login($user);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            $this->password() => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
        $this->sync_user_courses();
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return User::username();
    }
    public function password()
    {
        return User::password();
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }


    
}
