<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $authService)
    {
        $this->service = $authService;
    }

    public function login(Request $request)
    {
        $res = $this->service->login($request);
        return $this->token($res, __('success.login'));
    }

    public function logout()
    {
        $res = $this->service->logout();
        return $this->success($res, __('success.logout'));
    }

    public function register()
    {
        $res = $this->service->registerApp(request());
        return $this->success($res, __('success.register'));
    }

    public function me()
    {
        $res = $this->service->me();
        return $this->success($res, __('success.get_data'));
    }

    public function registerCompany()
    {
        $res = $this->service->registerCompany(request());
        return $this->success($res, __('success.register'));
    }
}
