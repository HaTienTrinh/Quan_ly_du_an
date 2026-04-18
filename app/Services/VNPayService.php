<?php

namespace App\Services;

use Illuminate\Http\Request;

class VNPayService
{
    public function createPaymentUrl(int $orderId, int $amount, string $orderInfo): string
    {
        $params = [
            'vnp_Version'   => '2.1.0',
            'vnp_Command'   => 'pay',
            'vnp_TmnCode'   => config('vnpay.tmn_code'),
            'vnp_Amount'    => $amount * 100,
            'vnp_CurrCode'  => 'VND',
            'vnp_TxnRef'    => $orderId . '_' . time(),
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => 'other',
            'vnp_Locale'    => 'vn',
            'vnp_ReturnUrl' => config('vnpay.return_url'),
            'vnp_IpAddr'    => request()->ip(),
            'vnp_CreateDate'=> now()->format('YmdHis'),
            'vnp_BankCode'  => 'VNPAYQR',
        ];

        ksort($params);
        $query = http_build_query($params);
        $hmac  = hash_hmac('sha512', $query, config('vnpay.hash_secret'));

        return config('vnpay.url') . '?' . $query . '&vnp_SecureHash=' . $hmac;
    }

    public function verifyReturn(Request $request): bool
    {
        $secureHash = $request->input('vnp_SecureHash');
        $params     = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);

        ksort($params);
        $hmac = hash_hmac('sha512', http_build_query($params), config('vnpay.hash_secret'));

        return hash_equals($hmac, $secureHash ?? '');
    }

    public function isSuccess(Request $request): bool
    {
        return $request->input('vnp_ResponseCode') === '00';
    }

    public function getOrderId(Request $request): int
    {
        return (int) explode('_', $request->input('vnp_TxnRef', '0_0'))[0];
    }
}
