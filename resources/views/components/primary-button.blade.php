<button {{ $attributes->merge(['type' => 'submit', 'class' => 'pixel-btn focus:outline-none']) }}>
    {{ $slot }}
</button>
