@props(['order'])

@php
    $steps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
    $isCancelled = $order->status === 'cancelled';
    $currentIndex = array_search($order->status, $steps);
    $history = $order->statusHistory->keyBy('status');
@endphp

<div class="status-timeline">
    @if ($isCancelled)
        <div class="status-timeline__item">
            <span class="status-timeline__dot" style="background:var(--color-danger)"></span>
            <div>
                <p class="status-timeline__label" style="color:var(--color-danger)">Commande annulée</p>
                @if ($order->cancellation_reason)
                    <p class="status-timeline__date">{{ $order->cancellation_reason }}</p>
                @endif
            </div>
        </div>
    @else
        @foreach ($steps as $i => $step)
            @php $done = $i <= $currentIndex; @endphp
            <div class="status-timeline__item">
                <span class="status-timeline__dot" style="{{ $done ? '' : 'background:var(--color-border)' }}"></span>
                <div>
                    <p class="status-timeline__label" style="{{ $done ? '' : 'color:var(--color-ink-faint);font-weight:500' }}">
                        {{ \App\Models\Order::STATUSES[$step] }}
                    </p>
                    @if (isset($history[$step]))
                        <p class="status-timeline__date">{{ $history[$step]->created_at->format('d/m/Y à H:i') }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
