<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder($userId, $data)
    {
        return $this->orderRepository->create([
            'user_id' => $userId,
            'order_date' => now(),
            'status' => 'pending',
            'shipping_option' => $data['shipping_option'] ?? 'default',
            'total_amount' => $data['total_amount']
        ]);
    }

    public function updateOrderInfo($orderId, $data)
    {
        return $this->orderRepository->update($orderId, [
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'province' => $data['province'],
            'district' => $data['district'],
            'ward' => $data['ward'],
        ]);
    }

    public function getOrderConfirmationDetails($userId)
    {
        return $this->orderRepository->getLatestOrderByUser($userId);
    }

    public function confirmOrderAndSendMail($userId)
    {
        $orderData = $this->orderRepository->getLatestOrderByUser($userId);

        $email = $orderData['customer']['email'];
        $name = $orderData['customer']['fullname'];
        $orderCode = $orderData['order_code'];

        // Tạo PDF từ view 'invoice'
        $pdf = Pdf::loadView('invoice', [
            'orderCode' => $orderCode,
            'customer' => $orderData['customer'],
            'items' => $orderData['items'],
            'summary' => $orderData['summary']
        ]);

        $filename = 'Invoice_' . Str::slug($orderCode) . '.pdf';
        $pdfPath = storage_path('app/public/' . $filename);
        $pdf->save($pdfPath);

        // Tạo nội dung HTML email
        $body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Order Confirmation</title>
            </head>
            <body>
                <h3>Hello ' . $name . ',</h3>

                <p>Thank you very much for your recent order with us!</p>

                <p>We’re excited to let you know that your order has been successfully processed.</p>

                <p><strong>Order Code:</strong> ' . $orderCode . '</p>

                <p>Please find your invoice attached to this email for your records. It includes the full details of your purchase.</p>

                <p>If you have any questions or concerns regarding your order, feel free to reach out to our support team at any time. We are always happy to help!</p>

                <p>Once again, thank you for choosing our store. We truly appreciate your business and hope to serve you again in the future.</p>

                <p>Best regards,<br>
                The ITDragons Team</p>
            </body>
            </html>
        ';

        // Gửi mail dùng MailerService
        try {
            $mailer = app(\App\Services\MailerService::class);
            $mailer->send($email, 'Order Confirmation - ' . $orderCode, $body, $pdfPath);
        } catch (\Exception $e) {
            Log::error('Send mail failed: ' . $e->getMessage());
        }

        // Xoá file PDF sau khi gửi
        unlink($pdfPath);
    }
    public function getOrderHistoryByDate($userId, $date)
    {
        return Order::with([
            'orderDetails.product.images'
        ])
            ->where('user_id', $userId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('order_date', $date);
            })
            ->orderBy('order_date', 'desc')
            ->get();
    }
    public function deleteOrderHistory(array $data)
    {
        return $this->orderRepository->deleteHistory($data['user_id'], $data['product_id'], $data['date']);
    }
    public function deleteSingleOrderDetail($orderDetailId)
{
    return \App\Models\OrderDetail::where('id', $orderDetailId)->delete();
}

    public function getAllOrders()
    {
        return $this->orderRepository->fetchOrdersWithUser();
    }

    public function deleteOrder($id)
    {
        return $this->orderRepository->deleteOrderById($id);
    }
}
