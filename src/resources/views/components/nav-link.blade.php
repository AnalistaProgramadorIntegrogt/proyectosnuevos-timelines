@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[var(--integro-red)] text-sm font-semibold leading-5 text-white focus:outline-none focus:border-[var(--integro-red)] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[var(--integro-gray-text)] hover:text-white hover:border-[var(--integro-gray)] focus:outline-none focus:text-white focus:border-[var(--integro-gray)] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
