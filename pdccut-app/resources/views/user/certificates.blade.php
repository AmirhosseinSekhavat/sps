@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-certificate text-blue-600 ml-2"></i>
            گواهی‌های سهام
        </h1>
        <p class="mt-2 text-gray-600">
            تمام گواهی‌های سهام شما در سال‌های مختلف
        </p>
    </div>

    <!-- User Info Summary -->
    <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="h-12 w-12 bg-blue-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold">{{ substr($user->first_name, 0, 1) }}</span>
                </div>
                <div class="mr-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        کد ملی: {{ $user->national_code }} | شماره عضویت: {{ $user->membership_number }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificates List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-certificate text-blue-600 ml-2"></i>
                لیست گواهی‌های سهام
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                تمام گواهی‌های سهام شما در سال‌های مختلف
            </p>
        </div>

        @if($certificates->count() > 0)
        <ul class="divide-y divide-gray-200">
            @foreach($certificates as $certificate)
            <li class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-certificate text-blue-600 text-xl"></i>
                        </div>
                        <div class="mr-4">
                            <h4 class="text-lg font-medium text-gray-900">
                                گواهی سهام سال {{ $certificate->year }}
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2 text-sm">
                                <div>
                                    <span class="text-gray-500">مبلغ سهام:</span>
                                    <span class="font-medium text-gray-900">{{ number_format($certificate->share_amount) }} ریال</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">تعداد سهام:</span>
                                    <span class="font-medium text-gray-900">{{ number_format($certificate->share_count) }} عدد</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">سود سالانه:</span>
                                    <span class="font-medium text-gray-900">{{ number_format($certificate->annual_profit_amount) }} ریال</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">سود سهام پرداختی سال:</span>
                                    <span class="font-medium text-gray-900">{{ number_format($certificate->annual_payment) }} ریال</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @php
                            $hasFile = $certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path);
                        @endphp

                        @if($hasFile)
                            <a href="{{ route('user.certificate.view', $certificate->year) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700">
                                <i class="fas fa-eye ml-2"></i> مشاهده PDF
                            </a>
                            <a href="{{ route('user.certificate.download', $certificate->year) }}" class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-gray-600 text-white hover:bg-gray-700">
                                <i class="fas fa-download ml-2"></i> دانلود
                            </a>
                        @else
                            <a href="{{ route('user.certificate', $certificate->year) }}" class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">
                                <i class="fas fa-eye ml-2"></i> مشاهده
                            </a>
                        @endif
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="px-6 py-8 text-center">
            <i class="fas fa-certificate text-gray-400 text-4xl mb-4"></i>
            <p class="text-gray-500">هیچ گواهی سهامی یافت نشد.</p>
        </div>
        @endif
    </div>

    <!-- Year Selection -->
    @if($years->count() > 0)
    <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                <i class="fas fa-calendar-alt text-blue-600 ml-2"></i>
                سال‌های موجود
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($years as $year)
                <a href="{{ route('user.certificate', $year) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-gray-50 hover:bg-gray-100">
                    {{ $year }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 