<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    protected $repo;

    public function __construct(DashboardRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getSummary()
    {
        return $this->repo->getSummary();
    }

    public function getMonthlyRevenue()
    {
        return $this->repo->getMonthlyRevenue();
    }

    public function getRevenueByCategory()
    {
        return $this->repo->getRevenueByCategory();
    }

    public function getOrderStatusDistribution()
    {
        return $this->repo->getOrderStatusDistribution();
    }

    public function getTopSellingProducts()
    {
        return $this->repo->getTopSellingProducts();
    }
}
