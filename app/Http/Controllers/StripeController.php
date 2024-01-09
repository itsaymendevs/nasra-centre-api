<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StripePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\StripeClient;

class StripeController extends Controller
{




    public function makePayment(Request $request, $orderNumber)
    {


        // ::root
        // $order = Order::where('orderNumber', $orderNumber)->first();
        // $amount = doubleval($order->orderTotalPrice);
        // $currency = $order->country->currency;

        // ::root - fake
        $amount = doubleval('15200');
        $currency = 'GBP';



        // 1: init stripe
        $stripeSecretKey = env('STRIPE_SECRET');
        $stripe = new StripeClient(env('STRIPE_SECRET'));


        // 1.2: init intent
        $paymentIntent = $stripe->paymentIntents->create([

            'amount' => doubleval($amount) * 100,
            'currency' => $currency,
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);




        // 2: get clientSecret
        $clientSecret = $paymentIntent->client_secret;
        $publicKey = env('STRIPE_KEY');
        $secretKey = env('STRIPE_SECRET');


        return view('stripe.index', compact('clientSecret', 'publicKey', 'secretKey', 'amount', 'currency', 'orderNumber'));


    } // end function






    // ---------------------------------------------------------------------
    // ---------------------------------------------------------------------






    public function confirmPayment(Request $request, $orderNumber)
    {



        // :: check params
        // if (! empty($request->payment_intent) && ! empty($request->payment_intent_client_secret)) {


        //     // 1: updateOrder
        //     $order = Order::where('orderNumber', $orderNumber)->first();

        //     $order->isPaymentDone = true;
        //     $order->isConfirmed = true;
        //     $order->paymentDateTime = Carbon::now()->addHours(2);
        //     $order->save();



        //     // 2: save StripePayment
        //     $stripePayment = new StripePayment();

        //     $stripePayment->orderId = $order->id;
        //     $stripePayment->paymentIntent = $request->payment_intent;
        //     $stripePayment->clientSecret = $request->payment_intent_client_secret;
        //     $stripePayment->save();


        // } // end if




        return redirect()->route('stripe.success');


    } // end function










    // ---------------------------------------------------------------------
    // ---------------------------------------------------------------------






    public function success(Request $request)
    {


        return view('stripe.success');


    } // end function





} // end controller
