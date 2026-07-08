<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-secondary inline-flex items-center text-xs']) }}>
    {{ $slot }}
</button>
