<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->required()
                    ->label('Заголовок'),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->label('Короткий опис'),

                RichEditor::make('text')
                    ->columnSpanFull()
                    ->label('Опис')
                    ->required(),

                SpatieMediaLibraryFileUpload::make('image')
                    ->label('Зображення')
                    ->image()
                    ->disk('post')
                    ->columnSpanFull()
                    ->maxSize('9990')
                    ->required()
                    ->collection('image')
                    ->columnSpanFull()
                    ->maxFiles(1),

                Toggle::make('active')
                    ->label('Відображати')
                    ->required(),
            ]);
    }
}
