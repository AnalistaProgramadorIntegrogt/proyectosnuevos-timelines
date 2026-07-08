@if ($errors->any())
    <div {{ $attributes }}>
        <div class="font-semibold text-[var(--integro-red)] text-sm">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-3 list-disc list-inside text-sm text-[var(--integro-red)]">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
