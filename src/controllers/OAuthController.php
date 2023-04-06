<?php

namespace Shuvo\BdrenOauth\controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class OAuthController extends Controller
{

    private string $client_id;
    private string $client_secret;
    private string $base_url;
    private string $user;
    private string $success_redirect;
    private string $error_redirect;

    public function __construct()
    {
        $this->client_id = config('bdren_oauth.oauth_client_id');
        $this->client_secret = config('bdren_oauth.oauth_client_secret');
        $this->base_url = config('bdren_oauth.oauth_base_url');
        $this->user = app(config('bdren_oauth.oauth_user_model'));
        $this->success_redirect = config('bdren_oauth.oauth_success_url');
        $this->error_redirect = config('bdren_oauth.oauth_error_url');

        // check if client id and client secret is set
        if (empty($this->client_id) || empty($this->client_secret) || empty($this->base_url)) {
            throw new \Exception('Client ID or Client Secret or Base URL is not set');
        }


        // check base url has / at the end
        if (substr($this->base_url, -1) != '/') {
            // remove / from the end
            $this->base_url = rtrim($this->base_url, '/');
        }

        if ($this->success_redirect == '/') {
            $this->success_redirect = url('/');
        }

        if ($this->error_redirect == '/') {
            $this->error_redirect = url('/');
        }
    }

    public function login_redirect($msg)
    {
        $login_route = route('login');
        return <<<HTML
                    <script>
                        alert('{$msg}');
                        window.location.href = '{$login_route}';
                    </script>
               HTML;
    }

    public function login(Request $request)
    {
        if (Auth::check()) {
            return redirect()->back();
        }

        $scope = 'profile';
        $response_type = 'code';


        // generate random state
        $state = md5(rand());

        // save state in session
        $request->session()->put('oauth_state', $state);

        $params = http_build_query([
            'client_id' => $this->client_id,
            'response_type' => $response_type,
            'scope' => $scope,
            'state' => $state
        ]);


        // make url with params
        $oauth_url = $this->base_url . "/oauth/authorize/?$params";

        // redirect to oauth server
        return redirect($oauth_url);
    }


    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return $this->login_redirect($request->error_description);
        }


        $code = $request->code;
        $state = $request->state;

        // check state
        if ($state != $request->session()->get('oauth_state')) {
            // give error message
            return $this->login_redirect('Invalid State');
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url . '/oauth/token/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=authorization_code&code=' . $code,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($this->client_id . ':' . $this->client_secret),
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

//        return $response;
        // json decode response
        $response = json_decode($response);


        if (isset($response->error)) {
            return $this->login_redirect($response->error_description);
        }


        $access_token = $response->access_token;

        // get user info
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->base_url . '/oauth/profile/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

//        return $response;
        // json decode response
        $response = json_decode($response);

        if (isset($response->error)) {
            return $this->login_redirect($response->error_description);
        }

        $email = $response->email;

        // check user exist or not
        $user = $this->user->where('email', $email)->first();

        if (!$user) {
            // create user
            $user = $this->user->create([
                'name' => $response->first_name . ' ' . $response->last_name,
                'email' => $response->email,
                'password' => bcrypt(rand(123456789, 987654321)),
                'oauth_token' => $access_token,
            ]);

            // login user
        }
        Auth::login($user);
        return redirect($this->success_redirect);
    }


    public function logout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back();
        }

        $user = Auth::user();
        $user->oauth_token = null;
        $user->save();

        Auth::logout();
        return redirect($this->success_redirect);
    }
}
