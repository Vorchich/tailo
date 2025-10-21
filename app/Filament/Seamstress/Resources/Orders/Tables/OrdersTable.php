<?php

namespace App\Filament\Seamstress\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->searchable()
                    ->label('Категорія товару'),

                TextColumn::make('user.last_name')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->user->name . ' ' . $order->user->last_name;
                    })
                    ->searchable()
                    ->label('Замовник'),

                // TextColumn::make('user.phone')
                //     ->description(function ($state, Order $order) {
                //         return $order->user->email ?? null;
                //     })
                //     ->searchable()
                //     ->label('Контакти замовника'),

                TextColumn::make('seamstress.last_name')
                    ->formatStateUsing(function ($state, Order $order) {
                        return $order->seamstress->name . ' ' . $order->seamstress->last_name;
                    })
                    ->searchable()
                    ->label('Швачка'),

                // TextColumn::make('seamstress.phone')
                //     ->description(function ($state, Order $order) {
                //         return $order->seamstress->email ?? null;
                //     })
                //     ->searchable()
                //     ->label('Контакти швачки'),

                SelectColumn::make('status')
                    ->label('Статус')
                    ->options(Order::getStatuses())
                    ->selectablePlaceholder(false)
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->multiple()
                    ->options([
                        Order::getStatuses(),
                    ]),
                SelectFilter::make('category')
                    ->label('Категорія')
                    ->relationship('category', 'name'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('view_tasks_board')
                    ->label('Дошка')
                    ->icon('heroicon-o-view-columns')
                    ->url(fn ($record) => url("/seamstress/orders/{$record->id}/tasks")),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
