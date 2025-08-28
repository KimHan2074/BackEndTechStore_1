<?php

// namespace App\Services;

// use App\Models\Order;
// use App\Models\OrderDetail;
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
//     // public function confirmPayment($data)
//     // {
//     //     $payment = $this->paymentRepo->create([
//     //         'order_id' => $data['order_id'],
//     //         'method' => $data['method'],
//     //         'status' => 'Completed',
//     //         'payment_date' => Carbon::now(),
//     //     ]);
//     //     return $payment;

//     // }


//     public function confirmPayment($data)
//     {
//         // 1. Kiểm tra đơn hàng đã thanh toán chưa
//         $existingPayment = Payment::where('order_id', $data['order_id'])->first();
//         if ($existingPayment) {
//             return $existingPayment; // Nếu đã thanh toán thì trả về luôn, không tạo lại
//         }

//         // 2. Tạo payment mới
//         $payment = $this->paymentRepo->create([
//             'order_id' => $data['order_id'],
//             'method' => $data['method'],
//             'status' => 'Completed',
//             'payment_date' => Carbon::now(),
//         ]);

//         // 3. Chỉ tạo OrderDetail nếu chưa có
//         foreach ($data['items'] as $item) {
//         $existingOrderDetail = OrderDetail::where('order_id', $data['order_id'])
//             ->where('product_id', $item['product_id'])
//             ->first();

//         if ($existingOrderDetail) {
//             // Nếu màu đã thay đổi hoặc số lượng hoặc giá thay đổi → cập nhật lại
//             if (
//                 $existingOrderDetail->color !== ($item['color'] ?? 'black') ||
//                 $existingOrderDetail->quantity !== $item['quantity'] ||
//                 $existingOrderDetail->unit_price != $item['unit_price']
//             ) {
//                 $existingOrderDetail->update([
//                     'color' => $item['color'] ?? 'black',
//                     'quantity' => $item['quantity'],
//                     'unit_price' => $item['unit_price'],
//                 ]);
//             }
//         } else {
//             // Nếu chưa có thì tạo mới
//             OrderDetail::create([
//                 'order_id' => $data['order_id'],
//                 'product_id' => $item['product_id'],
//                 'quantity' => $item['quantity'],
//                 'unit_price' => $item['unit_price'],
//                 'color' => $item['color'] ?? 'black',
//             ]);
//         }
//     }


//         return $payment;
//     }
// }




















// // // namespace App\Services;

// // // use App\Models\Order;
// // // use App\Models\Payment;
// // // use App\Repositories\PaymentRepository;
// // // use Illuminate\Support\Carbon;

// // // class PaymentService
// // // {
// // //     protected $paymentRepo;
// // //     public function __construct(PaymentRepository $paymentRepo)
// // //     {
// // //         $this->paymentRepo = $paymentRepo;
// // //     }
// // //     public function createPayment(array $data)
// // //     {
// // //         $paymentMethod = $data['payment_method'];

// // //         $status = ($paymentMethod === 'cash') ? 'processing' : 'Completed';

// // //         $order = Order::findOrFail($data['order_id']);
// // //         $order->status = $status;
// // //         $order->save();
// // //         $payment = Payment::create([
// // //             'order_id' => $order->id,
// // //             'method' => $paymentMethod,
// // //             'amount' => $data['amount'],
// // //             'status' => $status,
// // //         ]);

// // //         return $payment;
// // //     }
// // //     public function confirmPayment($data)
// // //     {
// // //         $payment = $this->paymentRepo->create([
// // //             'order_id' => $data['order_id'],
// // //             'method' => $data['method'],
// // //             'status' => 'Completed',
// // //             'payment_date' => Carbon::now(),
// // //         ]);
// // //         return $payment;

// // //     }
// // // }

// // namespace App\Services;

// // use App\Models\Order;
// // use App\Models\OrderDetail;
// // use App\Models\Payment;
// // use App\Repositories\PaymentRepository;
// // use Illuminate\Support\Carbon;

// // class PaymentService
// // {
// //     protected $paymentRepo;
// //     public function __construct(PaymentRepository $paymentRepo)
// //     {
// //         $this->paymentRepo = $paymentRepo;
// //     }
// //     public function createPayment(array $data)
// //     {
// //         $paymentMethod = $data['payment_method'];

// //         $status = ($paymentMethod === 'cash') ? 'processing' : 'Completed';

// //         $order = Order::findOrFail($data['order_id']);
// //         $order->status = $status;
// //         $order->save();
// //         $payment = Payment::create([
// //             'order_id' => $order->id,
// //             'method' => $paymentMethod,
// //             'amount' => $data['amount'],
// //             'status' => $status,
// //         ]);

