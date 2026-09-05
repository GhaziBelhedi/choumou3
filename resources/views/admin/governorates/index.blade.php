@extends('admin.layouts.admin')

@section('title', 'Gouvernorats')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Gouvernorats livrés</h1>
            <p>Activez ou désactivez la livraison vers chaque gouvernorat. Le tarif de livraison est unique pour toute la Tunisie — réglable dans <a href="{{ route('admin.settings.edit') }}" class="text-primary">Paramètres</a>.</p>
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
            <thead><tr><th>Gouvernorat</th><th>Actif</th><th></th></tr></thead>
            <tbody>
                @foreach ($governorates as $gov)
                    <tr>
                        <td style="font-weight:600">{{ $gov->name }}</td>
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
