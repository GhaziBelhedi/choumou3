@extends('admin.layouts.admin')

@section('title', 'Modifier le coupon')

@section('content')
    <div class="admin-page-header">
        <div><h1>Modifier « {{ $coupon->code }} »</h1></div>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="card" style="max-width:640px">
        @csrf
        @method('PUT')
        @include('admin.coupons._form', ['coupon' => $coupon])
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
@endsection
