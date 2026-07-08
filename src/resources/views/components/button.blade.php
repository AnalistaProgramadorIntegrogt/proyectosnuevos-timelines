<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary inline-flex items-center px-4 py-2 text-xs']) }}>
    {{ $slot }}
</button>
