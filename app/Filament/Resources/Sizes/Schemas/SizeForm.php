<?php

namespace App\Filament\Resources\Sizes\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;


class SizeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('key', Str::slug($state)))
                    ->label('Код')
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                TextInput::make('key')
                    ->label('Ключ')
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->required()
                    ->label('Назва')
                    ->columnSpanFull(),

                TextInput::make('link')
                    ->label('Посилання')
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('preview')
                    ->label('Зображення')
                    ->conversion('preview')
                    ->image()
                    ->disk('sizes')
                    ->collection('image')
                    ->columnSpanFull()
                    ->maxSize('9990')
                    ->required()
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('video')
                    ->label('Відео')
                    ->collection('video')
                    ->acceptedFileTypes(['video/*'])
                    ->disk('sizes')
                    ->columnSpanFull()
                    ->preserveFilenames()
                    ->maxSize(50000)
                    ->columnSpanFull(),
            ]);
    }
}
