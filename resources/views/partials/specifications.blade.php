@php($specifications = collect($specifications ?? []))
@if($specifications->get('variant'))
    <div>Varian: {{ $specifications->get('variant') }}</div>
@endif
@if(collect($specifications->get('variants'))->isNotEmpty())
    <div>Multi varian:
        {{ collect($specifications->get('variants'))->map(fn ($row) => ($row['variant'] ?? '-') . ' ' . ($row['quantity'] ?? 0) . ' pcs')->join(', ') }}
    </div>
@endif
@if(collect($specifications->get('options'))->isNotEmpty())
    <div>Opsi: {{ collect($specifications->get('options'))->join(', ') }}</div>
@endif
@if($specifications->get('width'))
    <div>Lebar: {{ $specifications->get('width') }}</div>
@endif
@if($specifications->get('height'))
    <div>Tinggi: {{ $specifications->get('height') }}</div>
@endif
@if($specifications->get('quantity'))
    <div>Qty: {{ $specifications->get('quantity') }}</div>
@endif
