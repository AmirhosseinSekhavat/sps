<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static ?string $navigationLabel = 'اعلان‌ها';
    
    protected static ?string $modelLabel = 'اعلان';
    
    protected static ?string $pluralModelLabel = 'اعلان‌ها';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات اعلان')
            ->schema([
                				Forms\Components\Select::make('send_to')
					->label('ارسال به')
					->options([
						'single' => 'یک کاربر مشخص',
						'all' => 'همه کاربران',
						'active' => 'فقط کاربران فعال',
					])
					->default('single')
					->live()
					->dehydrated(false)
					->required(),
                Forms\Components\Select::make('user_id')
                            ->label('کاربر')
                            ->relationship('user', 'name')
                            ->searchable(['first_name', 'last_name', 'mobile_number', 'national_code'])
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->getSearchResultsUsing(function (string $search) {
                                $normalized = self::normalizeSearchString($search, convertDigits: true);
                                $driver = \DB::connection()->getDriverName();
                                $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                                return \App\Models\User::query()
                                    ->whereRaw("REPLACE(REPLACE(first_name,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%'])
                                    ->orWhereRaw("REPLACE(REPLACE(last_name,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%'])
                                    ->orWhereRaw("mobile_number{$collate} LIKE ?", ['%' . $normalized . '%'])
                                    ->orWhereRaw("national_code{$collate} LIKE ?", ['%' . $normalized . '%'])
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        return [$user->id => $user->name];
                                    });
                            })
                        ->hidden(fn ($get) => $get('send_to') !== 'single')
                        ->required(fn ($get) => $get('send_to') === 'single'),
                Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255),
                Forms\Components\Textarea::make('message')
                            ->label('پیام')
                    ->required()
                            ->rows(4),
                Forms\Components\Toggle::make('is_read')
                            ->label('خوانده شده')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('read_at')
                            ->label('تاریخ خوانده شدن')
                            ->visible(fn ($get) => $get('is_read')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereHas('user', function (Builder $uq) use ($normalized, $collate) {
                            $uq->whereRaw("REPLACE(REPLACE(first_name,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%'])
                               ->orWhereRaw("REPLACE(REPLACE(last_name,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%'])
                               ->orWhereRaw("REPLACE(REPLACE(name,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%']);
                        });
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $normalized = self::normalizeSearchString($search);
                        $driver = $query->getConnection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return $query->whereRaw("REPLACE(REPLACE(title,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%']);
                    })
                    ->limit(50),

                Tables\Columns\IconColumn::make('is_read')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('تاریخ خوانده شدن')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('کاربر')
                    ->relationship('user', 'name')
                    ->searchable(['first_name', 'last_name', 'mobile_number', 'national_code'])
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->getSearchResultsUsing(function (string $search) {
                        $normalized = self::normalizeSearchString($search, convertDigits: true);
                        $driver = \DB::connection()->getDriverName();
                        $collate = $driver === 'mysql' ? ' COLLATE utf8mb4_unicode_ci' : '';
                        return \App\Models\User::query()
                            ->whereRaw("REPLACE(REPLACE(first_name,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%'])
                            ->orWhereRaw("REPLACE(REPLACE(last_name,'ي','ی'),'ك','ک'){$collate} LIKE ?", ['%' . $normalized . '%'])
                            ->orWhereRaw("mobile_number{$collate} LIKE ?", ['%' . $normalized . '%'])
                            ->orWhereRaw("national_code{$collate} LIKE ?", ['%' . $normalized . '%'])
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($user) {
                                return [$user->id => $user->name];
                            });
                    }),
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('وضعیت')
                    ->placeholder('همه')
                    ->trueLabel('خوانده شده')
                    ->falseLabel('نخوانده شده'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_message')
                    ->label('مشاهده متن')
                    ->icon('heroicon-o-document-text')
                    ->modalHeading('متن اعلان')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('بستن')
                    ->modalContent(fn (\App\Models\Notification $record) => view('filament.modals.notification-message', [
                        'title' => $record->title,
                        'message' => $record->message,
                    ])),
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
