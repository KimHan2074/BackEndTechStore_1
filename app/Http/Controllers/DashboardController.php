<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    public function getSummary()
    {
        return response()->json($this->service->getSummary());
    }

    public function getMonthlyRevenue()
    {
        return response()->json($this->service->getMonthlyRevenue());
    }

    public function getRevenueByCategory()
    {
        return response()->json($this->service->getRevenueByCategory());
    }

    public function getOrderStatusDistribution()
    {
        return response()->json($this->service->getOrderStatusDistribution());
    }

    public function getTopSellingProducts()
    {
        return response()->json($this->service->getTopSellingProducts());
    }
}
