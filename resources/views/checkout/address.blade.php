@extends('layouts.app')

@section('title', 'Adresse de livraison — Choumou3')

@section('content')
    <div class="container section">
        <div class="checkout-steps">
            <div class="checkout-step is-active"><span class="checkout-step__circle">1</span> Adresse</div>
            <span class="checkout-step__sep"></span>
            <div class="checkout-step"><span class="checkout-step__circle">2</span> Récapitulatif</div>
            <span class="checkout-step__sep"></span>
            <div class="checkout-step"><span class="checkout-step__circle">3</span> Confirmation</div>
        </div>

        <div class="card" style="max-width:640px;margin-inline:auto">
            <h1 style="font-family:var(--font-serif);font-size:var(--text-2xl);margin-bottom:var(--space-6)">Adresse de livraison</h1>

            @if ($savedAddresses->isNotEmpty())
                <div class="field">
                    <label class="field__label">Utiliser une adresse enregistrée</label>
                    <select class="select" id="saved-address-select" style="margin-top:var(--space-2)">
                        <option value="">— Nouvelle adresse —</option>
                        @foreach ($savedAddresses as $saved)
                            <option
                                value="{{ $saved->id }}"
                                data-full-name="{{ $saved->full_name }}"
                                data-phone="{{ $saved->phone }}"
                                data-governorate-id="{{ $saved->governorate_id }}"
                                data-city="{{ $saved->city }}"
                                data-address-line="{{ $saved->address_line }}"
                                data-postal-code="{{ $saved->postal_code }}"
                            >{{ $saved->label ?? $saved->full_name }} — {{ $saved->city }}</option>
                        @endforeach
                    </select>
                </div>
                <hr style="border-color:var(--color-border);margin-block:var(--space-5)">
            @endif

            <form method="POST" action="{{ route('checkout.address.store') }}" id="address-form">
                @csrf

                <div class="field">
                    <label class="field__label" for="full_name">Nom complet</label>
                    <input class="input" type="text" id="full_name" name="full_name" value="{{ old('full_name', $old['full_name'] ?? '') }}" required>
                </div>

                <div class="field">
                    <label class="field__label" for="phone">Téléphone</label>
                    <input class="input" type="tel" id="phone" name="phone" value="{{ old('phone', $old['phone'] ?? '') }}" required>
                </div>

                @guest
                    <div class="field">
                        <label class="field__label" for="guest_email">Adresse e-mail</label>
                        <input class="input" type="email" id="guest_email" name="guest_email" value="{{ old('guest_email', $old['guest_email'] ?? '') }}" required>
                        <span class="field__hint">Pour recevoir la confirmation de votre commande.</span>
                    </div>
                @endguest

                <div class="field">
                    <label class="field__label" for="governorate_id">Gouvernorat</label>
                    <select class="select" id="governorate_id" name="governorate_id" required>
                        <option value="">Sélectionner...</option>
                        @foreach ($governorates as $gov)
                            <option value="{{ $gov->id }}" @selected(old('governorate_id', $old['governorate_id'] ?? null) == $gov->id)>
                                {{ $gov->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="field__hint">Livraison à {{ number_format((float) \App\Models\Setting::get('flat_shipping_price', 0), 2) }} DT partout en Tunisie.</span>
                </div>

                <div class="field">
                    <label class="field__label" for="city">Ville</label>
                    <input class="input" type="text" id="city" name="city" value="{{ old('city', $old['city'] ?? '') }}" required>
                </div>

                <div class="field">
                    <label class="field__label" for="address_line">Adresse complète</label>
                    <textarea class="textarea" id="address_line" name="address_line" rows="2" required>{{ old('address_line', $old['address_line'] ?? '') }}</textarea>
                </div>

                <div class="field">
                    <label class="field__label" for="postal_code">Code postal <span class="text-faint">(optionnel)</span></label>
                    <input class="input" type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $old['postal_code'] ?? '') }}">
                </div>

                @auth
                    <label class="checkbox-row" style="margin-bottom:var(--space-5)">
                        <input type="checkbox" name="save_address" value="1">
                        Enregistrer cette adresse pour mes prochaines commandes
                    </label>
                @endauth

                <button type="submit" class="btn btn-primary btn-block btn-lg">Continuer vers le récapitulatif</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var select = document.getElementById('saved-address-select');
            if (!select) return;
            select.addEventListener('change', function () {
                var opt = select.options[select.selectedIndex];
                if (!opt.value) return;
                document.getElementById('full_name').value = opt.getAttribute('data-full-name') || '';
                document.getElementById('phone').value = opt.getAttribute('data-phone') || '';
                document.getElementById('governorate_id').value = opt.getAttribute('data-governorate-id') || '';
                document.getElementById('city').value = opt.getAttribute('data-city') || '';
                document.getElementById('address_line').value = opt.getAttribute('data-address-line') || '';
                document.getElementById('postal_code').value = opt.getAttribute('data-postal-code') || '';
            });
        })();
    </script>
@endsection
