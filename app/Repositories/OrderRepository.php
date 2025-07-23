<?php
namespace App\Repositories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;


class OrderRepository
{
    public function create(array $data)
    {
        return Order::create($data);
    }

    public function update($orderId, array $data)
    {
        $order = Order::findOrFail($orderId);
        $order->update($data);
        return $order;
    }

    public function getLatestOrderByUser($userId)
    {
        $order = Order::with('user')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->firstOrFail();

        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment) {
            throw new \Exception("Payment not found for order ID: {$order->id}");
        }

        $details = OrderDetail::with('product.firstImage')
            ->where('order_id', $order->id)
            ->get();

        $subtotal = $details->sum(fn($item) => $item->unit_price * $item->quantity);

        $shippingFee = match ($order->shipping_option) {
            'free' => 0,
            'local' => 5,
            'flat' => 15,
            default => 0,
        };

        $discount = $order->discount ?? 0;
        $total = $subtotal + $shippingFee - $discount;

        return [
            'order_code' => '#ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'customer' => [
                'fullname' => $order->full_name,
                'phone' => $order->phone,
                'email' => $order->user->email ?? '',
                'address' => "{$order->address} - {$order->ward} - {$order->district} - {$order->province}",
            ],
            'payment' => [
                'method' => $payment->method,
                'status' => $payment->status,
            ],
            'items' => $details->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'color' => $item->color,
                    'image' => optional($item->product->firstImage)->image_url,
                ];
            }),
            'summary' => [
                'subtotal' => number_format($subtotal, 2),
                'shipping_fee' => number_format($shippingFee, 2),
                'discount' => number_format($discount, 2),
                'total' => number_format($total, 2),
            ]
        ];
    }
    public function getOrdersByUserAndDate($userId, $date = null)
    {
        return Order::with(['orderDetails.product'])
            ->where('user_id', $userId)
            ->when($date, function ($query) use ($date) {
                return $query->whereDate('order_date', $date);
            })
            ->orderBy('order_date', 'desc')
            ->get();
    }
    public function deleteHistory($userId, $productId, $date)
    {
        return OrderDetail::whereHas('order', function ($query) use ($userId, $date) {
            $query->where('user_id', $userId)
                ->whereDate('order_date', $date);
        })
            ->where('product_id', $productId)
            ->delete();
    }

        public function fetchOrdersWithUser()
    {
        return DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.id',
                'users.name as user_name',
                'orders.status',
                'orders.shipping_option',
                'orders.total_amount',
                'orders.created_at as order_date'
            )
            ->orderBy('orders.created_at', 'desc')
            ->get();
    }

    public function deleteOrderById($id)
    {
        return DB::table('orders')->where('id', $id)->delete();
    }


}
