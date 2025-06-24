<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Document;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Actions\Action;
use App\Filament\Resources\DocumentResource\Pages;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->label('User'),
                    ]),
                Forms\Components\Section::make('Documents')
                    ->schema([
                        Forms\Components\FileUpload::make('id_copy')
                            ->label('ID Copy')
                            ->directory('documents/id_copies')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                        Forms\Components\Select::make('id_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'declined' => 'Declined',
                            ])
                            ->default('pending')
                            ->label('ID Status'),
                        Forms\Components\FileUpload::make('coida_certificate')
                            ->label('COIDA Certificate')
                            ->directory('documents/coida_certificates')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                        Forms\Components\Select::make('coida_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'declined' => 'Declined',
                            ])
                            ->default('pending')
                            ->label('COIDA Status'),
                        Forms\Components\FileUpload::make('uif_certificate')
                            ->label('UIF Certificate')
                            ->directory('documents/uif_certificates')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                        Forms\Components\Select::make('uif_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'declined' => 'Declined',
                            ])
                            ->default('pending')
                            ->label('UIF Status'),
                        Forms\Components\FileUpload::make('psira_certificate')
                            ->label('PSIRA Certificate')
                            ->directory('documents/psira_certificates')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                        Forms\Components\Select::make('psira_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'declined' => 'Declined',
                            ])
                            ->default('pending')
                            ->label('PSIRA Status'),
                        Forms\Components\FileUpload::make('firearm_competency')
                            ->label('Firearm Competency')
                            ->directory('documents/firearm_competencies')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                        Forms\Components\Select::make('firearm_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'declined' => 'Declined',
                            ])
                            ->default('pending')
                            ->label('Firearm Status'),
                        Forms\Components\FileUpload::make('statement_of_results')
                            ->label('Statement of Results')
                            ->directory('documents/statements')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
                        Forms\Components\Select::make('statement_status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'declined' => 'Declined',
                            ])
                            ->default('pending')
                            ->label('Statement Status'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.role')
                    ->label('Role')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'declined' => 'Declined',
                    ])
                    ->label('ID Status'),
                Tables\Filters\SelectFilter::make('coida_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'declined' => 'Declined',
                    ])
                    ->label('COIDA Status'),
                Tables\Filters\SelectFilter::make('uif_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'declined' => 'Declined',
                    ])
                    ->label('UIF Status'),
                Tables\Filters\SelectFilter::make('psira_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'declined' => 'Declined',
                    ])
                    ->label('PSIRA Status'),
                Tables\Filters\SelectFilter::make('firearm_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'declined' => 'Declined',
                    ])
                    ->label('Firearm Status'),
                Tables\Filters\SelectFilter::make('statement_status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'declined' => 'Declined',
                    ])
                    ->label('Statement Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('verify')
                    ->button()
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
                        $record->update([
                            'id_status' => 'verified',
                            'coida_status' => 'verified',
                            'uif_status' => 'verified',
                            'psira_status' => 'verified',
                            'firearm_status' => 'verified',
                            'statement_status' => 'verified',
                        ]);
                    })
                    ->label('Verify All'),
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
                // User Information Section
                Section::make('User Information')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Name')
                            ->weight('bold')
                            ->size('lg')
                            ->extraAttributes(['class' => 'text-gray-800 dark:text-gray-200']),
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->extraAttributes(['class' => 'text-gray-600 dark:text-gray-400']),
                        TextEntry::make('user.role')
                            ->label('Role')
                            ->badge()
                            ->color('primary')
                            ->extraAttributes(['class' => 'uppercase tracking-wide']),
                    ])
                    ->columns(3)
                    ->extraAttributes([
                        'class' => 'bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg p-6 mb-6 transition-all duration-300 hover:shadow-xl',
                    ]),

                // Documents Section
                Section::make('Documents')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // ID Copy Card
                                Group::make([
                                    ImageEntry::make('id_copy')
                                        ->disk('public')
                                        ->height(200)
                                        ->extraImgAttributes(['class' => 'rounded-lg shadow-md transition-transform duration-300 hover:scale-105']),
                                    TextEntry::make('id_status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'verified' => 'success',
                                            'declined' => 'danger',
                                            default => 'warning',
                                        })
                                        ->extraAttributes(['class' => 'mt-2']),
                                    Actions::make([
                                        Action::make('verifyId')
                                            ->label('Verify')
                                            ->button()
                                            ->color('success')
                                            ->extraAttributes(['class' => 'hover:bg-green-600 transition-colors duration-200'])
                                            ->requiresConfirmation()
                                            ->action(function (Document $record) {
                                                $record->id_status = 'verified';
                                                $record->save();
                                            }),
                                        Action::make('declineId')
                                            ->label('Decline')
                                            ->color('danger')
                                            ->button()
                                            ->extraAttributes(['class' => 'hover:bg-red-600 transition-colors duration-200'])
                                            ->requiresConfirmation()
                                            ->action(function (Document $record) {
                                                $record->id_status = 'declined';
                                                $record->save();
                                            }),
                                    ])->alignCenter(),
                                ])->extraAttributes(['class' => 'document-card bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 transition-all duration-300 hover:shadow-lg']),

                                // PSIRA Certificate Card
                                Group::make([
                                    ImageEntry::make('psira_certificate')
                                        ->disk('public')
                                        ->height(200)
                                        ->extraImgAttributes(['class' => 'rounded-lg shadow-md transition-transform duration-300 hover:scale-105']),
                                    TextEntry::make('psira_status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'verified' => 'success',
                                            'declined' => 'danger',
                                            default => 'warning',
                                        })
                                        ->extraAttributes(['class' => 'mt-2']),
                                    Actions::make([
                                        Action::make('verifyPsira')
                                            ->label('Verify')
                                            ->button()
                                            ->color('success')
                                            ->extraAttributes(['class' => 'hover:bg-green-600 transition-colors duration-200'])
                                            ->requiresConfirmation()
                                            ->action(function (Document $record) {
                                                $record->psira_status = 'verified';
                                                $record->save();
                                            }),
                                        Action::make('declinePsira')
                                            ->label('Decline')
                                            ->color('danger')
                                            ->button()
                                            ->extraAttributes(['class' => 'hover:bg-red-600 transition-colors duration-200'])
                                            ->requiresConfirmation()
                                            ->action(function (Document $record) {
                                                $record->psira_status = 'declined';
                                                $record->save();
                                            }),
                                    ])->alignCenter(),
                                ])->extraAttributes(['class' => 'document-card bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 transition-all duration-300 hover:shadow-lg']),

                                // Firearm Competency Card
                                Group::make([
                                    PdfViewerEntry::make('firearm_competency')
                                        ->label('Firearm Competency')
                                        ->minHeight('20svh')
                                        ->extraAttributes(['class' => 'rounded-lg shadow-md']),
                                    TextEntry::make('firearm_status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'verified' => 'success',
                                            'declined' => 'danger',
                                            default => 'warning',
                                        })
                                        ->extraAttributes(['class' => 'mt-2']),
                                    Actions::make([
                                        Action::make('verifyFirearm')
                                            ->label('Verify')
                                            ->button()
                                            ->color('success')
                                            ->extraAttributes(['class' => 'hover:bg-green-600 transition-colors duration-200'])
                                            ->requiresConfirmation()
                                            ->action(function (Document $record) {
                                                $record->firearm_status = 'verified';
                                                $record->save();
                                            }),
                                        Action::make('declineFirearm')
                                            ->label('Decline')
                                            ->color('danger')
                                            ->button()
                                            ->extraAttributes(['class' => 'hover:bg-red-600 transition-colors duration-200'])
                                            ->requiresConfirmation()
                                            ->action(function (Document $record) {
                                                $record->firearm_status = 'declined';
                                                $record->save();
                                            }),
                                    ])->alignCenter(),
                                ])->extraAttributes(['class' => 'document-card bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 transition-all duration-300 hover:shadow-lg']),

                                // Statement of Results Card
                                Group::make([
                                    ImageEntry::make('statement_of_results')
                                        ->disk('public')
                                        ->height(200)
                                        ->extraImgAttributes(['class' => 'rounded-lg shadow-md transition-transform duration-300 hover:scale-105']),
                                    TextEntry::make('statement_status')
                                        ->label('Status')
                                        ->badge()
                                        ->color(fn(string $state): string => match ($state) {
                                            'verified' => 'success',
                                            'declined' => 'danger',
                                            default => 'warning',
                                        })
                                        ->extraAttributes(['class' => 'mt-2']),
                                    Actions::make([
                                        Action::make('verifyStatement')
                                            ->label('Verify')
                                            ->button()
                                            ->color('success')
                                            ->extraAttributes(['class' => 'hover:bg-green-600 transition-colors duration-200'])
                                            ->requiresConfirmation()
                                            ->action(function (Document $record) {
                                                $record->statement_status = 'verified';
                                                $record->save();
                                            }),
                                        Action::make('declineStatement')
                                            ->label('Decline')
                                            ->color('danger')
                                            ->button()
                                            ->extraAttributes(['class' => 'hover:bg-red-600 transition-colors duration-200'])
                                            ->requiresConfirmation()
                                            ->action(function (Document $record) {
                                                $record->statement_status = 'declined';
                                                $record->save();
                                            }),
                                    ])->alignCenter(),
                                ])->extraAttributes(['class' => 'document-card bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 transition-all duration-300 hover:shadow-lg']),
                            ]),
                    ])
                    ->extraAttributes([
                        'class' => 'bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg p-6',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
