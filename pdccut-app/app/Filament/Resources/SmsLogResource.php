<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsLogResource\Pages;
use App\Filament\Resources\SmsLogResource\RelationManagers;
use App\Models\SmsLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmsLogResource extends Resource
{
    protected static ?string $model = SmsLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'پیامک‌ها';
    protected static ?string $modelLabel = 'پیامک';
    protected static ?string $pluralModelLabel = 'پیامک‌ها';
    protected static ?string $navigationGroup = 'مدیریت سیستم';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات اصلی')
                    ->schema([
                        Forms\Components\TextInput::make('national_code')
                            ->label('کد ملی')
                            ->maxLength(10)
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('mobile_number')
                            ->label('شماره موبایل')
                            ->disabled(),
                        
                        Forms\Components\Select::make('type')
                            ->label('نوع پیامک')
                            ->options([
                                'otp' => 'کد تایید',
                                'notification' => 'اعلان',
                            ])
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('otp_code')
                            ->label('کد OTP')
                            ->visible(fn ($record) => $record?->type === 'otp')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('message')
                            ->label('متن پیام')
                            ->visible(fn ($record) => $record?->type === 'notification')
                            ->disabled(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('وضعیت ارسال')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('وضعیت')
                            ->options([
                                'pending' => 'در انتظار',
                                'sent' => 'ارسال شده',
                                'failed' => 'ناموفق',
                                'delivered' => 'تحویل شده',
                            ])
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('provider')
                            ->label('ارائه‌دهنده')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('provider_response_id')
                            ->label('شناسه پاسخ ارائه‌دهنده')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('provider_response')
                            ->label('پاسخ ارائه‌دهنده')
                            ->disabled(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('اطلاعات فنی')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP')
                            ->disabled(),
                        
                        Forms\Components\KeyValue::make('metadata')
                            ->label('اطلاعات اضافی')
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('sent_at')
                            ->label('زمان ارسال')
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('زمان تحویل')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('national_code')
                    ->label('کد ملی')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search, convertDigits: true);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereRaw("national_code{$collate} LIKE ?", ['%' . $normalized . '%']);
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('mobile_number')
                    ->label('شماره موبایل')
                    ->formatStateUsing(fn ($record) => $record?->formatted_mobile ?? $record?->mobile_number ?? '')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search, convertDigits: true);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereRaw("mobile_number{$collate} LIKE ?", ['%' . $normalized . '%']);
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'otp' => 'primary',
                        'notification' => 'success',
                        default => 'secondary'
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'otp' => 'کد تایید',
                        'notification' => 'اعلان',
                        default => $state
                    }),
                
                Tables\Columns\TextColumn::make('otp_code')
                    ->label('کد OTP')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search, convertDigits: true);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereRaw("otp_code{$collate} LIKE ?", ['%' . $normalized . '%']);
                    })
                    ->visible(fn ($record) => $record?->type === 'otp'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->color(fn ($record) => $record?->status_color ?? 'secondary')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'در انتظار',
                        'sent' => 'ارسال شده',
                        'failed' => 'ناموفق',
                        'delivered' => 'تحویل شده',
                        default => $state
                    }),
                
                Tables\Columns\TextColumn::make('provider')
                    ->label('ارائه‌دهنده')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->visible(fn () => auth()->user()?->is_admin ?? false),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ارسال')
                    ->dateTime()
                    ->sortable()
                    ->searchable(false),
                
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('زمان ارسال')
                    ->dateTime()
                    ->sortable()
                    ->visible(fn ($record) => $record?->sent_at)
                    ->searchable(false),
                
                Tables\Columns\TextColumn::make('delivered_at')
                    ->label('زمان تحویل')
                    ->dateTime()
                    ->sortable()
                    ->visible(fn ($record) => $record?->delivered_at)
                    ->searchable(false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع پیامک')
                    ->options([
                        'otp' => 'کد تایید',
                        'notification' => 'اعلان',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'sent' => 'ارسال شده',
                        'failed' => 'ناموفق',
                        'delivered' => 'تحویل شده',
                    ]),
                
                Tables\Filters\Filter::make('date_range')
                    ->label('محدوده تاریخ')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('از تاریخ'),
                        Forms\Components\DatePicker::make('to')
                            ->label('تا تاریخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('مشاهده')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\Action::make('resend')
                    ->label('ارسال مجدد')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record) => $record?->status === 'failed')
                    ->action(function ($record) {
                        // TODO: Implement resend logic
                        return redirect()->back()->with('success', 'درخواست ارسال مجدد ثبت شد.');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف انتخاب شده‌ها'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    // Normalize Persian/Arabic characters, remove ZWNJ, and optionally convert Persian digits to English
    protected static function normalizeSearchString(string $value, bool $convertDigits = false): string
    {
        $map = [
            'ي' => 'ی',
            'ك' => 'ک',
            "\xE2\x80\x8C" => ' ',
        ];
        $normalized = strtr($value, $map);

        if ($convertDigits) {
            $digitsFa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
            $digitsEn = ['0','1','2','3','4','5','6','7','8','9'];
            $normalized = str_replace($digitsFa, $digitsEn, $normalized);
        }

        return $normalized;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmsLogs::route('/'),
            'view' => Pages\ViewSmsLog::route('/{record}'),
        ];
    }
}
