<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Imports\UsersImport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExcelManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    
    protected static ?string $navigationLabel = 'مدیریت Excel';
    
    protected static ?string $title = 'مدیریت Excel و عملیات گروهی';
    
    protected static ?string $slug = 'excel-management';
    
    protected static ?int $navigationSort = 3;
    
    protected static string $view = 'filament.pages.excel-management';

    // Declare properties used in the Blade view so Livewire can access them
    public int $totalUsers = 0;
    public int $activeUsers = 0;
    public int $inactiveUsers = 0;

    public function mount(): void
    {
        // Get statistics
        $this->totalUsers = \App\Models\User::count();
        $this->activeUsers = \App\Models\User::where('is_active', true)->count();
        $this->inactiveUsers = \App\Models\User::where('is_active', false)->count();
    }

    public function exportUsers($format = 'xlsx')
    {
        $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.' . $format;
        
        return Excel::download(new UsersExport, $filename);
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'financial_year' => 'required|integer|min:1300|max:1500',
        ]);

        try {
            $file = $request->file('file');
            $financialYear = $request->financial_year;
            
            // Import users with financial year
            Excel::import(new UsersImport($financialYear), $file);
            
            Notification::make()
                ->title('موفقیت')
                ->body("کاربران با موفقیت برای سال مالی {$financialYear} وارد شدند.")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('خطا')
                ->body('خطا در وارد کردن فایل: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function downloadTemplate()
    {
        $filename = 'users_template.xlsx';
        
        return Excel::download(new UsersExport, $filename);
    }

    public function getUserStats()
    {
        $stats = [
            'total' => \App\Models\User::count(),
            'active' => \App\Models\User::where('is_active', true)->count(),
            'inactive' => \App\Models\User::where('is_active', false)->count(),
            'with_certificates' => \App\Models\User::has('shareCertificates')->count(),
            'with_notifications' => \App\Models\User::has('notifications')->count(),
        ];

        return response()->json($stats);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_xlsx')
                ->label('خروجی Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => $this->exportUsers('xlsx')),
                
            Action::make('export_csv')
                ->label('خروجی CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(fn() => $this->exportUsers('csv')),
                
            Action::make('download_template')
                ->label('دانلود قالب')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->action(fn() => $this->downloadTemplate()),
        ];
    }
} 