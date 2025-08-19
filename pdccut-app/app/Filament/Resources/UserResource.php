<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'کاربران';
    
    protected static ?string $modelLabel = 'کاربر';
    
    protected static ?string $pluralModelLabel = 'کاربران';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات شخصی')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('نام')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name')
                                    ->label('نام خانوادگی')
                                    ->required(),
                                Forms\Components\TextInput::make('father_name')
                                    ->label('نام پدر')
                                    ->required(),
                                Forms\Components\TextInput::make('mobile_number')
                                    ->label('شماره موبایل')
                                    ->tel()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'این شماره موبایل قبلاً در سیستم ثبت شده است.',
                                    ]),
                                Forms\Components\TextInput::make('membership_number')
                                    ->label('شماره عضویت')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'این شماره عضویت قبلاً در سیستم ثبت شده است.',
                                    ]),
                                Forms\Components\TextInput::make('national_code')
                                    ->label('کد ملی')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'این کد ملی قبلاً در سیستم ثبت شده است.',
                                    ]),
                            ]),
                    ]),
                
                Section::make('اطلاعات مالی (آخرین سال)')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('latest_share_amount')
                                    ->label('مبلغ سهام')
                                    ->content(fn (?User $record) => $record && $record->shareCertificates()->exists()
                                        ? number_format(optional($record->shareCertificates()->orderByDesc('year')->first())->share_amount) . ' ریال'
                                        : '—'),
                                Forms\Components\Placeholder::make('latest_share_count')
                                    ->label('تعداد سهام')
                                    ->content(fn (?User $record) => $record && $record->shareCertificates()->exists()
                                        ? number_format(optional($record->shareCertificates()->orderByDesc('year')->first())->share_count) . ' عدد'
                                        : '—'),
                                Forms\Components\Placeholder::make('latest_annual_profit_amount')
                                    ->label('مبلغ سود سهام سال')
                                    ->content(fn (?User $record) => $record && $record->shareCertificates()->exists()
                                        ? number_format(optional($record->shareCertificates()->orderByDesc('year')->first())->annual_profit_amount) . ' ریال'
                                        : '—'),
                                Forms\Components\Placeholder::make('latest_annual_payment')
                                    ->label('سود سهام پرداختی سال')
                                    ->content(fn (?User $record) => $record && $record->shareCertificates()->exists()
                                        ? number_format(optional($record->shareCertificates()->orderByDesc('year')->first())->annual_payment) . ' ریال'
                                        : '—'),
                            ]),
                    ]),
                
                Section::make('تنظیمات')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),
                        Forms\Components\TextInput::make('password')
                            ->label('رمز عبور')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('نام کامل')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('national_code')
                    ->label('کد ملی')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile_number')
                    ->label('موبایل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('membership_number')
                    ->label('شماره عضویت')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latest_share_amount')
                    ->label('مبلغ سهام')
                    ->getStateUsing(fn (User $record) => optional($record->shareCertificates()->orderByDesc('year')->first())->share_amount)
                    ->formatStateUsing(fn ($state) => is_null($state) ? '—' : number_format($state) . ' ریال')
                    ->sortable(false),
                Tables\Columns\TextColumn::make('latest_share_count')
                    ->label('تعداد سهام')
                    ->getStateUsing(fn (User $record) => optional($record->shareCertificates()->orderByDesc('year')->first())->share_count)
                    ->formatStateUsing(fn ($state) => is_null($state) ? '—' : number_format($state))
                    ->sortable(false),
                Tables\Columns\TextColumn::make('latest_annual_profit_amount')
                    ->label('مبلغ سود سهام سال')
                    ->getStateUsing(fn (User $record) => optional($record->shareCertificates()->orderByDesc('year')->first())->annual_profit_amount)
                    ->formatStateUsing(fn ($state) => is_null($state) ? '—' : number_format($state) . ' ریال')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('latest_annual_payment')
                    ->label('سود سهام پرداختی سال')
                    ->getStateUsing(fn (User $record) => optional($record->shareCertificates()->orderByDesc('year')->first())->annual_payment)
                    ->formatStateUsing(fn ($state) => is_null($state) ? '—' : number_format($state) . ' ریال')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('آخرین ورود')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
                Tables\Filters\Filter::make('invalid_national_code')
                    ->label('کد ملی نامعتبر')
                    ->query(fn (Builder $query) => $query->where(function ($q) {
                        $q->whereNull('national_code')
                          ->orWhere('national_code', '=','')
                          ->orWhereRaw('length(national_code) <> 10');
                    })),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (User $record): string => $record->national_code ? route('admin.user.show', $record->national_code) : '#')
                    ->openUrlInNewTab()
                    ->label('مشاهده')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (User $record): bool => !empty($record->national_code)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (User $record) {
                        $shareCertificates = $record->shareCertificates;
                        if ($shareCertificates->count() > 0) {
                            $years = $shareCertificates->pluck('year')->implode(', ');
                            throw new \Exception("این کاربر دارای برگه سهام در سال‌های زیر است: {$years}. ابتدا برگه‌های سهام را حذف کنید.");
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                $shareCertificates = $record->shareCertificates;
                                if ($shareCertificates->count() > 0) {
                                    $years = $shareCertificates->pluck('year')->implode(', ');
                                    throw new \Exception("کاربر {$record->name} دارای برگه سهام در سال‌های زیر است: {$years}. ابتدا برگه‌های سهام را حذف کنید.");
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
