<?php

// namespace App\Services;

// use App\Models\Order;
// use App\Models\Payment;
// use App\Repositories\PaymentRepository;
// use Illuminate\Support\Carbon;

// class PaymentService
// {
//     protected $paymentRepo;
//     public function __construct(PaymentRepository $paymentRepo)
//     {
//         $this->paymentRepo = $paymentRepo;
//     }
//     public function createPayment(array $data)
//     {
//         $paymentMethod = $data['payment_method'];

//         $status = ($paymentMethod === 'cash') ? 'processing' : 'Completed';

//         $order = Order::findOrFail($data['order_id']);
//         $order->status = $status;
//         $order->save();
//         $payment = Payment::create([
//             'order_id' => $order->id,
//             'method' => $paymentMethod,
//             'amount' => $data['amount'],
//             'status' => $status,
//         ]);

//         return $payment;
//     }
//     public function confirmPayment($data)
//     {
//         $payment = $this->paymentRepo->create([
//             'order_id' => $data['order_id'],
//             'method' => $data['method'],
//             'status' => 'Completed',
//             'payment_date' => Carbon::now(),
//         ]);
//         return $payment;

//     }
// }

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Carbon;

class PaymentService
{
    protected $paymentRepo;
    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepo = $paymentRepo;
    }
    public function createPayment(array $data)
    {
        $paymentMethod = $data['payment_method'];

        $status = ($paymentMethod === 'cash') ? 'processing' : 'Completed';

        $order = Order::findOrFail($data['order_id']);
        $order->status = $status;
        $order->save();
        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => $paymentMethod,
            'amount' => $data['amount'],
            'status' => $status,
        ]);

        return $payment;
    }
    // public function confirmPayment($data)
    // {
    //     $payment = $this->paymentRepo->create([
    //         'order_id' => $data['order_id'],
    //         'method' => $data['method'],
    //         'status' => 'Completed',
    //         'payment_date' => Carbon::now(),
    //     ]);
    //     return $payment;

    // }


    public function confirmPayment($data)
    {
        $payment = $this->paymentRepo->create([
            'order_id' => $data['order_id'],
            'method' => $data['method'],
            'status' => 'Completed',
            'payment_date' => Carbon::now(),
        ]);

        // ✅ Ghi các item vào DB
        foreach ($data['items'] as $item) {
            OrderDetail::create([
                'order_id' => $data['order_id'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'color' => $item['color'] ?? 'black',
            ]);
        }

        return $payment;
    }

}
