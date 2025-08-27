<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ShareCertificate;
use App\Models\Notification;
use App\Models\EarnedProfit;
use App\Services\PdfService;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Show user profile
     */
    public function show($nationalCode)
    {
        $user = User::where('national_code', $nationalCode)->firstOrFail();
        $latestCertificate = $user->shareCertificates()->latest('year')->first();
        $unreadNotifications = $user->notifications()->unread()->count();
        $totalCertificates = $user->shareCertificates()->count();
        $totalNotifications = $user->notifications()->count();
        
        // Get earned profits data for chart (only years with data)
        $earnedProfits = EarnedProfit::active()
            ->orderBy('year')
            ->get()
            ->keyBy('year');

        return view('admin.user.show', compact('user', 'latestCertificate', 'unreadNotifications', 'totalCertificates', 'totalNotifications', 'earnedProfits'));
    }

    /**
     * Show user certificates
     */
    public function certificates($nationalCode)
    {
        $user = User::where('national_code', $nationalCode)->firstOrFail();
        $certificates = $user->shareCertificates()->orderBy('year', 'desc')->get();
        $years = $user->shareCertificates()->pluck('year')->unique()->sort()->reverse();

        return view('admin.user.certificates', compact('user', 'certificates', 'years'));
    }

    /**
     * Generate PDF for a specific year and attach to certificate
     */
    public function generateCertificatePdf(Request $request, $nationalCode, $year, PdfService $pdfService)
    {
        $user = User::where('national_code', $nationalCode)->firstOrFail();
        $certificate = $user->shareCertificates()->where('year', $year)->firstOrFail();

        // Generate and store PDF
        $path = $pdfService->generateShareCertificate($user, $certificate, (int) $year);

        // Save path to certificate
        $certificate->pdf_path = $path;
        $certificate->save();

        return back()->with('success', 'فایل PDF گواهی با موفقیت تولید شد.');
    }

    /**
     * Stream inline PDF for a given year
     */
    public function viewCertificatePdf($nationalCode, $year)
    {
        $user = User::where('national_code', $nationalCode)->firstOrFail();
                $certificate = $user->shareCertificates()->where('year', $year)->firstOrFail();
        
        if (request()->boolean('force') || app()->environment('local') || !$certificate->pdf_path || !Storage::disk('local')->exists($certificate->pdf_path)) {
            $pdfService = app(PdfService::class);
            $path = $pdfService->generateShareCertificate($user, $certificate, (int) $year);
            $certificate->pdf_path = $path;
            $certificate->save();
        }
        
        $path = Storage::disk('local')->path($certificate->pdf_path);
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate_'.$nationalCode.'_'.$year.'.pdf"',
        ]);
    }

    /**
     * Download PDF for a given year
     */
    public function downloadCertificatePdf($nationalCode, $year)
    {
        $user = User::where('national_code', $nationalCode)->firstOrFail();
                $certificate = $user->shareCertificates()->where('year', $year)->firstOrFail();
        
        if (request()->boolean('force') || app()->environment('local') || !$certificate->pdf_path || !Storage::disk('local')->exists($certificate->pdf_path)) {
            $pdfService = app(PdfService::class);
            $path = $pdfService->generateShareCertificate($user, $certificate, (int) $year);
            $certificate->pdf_path = $path;
            $certificate->save();
        }
        
        $path = Storage::disk('local')->path($certificate->pdf_path);
        return response()->download($path, 'certificate_'.$nationalCode.'_'.$year.'.pdf', [ 'Content-Type' => 'application/pdf' ]);
    }

    /**
     * Show user notifications
     */
    public function notifications($nationalCode)
    {
        $user = User::where('national_code', $nationalCode)->firstOrFail();
        $notifications = $user->notifications()->latest()->paginate(20);

        return view('admin.user.notifications', compact('user', 'notifications'));
    }
}
