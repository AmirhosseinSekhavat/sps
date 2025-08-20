<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به پرتال سهامداران - PDCCUT.IR</title>
    
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
                <div class="mx-auto h-20 w-20 bg-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-building text-white text-3xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    ورود به سیستم
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    sps.pdccut.ir - پرتال سهمداران
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

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('auth.send-otp') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="national_code" class="sr-only">کد ملی</label>
                        <input id="national_code" name="national_code" type="text" required 
                               class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm text-center"
                               placeholder="کد ملی خود را وارد کنید" 
                               maxlength="10" 
                               pattern="[0-9]{10}"
                               value="{{ old('national_code') }}">
                    </div>
                </div>

                @if(session('captcha_question'))
                    <div class="rounded-md shadow-sm">
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            <label for="captcha_answer" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-shield-alt text-blue-500 ml-1"></i>
                                کد امنیتی: {{ session('captcha_question.question') }}
                            </label>
                            <input id="captcha_answer" name="captcha_answer" type="number" required 
                                   class="appearance-none rounded-md relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm text-center"
                                   placeholder="پاسخ را وارد کنید"
                                   min="1"
                                   max="20">
                        </div>
                    </div>
                @endif

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-paper-plane text-blue-500 group-hover:text-blue-400"></i>
                        </span>
                        ارسال کد تایید
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-blue-500 ml-1"></i>
                        کد تایید به شماره موبایل ثبت شده ارسال خواهد شد
                    </p>
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
        // Auto-format national code input
        document.getElementById('national_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
