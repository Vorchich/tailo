<?php

namespace App\Filament\Seamstress\Resources\Orders\Pages;

use App\Filament\Seamstress\Resources\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
