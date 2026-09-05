@extends('admin.layouts.admin')

@section('title', 'Coupons')

@section('content')
    <div class="admin-page-header">
        <div><h1>Coupons de réduction</h1></div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">+ Ajouter un coupon</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Code</th><th>Type</th><th>Valeur</th><th>Utilisations</th><th>Validité</th><th>Statut</th><th></th></tr></thead>
            <tbody>
                @forelse ($coupons as $coupon)
                    <tr>
                        <td style="font-weight:700">{{ $coupon->code }}</td>
                        <td>{{ $coupon->type === 'percentage' ? 'Pourcentage' : 'Montant fixe' }}</td>
                        <td>{{ $coupon->type === 'percentage' ? $coupon->value.'%' : number_format((float) $coupon->value, 2).' DT' }}</td>
                        <td>{{ $coupon->usages_count }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                        <td style="font-size:var(--text-xs)">
                            @if ($coupon->expires_at)
                                Expire le {{ $coupon->expires_at->format('d/m/Y') }}
                            @else
                                Sans expiration
                            @endif
                        </td>
                        <td>
                            @if ($coupon->is_active)
                                <span class="badge badge-success">Actif</span>
                            @else
                                <span class="badge badge-neutral">Inactif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex" style="gap:var(--space-2)">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-primary" style="font-size:var(--text-sm)">Modifier</a>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Supprimer ce coupon ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="font-size:var(--text-sm);color:var(--color-danger)">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-faint" style="padding:var(--space-8)">Aucun coupon.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $coupons->links('pagination.custom') }}
@endsection
