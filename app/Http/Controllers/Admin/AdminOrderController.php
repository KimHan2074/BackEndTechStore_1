<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function getOrders()
    {
        $orders = $this->orderService->getAllOrders();
        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function deleteOrder($id)
    {
        $result = $this->orderService->deleteOrder($id);
        return response()->json([
            'status' => $result
        ]);
    }
}
