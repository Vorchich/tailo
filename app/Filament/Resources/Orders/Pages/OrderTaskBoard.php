<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Relaticle\Flowforge\Board;
use Relaticle\Flowforge\BoardResourcePage;
use Relaticle\Flowforge\Column;

class OrderTaskBoard extends BoardResourcePage
{
    protected static string $resource = OrderResource::class;

    public ?Order $record = null;

    public function mount(Order $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        $order = Order::find($this->record->id);

        return "Редагування дошки для замовлення #{$order->id}";
    }

    public function board(Board $board): Board
    {
        return $board
        ->query(
            $this->record
                ->tasks()
                ->getQuery()
            )
            ->columnIdentifier('status')
            ->positionIdentifier('position')
            // ->recordTitleAttribute('description')                   // Card title field


            ->columns([
                Column::make('todo')->label('Зробити')->color('gray'),
                Column::make('in_progress')->label('В процесі')->color('blue'),
                Column::make('completed')->label('Заершено')->color('green'),
            ])
            ->cardSchema(fn(Schema $schema) => $schema        // Rich card content
                ->components([
                    TextEntry::make('description')
                        ->hiddenLabel()
                        ->limit(50)
                    ,
                ])
            )
            ->cardActions([
                EditAction::make()
                    ->modalHeading('Редагування завдання')
                    ->schema([
                    TextInput::make('title')
                            ->label('Назва')
                            ->required(),

                        Textarea::make('description')
                            ->label('Опис')
                            ->required(),
                ])->model(Task::class),                              // Card-level actions
                DeleteAction::make()->model(Task::class)->modalHeading('Видалення завдання'),
            ])
            ->cardAction('edit')
            ->columnActions([
                CreateAction::make()
                    ->label('Додати завдання')
                    ->model(Task::class)
                    ->schema([
                        TextInput::make('title')
                            ->label('Назва')
                            ->required(),

                        Textarea::make('description')
                            ->label('Опис')
                            ->required(),

                        Select::make('priority')
                            ->label('Пріоритет')
                            ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                            ->default('medium'),
                    ])
                    ->mutateDataUsing(function (array $data, array $arguments): array {
                        if (isset($arguments['column'])) {
                            $data['order_id'] = $this->record->id;
                            $data['status'] = $arguments['column'];
                            $data['position'] = $this->getBoardPositionInColumn($arguments['column']);
                        }
                        return $data;
                    }),
            ]);
    }
}
