@php
    $map = [
        'pending_payment' => 'Menunggu pembayaran',
        'payment_confirmed' => 'Pembayaran dikonfirmasi',
        'waiting_design' => 'Menunggu desain',
        'file_received' => 'File diterima',
        'design_process' => 'Proses desain',
        'waiting_approval' => 'Menunggu approval',
        'printing' => 'Proses cetak',
        'finishing' => 'Finishing',
        'ready_pickup' => 'Siap diambil',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'unpaid' => 'Belum bayar',
        'waiting_confirmation' => 'Menunggu konfirmasi',
        'paid' => 'Dibayar',
        'rejected' => 'Ditolak',
    ];
@endphp
<span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">{{ $map[$status] ?? $status }}</span>
