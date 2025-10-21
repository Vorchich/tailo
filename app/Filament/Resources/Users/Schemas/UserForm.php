<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ім\'я')
                    ->required()
                    ->maxLength(255),
                TextInput::make('middle_name')
                    ->label('По-батькові')
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label('Прізвище')
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Пошта')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Телефон')
                    ->tel()
                    ->maxLength(30),
                Select::make('role')
                    ->label('Роль')
                    ->options([
                        'customer' => __('Замовник'),
                        'seamstress' => __('Швачка'),
                    ]),

                // Toggle::make('is_seamstress')
                //     ->label('Швачка')
                //     ->required(),
                // Toggle::make('is_customer')
                //     ->label('Замовник')
                //     ->required(),
            ]);
    }
}
