<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VnpayController extends Controller
{
    public function createPayment(Request $request)
    {
        try {
            $vnp_TmnCode = "EAH6WTTQ";
            $vnp_HashSecret = "KUR2TCT3VWLBNUKPPRGYC2UW91M2FJJK";
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = "http://localhost:3000/user/payment_confirmation";

            $orderId = $request->input('order_id');
            $amount = $request->input('amount');

            if (!$amount || !$orderId) {
                Log::error('VNPAY createPayment missing parameters', ['order_id' => $orderId, 'amount' => $amount]);
                return response()->json(['message' => 'Thiếu order_id hoặc amount'], 400);
            }

            $vnp_TxnRef = $orderId;
            $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $orderId;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $amount * 100;
            $vnp_Locale = 'vn';
            $vnp_BankCode = '';
            $vnp_IpAddr = $request->ip();

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => now()->format('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            ];

            ksort($inputData);
            $hashdata = '';
            $query = '';
            foreach ($inputData as $key => $value) {
                $hashdata .= urlencode($key) . '=' . urlencode($value) . '&';
                $query .= urlencode($key) . '=' . urlencode($value) . '&';
            }

            $hashdata = rtrim($hashdata, '&');
            $query = rtrim($query, '&');

            $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

            Log::info('VNPAY createPayment success', ['url' => $vnp_Url, 'inputData' => $inputData]);

            return response()->json(['url' => $vnp_Url]);

        } catch (\Exception $e) {
            Log::error('VNPAY createPayment error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Lỗi khi tạo liên kết thanh toán'], 500);
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            $inputData = $request->all();
            $vnp_HashSecret = "KUR2TCT3VWLBNUKPPRGYC2UW91M2FJJK";

            Log::info('VNPay return data:', $inputData);

            $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
            if (!$vnp_SecureHash) {
                Log::error('VNPay return missing vnp_SecureHash');
                return response()->json(['message' => 'Thiếu vnp_SecureHash'], 400);
            }

            unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

            ksort($inputData);
            $hashData = '';
            $i = 0;
            foreach ($inputData as $key => $value) {
                $hashData .= ($i ? '&' : '') . urlencode($key) . '=' . urlencode($value);
                $i++;
            }

            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            if ($secureHash === $vnp_SecureHash) {
                if (($request->vnp_ResponseCode ?? '') === '00') {
                    Log::info('VNPay Payment Success', $inputData);
                    return response()->json(['message' => 'Giao dịch thành công']);
                } else {
                    Log::warning('VNPay Payment Failed', $inputData);
                    return response()->json(['message' => 'Giao dịch không thành công']);
                }
            } else {
                Log::error('VNPay Invalid Hash', ['calculated' => $secureHash, 'received' => $vnp_SecureHash]);
                return response()->json(['message' => 'Chữ ký không hợp lệ'], 400);
            }
        } catch (\Exception $e) {
            Log::error('VNPay Return Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Lỗi xác thực giao dịch'], 500);
        }
    }
}
