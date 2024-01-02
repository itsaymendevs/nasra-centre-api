@extends('stripe.layout')


@section('pageTitle')
Payment Details
@endsection



@section('content')


<style>
    .p-CardCvcIcons-svg {
        fill: white !important
    }
</style>

<div class="container-fluid bg-cover bg--main px-0">
    <div class="row g-0 min-vh-100">
        <div class="col-12 align-self-center content--col px-0" id="content--col">
            <section id="content--main" class="d-block">
                <div>

                    {{-- row --}}
                    <form id="payment-form" class="row justify-content-center align-items-center" method="post">
                        @csrf

                        {{-- logo --}}
                        <div class="col-12 col-lg-5">
                            <img data-aos="fade-left" data-aos-duration="600" data-aos-delay="150" class="w-100 of-contain" src="{{asset('assets/img/Logo/logo.png')}}" style="height: 180px;">
                        </div>

                        {{-- title / hr --}}
                        {{-- <div class="col-12 mb-5">
                            <h4 class="text-center font--cour mb-0" data-aos="fade-down" data-aos-duration="600" data-aos-delay="150">Payment Details</h4>
                            <div class="d-flex align-items-center justify-content-center text-center">
                                <hr class="login--hr mb-0">
                                <hr class="login--hr mb-0">
                                <hr class="login--hr mb-0">
                                <hr class="login--hr mb-0">
                                <hr class="login--hr mb-0">
                                <hr class="login--hr black mb-0">
                                <hr class="login--hr black mb-0">
                                <hr class="login--hr black mb-0">
                            </div>
                        </div> --}}


                        {{-- content --}}
                        <div class="col-11 col-lg-5" data-aos="fade-right" data-aos-duration="600" data-aos-delay="150">

                            {{-- <label class="form-label form--label">Credit / Debit Card</label>
                            <input type="text" class="form--input mb-4 w-100"> --}}

                            <div id="payment-element">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>


                            <p class="mt-3 fs-12 text-danger d-flex align-baseline justify-content-center d-none" style="width: 90%;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-exclamation-circle-fill error--icon me-2">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"></path>
                                </svg><span id="payment-message"></span>
                            </p>


                            {{-- submit --}}
                            <div class="text-center d-block mt-4 pt-2">
                                <button id='submit' class="btn btn--theme btn--submit btn--sm rounded-1 fw-semibold" type="submit">
                                    Pay 20,500
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- empty --}}
                <div></div>

            </section>
        </div>
    </div>
</div>


@endsection
{{-- end section --}}









{{-- scripts --}}
@section('scripts')
<script src="https://js.stripe.com/v3/"></script>




<script>

    const publicKey = '{{ $publicKey }}';
    const secretKey = '{{ $secretKey }}';
    const clientSecret = '{{ $clientSecret }}';
    console.log(publicKey);

    // 1: init stripe
    const stripe = Stripe(publicKey);

    // 1.2: Items list
    const items = [{ id: "xl-tshirt" }];



    // 1.3: apperance init
    const appearance = {

        rules: {
            '.Input': {
                width: '100%',
                height: '52px',
                border: '1px solid #dcdcdc',
                borderRadius: '4px',
                padding: '21px 18px',
                fontSize: '14px',
                backgroundColor: '#fff',
                color: '#000',
                fontWeight: '500',
                letterSpacing: '1.2px',
                fontFamily: 'Ideal Sans, system-ui, sans-serif',
            },
            '.Input:focus': {
                boxShadow: 'none',
                border: '1px solid #44A08D',
            },

            '.Input::placeholder': {
                color: '#000',
            },
            '.Label': {
                fontSize: '14px',
                color: '#000',
                paddingLeft: '4px',
                marginTop: '12px',
                marginBottom: '8px',
                letterSpacing: '1.2px',
                fontWeight: '400',
                fontFamily: 'Ideal Sans, system-ui, sans-serif',
            },
            '.Error': {
                fontSize: '13px',
                paddingLeft: '4px',
                marginTop: '8px',
                letterSpacing: '1.2px',
                fontWeight: '500',
                fontFamily: 'Ideal Sans, system-ui, sans-serif',
            },
        }
    };


    // 1.4: init elements / call methods
    let elements;
    initialize();
    checkStatus();



    // 1.6: submit event
    document.querySelector("#payment-form").addEventListener("submit", handleSubmit);









    // 2: create payment-elements
    async function initialize() {
        elements = stripe.elements({clientSecret, appearance});

        const paymentElementOptions = {
            layout: "accordion",
        };

        const paymentElement = elements.create("payment", paymentElementOptions);
        paymentElement.mount("#payment-element");
    } // end function






    // 3: handle submit
    async function handleSubmit(e) {
        e.preventDefault();
        setLoading(true);

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: "http://localhost:8000/",
            },
        });

        // This point will only be reached if there is an immediate error when
        if (error.type === "card_error" || error.type === "validation_error") {
            showMessage(error.message);
        } else {
            showMessage("An unexpected error occurred.");
        }

        setLoading(false);
    }

    // Fetches the payment intent status after payment submission
    async function checkStatus() {
        const clientSecret = new URLSearchParams(window.location.search).get(
            "payment_intent_client_secret"
        );

        if (!clientSecret) {
            return;
        }

        const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

        switch (paymentIntent.status) {
            case "succeeded":
                showMessage("Payment succeeded!");
                break;
            case "processing":
                showMessage("Your payment is processing.");
                break;
            case "requires_payment_method":
                showMessage("Your payment was not successful, please try again.");
                break;
            default:
                showMessage("Something went wrong.");
                break;
        }
    }

    // ------- UI helpers -------

    function showMessage(messageText) {
        const messageContainer = document.querySelector("#payment-message");

        messageContainer.classList.remove("hidden");
        messageContainer.textContent = messageText;

        setTimeout(function () {
            messageContainer.classList.add("hidden");
            messageContainer.textContent = "";
        }, 4000);
    }

    // Show a spinner on payment submission
    function setLoading(isLoading) {
        if (isLoading) {
            document.querySelector("#submit").disabled = true;
        } else {
            document.querySelector("#submit").disabled = false;
        }
    }

</script>


@endsection
