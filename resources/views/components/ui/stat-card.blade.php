@props([
    'label',
    'value',
    'tone' => 'slate',
    'variant' => 'gradient',
    'icon' => null,
    'critical' => false,
])

@php
    $gradientToneClasses = [
        'brand' => 'from-blue-600 to-blue-500',
        'accent' => 'from-blue-600 to-pink-500',
        'soft' => 'from-blue-500 to-blue-400',
        'pink' => 'from-pink-500 to-fuchsia-500',
        'slate' => 'from-slate-700 to-slate-900',
    ];

    $solidToneClasses = [
        'brand' => 'bg-blue-600 text-white',
        'blue' => 'bg-blue-600 text-white',
        'yellow' => 'bg-amber-500 text-white',
        'red' => 'bg-rose-600 text-white',
        'purple' => 'bg-violet-600 text-white',
        'slate' => 'bg-slate-700 text-white',
        'accent' => 'bg-pink-500 text-white',
        'soft' => 'bg-blue-500 text-white',
        'pink' => 'bg-pink-500 text-white',
    ];

    $baseClasses = 'rounded-2xl p-5 shadow-lg hover-lift animate-rise';
    $isSolid = $variant === 'solid';

    $articleClasses = $isSolid
        ? $baseClasses.' '.($solidToneClasses[$tone] ?? $solidToneClasses['slate'])
        : $baseClasses.' bg-gradient-to-br '.($gradientToneClasses[$tone] ?? $gradientToneClasses['slate']).' text-white';

    $iconWrapperClasses = $isSolid
        ? 'bg-white/20 text-white'
        : 'bg-white/15 text-white';
@endphp

<article class="{{ $articleClasses }}">
    <div class="flex items-start justify-between gap-3">
        <p class="text-xs uppercase tracking-[0.2em] {{ $isSolid ? 'text-white/90' : 'text-white/80' }}">{{ $label }}</p>

        @if($icon)
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ $iconWrapperClasses }}">
                @if($icon === 'users')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V18a4 4 0 00-5-3.87M17 20H7m10 0v-2c0-.65-.12-1.28-.34-1.87M7 20H2V18a4 4 0 015-3.87M7 20v-2c0-.65.12-1.28.34-1.87m0 0a5 5 0 019.32 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                @elseif($icon === 'staff')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-8 8a6 6 0 00-6 6h20a6 6 0 00-6-6H8z" />
                    </svg>
                @elseif($icon === 'pets')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9a2 2 0 11-4 0 2 2 0 014 0zm5-2a2 2 0 100-4 2 2 0 000 4zm5 2a2 2 0 11-4 0 2 2 0 014 0zM8.1 14.8c1.9-2 5.9-2 7.8 0 2 2.1 1.8 5.4-.3 6.9-1.4.9-2.7.6-3.6.2-.9-.4-1.7-.4-2.6 0-.9.4-2.2.7-3.6-.2-2.1-1.5-2.3-4.8.3-6.9z" />
                    </svg>
                @elseif($icon === 'appointments')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-13 9h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v11a2 2 0 002 2z" />
                    </svg>
                @elseif($icon === 'vaccination')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3l13 13m-3 3l-6-6m-3 3l-4 4m0 0l-2-2m2 2l2 2m2-2l2-2" />
                    </svg>
                @elseif($icon === 'clock')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif($icon === 'alert')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @elseif($icon === 'hospital')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7a2 2 0 012-2h10a2 2 0 012 2v14M9 21v-4h6v4M10 9h4m-2-2v4" />
                    </svg>
                @elseif($icon === 'medicine')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 19.5l7.5-7.5m0 0l3-3a3 3 0 114.243 4.243l-3 3m-4.243-4.243l4.243 4.243M3 21h6" />
                    </svg>
                @endif
            </span>
        @endif
    </div>

    @php
        $valueColor = $isSolid ? 'text-white' : 'text-slate-900';
    @endphp
    <p class="mt-3 {{ $critical ? 'text-3xl font-extrabold' : 'text-3xl font-bold' }} {{ $valueColor }}">{{ $value }}</p>
</article>
