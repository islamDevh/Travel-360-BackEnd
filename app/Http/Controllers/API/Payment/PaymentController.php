<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Paytabscom\Laravel_paytabs\Facades\Paypage;

class PaymentController extends BaseController
{
    public function createPayment(Request $request)
    {
        $pay = Paypage::sendPaymentCode('creditcard, mada, stcpay, applepay, tamara, tabby')
            ->sendTransaction('sale', 'ecom')
            ->sendCustomerDetails(
                $request->input('name'),
                $request->input('email'),
                $request->input('phone'),
                $request->input('city'),
                $request->input('city'),
                $request->input('state'),
                $request->input('country'),
                null,
                null
            )
            ->sendCart('Order_101', $request->input('amount'), 'Order Description')
            ->sendHideShipping(true)
            ->sendURLs(
                route('payment.callback'),
                route('payment.callback')
            )
            ->create_pay_page(null, null, null, null, null, null, null);

        return $pay;
    }

    public function paymentCallback(Request $request)
    {
        Log::channel('PayTabs')->info('Callback RAW: ' . $request->getContent());
        Log::channel('PayTabs')->info('Callback ARRAY: ' . json_encode($request->all()));

        return $this->successResponse($request->all());
    }

    public function paymentReturn(Request $request)
    {
        Log::channel('PayTabs')->info('Payment Return Data: ', $request->all());

        return $this->successResponse($request->all());
    }
}
