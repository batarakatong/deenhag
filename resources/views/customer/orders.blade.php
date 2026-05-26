@extends('layouts.app')
@section('content')
<h1 class="mb-5 text-3xl font-bold">Riwayat Pesanan</h1>
@include('customer.order-list', ['orders' => $orders])
<div class="mt-5">{{ $orders->links() }}</div>
@endsection
