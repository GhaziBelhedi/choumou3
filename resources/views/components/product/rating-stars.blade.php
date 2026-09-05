@props(['rating' => 0, 'count' => null, 'size' => 14])

@php
    $rating = (float) $rating;
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
@endphp

<span class="flex" style="gap:2px" aria-label="{{ number_format($rating, 1) }} sur 5">
    @for ($i = 1; $i <= 5; $i++)
        @php
            $filled = $i <= $full;
            $isHalf = ! $filled && $half && $i === (int) $full + 1;
            $gradId = 'star-half-'.uniqid();
        @endphp
        <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" style="flex-shrink:0">
            @if ($isHalf)
                <defs>
                    <linearGradient id="{{ $gradId }}">
                        <stop offset="50%" stop-color="var(--color-gold)"/>
                        <stop offset="50%" stop-color="var(--color-border)"/>
                    </linearGradient>
                </defs>
            @endif
            <path
                fill="{{ $filled ? 'var(--color-gold)' : ($isHalf ? "url(#{$gradId})" : 'var(--color-border)') }}"
                d="M12 2l2.9 6.6 7.1.6-5.4 4.7L18.2 21 12 17.3 5.8 21l1.6-7.1L2 9.2l7.1-.6z"
            />
        </svg>
    @endfor

    @if (! is_null($count))
        <span class="text-faint" style="font-size:var(--text-xs);margin-left:var(--space-1)">({{ $count }})</span>
    @endif
</span>
