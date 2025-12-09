<?php

namespace App\Http\Controllers\API\V1\Payment;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Paytabscom\Laravel_paytabs\Facades\Paypage;
use Illuminate\Support\Facades\Log;


class PaymentController extends BaseController
{
    /**
     * Step 1: Create payment page for mobile app
     */
    public function createPayment(Request $request)
    {
        try {
            // receive order data from the request
            $order_id    = "Order_101";
            $description = "Umra Service";
            $amount      = $request->input('amount');
            $name        = $request->input('name');
            $email       = $request->input('email');
            $phone       = $request->input('phone');
            $address     = $request->input('address');
            $city        = $request->input('city');
            $state       = $request->input('state');
            $country     = $request->input('country');
            $zip         = $request->input('zip');
            $returnUrl   = $request->input('return_url');
            $callbackUrl = $request->input('callback_url');

            $pay = paypage::sendPaymentCode("creditcard, mada, stcpay, applepay, tamara, tabby") // required
                ->sendTransaction('sale', 'ecom')
                ->sendCustomerDetails($name, $email, $phone, $city, $city, $state, $country, null, null)
                ->sendCart("Order_101", 1000.00, 'Order Description')
                ->sendHideShipping(true)
                ->sendURLs(
                    "http://localhost:8000/api/v1/payment/callback",
                    "http://localhost:8000/api/v1/payment/callback"
                )
                ->create_pay_page(null, null, null, null, null, null, null);

            return $pay;
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * Step 2: Handle Callback (IPN Notification from PayTabs)
     */
    public function paymentCallback(Request $request)
    {

        $payload = $request->getContent();

        Log::channel('PayTabs')->info('Callback RAW: ' . $request->getContent());
        Log::channel('PayTabs')->info('Callback ARRAY: ' . json_encode($request->all()));

        $tranRef = $request->input('tran_ref');
        $cartId  = $request->input('cart_id');
        $status  = $request->input('resp_status');

        return response()->json(['message' => 'Callback success', 'data' => $request->all()]);
    }


    /**
     * Step 3: Return URL after user completes payment
     */
    public function paymentReturn(Request $request)
    {
        Log::channel('PayTabs')->info('Payment Return Data: ', $request->all());

        // You can redirect user or return JSON to mobile app
        return response()->json([
            'message' => 'Payment completed',
            'data'    => $request->all(),
        ]);
    }
}
