<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ім\'я')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Прізвище')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Пошта')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Роль')
                    ->formatStateUsing(fn (string $state) => __($state)),

                // IconColumn::make('is_seamstress')
                //     ->label('Швачка')
                //     ->boolean(),
                // IconColumn::make('is_customer')
                //     ->label('Замовник')
                //     ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
