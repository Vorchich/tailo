<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Назва')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                TextInput::make('slug')
                    ->label('Слаг')
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('preview')
                    ->label('Зображення')
                    ->conversion('preview')
                    ->image()
                    ->disk('categories')
                    ->columnSpanFull()
                    ->maxSize('9990')
                    ->required()
                    ->columnSpanFull(),

                Select::make('size_id')
                    ->label('Розміри')
                    ->relationship(name: 'sizes', titleAttribute: 'name')
                    ->multiple()
                    ->preload()
                    ->columnSpanFull(),

                Select::make('required_size_id')
                    ->label('Обовʼязкові розміри')
                    ->relationship(name: 'requiredSizes', titleAttribute: 'name')
                    ->multiple()
                    ->preload()
                    ->columnSpanFull(),
            ]);
    }
}
