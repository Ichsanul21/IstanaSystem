@props(['title', 'id'])
<x-ui.card>
    <x-slot:header><h3 class="text-lg font-bold text-dark dark:text-white">{{ $title }}</h3></x-slot:header>
    <canvas id="{{ $id }}" class="h-72"></canvas>
</x-ui.card>
