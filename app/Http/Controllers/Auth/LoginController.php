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

        // Para consultar los usuarios al CAMPUS VIRTUAL UCV
        // Si existe algun error / o las credenciales no coinciden se retorna a la vista anterior (login)
        $response = $this->cvucv_autenticacion($request);

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.

        $this->incrementLoginAttempts($request);

        //Si está registrado en el CVUCV, usaremos su token para puder usar los servicios $response['token']
        if (isset($response['error'])){
            return back()->withErrors(['cvucv_username' => $response['error']]);
        }elseif (!isset($response['token'])){
            return back()->withErrors(['cvucv_username' => 'Error inesperado. (cod=001) ']);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // Si no puedo logearme con esos datos
        // Puede siginificar dos cosas: Que la clave la cambio en el otro sistema, o simplemente no esta registrado en nuestro sistema
        
        //1. Consultamos si el username existe, y actualizariamos su clave
        $obj_user = User::where('cvucv_username',$request->cvucv_username) -> first();

        if($obj_user != null){
            $obj_user->password = bcrypt($request->password);
            $obj_user->save();

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
        }

        //2. O si no, Lo registramos...
        $new_profile = $this->cvucv_get_profile($request, $response['token']);

        if (empty($new_profile)){
            return back()->withErrors(['cvucv_username' => 'Error inesperado. (cod=002) ']);
        }

        $params = [
            '_token'            => $request->token,
            'cvucv_username'    => $new_profile['username'],
            'cvucv_id'          => $new_profile['id'],
            'cvucv_lastname'    => $new_profile['lastname'],
            'cvucv_suspended'   => $new_profile['suspended'],
            'cvucv_token'       => $response['token'],
            'email'             => $new_profile['email'],
            'name'              => $new_profile['firstname'],
            'password'          => $request->password,
            'avatar'            => $new_profile['profileimageurl']
        ];

        event(new Registered($user = $this->create($params)));

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
            'password' => 'required|string',
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
        return 'cvucv_username';
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

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => bcrypt($data['password']),
            'avatar'            => $data['avatar'],
            'cvucv_username'    => $data['cvucv_username'],
            'cvucv_id'          => $data['cvucv_id'],
            'cvucv_lastname'    => $data['cvucv_lastname'],
            'cvucv_suspended'   => $data['cvucv_suspended'],
            'cvucv_token'       => $data['cvucv_token'],
        ]);
    }

    public function sync_user_courses(){
        $user = Auth::user();

        //Consultamos los cursos del usuario
        $cursos_cvucv = $this->cvucv_get_users_courses($user->cvucv_id);
        
        if(!empty($cursos_cvucv)){


            foreach($cursos_cvucv as $data){

                $curso = Curso::find($data['id']);

                //1. Verificamos que existan los cursos
                //Si no existe, hay que crearlo
                if(empty($curso)){

                    $curso = new Curso;

                    $curso->id                  = $data['id'];
                    $curso->cvucv_shortname     = $data['shortname'];
                    $curso->cvucv_category_id   = $data['category'];
                    $curso->cvucv_fullname      = $data['fullname'];
                    $curso->cvucv_displayname   = $data['displayname'];
                    $curso->cvucv_summary       = $data['summary'];
                    $curso->cvucv_visible       = $data['visible'];
                    $curso->cvucv_link          = env("CVUCV_GET_SITE_URL","https://campusvirtual.ucv.ve")."/course/view.php?id=".$data['id'];

                    $curso->save();
                }

                //2. Verificamos que este matriculado en ese curso -> Solicitamos los participantes del curso
                $participantes_curso = $this->cvucv_get_participantes_curso($data['id']);

                foreach($participantes_curso as $participante){
                
                    //Buscamos el usuario actual
                    if($user->cvucv_id == $participante['id']){
                        $matriculacion = CursoParticipante::where('cvucv_user_id', $participante['id'])
                            ->where('cvucv_curso_id', $data['id'])
                            ->first();
                        //Si no esta, hay que matricularlo
                        if(empty($matriculacion)){
                            $matriculacion                 = new CursoParticipante;

                            $matriculacion->user_id        = $user->id;
                            $matriculacion->cvucv_user_id  = $participante['id'];
                            $matriculacion->cvucv_curso_id = $data['id'];
                            $matriculacion->user_sync      = true;
                        }
                        if(isset($participante['roles']) && !empty($participante['roles'])){
                            $matriculacion->cvucv_rol_id = $participante['roles'][0]['roleid'];
                        }      
                        /*$matriculacion->curso_sync   = true;*/
                        $matriculacion->save();

                        break;
                    }
                }                     
            }
            
        }
    }

    /**
     * CURL generíco usando GuzzleHTTP
     *
     */
    public function send_curl($request_type, $endpoint, $params){

        $client   = new \GuzzleHttp\Client();

        $response = $client->request($request_type, $endpoint, ['query' => $params ]);

        //$statusCode = $response->getStatusCode();

        $content    = json_decode($response->getBody(), true);

        return $content;
    }

    public function cvucv_autenticacion(Request $request)
    {
        $endpoint = env("CVUCV_GET_USER_TOKEN","https://campusvirtual.ucv.ve/moodle/login/token.php");
        $service  = env("CVUCV_GET_USER_TOKEN_SERVICE","moodle_mobile_app");

        $params = [
            'service'  => $service,
            'username' => $request->cvucv_username,
            'password' => $request->password
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response;
    }

    public function cvucv_get_profile(Request $request, $token)
    {
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'            => 'core_user_get_users_by_field',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'field'                 => 'username',
            'values[0]'             => $request->cvucv_username,
        ];

        $response = $this->send_curl('GET', $endpoint, $params);
        
        return $response[0];
    }

    /**
     * Obtiene los cursos en los que está matriculado un usuario
     *
     */
    public function cvucv_get_users_courses($user_id){
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'            => 'core_enrol_get_users_courses',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'userid'                => $user_id
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response;
    }

    /**
     * Obtiene los participantes de un curso
     *
     */
    public function cvucv_get_participantes_curso($course_id){
        $endpoint = env("CVUCV_GET_WEBSERVICE_ENDPOINT","https://campusvirtual.ucv.ve/moodle/webservice/rest/server.php");
        $wstoken  = env("CVUCV_ADMIN_TOKEN");

        $params = [
            'wsfunction'            => 'core_enrol_get_enrolled_users',
            'wstoken'               => $wstoken,
            'moodlewsrestformat'    => 'json',
            'courseid'              => $course_id
        ];

        $response = $this->send_curl('POST', $endpoint, $params);
        
        return $response;
    }
}
