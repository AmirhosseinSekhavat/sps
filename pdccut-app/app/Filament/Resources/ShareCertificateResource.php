<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShareCertificateResource\Pages;
use App\Filament\Resources\ShareCertificateResource\RelationManagers;
use App\Models\ShareCertificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class ShareCertificateResource extends Resource
{
    protected static ?string $model = ShareCertificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'برگه‌های سهام';
    
    protected static ?string $modelLabel = 'برگه سهام';
    
    protected static ?string $pluralModelLabel = 'برگه‌های سهام';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات کاربر')
            ->schema([
                Forms\Components\Select::make('user_id')
                            ->label('کاربر')
                            ->relationship('user', 'name')
                            ->searchable(['first_name', 'last_name', 'national_code', 'membership_number', 'mobile_number'])
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\User::query()
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('national_code', 'like', "%{$search}%")
                                    ->orWhere('membership_number', 'like', "%{$search}%")
                                    ->orWhere('mobile_number', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        return [$user->id => $user->name . ' - ' . $user->national_code . ' - ' . $user->membership_number];
                                    });
                            })
                            ->required(),
                    ]),
                
                Section::make('اطلاعات سال مالی')
                    ->schema([
                Forms\Components\TextInput::make('year')
                            ->label('سال مالی')
                            ->numeric()
                    ->required()
                            ->default(date('Y')),
                    ]),
                
                Section::make('اطلاعات مالی')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                Forms\Components\TextInput::make('share_amount')
                                    ->label('مبلغ سهام')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('ریال'),
                Forms\Components\TextInput::make('share_count')
                                    ->label('تعداد سهام')
                                    ->numeric()
                                    ->default(0),
                Forms\Components\TextInput::make('annual_profit_amount')
                                    ->label('مبلغ سود سهام سال')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('ریال'),

                Forms\Components\TextInput::make('annual_payment')
                                    ->label('سود سهام پرداختی سال')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('ریال'),
                            ]),
                    ]),
                
                Section::make('فایل PDF')
                    ->schema([
                        Forms\Components\TextInput::make('pdf_path')
                            ->label('مسیر فایل PDF')
                            ->helperText('مسیر فایل PDF تولید شده')
                            ->default(function ($get) {
                                $userId = $get('user_id');
                                $year = $get('year');
                                
                                if ($userId && $year) {
                                    $user = \App\Models\User::find($userId);
                                    if ($user) {
                                        return "certificates/{$user->national_code}_{$year}.pdf";
                                    }
                                }
                                
                                return null;
                            })
                            ->disabled()
                            ->dehydrated(false)
                            ->reactive(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('نام کاربر')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.national_code')
                    ->label('کد ملی')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('سال مالی')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('share_amount')
                    ->label('مبلغ سهام')
                    ->numeric()
                    ->sortable()
                    ->money('IRR'),
                Tables\Columns\TextColumn::make('share_count')
                    ->label('تعداد سهام')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('annual_profit_amount')
                    ->label('سود سالانه')
                    ->numeric()
                    ->sortable()
                    ->money('IRR'),

                Tables\Columns\TextColumn::make('annual_payment')
                    ->label('سود سهام پرداختی سال')
                    ->numeric()
                    ->sortable()
                    ->money('IRR'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('سال مالی')
                    ->options(function () {
                        $years = ShareCertificate::distinct()->pluck('year')->sort()->reverse();
                        $options = [];
                        foreach ($years as $year) {
                            if ($year !== null) {
                                $options[$year] = $year;
                            }
                        }
                        return $options;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        $year = $data['value'] ?? null;
                        if ($year !== null && $year !== '') {
                            session(['sc_year' => (int) $year]);
                            return $query->where('year', (int) $year);
                        }
                        session()->forget('sc_year');
                        return $query;
                    }),
                Tables\Filters\SelectFilter::make('user')
                    ->label('کاربر')
                    ->relationship('user', 'name')
                    ->searchable(['first_name', 'last_name', 'national_code', 'membership_number', 'mobile_number'])
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\User::query()
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('national_code', 'like', "%{$search}%")
                            ->orWhere('membership_number', 'like', "%{$search}%")
                            ->orWhere('mobile_number', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($user) {
                                return [$user->id => $user->name . ' - ' . $user->national_code . ' - ' . $user->membership_number];
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (ShareCertificate $record) {
                        // Check if share certificate exists for this year
                        $existingCertificate = \App\Models\ShareCertificate::where('user_id', $record->user_id)
                            ->where('year', $record->year)
                            ->first();
                        
                        if (!$existingCertificate) {
                            throw new \Exception("برگه سهام برای کاربر {$record->user->name} در سال {$record->year} وجود ندارد!");
                        }
                    })
                    ->modalHeading('حذف برگه سهام')
                    ->modalDescription(fn (ShareCertificate $record) => "آیا مطمئن هستید که می‌خواهید برگه سهام کاربر {$record->user->name} برای سال {$record->year} را حذف کنید؟")
                    ->modalSubmitActionLabel('بله، حذف کن')
                    ->modalCancelActionLabel('انصراف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                // Check if share certificate exists for this year
                                $existingCertificate = \App\Models\ShareCertificate::where('user_id', $record->user_id)
                                    ->where('year', $record->year)
                                    ->first();
                                
                                if (!$existingCertificate) {
                                    throw new \Exception("برگه سهام برای کاربر {$record->user->name} در سال {$record->year} وجود ندارد!");
                                }
                            }
                        })
                        ->modalHeading('حذف برگه‌های سهام')
                        ->modalDescription('آیا مطمئن هستید که می‌خواهید برگه‌های سهام انتخاب شده را حذف کنید؟')
                        ->modalSubmitActionLabel('بله، حذف کن')
                        ->modalCancelActionLabel('انصراف'),
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
            'index' => Pages\ListShareCertificates::route('/'),
            'create' => Pages\CreateShareCertificate::route('/create'),
            'edit' => Pages\EditShareCertificate::route('/{record}/edit'),
        ];
    }
}
