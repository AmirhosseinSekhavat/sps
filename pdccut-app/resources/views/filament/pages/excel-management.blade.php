<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Statistics Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-filament::card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-users class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-500">کل کاربران</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $this->totalUsers }}</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-check-circle class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-500">کاربران فعال</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $this->activeUsers }}</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-x-circle class="h-8 w-8 text-red-600" />
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-500">کاربران غیرفعال</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $this->inactiveUsers }}</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-chart-bar class="h-8 w-8 text-purple-600" />
                    </div>
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-500">نرخ فعال</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $this->totalUsers > 0 ? round(($this->activeUsers / $this->totalUsers) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <!-- Import/Export Section -->
        <x-filament::card>
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        <x-heroicon-o-arrow-up-tray class="h-5 w-5 inline mr-2 text-green-600" />
                        وارد کردن کاربران
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        فایل Excel یا CSV حاوی اطلاعات کاربران را انتخاب کنید
                    </p>
                </div>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="rounded-md bg-green-50 p-4 border border-green-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-check-circle class="h-5 w-5 text-green-400" />
                            </div>
                            <div class="mr-3">
                                <p class="text-sm text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="rounded-md bg-red-50 p-4 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-x-circle class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="mr-3">
                                <p class="text-sm text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="mr-3">
                                <h3 class="text-sm font-medium text-red-800">خطا در وارد کردن فایل</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.excel.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ uploading: false }" x-on:submit="uploading = true">
                    @csrf
                    
                    <!-- Financial Year Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            سال مالی *
                        </label>
                        <select name="financial_year" required 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">انتخاب سال مالی</option>
                            @for($year = 1400; $year <= 1410; $year++)
                                <option value="{{ $year }}" {{ $year == 1403 ? 'selected' : '' }}>
                                    {{ $year }} - {{ $year + 1 }}
                                </option>
                            @endfor
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            سال مالی برای اطلاعات مالی انتخاب کنید
                        </p>
                    </div>
                    
                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            انتخاب فایل
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            فرمت‌های پشتیبانی شده: .xlsx, .xls, .csv (حداکثر 10MB)
                        </p>
                    </div>
                    
                    <x-filament::button type="submit" color="success" :disabled="false">
                        <x-heroicon-o-arrow-up-tray class="h-4 w-4 mr-2" />
                        <span x-show="!uploading">وارد کردن کاربران</span>
                        <span x-show="uploading" class="inline-flex items-center">
                            در حال پردازش...
                        </span>
                    </x-filament::button>
                </form>
            </div>
        </x-filament::card>

        <!-- Export Section -->
        <x-filament::card>
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        <x-heroicon-o-arrow-down-tray class="h-5 w-5 inline mr-2 text-blue-600" />
                        خارج کردن کاربران
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        اطلاعات کاربران را در فرمت‌های مختلف دانلود کنید
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('admin.excel.export') }}?format=xlsx" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <x-heroicon-o-table-cells class="h-4 w-4 mr-2" />
                        دانلود Excel (.xlsx)
                    </a>
                    
                    <a href="{{ route('admin.excel.export') }}?format=csv" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <x-heroicon-o-document-text class="h-4 w-4 mr-2" />
                        دانلود CSV
                    </a>
                </div>
            </div>
        </x-filament::card>

        <!-- Template Download -->
        <x-filament::card>
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <x-heroicon-o-document-arrow-down class="h-5 w-5 inline mr-2 text-yellow-600" />
                    قالب Excel
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    برای وارد کردن صحیح اطلاعات، ابتدا قالب Excel را دانلود کنید
                </p>
                <a href="{{ route('admin.excel.template') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <x-heroicon-o-document-arrow-down class="h-4 w-4 mr-2" />
                    دانلود قالب Excel
                </a>
            </div>
        </x-filament::card>

        <!-- Instructions -->
        <x-filament::card>
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <x-heroicon-o-information-circle class="h-5 w-5 inline mr-2 text-blue-600" />
                    راهنمای استفاده
                </h3>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-information-circle class="h-5 w-5 text-blue-400" />
                        </div>
                        <div class="mr-3">
                            <h4 class="text-sm font-medium text-blue-800">نکات مهم:</h4>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>ستون‌های الزامی:</strong> نام، نام-خانوادگی، نام-پدر، شماره-موبایل، کد-ملی، شماره-عضویت</li>
                                    <li><strong>ستون‌های مالی:</strong> مبلغ-سهام، تعداد-سهام، مبلغ-سود-سهام-سال، سود-سهام-پرداختی-سال</li>
                                    <li><strong>ترتیب ستون‌ها:</strong> نام | نام-خانوادگی | نام-پدر | شماره-موبایل | شماره-عضویت | کد-ملی | مبلغ-سهام | تعداد-سهام | مبلغ-سود-سهام-سال | سود-سهام-پرداختی-سال</li>
                                    <li><strong>سال مالی:</strong> قبل از آپلود، سال مالی مورد نظر را انتخاب کنید</li>
                                    <li><strong>شماره موبایل:</strong> باید با فرمت 09xxxxxxxxx باشد</li>
                                    <li><strong>کد ملی:</strong> باید 10 رقم باشد</li>
                                    <li><strong>مقادیر مالی:</strong> باید عدد باشند (پشتیبانی از اعداد فارسی)</li>
                                    <li><strong>حداکثر حجم فایل:</strong> 10MB</li>
                                    <li><strong>نکته:</strong> اگر کاربر قبلاً وجود داشته باشد، اطلاعات مالی سال جدید به‌روزرسانی می‌شود</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Upload History -->
        <x-filament::card>
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <x-heroicon-o-clock class="h-5 w-5 inline mr-2 text-gray-600" />
                    تاریخچه آپلودها
                </h3>

                @php
                    $history = \App\Models\ExcelUpload::with('user')
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get();
                @endphp

                @if($history->count() === 0)
                    <p class="text-sm text-gray-500">هنوز آپلودی ثبت نشده است.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاریخ</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">کاربر</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نام فایل</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سال مالی</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">حجم</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">وضعیت</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($history as $item)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ verta($item->created_at)->format('Y/m/d H:i') }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ optional($item->user)->first_name }} {{ optional($item->user)->last_name }} ({{ $item->user_id }})</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $item->original_name }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $item->financial_year }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ number_format((int) $item->size_bytes) }} بایت</td>
                                        <td class="px-4 py-2 text-sm">
                                            @if($item->status === 'success')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">موفق</span>
                                            @elseif($item->status === 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" title="{{ $item->error_message }}">ناموفق</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">در حال پردازش</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page> 