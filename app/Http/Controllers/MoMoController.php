<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MoMoController extends Controller
 {protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
            Log::error("❌ MoMo CURL ERROR: $error");
        }

        curl_close($ch);
        return $result;
    }


    public function momoPayment(Request $request)
    {
        Log::info('🎯 Data received from React:', $request->all());

        $amount = $request->input('amount');
        $orderId = $request->input('order_id');
        $momoOrderId = "MOMO_" . $orderId . "_" . time();

        if (!$amount || !$orderId) {
            return response()->json([
                'message' => 'The request is missing the amount or order_id!'
            ], 400);
        }

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = 'MOMOBKUN20180529';
        $accessKey   = 'klm05TvNBzhg7h7j';
        $secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

        $orderInfo   = "Pay for the order #$orderId";


        $redirectUrl = "https://front-end-tech-store-henna.vercel.app/user/payment_confirmation"; 
        $ipnUrl      = "http://localhost:8000/api/momo/ipn"; 

        $extraData   = "";
        $requestId   = time() . "";
        $requestType = "payWithATM";

        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$momoOrderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "MoMoTest",
            'storeId'     => "MomoTestStore",
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $momoOrderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature
        ];

        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        Log::info('📥 Response from MoMo:', $jsonResult);

        if (!isset($jsonResult['payUrl'])) {
            return response()->json([
                'payUrl' => null,
                'message' => 'MoMo responded with an error or invalid data.'
            ], 400);
        }

        return response()->json([
            'payUrl' => $jsonResult['payUrl'],
            'message' => 'Successfully generated MoMo payment link.'
        ]);
    }
    public function momoIpn(Request $request)
    {
        Log::info('📩 MoMo IPN callback received:', $request->all());

        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $params = $request->all();

        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $params['amount'] .
            "&extraData=" . $params['extraData'] .
            "&message=" . $params['message'] .
            "&orderId=" . $params['orderId'] .
            "&orderInfo=" . $params['orderInfo'] .
            "&orderType=" . $params['orderType'] .
            "&partnerCode=" . $params['partnerCode'] .
            "&payType=" . $params['payType'] .
            "&requestId=" . $params['requestId'] .
            "&responseTime=" . $params['responseTime'] .
            "&resultCode=" . $params['resultCode'] .
            "&transId=" . $params['transId'];

        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        if ($signature !== $params['signature']) {
            Log::error("❌ Invalid signature from MoMo");
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if ($params['resultCode'] == 0) {
            Log::info("✅ Order {$params['orderId']} thanh toán thành công!");
        } else {
            Log::warning("⚠️ Order {$params['orderId']} thất bại với mã {$params['resultCode']}");
        }

        return response()->json(['message' => 'IPN received'], 200);
    }

    public function handleReturn(Request $request)
    {
        Log::info('MoMo Return Params:', $request->all());

        $orderId = $request->input('orderId');
        $resultCode = $request->input('resultCode'); // 0 = success
        $amount = $request->input('amount');

        if (!$orderId || $resultCode === null) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Missing parameters'
            ]);
        }

        // Kiểm tra thành công từ MoMo
        if ($resultCode == 0) {
            // Cập nhật trạng thái đơn hàng trong DB
            $this->paymentService->confirmPayment([
                'order_id' => $orderId,
                'method' => 'Momo'
            ]);

            return response()->json([
                'status' => 'success',
                'orderId' => $orderId
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'orderId' => $orderId
            ]);
        }
    }
}



// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;

// class MoMoController extends Controller
// {
//     private function execPostRequest($url, $data)
//     {
//         $ch = curl_init($url);
//         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($ch, CURLOPT_HTTPHEADER, [
//             'Content-Type: application/json',
//             'Content-Length: ' . strlen($data)
//         ]);
//         curl_setopt($ch, CURLOPT_TIMEOUT, 10);
//         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

//         $result = curl_exec($ch);

//         if ($result === false) {
//             $error = curl_error($ch);
//             Log::error("❌ MoMo CURL ERROR: $error");
//         }

//         curl_close($ch);
//         return $result;
//     }

//     // ✅ B1: Tạo link thanh toán
//     public function momoPayment(Request $request)
//     {
//         Log::info('🎯 Data received from React:', $request->all());

//         $amount  = $request->input('amount');
//         $orderId = $request->input('order_id');
//         $momoOrderId = "MOMO_" . $orderId . "_" . time();

