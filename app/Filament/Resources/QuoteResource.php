<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Quote;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\QuoteResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QuoteResource\RelationManagers;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Quote';
    protected static ?string $pluralModelLabel = 'Quotes';
    protected static ?string $navigationLabel = 'Quotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Quote Information')
                    ->schema([
                        Forms\Components\TextInput::make('quote_name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('area_of_operation')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Client Details')
                    ->schema([
                        Forms\Components\TextInput::make('client_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('client_contact')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('quote_status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'accepted' => 'Accepted',
                                'declined' => 'Declined',
                                'modified' => 'Modified',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quote_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client_email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('primary'),

                Tables\Columns\TextColumn::make('area_of_operation')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('quote_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'accepted' => 'success',
                        'declined' => 'danger',
                        'modified' => 'info',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        'accepted' => 'heroicon-o-hand-thumb-up',
                        'declined' => 'heroicon-o-hand-thumb-down',
                        'modified' => 'heroicon-o-pencil',
                    })
                    ->sortable()
                    ->action(
                        Tables\Actions\Action::make('updateStatus')
                            ->form([
                                Forms\Components\Select::make('quote_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'accepted' => 'Accepted',
                                        'declined' => 'Declined',
                                        'modified' => 'Modified',
                                    ])
                                    ->required()
                                    ->native(false)
                            ])
                            ->action(function ($record, $data) {
                                $record->update(['quote_status' => $data['quote_status']]);
                            })
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('quote_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'accepted' => 'Accepted',
                        'declined' => 'Declined',
                        'modified' => 'Modified',
                    ])
                    ->label('Status')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Quote Overview')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\Group::make()
                                    ->schema([
                                        Components\TextEntry::make('quote_name')
                                            ->label('Quote Name')
                                            ->weight('bold')
                                            ->icon('heroicon-o-document-text'),
                                        Components\TextEntry::make('company_name')
                                            ->label('Company')
                                            ->icon('heroicon-o-building-office'),
                                        Components\TextEntry::make('area_of_operation')
                                            ->label('Area')
                                            ->icon('heroicon-o-map'),
                                    ]),
                                Components\Group::make()
                                    ->schema([
                                        Components\TextEntry::make('client_email')
                                            ->label('Email')
                                            ->icon('heroicon-o-envelope'),
                                        Components\TextEntry::make('client_contact')
                                            ->label('Contact')
                                            ->icon('heroicon-o-phone'),
                                    ]),
                                Components\Group::make()
                                    ->schema([
                                        Components\TextEntry::make('quote_status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'accepted' => 'success',
                                                'declined' => 'danger',
                                                'modified' => 'info',
                                            })
                                            ->icon(fn (string $state): string => match ($state) {
                                                'pending' => 'heroicon-o-clock',
                                                'approved' => 'heroicon-o-check-circle',
                                                'rejected' => 'heroicon-o-x-circle',
                                                'accepted' => 'heroicon-o-hand-thumb-up',
                                                'declined' => 'heroicon-o-hand-thumb-down',
                                                'modified' => 'heroicon-o-pencil',
                                            }),
                                        Components\TextEntry::make('created_at')
                                            ->label('Created')
                                            ->dateTime()
                                            ->icon('heroicon-o-calendar'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                Components\Section::make('Status History')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Components\Actions::make([
                            Components\Actions\Action::make('updateStatus')
                                ->label('Change Status')
                                ->icon('heroicon-o-arrow-path')
                                ->form([
                                    Forms\Components\Select::make('quote_status')
                                        ->options([
                                            'pending' => 'Pending',
                                            'approved' => 'Approved',
                                            'rejected' => 'Rejected',
                                            'accepted' => 'Accepted',
                                            'declined' => 'Declined',
                                            'modified' => 'Modified',
                                        ])
                                        ->required()
                                        ->native(false)
                                ])
                                ->action(function ($record, $data) {
                                    $record->update(['quote_status' => $data['quote_status']]);
                                })
                                ->modalHeading('Update Quote Status')
                                ->modalSubmitActionLabel('Save Changes'),
                        ])
                        ->alignCenter(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view' => Pages\ViewQuote::route('/{record}'),

        ];
    }
}