// //         return $payment;
// //     }
// //     // public function confirmPayment($data)
// //     // {
// //     //     $payment = $this->paymentRepo->create([
// //     //         'order_id' => $data['order_id'],
// //     //         'method' => $data['method'],
// //     //         'status' => 'Completed',
// //     //         'payment_date' => Carbon::now(),
// //     //     ]);
// //     //     return $payment;

// //     // }


// //     public function confirmPayment($data)
// //     {
// //         // get order id 
// //         $orderIdFull = $data['orderId'];
// //         $system_order_id = explode('_', $orderIdFull)[1] ?? 0; // Giả sử định dạng là MOMO_{order_id}_{timestamp}
// //         // 1. Kiểm tra đơn hàng đã thanh toán chưa
// //         $existingPayment = Payment::where('order_id', $system_order_id)->first();
// //         if ($existingPayment) {
// //             return $existingPayment; // Nếu đã thanh toán thì trả về luôn, không tạo lại
// //         }

// //         // 2. Tạo payment mới 
// //         $payment = $this->paymentRepo->create([
// //             'order_id' => $system_order_id,
// //             'method' => "method", // xem lại bên data trả về có biên methosd ko?
// //             'status' => 'Completed',
// //             'payment_date' => Carbon::now(),
// //         ]);

// //         // 3. Chỉ tạo OrderDetail nếu chưa có
// //         foreach ($data['items'] as $item) {
// //         $existingOrderDetail = OrderDetail::where('order_id', $system_order_id)
// //             ->where('product_id', $item['product_id'])
// //             ->first();

// //         if ($existingOrderDetail) {
// //             // Nếu màu đã thay đổi hoặc số lượng hoặc giá thay đổi → cập nhật lại
// //             if (
// //                 $existingOrderDetail->color !== ($item['color'] ?? 'black') ||
// //                 $existingOrderDetail->quantity !== $item['quantity'] ||
// //                 $existingOrderDetail->unit_price != $item['unit_price']
// //             ) {
// //                 $existingOrderDetail->update([
// //                     'color' => $item['color'] ?? 'black',
// //                     'quantity' => $item['quantity'],
// //                     'unit_price' => $item['unit_price'],
// //                 ]);
// //             }
// //         } else {
// //             // Nếu chưa có thì tạo mới
// //             OrderDetail::create([
// //                 'order_id' => $system_order_id,
// //                 'product_id' => $item['product_id'],
// //                 'quantity' => $item['quantity'],
// //                 'unit_price' => $item['unit_price'],
// //                 'color' => $item['color'] ?? 'black',
// //             ]);
// //         }
// //     }


// //         return $payment;
// //     }
// // }


namespace App\Services;


use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Support\Carbon;


class PaymentService
{
    /**
     * Tạo Payment trước khi redirect sang MoMo
     */
    public function createPayment(array $data)
    {
        $orderIdFull = $data['order_id']; // ví dụ: "MOMO_77_1756377641"
        $systemOrderId = explode('_', $orderIdFull)[1] ?? $orderIdFull;


        if (!$systemOrderId) {
            throw new \Exception("Invalid order ID format");
        }


        $paymentMethod = $data['payment_method'] ?? $data['method'] ?? 'momo';


        // 1️⃣ Tạo payment pending
        $payment = Payment::updateOrCreate(
            ['order_id' => $systemOrderId],
            [
                'momo_order_id' => $orderIdFull,
                'method' => $paymentMethod,
                'amount' => $data['amount'],
                'status' => 'pending', // chưa thanh toán xong
                'payment_date' => Carbon::now(),
            ]
        );


        // 2️⃣ Cập nhật OrderDetail nếu có items
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $detail = OrderDetail::updateOrCreate(
                    ['order_id' => $systemOrderId, 'product_id' => $item['product_id']],
                    [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'color' => $item['color'] ?? 'black',
                    ]
                );
            }
        }


        \Log::info("Payment created for MoMo", [
            'order_id' => $systemOrderId,
            'momo_order_id' => $orderIdFull,
            'payment_id' => $payment->id,
            'method' => $paymentMethod,
            'amount' => $data['amount'],
        ]);


        return $payment;
    }


    /**
     * Callback từ MoMo: update payment status
     */
    public function updatePaymentStatus($momoOrderId, $resultCode)
    {
        $systemOrderId = explode('_', $momoOrderId)[1] ?? null;
        if (!$systemOrderId) {
            throw new \Exception("Invalid MoMo order ID");
        }


        $payment = Payment::where('order_id', $systemOrderId)
                          ->where('momo_order_id', $momoOrderId)
                          ->firstOrFail();


        $status = ($resultCode == 0) ? 'success' : 'failed';
        $payment->update(['status' => $status]);


        // Update order status
        $order = Order::find($systemOrderId);
        if ($order) {
            $order->status = $status;
            $order->save();
        }


        \Log::info("Payment status updated", [
            'order_id' => $systemOrderId,
            'momo_order_id' => $momoOrderId,
            'status' => $status
        ]);


        return $payment;
    }
}
