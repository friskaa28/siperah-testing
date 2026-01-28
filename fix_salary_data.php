<?php
$slips = App\Models\SlipPembayaran::where('total_pembayaran', 0)->get();
foreach($slips as $s) {
    if ($s->jumlah_susu > 0) {
        $s->harga_satuan = 7000; // Hardcode fallback for bulk fix
        $s->save(); // This triggers the model's booted() logic to recalc total & sisa
        echo "Fixed Slip ID: " . $s->idslip . " - Rp " . $s->total_pembayaran . "\n";
    }
}
echo "Done.\n";
