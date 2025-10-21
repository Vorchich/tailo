<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('author')
                    ->label('Автор')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('price')
                    ->label('Ціна')
                    // ->money('uah')
                    ->sortable(),
                ToggleColumn::make('active')
                    ->label('Активна'),
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
