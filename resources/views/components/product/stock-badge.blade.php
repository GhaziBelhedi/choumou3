@props(['quantity' => 0])

@php
    $quantity = (int) $quantity;
@endphp

@if ($quantity <= 0)
    <span class="badge badge-danger">Rupture de stock</span>
@elseif ($quantity <= 5)
    <span class="badge badge-warning">Plus que {{ $quantity }} en stock</span>
@else
    <span class="badge badge-success">En stock</span>
@endif
