@extends('admin.layouts.admin')

@section('title', 'Paramètres')

@section('content')
    <div class="admin-page-header">
        <div><h1>Paramètres généraux</h1></div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="card" style="max-width:520px">
        @csrf
        @method('PATCH')

        <div class="field">
            <label class="field__label" for="flat_shipping_price">Tarif de livraison (DT)</label>
            <input class="input" type="number" step="0.5" min="0" id="flat_shipping_price" name="flat_shipping_price" value="{{ old('flat_shipping_price', $flatShippingPrice) }}" required>
            <span class="field__hint">Tarif unique appliqué à toute commande, quel que soit le gouvernorat.</span>
        </div>

        <div class="field">
            <label class="field__label" for="free_shipping_threshold">Seuil de livraison gratuite (DT)</label>
            <input class="input" type="number" step="0.5" min="0" id="free_shipping_threshold" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $freeShippingThreshold) }}" required>
            <span class="field__hint">Au-delà de ce montant, la livraison est offerte. Mettre 0 pour désactiver.</span>
        </div>

        <div class="field">
            <label class="field__label" for="site_phone">Téléphone de contact</label>
            <input class="input" type="text" id="site_phone" name="site_phone" value="{{ old('site_phone', $sitePhone) }}">
        </div>

        <div class="field">
            <label class="field__label" for="site_email">E-mail de contact</label>
            <input class="input" type="email" id="site_email" name="site_email" value="{{ old('site_email', $siteEmail) }}">
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
@endsection
