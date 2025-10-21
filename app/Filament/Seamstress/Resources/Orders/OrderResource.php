<?php

namespace App\Filament\Seamstress\Resources\Orders;

use App\Filament\Seamstress\Resources\Orders\Pages\CreateOrder;
use App\Filament\Seamstress\Resources\Orders\Pages\EditOrder;
use App\Filament\Seamstress\Resources\Orders\Pages\ListOrders;
use App\Filament\Seamstress\Resources\Orders\Pages\OrderTaskBoard;
use App\Filament\Seamstress\Resources\Orders\Schemas\OrderForm;
use App\Filament\Seamstress\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Scissors;

    protected static string | UnitEnum | null $navigationGroup = 'Запити';

    protected static ?string $label= "замовлення";

    protected static ?string $navigationLabel = "Замовлення";

    protected static ?string $pluralLabel = "Замовлення";

    protected static bool $hasTitleCaseModelLabel = false;

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
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
            // 'index' => ListOrders::route('/'),
            // 'create' => CreateOrder::route('/create'),
            // 'edit' => EditOrder::route('/{record}/edit'),
            'tasks' => OrderTaskBoard::route('/{record}/tasks'),
        ];
    }
}
