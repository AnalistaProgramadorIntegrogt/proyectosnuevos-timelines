<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-danger inline-flex items-center text-xs']) }}>
    {{ $slot }}
</button>
