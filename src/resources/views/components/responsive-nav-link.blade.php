@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[var(--integro-red)] text-start text-base font-semibold text-white bg-white/5 focus:outline-none focus:text-white focus:bg-white/5 focus:border-[var(--integro-red)] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[var(--integro-gray-text)] hover:text-white hover:bg-white/5 hover:border-[var(--integro-gray)] focus:outline-none focus:text-white focus:bg-white/5 focus:border-[var(--integro-gray)] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
