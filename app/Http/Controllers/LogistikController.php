<?php

namespace App\Http\Controllers;

use App\Models\KatalogLogistik;
use Illuminate\Http\Request;

class LogistikController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $items = KatalogLogistik::paginate($perPage)->withQueryString();
        return view('logistik.index', compact('items', 'perPage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        KatalogLogistik::create($validated);

        return back()->with('success', 'Barang berhasil ditambahkan ke katalog!');
    }

    public function update(Request $request, $id)
    {
        $item = KatalogLogistik::findOrFail($id);
        
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $item->update($validated);

        return back()->with('success', 'Katalog barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $item = KatalogLogistik::findOrFail($id);
        $item->delete();

        return back()->with('success', 'Barang berhasil dihapus dari katalog!');
    }
}
