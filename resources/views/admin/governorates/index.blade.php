@extends('admin.layouts.admin')

@section('title', 'Tarifs de livraison')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Tarifs de livraison par gouvernorat</h1>
            <p>Modifiez le tarif et validez pour chaque ligne.</p>
        </div>
    </div>

    @foreach ($governorates as $gov)
        <form id="gov-form-{{ $gov->id }}" method="POST" action="{{ route('admin.governorates.update', $gov) }}">
            @csrf
            @method('PATCH')
        </form>
    @endforeach

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Gouvernorat</th><th>Tarif (DT)</th><th>Actif</th><th></th></tr></thead>
            <tbody>
                @foreach ($governorates as $gov)
                    <tr>
                        <td style="font-weight:600">{{ $gov->name }}</td>
                        <td>
                            <input class="input" style="max-width:120px" type="number" step="0.5" min="0" name="shipping_price" form="gov-form-{{ $gov->id }}" value="{{ $gov->shipping_price }}">
                        </td>
                        <td>
                            <label class="checkbox-row"><input type="checkbox" name="is_active" value="1" form="gov-form-{{ $gov->id }}" @checked($gov->is_active)></label>
                        </td>
                        <td>
                            <button type="submit" form="gov-form-{{ $gov->id }}" class="btn btn-secondary btn-sm">Enregistrer</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
