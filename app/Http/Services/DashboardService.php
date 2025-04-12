<?php
namespace App\Http\Services;

use App\Http\Respositories\DashboardRespository;

class DashboardService
{
    public function __construct(private DashboardRespository $dashboardRespository)
    {
        
    }
}