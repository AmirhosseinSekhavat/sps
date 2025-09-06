<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'تنظیمات';
    
    protected static ?string $modelLabel = 'تنظیم';
    
    protected static ?string $pluralModelLabel = 'تنظیمات';

    // Hide from admin navigation
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات تنظیم')
            ->schema([
                Forms\Components\TextInput::make('key')
                            ->label('کلید')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('مثال: logo, company_name'),
                        Forms\Components\TextInput::make('value')
                            ->label('مقدار')
                            ->required()
                            ->placeholder('مقدار تنظیم'),
                        Forms\Components\Select::make('type')
                            ->label('نوع')
                            ->options([
                                'string' => 'متن',
                                'file' => 'فایل',
                                'boolean' => 'بله/خیر',
                                'number' => 'عدد',
                                'json' => 'JSON',
                            ])
                            ->default('string')
                    ->required(),
                        Forms\Components\Select::make('group')
                            ->label('گروه')
                            ->options([
                                'general' => 'عمومی',
                                'appearance' => 'ظاهر',
                                'business' => 'کسب و کار',
                                'system' => 'سیستم',
                            ])
                            ->default('general')
                    ->required(),
                Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(3)
                            ->placeholder('توضیحات مربوط به این تنظیم'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('کلید')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereRaw("REPLACE(REPLACE(`key`,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%']);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('مقدار')
                    ->limit(50)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereRaw("REPLACE(REPLACE(`value`,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%']);
                    }),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'gray',
                        'file' => 'blue',
                        'boolean' => 'green',
                        'number' => 'yellow',
                        'json' => 'purple',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('group')
                    ->label('گروه')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'appearance' => 'blue',
                        'business' => 'green',
                        'system' => 'red',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('توضیحات')
                    ->limit(50),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخرین بروزرسانی')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع')
                    ->options([
                        'string' => 'متن',
                        'file' => 'فایل',
                        'boolean' => 'بله/خیر',
                        'number' => 'عدد',
                        'json' => 'JSON',
                    ]),
                Tables\Filters\SelectFilter::make('group')
                    ->label('گروه')
                    ->options([
                        'general' => 'عمومی',
                        'appearance' => 'ظاهر',
                        'business' => 'کسب و کار',
                        'system' => 'سیستم',
                    ]),
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
            ->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
