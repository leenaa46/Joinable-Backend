<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Company\CompanyService;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    protected $service;

    public function __construct(CompanyService $companyService)
    {
        $this->service = $companyService;
    }

    public function getByCode($joinableCode)
    {
        $res = $this->service->getByCode($joinableCode);
        return $this->success($res, __('success.get_data'));
    }
}
