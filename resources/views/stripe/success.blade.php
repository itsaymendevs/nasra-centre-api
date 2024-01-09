@extends('stripe.layout')


@section('pageTitle')
Payment Success
@endsection



@section('content')



<div class="container-fluid bg-cover bg--main px-0">
    <div class="row g-0 min-vh-100">
        <div class="col-12 align-self-center content--col px-0" id="content--col">
            <section id="content--main" class="d-block">
                <div>

                    {{-- row --}}
                    <div class="row justify-content-center align-items-center">

                        {{-- logo --}}
                        <div class="col-12 col-lg-5">
                            <img data-aos="fade-down" data-aos-duration="1200" data-aos-delay="150" data-aos-once='true'
                                class="w-100 of-contain mb-3" src="{{asset('assets/img/Logo/logo.png')}}"
                                style="height: 160px;">
                        </div>



                        {{-- content --}}
                        <div class="col-11 col-lg-5 text-center">


                            <div class="d-flex align-items-center justify-content-center text-center mx-auto mb-4"
                                style="width: 70%" data-aos="fade-zoom-in" data-aos-duration="1200">
                                <hr class="login--hr" />
                                <hr class="login--hr" />
                                <hr class="login--hr black" />
                                <hr class="login--hr black" />
                                <hr class="login--hr black" />
                                <hr class="login--hr black" />
                                <hr class="login--hr black" />
                                <hr class="login--hr black" />
                                <hr class="login--hr black" />
                            </div>


                            <h2 data-aos="fade-zoom-in" data-aos-duration="1200"
                                class="text-center font--cour mb-0 fw-bold px-3" data-aos-delay="150"
                                style="color: #222; letter-spacing: 1px; line-height: 37px;">
                                Thanks For Your Patience,<br>Your Order is Processing :)
                            </h2>




                            {{-- return --}}
                            <div class="text-center d-block mt-4 pt-2" data-aos="fade-up" data-aos-duration="1200"
                                data-aos-delay="150" data-aos-once='true'>
                                <a class="btn btn--theme btn--submit btn--sm rounded-1 fw-semibold"
                                    onclick="window.close()"
                                    style="width: 250px; height:45px; background-color: black; border-color: black !important; color: white;">
                                    Return Back
                                </a>
                            </div>
                        </div>
                    </div>
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




@endsection