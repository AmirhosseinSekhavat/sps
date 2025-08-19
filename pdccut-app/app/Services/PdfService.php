<?php

namespace App\Services;

use App\Models\User;
use App\Models\ShareCertificate;
use App\Models\EarnedProfit;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PdfService
{
    public function generateShareCertificate(User $user, ShareCertificate $certificate, int $year): string
    {
        try {
            $earnedProfits = EarnedProfit::active()->forYear($year)->get();
            $html = $this->generateCertificateHtml($user, $certificate, $earnedProfits, $year);
            $relativePath = "certificates/{$user->national_code}_{$year}.pdf";
            
            // Ensure directory exists
            $fullPath = Storage::disk('public')->path($relativePath);
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            
            // Use mPDF for Persian text support
            if (!class_exists('\\Mpdf\\Mpdf')) {
                throw new RuntimeException('mPDF نصب نشده است.');
            }
            
            // Create temp directory for mPDF
            $tempDir = storage_path('app/mpdf');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0775, true);
            }
            
            // Configure mPDF with proper margins and font
            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => $tempDir,
                'margin_top' => 10,
                'margin_right' => 10,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'mode' => 'utf-8',
                'default_font' => 'dejavusans',
                'format' => 'A4',
            ]);
            
            // Enable Persian text support
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->SetDirectionality('rtl');
            
            // Write HTML content
            $mpdf->WriteHTML($html);
            
            // Get PDF content
            $pdfContent = $mpdf->Output('', 'S');
            
            // Save to storage
            Storage::disk('public')->put($relativePath, $pdfContent);
            
            return $relativePath;

        } catch (\Throwable $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            throw new RuntimeException('خطا در تولید PDF: ' . $e->getMessage());
        }
    }

    private function generateCertificateHtml(User $user, ShareCertificate $certificate, $earnedProfits, int $year): string
    {
        // Build dynamic chart data from EarnedProfit admin table
        $chartData = EarnedProfit::active()
            ->orderBy('year')
            ->get(['year', 'amount']);
        
        $barsSvg = '';
        $xLabelsSvg = '';
        $maxAmount = 0;
        $points = [];
        foreach ($chartData as $row) {
            $amount = (float) $row->amount;
            $maxAmount = max($maxAmount, $amount);
            $points[] = ['year' => (int) $row->year, 'amount' => $amount];
        }
        if ($maxAmount <= 0) { $maxAmount = 1; }
        
        $minDataYear = count($points) ? $points[0]['year'] : $year;
        $maxDataYear = count($points) ? $points[count($points)-1]['year'] : $year;
        
        // Chart dimensions - optimized for single page
        $chartWidth = 500;
        $chartHeight = 180;
        $bottomY = 160;
        $maxBarHeight = 140;
        
        // Dynamic sizing
        $leftPadding = 60;
        $rightPadding = 20;
        $barCount = max(1, count($points));
        $usableWidth = $chartWidth - $leftPadding - $rightPadding;
        $perSlot = $usableWidth / $barCount;
        $barWidth = max(4, min(20, (int) floor($perSlot * 0.6)));
        $slotCenterOffset = ($perSlot - $barWidth) / 2;
        
        // Build bars
        $x = $leftPadding + $slotCenterOffset;
        foreach ($points as $p) {
            $h = (int) round(($p['amount'] / $maxAmount) * $maxBarHeight);
            $y = max(0, $bottomY - $h);
            
            // Main bar
            $barsSvg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $barWidth . '" height="' . $h . '" fill="#E53935" rx="2"/>';
            
            // 3D effect - side face
            $barsSvg .= '<polygon points="'
                . ($x + $barWidth) . ',' . $y . ' '
                . ($x + $barWidth + 3) . ',' . ($y - 2) . ' '
                . ($x + $barWidth + 3) . ',' . ($y - 2 + $h) . ' '
                . ($x + $barWidth) . ',' . ($y + $h) . '" fill="#C62828"/>';
            
            // 3D effect - top face
            $barsSvg .= '<polygon points="'
                . $x . ',' . $y . ' '
                . ($x + $barWidth) . ',' . $y . ' '
                . ($x + $barWidth + 3) . ',' . ($y - 2) . ' '
                . ($x + 3) . ',' . ($y - 2) . '" fill="#FF8A80"/>';
            
            // Year labels
            $label = $this->toPersianDigits(substr((string) $p['year'], -2));
            $xLabelsSvg .= '<text x="' . ($x + ($barWidth/2)) . '" y="' . ($bottomY + 15) . '" font-size="9" fill="#37474F" text-anchor="middle">' . $label . '</text>';
            
            $x += $perSlot;
        }
        
        // Chart background and axes
        $plotX = $leftPadding;
        $plotYTop = 20;
        $plotWidth = $chartWidth - $plotX - $rightPadding;
        $plotHeight = $bottomY - $plotYTop;
        
        $axesSvg = '<rect x="' . $plotX . '" y="' . $plotYTop . '" width="' . $plotWidth . '" height="' . $plotHeight . '" fill="#FAFAFA" stroke="#E0E0E0" stroke-width="1" rx="3"/>';
        
        // Y-axis labels
        $topLabel = $this->roundUpNice($maxAmount);
        $threeQuarter = $this->roundUpNice($topLabel * 0.75);
        $half = $this->roundUpNice($topLabel * 0.5);
        $quarter = $this->roundUpNice($topLabel * 0.25);
        
        $yLabelsSvg = ''
            . '<text x="' . ($plotX - 8) . '" y="' . ($plotYTop + 15) . '" font-size="9" fill="#455A64" text-anchor="end">' . $this->toPersianDigits(number_format((int)$topLabel)) . '</text>'
            . '<text x="' . ($plotX - 8) . '" y="' . ($plotYTop + 55) . '" font-size="9" fill="#455A64" text-anchor="end">' . $this->toPersianDigits(number_format((int)$threeQuarter)) . '</text>'
            . '<text x="' . ($plotX - 8) . '" y="' . ($plotYTop + 95) . '" font-size="9" fill="#455A64" text-anchor="end">' . $this->toPersianDigits(number_format((int)$half)) . '</text>'
            . '<text x="' . ($plotX - 8) . '" y="' . ($plotYTop + 135) . '" font-size="9" fill="#455A64" text-anchor="end">' . $this->toPersianDigits(number_format((int)$quarter)) . '</text>'
            . '<text x="' . ($plotX - 8) . '" y="' . ($bottomY + 5) . '" font-size="9" fill="#455A64" text-anchor="end">' . $this->toPersianDigits('0') . '</text>';
        
        // Grid lines
        $gridLines = ''
            . '<line x1="' . $plotX . '" y1="' . ($plotYTop + 40) . '" x2="' . ($chartWidth - $rightPadding) . '" y2="' . ($plotYTop + 40) . '" stroke="#E0E0E0" stroke-width="0.5"/>'
            . '<line x1="' . $plotX . '" y1="' . ($plotYTop + 80) . '" x2="' . ($chartWidth - $rightPadding) . '" y2="' . ($plotYTop + 80) . '" stroke="#E0E0E0" stroke-width="0.5"/>'
            . '<line x1="' . $plotX . '" y1="' . ($plotYTop + 120) . '" x2="' . ($chartWidth - $rightPadding) . '" y2="' . ($plotYTop + 120) . '" stroke="#E0E0E0" stroke-width="0.5"/>';
        
        return '
        <!DOCTYPE html>
        <html dir="rtl" lang="fa">
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: dejavusans, Arial, sans-serif;
                    font-size: 13px;
                    line-height: 1.5;
                    margin: 0;
                    padding: 0;
                    direction: rtl;
                }
                .page {
                    border: 3px solid #263238;
                    padding: 15px;
                    margin: 5px;
                    min-height: 100vh;
                    box-sizing: border-box;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 15px;
                }
                .logo {
                    width: 70px;
                    height: 70px;
                    vertical-align: middle;
                }
                .title {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 8px;
                    color: #263238;
                }
                .establishment-date {
                    font-size: 14px;
                    font-weight: bold;
                    color: #555;
                    margin-bottom: 0;
                }
                .section-title {
                    font-size: 16px;
                    font-weight: bold;
                    text-align: center;
                    margin: 20px 0 15px 0;
                    padding: 10px;
                    background-color: #f5f5f5;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                .content-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                .left-column, .right-column {
                    width: 50%;
                    vertical-align: top;
                    padding: 25px;
                }
                .separator-column {
                    width: 0%;
                    background: none;
                }
                .info-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                    table-layout: fixed;
                }
                .info-table td {
                    padding: 8px 10px;
                    border-bottom: 1px solid #eee;
                    font-size: 13px;
                    line-height: 1.4;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }
                .info-table .label {
                    font-weight: bold;
                    color: #37474F;
                    width: 55%;
                }
                .info-table .value {
                    color: #263238;
                    font-weight: bold;
                    width: 45%;
                }
                .capital-section {
                    text-align: center;
                    margin: 20px 0;
                    padding: 15px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                }
                .capital-text {
                    font-size: 15px;
                    font-weight: bold;
                    color: #263238;
                    margin: 0;
                }
                .chart-section {
                    margin-top: 20px;
                    text-align: center;
                }
                .chart-container {
                    margin: 15px auto;
                    max-width: 500px;
                }
                .chart-caption {
                    margin-top: 10px;
                    font-size: 13px;
                    color: #37474F;
                    font-weight: bold;
                }
                .footer {
                    margin-top: 20px;
                    text-align: left;
                    font-size: 12px;
                    color: #666;
                    border-top: 1px solid #eee;
                    padding-top: 10px;
                }
                .email {
                    color: #0066cc;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="page">
                <div class="header">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                                                    <td width="70" align="right" valign="middle">
                            <img src="' . public_path('images/logo.png') . '" alt="لوگو شرکت" class="logo" />
                        </td>
                        <td align="center" valign="middle">
                            <div class="title">شرکت تعاونی تولیدی توزیعی هیأت علمی دانشگاه تهران</div>
                            <div class="establishment-date">تأسیس ( ۱۳۷۴/۰۶/۲۹ )</div>
                        </td>
                        <td width="70"></td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">مشخصات عضو ، تعداد سهام و سود عضو در سال ' . $this->toPersianDigits($certificate->year) . '</div>
                
                <table class="content-table" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="left-column">
                            <table class="info-table">
                                <tr>
                                    <td class="label">نام و نام خانوادگی:</td>
                                    <td class="value">' . $user->name . '</td>
                                </tr>
                                <tr>
                                    <td class="label">کد ملی:</td>
                                    <td class="value">' . $this->toPersianDigits($user->national_code) . '</td>
                                </tr>
                                <tr>
                                    <td class="label">شماره عضویت:</td>
                                    <td class="value">' . $this->toPersianDigits($user->membership_number) . '</td>
                                </tr>
                            </table>
                        </td>
                        <td class="separator-column"></td>
                        <td class="right-column">
                            <table class="info-table">
                                <tr>
                                    <td class="label">تعداد سهام عضو در پایان سال (' . $this->toPersianDigits($certificate->year - 1) . '):</td>
                                    <td class="value">' . $this->toPersianDigits(number_format((int)$certificate->share_count)) . ' سهم</td>
                                </tr>
                                <tr>
                                    <td class="label">سود هر سهم (عملکرد سال ' . $this->toPersianDigits($certificate->year) . '):</td>
                                    <td class="value">' . $this->toPersianDigits(number_format((int)($certificate->annual_profit_amount / max(1, (int)$certificate->share_count)))) . ' ریال</td>
                                </tr>
                                <tr>
                                    <td class="label">سود سال ' . $this->toPersianDigits($certificate->year) . ' عضو:</td>
                                    <td class="value">' . $this->toPersianDigits(number_format((int)$certificate->annual_profit_amount)) . ' ریال</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <div class="capital-section">
                    <p class="capital-text">سرمایه شرکت: ' . $this->toPersianDigits(number_format(54628600000)) . ' ریال</p>
                </div>
                
                <div class="chart-section">
                    <div class="chart-container">
                        <svg width="' . $chartWidth . '" height="' . $chartHeight . '" viewBox="0 0 ' . $chartWidth . ' ' . $chartHeight . '">
                            <defs>
                                <linearGradient id="barGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:#EF5350;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#E53935;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <rect width="' . $chartWidth . '" height="' . $chartHeight . '" fill="#ffffff"/>
                            ' . $axesSvg . '
                            ' . $gridLines . '
                            ' . $yLabelsSvg . '
                            ' . $barsSvg . '
                            ' . $xLabelsSvg . '
                        </svg>
                    </div>
                    <div class="chart-caption">نمودار سودهای اکتسابی عملکرد شرکت طی سالهای ' . $this->toPersianDigits($minDataYear) . ' لغایت ' . $this->toPersianDigits($maxDataYear) . '</div>
                </div>

                <div class="footer">
                    <p class="email">پست الکترونیک : info@pdccut.ir</p>
                </div>
            </div>
        </body>
        </html>';
    }

    private function persianNumberFormat($number)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $formatted = number_format($number);
        return str_replace($english, $persian, $formatted);
    }
    
    private function persianDate($date)
    {
        return $this->toPersianDigits($date->format('Y/m/d'));
    }

    private function toPersianDigits($value)
    {
        if ($value === null) return '';
        $value = (string) $value;
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        return str_replace($english, $persian, $value);
    }

    private function roundUpNice(float $value): int
    {
        if ($value <= 0) return 0;
        $pow = pow(10, max(0, strlen((string) ((int)$value)) - 2));
        return (int) (ceil($value / $pow) * $pow);
    }
}
