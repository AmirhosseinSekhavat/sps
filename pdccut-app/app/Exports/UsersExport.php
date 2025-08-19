<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $headers;

    public function __construct($data = null, $headers = null)
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    public function collection()
    {
        if ($this->data) {
            return collect($this->data);
        }

        return User::with('shareCertificates')->get();
    }

    public function headings(): array
    {
        if ($this->headers) {
            return $this->headers;
        }

        return [
            'نام',
            'نام-خانوادگی',
            'نام-پدر',
            'شماره-موبایل',
            'شماره-عضویت',
            'کد-ملی',
            'مبلغ-سهام',
            'تعداد-سهام',
            'مبلغ-سود-سهام-سال',
            'سود-سهام-پرداختی-سال'
        ];
    }

    public function map($user): array
    {
        if ($this->data) {
            return $user;
        }

        $latestCertificate = $user->shareCertificates()->latest('year')->first();

        return [
            $user->first_name ?? '',
            $user->last_name ?? '',
            $user->father_name ?? '',
            $user->mobile_number ?? '',
            $user->membership_number ?? '',
            $user->national_code ?? '',
            $latestCertificate ? number_format($latestCertificate->share_amount) : '',
            $latestCertificate ? number_format($latestCertificate->share_count) : '',
            $latestCertificate ? number_format($latestCertificate->annual_profit_amount) : '',
            $latestCertificate ? number_format($latestCertificate->annual_payment) : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }
} 