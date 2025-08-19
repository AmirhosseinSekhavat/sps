<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\ShareCertificate;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * Show import/export page
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalCertificates = ShareCertificate::count();
        
        return view('admin.excel.index', compact('totalUsers', 'totalCertificates'));
    }

    /**
     * Export users to Excel
     */
    public function exportUsers()
    {
        return Excel::download(new UsersExport, 'users_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Import users from Excel
     */
    public function importUsers(Request $request)
    {
        \Log::info('Excel import request received', [
            'request_data' => $request->all(),
            'files' => $request->allFiles(),
            'user' => auth()->user()
        ]);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'financial_year' => 'required|integer|min:1300|max:1500',
        ]);

        try {
            $file = $request->file('file');
            $financialYear = $request->financial_year;

            \Log::info('Starting Excel import', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'financial_year' => $financialYear
            ]);

            // Import users with financial year
            Excel::import(new UsersImport($financialYear), $file);

            \Log::info('Excel import completed successfully');
            return back()->with('success', "کاربران با موفقیت برای سال مالی {$financialYear} وارد شدند.");
        } catch (\Exception $e) {
            \Log::error('Excel import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['file' => 'خطا در وارد کردن فایل: ' . $e->getMessage()]);
        }
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
    {
        $templatePath = storage_path('app/templates/users_template.xlsx');
        
        if (!file_exists($templatePath)) {
            $this->createTemplate();
        }
        
        return response()->download($templatePath, 'users_template.xlsx');
    }

    /**
     * Create Excel template
     */
    public function createTemplate()
    {
        $headers = [
            'نام',
            'نام-خانوادگی',
            'نام-پدر',
            'شماره-موبایل',
            'شماره-عضویت',
            'کد-ملی',
            'مبلغ-سهام',
            'تعداد-سهام',
            'مبلغ-سود-سهام-سال',
            'سود-سهام-پرداختی-سال',
        ];

        $sampleRow = [
            'علی',
            'احمدی',
            'محمد',
            '09123456789',
            'M001',
            '1234567890',
            '1000000',
            '100',
            '50000',
            '50000',
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($headers as $idx => $header) {
            $sheet->setCellValueByColumnAndRow($idx + 1, 1, $header);
        }

        foreach ($sampleRow as $idx => $value) {
            $sheet->setCellValueByColumnAndRow($idx + 1, 2, $value);
        }

        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $templateDir = storage_path('app/templates');
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0775, true);
        }
        $filePath = $templateDir . '/users_template.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
    }
}
