<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\ShareCertificate;
use App\Models\Notification;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestActivities extends BaseWidget
{
    protected static ?string $heading = 'آخرین فعالیت‌ها';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->latest('updated_at')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label('نام و نام خانوادگی')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('national_code')
                    ->label('کد ملی')
                    ->searchable(),

                TextColumn::make('membership_number')
                    ->label('شماره عضویت')
                    ->searchable(),

                BadgeColumn::make('is_active')
                    ->label('وضعیت')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn (bool $state): string => $state ? 'فعال' : 'غیرفعال'),

                TextColumn::make('updated_at')
                    ->label('آخرین بروزرسانی')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
