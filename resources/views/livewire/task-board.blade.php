@extends('layouts.app') {{-- Твоє кастомне розташування без сайдбару --}}

@section('content')
<div>
    <div class="fi-flowforge **fi-layout--app**">
    <h1 class="text-2xl font-bold mb-6">Task Board</h1>
    {{ $this->board }}
    <x-filament::button color="success">
        Test Button
    </x-filament::button>
    </div>
</div>
@endsection
