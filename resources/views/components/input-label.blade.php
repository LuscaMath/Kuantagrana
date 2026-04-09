@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-extrabold uppercase tracking-[0.16em] text-[color:var(--vm-border)]']) }}>
    {{ $value ?? $slot }}
</label>
