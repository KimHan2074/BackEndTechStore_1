<?php

// namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Services\PaymentService;

// class PaymentController extends Controller
// {
//     protected $paymentService;

//     public function __construct(PaymentService $paymentService)
//     {
//         $this->paymentService = $paymentService;
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'order_id' => 'required|exists:orders,id',
//             'payment_method' => 'required|string|in:momo,cash,vnpay,qr',
//             'amount' => 'required|numeric|min:0',
//         ]);

//         $payment = $this->paymentService->createPayment($request->all());

//         return response()->json([
//             'message' => 'Payment method was stored successfully!',
//             'data' => $payment,
//         ]);
//     }
//     public function confirm(Request $request)
//     {
//         $validated = $request->validate([
//             'order_id' => 'required|exists:orders,id',
//             'method' => 'required|in:COD,VNPay,Momo,PayPal,QR'
//         ]);

//         $result = $this->paymentService->confirmPayment($validated);

//         return response()->json([
//             'message' => 'Payment confirmation successful.',
//             'payment_id' => $result->id,
//         ]);
//     }
// }


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


    // Tạo payment (frontend)
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string|in:momo,cash,vnpay,qr',
            'amount' => 'required|numeric|min:0',
        ]);


        $payment = $this->paymentService->createPayment($request->all());


        return response()->json([
            'message' => 'Payment stored successfully!',
            'data' => $payment,
        ]);
    }


    // Xác nhận payment (callback từ MoMo/VNPay)
    public function confirm(Request $request)
    {
        $orderIdFull = $request->input('order_id'); // vd: "MOMO_76_1756375954"
        $systemOrderId = explode('_', $orderIdFull)[1] ?? $orderIdFull;


        $order = \App\Models\Order::find($systemOrderId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }


        $paymentData = [
            'order_id' => $orderIdFull,
            'method' => $request->input('method', 'momo'),
            'amount' => $request->input('amount'),
            'items' => $order->orderDetails->map(fn($d) => [
                'product_id' => $d->product_id,
                'quantity' => $d->quantity,
                'unit_price' => $d->unit_price,
                'color' => $d->color,
            ])->toArray(),
        ];


        $payment = $this->paymentService->createPayment($paymentData);


        return response()->json(['payment' => $payment]);
    }
}


