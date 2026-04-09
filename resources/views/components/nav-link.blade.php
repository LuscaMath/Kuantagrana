@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-4 px-2 pt-1 text-sm font-extrabold uppercase tracking-[0.14em] text-[color:var(--vm-border)] transition'
            : 'inline-flex items-center border-b-4 border-transparent px-2 pt-1 text-sm font-extrabold uppercase tracking-[0.14em] text-[color:var(--vm-wood)] transition hover:text-[color:var(--vm-border)]';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => ($active ?? false) ? 'border-color: var(--vm-accent-strong);' : '']) }}>
    {{ $slot }}
</a>
