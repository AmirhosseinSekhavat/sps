<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت Excel - PDCCUT.IR</title>
    
    <!-- Load Font Awesome first -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Load Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    
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
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="/admin" class="text-blue-600 hover:text-blue-500">
                        <i class="fas fa-arrow-right text-xl"></i>
                    </a>
                    <h1 class="mr-3 text-xl font-semibold text-gray-900">مدیریت Excel</h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-500">کل کاربران</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($totalUsers) }} نفر</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-certificate text-2xl text-green-600"></i>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-500">کل گواهی‌ها</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($totalCertificates) }} عدد</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import/Export Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Export Section -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <i class="fas fa-download text-blue-600 ml-2"></i>
                        خروجی Excel
                    </h3>
                    
                    <p class="text-sm text-gray-600 mb-4">
                        تمام کاربران را به صورت فایل Excel دانلود کنید.
                    </p>
                    
                    <a href="{{ route('admin.excel.export') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-download ml-2"></i>
                        دانلود Excel
                    </a>
                </div>
            </div>

            <!-- Import Section -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        <i class="fas fa-upload text-green-600 ml-2"></i>
                        ورودی Excel
                    </h3>
                    
                    <p class="text-sm text-gray-600 mb-4">
                        کاربران جدید را از فایل Excel وارد کنید.
                    </p>
                    
                    <form action="{{ route('admin.excel.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                   required>
                        </div>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-upload ml-2"></i>
                            آپلود و وارد کردن
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Template Download -->
        <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-file-alt text-purple-600 ml-2"></i>
                    قالب Excel
                </h3>
                
                <p class="text-sm text-gray-600 mb-4">
                    برای وارد کردن کاربران جدید، ابتدا قالب Excel را دانلود کنید و آن را با اطلاعات کاربران پر کنید.
                </p>
                
                <a href="{{ route('admin.excel.template') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-download ml-2"></i>
                    دانلود قالب
                </a>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-900 mb-2">
                <i class="fas fa-info-circle text-blue-600 ml-1"></i>
                راهنمای استفاده:
            </h4>
            <ul class="text-sm text-blue-800 space-y-1 mr-4">
                <li>• فایل Excel باید شامل ستون‌های: نام، نام خانوادگی، نام پدر، موبایل، شماره عضویت، کد ملی</li>
                <li>• برای کاربران موجود، اطلاعات بر اساس کد ملی به‌روزرسانی می‌شود</li>
                <li>• برای کاربران جدید، حساب کاربری جدید ایجاد می‌شود</li>
                <li>• رمز عبور پیش‌فرض برای کاربران جدید: 123456</li>
            </ul>
        </div>
    </main>
</body>
</html>