//         if (!$amount || !$orderId) {
//             return response()->json([
//                 'message' => 'The request is missing the amount or order_id!'
//             ], 400);
//         }

//         $endpoint    = "https://test-payment.momo.vn/v2/gateway/api/create";
//         $partnerCode = 'MOMOBKUN20180529';
//         $accessKey   = 'klm05TvNBzhg7h7j';
//         $secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

//         $orderInfo   = "Pay for the order #$orderId";

//         // ⚡ redirectUrl trỏ về 1 route của backend (momoReturn)
//         $redirectUrl = "http://localhost:8000/api/momo/return";
//         $ipnUrl      = "http://localhost:8000/api/momo/ipn"; 

//         $extraData   = "";
//         $requestId   = time() . "";
//         $requestType = "payWithATM";

//         $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$momoOrderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
//         $signature = hash_hmac("sha256", $rawHash, $secretKey);

//         $data = [
//             'partnerCode' => $partnerCode,
//             'partnerName' => "MoMoTest",
//             'storeId'     => "MomoTestStore",
//             'requestId'   => $requestId,
//             'amount'      => $amount,
//             'orderId'     => $momoOrderId,
//             'orderInfo'   => $orderInfo,
//             'redirectUrl' => $redirectUrl,
//             'ipnUrl'      => $ipnUrl,
//             'lang'        => 'vi',
//             'extraData'   => $extraData,
//             'requestType' => $requestType,
//             'signature'   => $signature
//         ];

//         $result = $this->execPostRequest($endpoint, json_encode($data));
//         $jsonResult = json_decode($result, true);

//         Log::info('📥 Response from MoMo:', $jsonResult);

//         if (!isset($jsonResult['payUrl'])) {
//             return response()->json([
//                 'payUrl' => null,
//                 'message' => 'MoMo responded with an error or invalid data.'
//             ], 400);
//         }

//         return response()->json([
//             'payUrl' => $jsonResult['payUrl'],
//             'message' => 'Successfully generated MoMo payment link.'
//         ]);
//     }

//     // ✅ B2: MoMo gọi redirectUrl (người dùng quay lại sau thanh toán)
//     public function momoReturn(Request $request)
//     {
//         Log::info('🔙 MoMo return URL with query:', $request->all());

//         // Nếu muốn, bạn có thể check resultCode ở đây
//         if ($request->input('resultCode') == 0) {
//             // Thành công
//             return redirect()->away("https://front-end-tech-store-henna.vercel.app/user/payment_confirmation");
//         } else {
//             // Thất bại
//             return redirect()->away("https://front-end-tech-store-henna.vercel.app/user/payment_confirmation?status=fail");
//         }
//     }

//     // ✅ B3: MoMo gọi IPN (server-to-server)
//     public function momoIpn(Request $request)
//     {
//         Log::info('📩 MoMo IPN callback received:', $request->all());

//         $accessKey = 'klm05TvNBzhg7h7j';
//         $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
//         $params = $request->all();

//         $rawHash = "accessKey=" . $accessKey .
//             "&amount=" . $params['amount'] .
//             "&extraData=" . $params['extraData'] .
//             "&message=" . $params['message'] .
//             "&orderId=" . $params['orderId'] .
//             "&orderInfo=" . $params['orderInfo'] .
//             "&orderType=" . $params['orderType'] .
//             "&partnerCode=" . $params['partnerCode'] .
//             "&payType=" . $params['payType'] .
//             "&requestId=" . $params['requestId'] .
//             "&responseTime=" . $params['responseTime'] .
//             "&resultCode=" . $params['resultCode'] .
//             "&transId=" . $params['transId'];

//         $signature = hash_hmac("sha256", $rawHash, $secretKey);

//         if ($signature !== $params['signature']) {
//             Log::error("❌ Invalid signature from MoMo");
//             return response()->json(['message' => 'Invalid signature'], 400);
//         }

//         if ($params['resultCode'] == 0) {
//             Log::info("✅ Order {$params['orderId']} thanh toán thành công!");
//             // 👉 update DB: order paid
//         } else {
//             Log::warning("⚠️ Order {$params['orderId']} thất bại với mã {$params['resultCode']}");
//             // 👉 update DB: order failed
//         }

//         return response()->json(['message' => 'IPN received'], 200);
//     }
// }
