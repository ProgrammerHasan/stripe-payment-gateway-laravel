<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use Session;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return view('stripe');
    }

    public function create_checkout_session(Request $request) {
        $amount = 0;
        $order = Order::findOrFail($request->get('order_id'));
        $amount = round($order->total * 100);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                    'currency' => 'BDT',
                    'product_data' => [
                        'name' => "Payment"
                    ],
                    'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                    ]
                ],
            'mode' => 'payment',
            'success_url' => route('payment.stripe.success'),
            'cancel_url' => route('payment.stripe.cancel'),
        ]);

        return response()->json(['id' => $session->id, 'status' => 200]);
    }

    public function success() {
        try{
            $payment = ["status" => "Success"];

            $payment_type = Session::get('payment_type');
            // and then execute your order code/logic for order status

        }
        catch (\Exception $e) {
            // Payment failed
    	    return redirect()->route('checkout');
        }
    }

    public function cancel(Request $request){
        // Payment failed
        return redirect()->route('checkout');
    }
}
