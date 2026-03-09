@props(['href' => null, 'variant' => 'primary'])

@php
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 shadow-md shadow-blue-100',
        'secondary' => 'bg-pink-100 text-blue-700 hover:bg-pink-200 focus:ring-pink-300',
        'danger' => 'bg-pink-500 text-white hover:bg-pink-600 focus:ring-pink-400',
    ];
    $classes = 'inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold transition hover-lift focus:outline-none focus:ring-2 focus:ring-offset-2 '.($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
