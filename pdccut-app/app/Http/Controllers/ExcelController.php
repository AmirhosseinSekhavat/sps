<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\ShareCertificate;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ExcelUpload;

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

        $uploadLog = new ExcelUpload();
        $uploadLog->user_id = auth()->id();
        $uploadLog->financial_year = (int) $request->financial_year;

        try {
            $file = $request->file('file');
            $uploadLog->original_name = $file->getClientOriginalName();
            $uploadLog->stored_name = null;
            $uploadLog->mime_type = $file->getClientMimeType();
            $uploadLog->size_bytes = $file->getSize();
            $uploadLog->status = 'processing';
            $uploadLog->save();

            \Log::info('Starting Excel import', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'financial_year' => $uploadLog->financial_year
            ]);

            // Import users with financial year
            Excel::import(new UsersImport($uploadLog->financial_year), $file);

            $uploadLog->status = 'success';
            $uploadLog->save();

            \Log::info('Excel import completed successfully');
            return back()->with('success', "کاربران با موفقیت برای سال مالی {$uploadLog->financial_year} وارد شدند.");
        } catch (\Exception $e) {
            $uploadLog->status = 'failed';
            $uploadLog->error_message = $e->getMessage();
            $uploadLog->save();

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
