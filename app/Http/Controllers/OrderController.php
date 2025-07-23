<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // B1: Tạo đơn hàng sơ khởi
    public function createOrder(Request $request)
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $order = $this->orderService->createOrder($user->id, $request->all());

        return response()->json(['message' => 'Order created', 'order_id' => $order->id]);
    }

    // B2: Cập nhật thông tin người mua
    public function updateOrderInfo(Request $request, $orderId)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
        ]);

        $this->orderService->updateOrderInfo($orderId, $validated);

        return response()->json(['message' => 'Customer info updated successfully']);
    }

    public function getConfirmationDetails()
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }


        $data = $this->orderService->getOrderConfirmationDetails($user->id);
        return response()->json($data);
    }

    public function confirmPayment()
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $this->orderService->confirmOrderAndSendMail($user->id);
        return response()->json(['message' => 'Order confirmed and email sent successfully.']);
    }
    public function create(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $userId = Auth::guard('user')->id(); // Lấy user từ token

            if (!$userId) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $total = collect($request->products)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            // $order = Order::create([
            //     'user_id' => $userId,
            //     'order_date' => now(),
            //     'status' => 'pending',
            //     'shipping_option' => "free",
            //     'total_amount' => $total,
            //     'coupon_code' => null,
            //     'discount' => 0,
            // ]);

            $order = Order::create([
                'user_id' => $userId,
                'order_date' => now(),
                'status' => 'pending',
                'shipping_option' => "free",
                'total_amount' => $total,
                'coupon_code' => null,
                'discount' => 0,
            ]);

            foreach ($request->products as $product) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price'],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => 'cash', // hoặc từ $request->method nếu cần
                'status' => 'pending',
                'payment_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully.',
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($orderId)
    {
        $order = Order::with(['orderDetails.product.images'])->findOrFail($orderId);

        return response()->json([
            'order' => [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'order_date' => $order->order_date,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'order_details' => $order->orderDetails->map(function ($detail) {
                    return [
                        'product_id' => $detail->product->id,
                        'name' => $detail->product->name,
                        'unit_price' => $detail->unit_price,
                        'quantity' => $detail->quantity,
                        'image' => $detail->product->images->first()->image_url ?? null,
                    ];
                }),
            ]
        ]);
    }
    public function getOrderHistoryByDate(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date');

        $orders = $this->orderService->getOrderHistoryByDate($user->id, $date);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function deleteHistory(Request $request)
    {
        $orderId = $request->input('order_id');

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->orderDetails()->delete();
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

}
