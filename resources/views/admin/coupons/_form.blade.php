@php $coupon = $coupon ?? null; @endphp

<div class="form-grid form-grid-2">
    <div class="field">
        <label class="field__label" for="code">Code</label>
        <input class="input @error('code') has-error @enderror" type="text" id="code" name="code" value="{{ old('code', $coupon->code ?? '') }}" style="text-transform:uppercase" required>
        @error('code') <span class="field__error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label class="field__label" for="type">Type de réduction</label>
        <select class="select" id="type" name="type" required>
            <option value="percentage" @selected(old('type', $coupon->type ?? '') === 'percentage')>Pourcentage (%)</option>
            <option value="fixed" @selected(old('type', $coupon->type ?? '') === 'fixed')>Montant fixe (DT)</option>
        </select>
    </div>

    <div class="field">
        <label class="field__label" for="value">Valeur</label>
        <input class="input @error('value') has-error @enderror" type="number" step="0.01" min="0" id="value" name="value" value="{{ old('value', $coupon->value ?? '') }}" required>
        @error('value') <span class="field__error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label class="field__label" for="max_discount_amount">Réduction max (DT) <span class="text-faint">(optionnel)</span></label>
        <input class="input" type="number" step="0.01" min="0" id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon->max_discount_amount ?? '') }}">
    </div>

    <div class="field">
        <label class="field__label" for="min_order_amount">Montant minimum de commande (DT) <span class="text-faint">(optionnel)</span></label>
        <input class="input" type="number" step="0.01" min="0" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}">
    </div>

    <div></div>

    <div class="field">
        <label class="field__label" for="usage_limit">Limite d'utilisation totale <span class="text-faint">(optionnel)</span></label>
        <input class="input" type="number" min="1" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}">
    </div>

    <div class="field">
        <label class="field__label" for="usage_limit_per_user">Limite par client <span class="text-faint">(optionnel)</span></label>
        <input class="input" type="number" min="1" id="usage_limit_per_user" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user ?? '') }}">
    </div>

    <div class="field">
        <label class="field__label" for="starts_at">Date de début <span class="text-faint">(optionnel)</span></label>
        <input class="input" type="date" id="starts_at" name="starts_at" value="{{ old('starts_at', optional($coupon?->starts_at)->format('Y-m-d')) }}">
    </div>

    <div class="field">
        <label class="field__label" for="expires_at">Date d'expiration <span class="text-faint">(optionnel)</span></label>
        <input class="input @error('expires_at') has-error @enderror" type="date" id="expires_at" name="expires_at" value="{{ old('expires_at', optional($coupon?->expires_at)->format('Y-m-d')) }}">
        @error('expires_at') <span class="field__error">{{ $message }}</span> @enderror
    </div>
</div>

<label class="checkbox-row" style="margin-bottom:var(--space-4)">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true))>
    Actif
</label>
