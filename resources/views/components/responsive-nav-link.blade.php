@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 bg-[color:var(--vm-panel-strong)] ps-3 pe-4 py-2 text-start text-base font-extrabold uppercase tracking-[0.12em] text-[color:var(--vm-border)] transition'
            : 'block w-full border-l-4 border-transparent ps-3 pe-4 py-2 text-start text-base font-extrabold uppercase tracking-[0.12em] text-[color:var(--vm-wood)] transition hover:bg-[color:var(--vm-panel)] hover:text-[color:var(--vm-border)]';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => ($active ?? false) ? 'border-color: var(--vm-accent-strong);' : '']) }}>
    {{ $slot }}
</a>
