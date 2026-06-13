<?php

namespace App\Http\Controllers\Api\Private;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth:api');
        $this->dashboardService = $dashboardService;
    }


    public function mainstats(Request $request){
        return $this->dashboardService->getMainStats($request);
    }
}
