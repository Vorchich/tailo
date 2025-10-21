<?php

namespace App\Livewire;

use App\Models\Task;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Relaticle\Flowforge\Board;
use Relaticle\Flowforge\Column;
use Relaticle\Flowforge\Concerns\InteractsWithBoard;
use Relaticle\Flowforge\Contracts\HasBoard;

class TaskBoard extends Component implements HasBoard, HasActions, HasForms
{
    use InteractsWithBoard, InteractsWithActions {
        // Кажемо PHP, що метод getDefaultActionRecord від InteractsWithActions
        // має використовуватись замість (insteadof) методу InteractsWithBoard.
        InteractsWithActions::getDefaultActionRecord insteadof InteractsWithBoard;

        // Можливо, знадобиться також для інших спільних методів, наприклад:
        // InteractsWithActions::getOwner insteadof InteractsWithBoard;
    }
    use InteractsWithForms;

    public int $orderId;

    public function board(Board $board): Board
    {
        // dd(1);
        return $board
            ->query(Task::where('order_id', $this->orderId))
            ->columnIdentifier('status')
            ->positionIdentifier('position')
            ->columns([
                Column::make('todo')->label('To Do')->color('gray'),
                Column::make('in_progress')->label('In Progress')->color('blue'),
                Column::make('completed')->label('Completed')->color('green'),
            ]);
    }

    public function mount($order)
    {
        $this->orderId = $order->id ?? $order;
    }

    public function render()
    {
        return view('livewire.task-board');
    }
}
