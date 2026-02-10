@props([
    'title' => null,
])

@include('layouts.manexo-app', [
    'title' => $title,
    'slot' => $slot,
])

