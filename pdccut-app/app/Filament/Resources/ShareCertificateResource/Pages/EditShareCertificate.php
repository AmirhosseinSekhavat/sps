<?php

namespace App\Filament\Resources\ShareCertificateResource\Pages;

use App\Filament\Resources\ShareCertificateResource;
use App\Services\PdfService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class EditShareCertificate extends EditRecord
{
    protected static string $resource = ShareCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_pdf')
                ->label('تولید PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn () => !$this->record->pdf_path || !Storage::disk('public')->exists($this->record->pdf_path))
                ->action(function () {
                    try {
                        $pdfService = app(PdfService::class);
                        $path = $pdfService->generateShareCertificate(
                            $this->record->user,
                            $this->record,
                            $this->record->year
                        );
                        
                        $this->record->update(['pdf_path' => $path]);
                        
                        Notification::make()
                            ->title('PDF با موفقیت تولید شد')
                            ->success()
                            ->send();
                            
                        $this->refreshFormData(['pdf_path']);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('خطا در تولید PDF')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            
            Actions\Action::make('regenerate_pdf')
                ->label('بازتولید PDF')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => $this->record->pdf_path && Storage::disk('public')->exists($this->record->pdf_path))
                ->action(function () {
                    try {
                        // Delete existing PDF
                        if ($this->record->pdf_path && Storage::disk('public')->exists($this->record->pdf_path)) {
                            Storage::disk('public')->delete($this->record->pdf_path);
                        }
                        
                        $pdfService = app(PdfService::class);
                        $path = $pdfService->generateShareCertificate(
                            $this->record->user,
                            $this->record,
                            $this->record->year
                        );
                        
                        $this->record->update(['pdf_path' => $path]);
                        
                        Notification::make()
                            ->title('PDF با موفقیت بازتولید شد')
                            ->success()
                            ->send();
                            
                        $this->refreshFormData(['pdf_path']);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('خطا در بازتولید PDF')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            
            Actions\Action::make('delete_pdf')
                ->label('حذف PDF')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn () => $this->record->pdf_path && Storage::disk('public')->exists($this->record->pdf_path))
                ->requiresConfirmation()
                ->modalHeading('حذف فایل PDF')
                ->modalDescription('آیا مطمئن هستید که می‌خواهید فایل PDF را حذف کنید؟')
                ->action(function () {
                    if ($this->record->pdf_path && Storage::disk('public')->exists($this->record->pdf_path)) {
                        Storage::disk('public')->delete($this->record->pdf_path);
                        $this->record->update(['pdf_path' => null]);
                        
                        Notification::make()
                            ->title('فایل PDF حذف شد')
                            ->success()
                            ->send();
                            
                        $this->refreshFormData(['pdf_path']);
                    }
                }),
                
            Actions\Action::make('view_pdf')
                ->label('مشاهده PDF')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->visible(fn () => $this->record->pdf_path && Storage::disk('public')->exists($this->record->pdf_path))
                ->url(fn () => route('admin.user.certificates.view', [
                    'national_code' => $this->record->user->national_code,
                    'year' => $this->record->year
                ]))
                ->openUrlInNewTab(),
                
            Actions\Action::make('download_pdf')
                ->label('دانلود PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->visible(fn () => $this->record->pdf_path && Storage::disk('public')->exists($this->record->pdf_path))
                ->url(fn () => route('admin.user.certificates.download', [
                    'national_code' => $this->record->user->national_code,
                    'year' => $this->record->year
                ])),
            
            Actions\DeleteAction::make(),
        ];
    }
}
