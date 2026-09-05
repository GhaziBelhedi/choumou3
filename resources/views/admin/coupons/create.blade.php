@extends('admin.layouts.admin')

@section('title', 'Ajouter un coupon')

@section('content')
    <div class="admin-page-header">
        <div><h1>Ajouter un coupon</h1></div>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.coupons.store') }}" class="card" style="max-width:640px">
        @csrf
        @include('admin.coupons._form', ['coupon' => null])
        <button type="submit" class="btn btn-primary">Créer le coupon</button>
    </form>
@endsection
