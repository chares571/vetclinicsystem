@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-blue-100 focus:border-blue-500 focus:ring-blue-300 rounded-lg shadow-sm']) }}>
