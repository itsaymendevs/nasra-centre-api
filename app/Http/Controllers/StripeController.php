<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller {




    public function create(Request $request) {


        // 1: init stripe
        $stripeSecretKey = env('STRIPE_SECRET');
        $stripe = new StripeClient(env('STRIPE_SECRET'));


        // 1.2: init intent
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => 100 * 100,
            'currency' => 'EGP',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);




        // 2: get clientSecret
        $clientSecret = $paymentIntent->client_secret;
        $publicKey = env('STRIPE_KEY');
        $secretKey = env('STRIPE_SECRET');


        return view('stripe.index', compact('clientSecret', 'publicKey', 'secretKey'));


    } // end function

} // end controller
