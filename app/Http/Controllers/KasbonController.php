<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use App\Models\Peternak;
use App\Models\KatalogLogistik;
use Illuminate\Http\Request;

class KasbonController extends Controller
{
    public function index(Request $request)
    {
        $query = Kasbon::with(['peternak', 'logistik'])->orderBy('tanggal', 'desc');
        
        if ($request->idpeternak) {
            $query->where('idpeternak', $request->idpeternak);
        }

        $perPage = $request->get('per_page', 10);
        $kasbons = $query->paginate($perPage)->withQueryString();
        $peternaks = Peternak::all();
        $items = KatalogLogistik::all();

        return view('kasbon.index', compact('kasbons', 'peternaks', 'items'));
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
        $kasbon->delete();

        return back()->with('success', 'Data kasbon berhasil dihapus!');
    }
}
