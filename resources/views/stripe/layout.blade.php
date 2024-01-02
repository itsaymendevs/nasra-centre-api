<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>
        @yield('pageTitle')
    </title>
    <link rel="stylesheet" href="{{asset('assets/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cairo:400,500,600,700&amp;subset=arabic&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Courgette&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans+Arabic:400,500,600,700&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,800,900&amp;display=swap">
    <link rel="stylesheet" href="{{asset('assets/css/aos.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/filters.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/globals.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/home.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/login.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/modal.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/navbar.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/orders.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/profile.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/scroll.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/select2-custom.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/select2.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/sms.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/sort.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/styles.css')}}">

    <link rel="stylesheet" href="{{asset('assets/css/checkout.css')}}" />

    {{-- JQUERY  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>



{{-- ------------------------------------------------------------------------------------ --}}


<body>
    <section data-aos="zoom-out" data-aos-duration="450" data-aos-delay="50" data-aos-once="true" id="section--body">


        @yield('content')





        {{-- --------------------- --}}


        <script src="{{asset('assets/bootstrap/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('assets/js/aos.min.js')}}"></script>
        <script src="{{asset('assets/js/bs-init.js')}}"></script>
        <script src="{{asset('assets/js/init.js')}}"></script>
        <script src="{{asset('assets/js/select2.min.js')}}"></script>


        @yield('scripts')


    </section>
</body>
{{-- end body --}}




{{-- ------------------------------------------------------------------------------------ --}}



</html>
{{-- end html --}}
