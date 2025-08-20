<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تایید کد - PDCCUT.IR</title>
    
    <!-- Load Font Awesome first -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Load Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    <!-- Preload critical Farsi fonts -->
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/Farsi numerals/Webfonts/fonts/Woff2/IRANSansXFaNum-Regular.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/Farsi numerals/Webfonts/fonts/Woff2/IRANSansXFaNum-Bold.woff2') }}" crossorigin>

    <!-- Load our custom CSS last -->
    @vite(['resources/css/app.css'])
    
    <style>
        /* Ensure Farsi font is applied to all elements */
        * {
            font-family: 'IRANSansX', 'Tahoma', 'Arial', sans-serif !important;
        }
        
        body { 
            font-family: 'IRANSansX', 'Tahoma', 'Arial', sans-serif !important; 
            font-weight: 400;
            line-height: 1.6;
        }
        
        .rtl { direction: rtl; }
        
        /* Ensure proper font rendering for Farsi text */
        html[lang="fa"] {
            font-family: 'IRANSansX', 'Tahoma', 'Arial', sans-serif !important;
        }
        
        /* Ensure Font Awesome icons are visible */
        .fas, .far, .fab {
            font-family: 'Font Awesome 6 Free', 'Font Awesome 6 Pro', 'Font Awesome 6 Brands' !important;
            font-weight: 900;
        }
        
        .far {
            font-weight: 400;
        }
        
        .fab {
            font-family: 'Font Awesome 6 Brands' !important;
            font-weight: 400;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="flex justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="PDCCUT.IR" class="h-20 w-auto">
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    تایید کد
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    کد 6 رقمی ارسال شده را وارد کنید
                </p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('auth.verify-otp.verify') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="otp_code" class="sr-only">کد تایید</label>
                        <input id="otp_code" name="otp_code" type="text" required 
                               class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm text-center text-2xl tracking-widest"
                               placeholder="000000" 
                               maxlength="6" 
                               pattern="[0-9]{6}"
                               autocomplete="off">
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-green-500 group-hover:text-green-400"></i>
                        </span>
                        ورود به سیستم
                    </button>
                </div>

                <div class="text-center space-y-4">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-clock text-orange-500 ml-1"></i>
                        کد تایید تا 5 دقیقه معتبر است
                    </div>
                    
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-mobile-alt text-blue-500 ml-1"></i>
                        کد به شماره موبایل ثبت شده ارسال شده است
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('auth.login') }}" class="text-blue-600 hover:text-blue-500 text-sm">
                        <i class="fas fa-arrow-right ml-1"></i>
                        بازگشت به صفحه ورود
                    </a>
                </div>
            </form>

            <div class="text-center">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} PDCCUT.IR. تمامی حقوق محفوظ است.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto-format OTP input
        document.getElementById('otp_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            e.target.value = value;
        });

        // Auto-focus on OTP input
        document.getElementById('otp_code').focus();
    </script>
</body>
</html>
