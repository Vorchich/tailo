<?php

namespace App\Filament\Resources\Applications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use App\Models\Application;
use Filament\Tables\Columns\TextColumn;


class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                        ->label('Користувач')
                        ->formatStateUsing(function ($state, Application $application) {
                            return $application->user->name . ' ' . $application->user->last_name;
                        }),

                    TextColumn::make('query')
                        ->label('Повідомлення')
                        ->limit(40),

                    TextColumn::make('admin_response')
                        ->label('Відповідь')
                        ->limit(40),
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
