@props(['type' => 'info'])

@php
    $styles = [
        'success' => 'border-blue-200 bg-blue-50 text-blue-800',
        'error' => 'border-pink-200 bg-pink-50 text-pink-800',
        'warning' => 'border-pink-100 bg-pink-50 text-pink-700',
        'info' => 'border-slate-200 bg-slate-50 text-slate-700',
    ][$type] ?? 'border-slate-200 bg-slate-50 text-slate-800';
@endphp

<div {{ $attributes->merge(['class' => "rounded-xl border px-4 py-3 text-sm shadow-sm animate-rise {$styles}"]) }}>
    {{ $slot }}
</div>
