<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use App\Models\Peternak;
use App\Models\KatalogLogistik;
use App\Models\SlipPembayaran;
use Illuminate\Http\Request;

class KasbonController extends Controller
{
    public function index(Request $request)
    {
        $query = Kasbon::with(['peternak', 'logistik'])->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');
        
        if ($request->idpeternak) {
            $query->where('idpeternak', $request->idpeternak);
        }

        if ($request->filled('jenis_inputan')) {
            $query->where('nama_item', $request->query('jenis_inputan'));
        }

        $perPage = $request->get('per_page', 10);
        $kasbons = $query->paginate($perPage)->withQueryString();
        $peternaks = Peternak::orderBy('nama_peternak')->get();
        $items = KatalogLogistik::orderBy('nama_barang')->get();
        
        // Get unique item names for filter dropdown
        $itemNames = Kasbon::select('nama_item')->distinct()->orderBy('nama_item')->pluck('nama_item');

        return view('kasbon.index', compact('kasbons', 'peternaks', 'items', 'itemNames'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idpeternak' => 'required|exists:peternak,idpeternak',
            'idlogistik' => 'required|exists:katalog_logistik,id',
            'qty' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
        ]);

        $item = KatalogLogistik::findOrFail($validated['idlogistik']);
        
        Kasbon::create([
            'idpeternak' => $validated['idpeternak'],
            'idlogistik' => $validated['idlogistik'],
            'nama_item' => $item->nama_barang,
            'qty' => $validated['qty'],
            'harga_satuan' => $item->harga_satuan,
            'total_rupiah' => $validated['qty'] * $item->harga_satuan,
            'tanggal' => $validated['tanggal'],
        ]);

        return back()->withInput()->with('success', 'Kasbon berhasil dicatat!');
    }

    public function destroy($id)
    {
        $kasbon = Kasbon::findOrFail($id);
        
        if ($kasbon->idslip) {
            $slip = SlipPembayaran::find($kasbon->idslip);
            if ($slip && $slip->isSigned()) {
                return back()->with('error', 'Tidak dapat menghapus potongan yang sudah masuk slip gaji yang ditandatangani.');
            }
        }

        $kasbon->delete();

        return back()->with('success', 'Data kasbon berhasil dihapus!');
    }

    public function edit($id)
    {
        $kasbon = Kasbon::with('peternak')->findOrFail($id);
        
        if ($kasbon->idslip) {
            $slip = SlipPembayaran::find($kasbon->idslip);
            if ($slip && $slip->isSigned()) {
                return redirect()->route('kasbon.index')->with('error', 'Tidak dapat mengedit potongan yang sudah masuk slip gaji yang ditandatangani.');
            }
        }

        $peternaks = Peternak::orderBy('nama_peternak')->get();
        $items = KatalogLogistik::orderBy('nama_barang')->get();

        return view('kasbon.edit', compact('kasbon', 'peternaks', 'items'));
    }

    public function update(Request $request, $id)
    {
        $kasbon = Kasbon::findOrFail($id);

        if ($kasbon->idslip) {
            $slip = SlipPembayaran::find($kasbon->idslip);
            if ($slip && $slip->isSigned()) {
                return back()->with('error', 'Data terkunci (slip sudah ditandatangani).');
            }
        }

        $request->validate([
            'idpeternak' => 'required|exists:peternak,idpeternak',
            'idlogistik' => 'required|exists:katalog_logistik,id',
            'qty' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
        ]);

        $item_logistik = KatalogLogistik::findOrFail($request->idlogistik);
        $total = $item_logistik->harga_satuan * $request->qty;

        $kasbon->update([
            'idpeternak' => $request->idpeternak,
            'idlogistik' => $request->idlogistik,
            'nama_item' => $item_logistik->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $item_logistik->harga_satuan,
            'total_rupiah' => $total,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('kasbon.index')->with('success', 'Data potongan berhasil diperbarui.');
    }
}
