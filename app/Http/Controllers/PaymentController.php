<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string|in:momo,cash,vnpay,qr',
            'amount' => 'required|numeric|min:0',
        ]);

        $payment = $this->paymentService->createPayment($request->all());

        return response()->json([
            'message' => 'Payment method was stored successfully!',
            'data' => $payment,
        ]);
    }
     public function confirm(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|in:COD,VNPay,Momo,PayPal,QR'
        ]);

        $result = $this->paymentService->confirmPayment($validated);

        return response()->json([
            'message' => 'Payment confirmation successful.',
            'payment_id' => $result->id,
        ]);
    }
}
