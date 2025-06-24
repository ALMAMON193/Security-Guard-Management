<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use Filament\Actions;
use App\Filament\Resources\QuoteResource;
use Filament\Resources\Pages\ListRecords;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
