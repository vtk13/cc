<?php
namespace Vtk13\Cc\Controller;

use GuzzleHttp\Client;
use Vtk13\Cc\BaseController;
use Vtk13\Mvc\Http\RedirectResponse;
use Vtk13\Mvc\Http\Response;

class AuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct('auth');
    }

    public function logoutGET()
    {
        session_destroy();
        return new RedirectResponse('/');
    }

    public function googleLoginGET()
    {
        $url = 'https://accounts.google.com/o/oauth2/auth';
        $google = json_decode(file_get_contents(stream_resolve_include_path('../google.json')), true);
        $params = array(
            'redirect_uri'  => "http://{$_SERVER['HTTP_HOST']}/auth/google-callback",
            'response_type' => 'code',
            'client_id'     => $google['web']['client_id'],
            'scope'         => 'https://www.googleapis.com/auth/userinfo.email'
        );

        return new RedirectResponse($url . '?' . http_build_query($params));
    }

    public function googleCallbackGET()
    {
        if (empty($_GET['code'])) {
            return new Response('Invalid Code param', 400);
        }

        $google = json_decode(file_get_contents(stream_resolve_include_path('../google.json')), true);
        $params = array(
            'client_id'     => $google['web']['client_id'],
            'client_secret' => $google['web']['client_secret'],
            'redirect_uri'  => "http://{$_SERVER['HTTP_HOST']}/auth/google-callback",
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code']
        );

        $url = 'https://accounts.google.com/o/oauth2/token';

//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL, $url);
//            curl_setopt($curl, CURLOPT_POST, 1);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//            $result = curl_exec($curl);
//            curl_close($curl);
//
//            $tokenInfo = json_decode($result, true);
//            var_dump($tokenInfo);die;

        $client = new Client();
        $response = $client->post($url, [
            'form_params' => $params,
        ]);
        $tokenInfo = json_decode($response->getBody()->getContents(), true);

        if (isset($tokenInfo['access_token'])) {
            $params['access_token'] = $tokenInfo['access_token'];

            $client = new Client();
            $response = $client->get('https://www.googleapis.com/oauth2/v1/userinfo?' . http_build_query($params));
            $userData = json_decode($response->getBody()->getContents(), true);
            if (isset($userData['email']) && $userData['verified_email'] == true) {
                $userId = $this->db->selectValue(
                    'SELECT id
                       FROM user
                      WHERE ' . $this->db->where('email', $userData['email'])
                );
                if (empty($userId)) {
                    $this->db->insert('user', ['email' => $userData['email']]);
                    $userId = $this->db->insertId();
                }
                $_SESSION['userId'] = $userId;
                $redirect = isset($_SESSION['login-redirect']) ? $_SESSION['login-redirect'] : '/';
                unset($_SESSION['login-redirect']);
                return new RedirectResponse($redirect);
            }
        }

        return new Response('Cannot authorize :(', 400);
    }
}
