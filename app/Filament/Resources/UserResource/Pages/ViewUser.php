<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Notifications\RejectRegistration;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\UserResource\Pages\ListUsers;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Information')
                    ->description('Details of the user profile')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Full Name')
                            ->weight('bold')
                            ->size('lg')
                            ->icon('heroicon-o-user')
                            ->color('primary'),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage('Email copied to clipboard!')
                            ->color('gray'),
                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-o-phone')
                            ->formatStateUsing(fn ($state) => $state ? $state : 'N/A'),
                        TextEntry::make('passport_number')
                            ->label('Passport Number')
                            ->icon('heroicon-o-identification')
                            ->formatStateUsing(fn ($state) => $state ? strtoupper($state) : 'N/A'),
                        TextEntry::make('registration_code')
                            ->label('Registration Code')
                            ->icon('heroicon-o-key')
                            ->formatStateUsing(fn ($state) => $state ? strtoupper($state) : 'N/A'),
                    ])
                    ->columns(2)
                    ->compact(),
                Section::make('Status')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'verified' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'primary',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->size('lg'),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('verify_now')
                ->label('Verify Now')
                ->visible(fn () => $this->record->status !== 'verified' && $this->record->status !== 'rejected')
                ->action(function () {
                    $this->record->update(['status' => 'verified']);
                    Notification::make()
                        ->title('User Verified')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Confirm Verification')
                ->modalDescription('Are you sure you want to verify this user?')
                ->modalSubmitActionLabel('Yes, Verify')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            Actions\Action::make('reject_now')
                ->label('Reject Now')
                ->visible(fn () => $this->record->status !== 'rejected')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Reason for Rejection')
                        ->required()
                        ->maxLength(500)
                        ->placeholder('Please provide the reason for rejecting this user')
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'rejected',
                        'rejection_reason' => $data['rejection_reason']
                    ]);

                    // Check if user relationship exists and is notifiable
                    if ($this->record->user && method_exists($this->record->user, 'notify')) {
                        $this->record->user->notify(new RejectRegistration($data['rejection_reason']));
                    } else {

                        Notification::make()
                            ->title('Warning')
                            ->body('No user associated with this record. Rejection recorded, but no email sent.')
                            ->warning()
                            ->send();
                    }
                    // Admin notification
                    Notification::make()
                        ->title('User Rejected')
                        ->body("User rejected successfully. Reason: {$data['rejection_reason']}")
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Confirm Rejection')
                ->modalDescription('Please provide a reason for rejecting this user.')
                ->modalSubmitActionLabel('Yes, Reject')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
            Actions\Action::make('back_to_dashboard')
                ->label('Back to Dashboard')
                ->url(fn(): string => ListUsers::getUrl())
                ->icon('heroicon-s-chevron-left'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
            'infolist' => $this->infolist,
        ];
    }
}
