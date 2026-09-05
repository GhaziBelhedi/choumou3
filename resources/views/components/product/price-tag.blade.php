@props(['product', 'size' => 'base'])

@php
    $fontSize = match ($size) {
        'lg' => 'var(--text-2xl)',
        'sm' => 'var(--text-sm)',
        default => 'var(--text-lg)',
    };
@endphp

<span class="flex" style="gap:var(--space-2);align-items:baseline;flex-wrap:wrap">
    <span style="font-weight:700;font-size:{{ $fontSize }};color:var(--color-primary)">
        {{ number_format((float) $product->price, 2) }} DT
    </span>

    @if ($product->isOnSale())
        <span class="text-faint" style="text-decoration:line-through;font-size:var(--text-sm)">
            {{ number_format((float) $product->compare_at_price, 2) }} DT
        </span>
        <span class="badge badge-primary">-{{ $product->discountPercent() }}%</span>
    @endif
</span>
