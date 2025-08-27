<?php

namespace App\Imports;

use App\Models\User;
use App\Models\ShareCertificate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, WithCalculatedFormulas
{
    use Importable;

    protected $financialYear;

    public function __construct($financialYear)
    {
        $this->financialYear = $financialYear;
        HeadingRowFormatter::default('none');
        if (app()->environment('local')) {
        Log::info('UsersImport initialized with financial year: ' . $financialYear);
        }
    }

    public function model(array $row)
    {
        if (app()->environment('local')) {
            Log::info('Processing Excel row');
        }

        $firstName = $row['نام'] ?? null;
        $lastName = $row['نام-خانوادگی'] ?? null;
        $fatherName = $row['نام-پدر'] ?? null;
        $mobile = $this->normalizeMobile($row['شماره-موبایل'] ?? null);
        $membership = $this->normalizeMembership($row['شماره-عضویت'] ?? null);
        $nationalCode = $this->normalizeNationalCode($row['کد-ملی'] ?? null);

        if (empty($nationalCode) && empty($membership) && empty($mobile)) {
            Log::warning('Skipping row - no identifier provided');
            return null;
        }

        // Try to find existing user by priority: national code -> membership -> mobile
        $user = null;
        if (!empty($nationalCode)) {
            $user = User::where('national_code', $nationalCode)->first();
        }
        if (!$user && !empty($membership)) {
            $user = User::where('membership_number', $membership)->first();
        }
        if (!$user && !empty($mobile)) {
            $user = User::where('mobile_number', $mobile)->first();
        }

        if ($user) {
            if (app()->environment('local')) {
                Log::info('Updating existing user', ['user_id' => $user->id]);
            }

            $user->update([
                'first_name' => $firstName ?? $user->first_name,
                'last_name' => $lastName ?? $user->last_name,
                'father_name' => $fatherName ?? $user->father_name,
                'mobile_number' => $mobile ?? $user->mobile_number,
                'membership_number' => $membership ?? $user->membership_number,
                'national_code' => $nationalCode ?? $user->national_code,
            ]);

            $this->createOrUpdateShareCertificate($user, $row);
            return null;
        }

        if (app()->environment('local')) {
            Log::info('Creating new user');
        }

        // Guard against unique collisions explicitly before insert
        if (!empty($membership) && User::where('membership_number', $membership)->exists()) {
            throw new \Exception("شماره عضویت {$membership} از قبل وجود دارد. ردیف با کد ملی {$nationalCode} پردازش نشد.");
        }
        if (!empty($mobile) && User::where('mobile_number', $mobile)->exists()) {
            throw new \Exception("شماره موبایل {$mobile} از قبل وجود دارد. ردیف با کد ملی {$nationalCode} پردازش نشد.");
        }
        if (!empty($nationalCode) && User::where('national_code', $nationalCode)->exists()) {
            throw new \Exception("کد ملی {$nationalCode} از قبل وجود دارد. ردیف پردازش نشد.");
        }

        $newUser = new User([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'father_name' => $fatherName,
            'mobile_number' => $mobile,
            'membership_number' => $membership,
            'national_code' => $nationalCode,
            'is_active' => true,
        ]);

        $newUser->save();

        if (app()->environment('local')) {
            Log::info('New user created', ['user_id' => $newUser->id]);
        }

        $this->createOrUpdateShareCertificate($newUser, $row);

        return $newUser;
    }

    protected function createOrUpdateShareCertificate(User $user, array $row)
    {
        if (app()->environment('local')) {
            Log::info('Creating/updating share certificate', ['user_id' => $user->id, 'year' => $this->financialYear]);
        }
        try {
            $certificate = ShareCertificate::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'year' => $this->financialYear,
                ],
                [
                    'share_amount' => $this->parseNumericValue($row['مبلغ-سهام'] ?? 0),
                    'share_count' => $this->parseNumericValue($row['تعداد-سهام'] ?? 0),
                    'annual_profit_amount' => $this->parseNumericValue($row['مبلغ-سود-سهام-سال'] ?? 0),
                    'annual_payment' => $this->parseNumericValue($row['سود-سهام-پرداختی-سال'] ?? 0),
                ]
            );
            if (app()->environment('local')) {
                Log::info('Share certificate created/updated', ['certificate_id' => $certificate->id]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating share certificate', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function parseNumericValue($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
        if (empty($value)) {
            return 0;
        }
        $persianNumbers = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $englishNumbers = ['0','1','2','3','4','5','6','7','8','9'];
        $value = str_replace($persianNumbers, $englishNumbers, $value);
        $value = preg_replace('/[^0-9.]/', '', (string) $value);
        if (app()->environment('local')) {
            Log::info('Parsed numeric value');
        }
        return is_numeric($value) ? $value : 0;
    }

    protected function normalizeMobile($value): ?string
    {
        if (empty($value)) return null;
        $digits = preg_replace('/\D+/', '', (string) $value);
        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $digits = '0' . $digits; // make 11-digit 09xxxxxxxxx
        }
        return $digits ?: null;
    }

    protected function normalizeMembership($value): ?string
    {
        if ($value === null || $value === '') return null;
        return (string) trim((string) $value);
    }

    protected function normalizeNationalCode($value): ?string
    {
        if ($value === null || $value === '') return null;
        // convert Persian digits
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $latin =   ['0','1','2','3','4','5','6','7','8','9'];
        $digits = str_replace($persian, $latin, (string) $value);
        // keep only digits
        $digits = preg_replace('/\D+/', '', $digits);
        // treat all-zeros or wrong length as null
        if (strlen($digits) !== 10 || preg_match('/^0+$/', $digits)) {
            Log::info('National code normalized to NULL due to invalid value');
            return null;
        }
        return $digits;
    }

    public function rules(): array
    {
        return [
            'نام' => 'nullable',
            'نام-خانوادگی' => 'nullable',
            'نام-پدر' => 'nullable',
            'شماره-موبایل' => 'nullable',
            'شماره-عضویت' => 'nullable',
            'کد-ملی' => 'nullable',
            'مبلغ-سهام' => 'nullable',
            'تعداد-سهام' => 'nullable',
            'مبلغ-سود-سهام-سال' => 'nullable',
            'سود-سهام-پرداختی-سال' => 'nullable',
        ];
    }
}
