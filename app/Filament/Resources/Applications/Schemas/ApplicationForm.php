<?php

namespace App\Filament\Resources\Applications\Schemas;

use Filament\Schemas\Schema;
use App\Models\Application;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')
                    ->label('Користувач')
                    // ->formatStateUsing(function ($state, Application $application) {
                    //     return $application->user->name . ' ' . $application->user->last_name;
                    // })
                    ->columnSpanFull()
                    ->readOnly(),

                Textarea::make('query')
                    ->label('Повідомлення')
                    ->columnSpanFull()
                    ->readOnly(),

                Textarea::make('admin_response')
                    ->label('Відповідь адміністратора')
                    ->columnSpanFull(),
            ]);
    }
}
