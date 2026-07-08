@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-[var(--border-soft)] bg-surface text-text-primary focus:border-[var(--integro-red)] focus:ring-[var(--integro-red)] rounded placeholder:text-[var(--integro-gray-text)] shadow-sm']) !!}>
