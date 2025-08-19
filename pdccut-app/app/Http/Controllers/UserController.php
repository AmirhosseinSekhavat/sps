<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\PdfService;
use App\Models\User;
use App\Models\ShareCertificate;
use App\Models\Notification;
use App\Models\EarnedProfit;

class UserController extends Controller
{
    public function __construct()
    {
        // Laravel 12: middleware is now applied in routes or using middleware() method
        // The 'auth' middleware is already applied in routes/web.php
    }

    /**
     * Show user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $latestCertificate = $user->shareCertificates()->latest('year')->first();
        $unreadNotifications = $user->notifications()->unread()->count();
        $totalCertificates = $user->shareCertificates()->count();
        $totalNotifications = $user->notifications()->count();
        
        // Get available financial years
        $availableYears = $user->getAvailableFinancialYears();
        $selectedYear = request('year', $user->getLatestFinancialYear());
        
        // Get certificate for selected year
        $selectedCertificate = $user->getShareCertificateByYear($selectedYear);
        
        // Get all certificates for comparison
        $allCertificates = $user->shareCertificates()
            ->orderBy('year', 'desc')
            ->get()
            ->groupBy('year');

        // Get earned profits data for chart (only years with data)
        $earnedProfits = EarnedProfit::active()
            ->orderBy('year')
            ->get()
            ->keyBy('year');

        return view('user.dashboard', compact(
            'user', 
            'latestCertificate', 
            'unreadNotifications', 
            'totalCertificates',
            'totalNotifications',
            'availableYears', 
            'selectedYear',
            'selectedCertificate',
            'allCertificates',
            'earnedProfits'
        ));
    }

    /**
     * Show all user certificates
     */
    public function certificates()
    {
        $user = Auth::user();
        $certificates = $user->shareCertificates()->orderBy('year', 'desc')->get();
        $years = $user->shareCertificates()->pluck('year')->unique()->sort()->reverse();

        return view('user.certificates', compact('user', 'certificates', 'years'));
    }

    /**
     * Show share certificate for specific year
     */
    public function showCertificate($year)
    {
        $user = Auth::user();
        $certificate = $user->shareCertificates()->where('year', $year)->first();
        
        if (!$certificate) {
            return back()->withErrors(['year' => 'گواهی سهام برای این سال یافت نشد.']);
        }

        $earnedProfits = EarnedProfit::active()->forYear($year)->get();

        return view('user.certificate', compact('certificate', 'earnedProfits', 'year'));
    }

    /**
     * Stream inline PDF view of the certificate
     */
    public function viewCertificatePdf($year)
    {
        $user = Auth::user();
        $certificate = $user->shareCertificates()->where('year', $year)->first();
        
        if (!$certificate) {
            abort(404);
        }

        if (request()->boolean('force') || app()->environment('local') || !$certificate->pdf_path || !Storage::disk('public')->exists($certificate->pdf_path)) {
            // Try to generate on the fly
            $pdfService = app(PdfService::class);
            $path = $pdfService->generateShareCertificate($user, $certificate, (int) $year);
            $certificate->pdf_path = $path;
            $certificate->save();
        }

        $path = Storage::disk('public')->path($certificate->pdf_path);
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate_'.$user->national_code.'_'.$year.'.pdf"',
        ]);
    }

    /**
     * Download PDF certificate
     */
    public function downloadCertificate($year)
    {
        $user = Auth::user();
        $certificate = $user->shareCertificates()->where('year', $year)->first();
        
        if (!$certificate) {
            return back()->withErrors(['year' => 'گواهی سهام برای این سال یافت نشد.']);
            
        }

        if (request()->boolean('force') || app()->environment('local') || !$certificate->pdf_path || !Storage::disk('public')->exists($certificate->pdf_path)) {
            // Generate on the fly
            $pdfService = app(PdfService::class);
            $path = $pdfService->generateShareCertificate($user, $certificate, (int) $year);
            $certificate->pdf_path = $path;
            $certificate->save();
        }

        $absolutePath = Storage::disk('public')->path($certificate->pdf_path);
        return response()->download($absolutePath, 'certificate_'.$user->national_code.'_'.$year.'.pdf', [ 'Content-Type' => 'application/pdf' ]);
    }

    /**
     * Show notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(10);

        return view('user.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'اعلان خوانده شد.');
    }

}
