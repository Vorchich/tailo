<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Назва')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                TextInput::make('author')
                    ->label('Автор')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('image')
                    ->label('Зображення')
                    ->image()
                    ->disk('books')
                    ->columnSpanFull()
                    ->maxSize('9990')
                    ->required()
                    ->collection('image')
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('book')
                    ->label('Книга')
                    ->downloadable()
                    ->disk('books')
                    ->columnSpanFull()
                    ->required()
                    ->maxSize(2048000)
                    ->collection('book')
                    ->acceptedFileTypes(['application/pdf'])
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('book_trial')
                    ->label('Пробна версія книги')
                    ->downloadable()
                    ->disk('books')
                    ->maxSize(2048000)
                    ->columnSpanFull()
                    ->required()
                    ->collection('book_trial')
                    ->acceptedFileTypes(['application/pdf'])
                    ->columnSpanFull(),
                TextInput::make('description')
                    ->label('Опис')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                TextInput::make('price')
                    ->label('Ціна')
                    ->numeric()
                    ->columnSpanFull()
                    ->prefix('₴'),
                TextInput::make('pages')
                    ->label('Кількість сторінок')
                    ->required()
                    ->columnSpanFull()
                    ->numeric(),
                TextInput::make('articles')
                    ->label('Кількість розділів')
                    ->required()
                    ->columnSpanFull()
                    ->numeric(),
                Toggle::make('active')
                    ->label('Активна')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
