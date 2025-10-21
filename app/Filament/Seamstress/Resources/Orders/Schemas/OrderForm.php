<?php

namespace App\Filament\Seamstress\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use App\Models\Order;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user')
                    ->label('Замовник')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->user->name . ' ' . $order->user->last_name;
                    })
                    ->readOnly()
                    ->columnSpanFull(),

                TextInput::make('user_phone')
                    ->label('Номер користувача')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->user->phone;
                    })
                    ->readOnly(),

                TextInput::make('user_email')
                    ->label('Емейл користувача')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->user->email;
                    })
                    ->readOnly(),

                    TextInput::make('seamstress')
                    ->label('Швачка')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->seamstress->name . ' ' . $order->seamstress->last_name;
                    })
                    ->readOnly()
                    ->columnSpanFull(),

                TextInput::make('seamstress_phone')
                    ->label('Номер швачки')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->seamstress->phone;
                    })
                    ->readOnly(),

                TextInput::make('seamstress_email')
                    ->label('Емейл швачки')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->seamstress->email;
                    })
                    ->readOnly(),

                Select::make('status')
                    ->label('Статус')
                    ->required()
                    ->options(Order::getStatuses())
                    ->selectablePlaceholder(false)
                    ->columnSpanFull(),

                TextInput::make('category')
                    ->label('Категорія товару')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->category->name;
                    })
                    ->readOnly()
                    ->columnSpanFull(),

                // Repeater::make('sizes')
                //     ->relationship()
                //     ->schema([
                //         KeyValue::make('sizes')
                //     ])
                //     ->columns(2),


                KeyValue::make('sizes')
                    ->formatStateUsing(function ($state, Order $order) {
                        $arr = [];
                        foreach($order->sizes as $size){
                            $arr[$size->name] = $size->pivot->value;
                        }
                        return $arr;
                    })
                    ->afterStateUpdated(function ($state, Order $order) {
                        foreach ($state as $name => $value) {
                            $size = $order->sizes()->where('name', $name)->first();
                            if ($size) {
                                $order->sizes()->updateExistingPivot($size->id, ['value' => $value]);
                            }
                        }
                    })

                    ->label('Розміри')
                    ->keyLabel('Розмір')
                    ->valueLabel('Розмір см')
                    ->editableKeys(false)
                    ->deletable(false)
                    ->addable(false)
                    ->columnSpanFull(),
            ]);
    }
}
