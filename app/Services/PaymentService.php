<?php

namespace App\Services;

use App\Models\Order;
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
    public function confirmPayment($data)
    {
        $payment = $this->paymentRepo->create([
            'order_id' => $data['order_id'],
            'method' => $data['method'],
            'status' => 'Completed',
            'payment_date' => Carbon::now(),
        ]);
        return $payment;

    }
}
