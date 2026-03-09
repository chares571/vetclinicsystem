@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'rounded-2xl border border-blue-100 bg-white/95 p-5 shadow-sm hover-lift animate-fade-in']) }}>
    @if($title || $subtitle)
        <header class="mb-4">
            @if($title)
                <h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
