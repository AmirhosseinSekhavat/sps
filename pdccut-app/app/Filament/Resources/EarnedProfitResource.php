<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EarnedProfitResource\Pages;
use App\Filament\Resources\EarnedProfitResource\RelationManagers;
use App\Models\EarnedProfit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class EarnedProfitResource extends Resource
{
    protected static ?string $model = EarnedProfit::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationLabel = 'سودهای اکتسابی';
    
    protected static ?string $modelLabel = 'سود اکتسابی';
    
    protected static ?string $pluralModelLabel = 'سودهای اکتسابی';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات سود')
            ->schema([
                Forms\Components\TextInput::make('year')
                            ->label('سال مالی')
                            ->numeric()
                    ->required()
                            ->default(date('Y')),
                Forms\Components\TextInput::make('profit_type')
                            ->label('نوع سود')
                            ->required()
                            ->placeholder('مثال: سود عملیاتی، سود سرمایه‌گذاری'),
                Forms\Components\TextInput::make('amount')
                            ->label('مبلغ')
                            ->numeric()
                    ->required()
                            ->suffix('ریال'),
                Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(3)
                            ->placeholder('توضیحات مربوط به این سود'),
                Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('سال مالی')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profit_type')
                    ->label('نوع سود')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereRaw("REPLACE(REPLACE(profit_type,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%']);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->sortable()
                    ->money('IRR'),
                Tables\Columns\TextColumn::make('description')
                    ->label('توضیحات')
                    ->limit(50)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereRaw("REPLACE(REPLACE(description,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%']);
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('سال مالی')
                    ->options(function () {
                        $years = EarnedProfit::distinct()->pluck('year')->sort()->reverse();
                        return $years->mapWithKeys(function ($year) {
                            return [$year => (string) $year];
                        })->toArray();
                    }),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListEarnedProfits::route('/'),
            'create' => Pages\CreateEarnedProfit::route('/create'),
            'edit' => Pages\EditEarnedProfit::route('/{record}/edit'),
        ];
    }
}
