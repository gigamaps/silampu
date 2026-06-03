<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index()
    {
        ActivityLogger::log('view_units', 'Melihat daftar unit sekolah');
        $units = Unit::latest()->get();
        return view('admin.units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:50|unique:units,nama_unit',
            'alamat' => 'nullable|string',
        ], [
            'nama_unit.unique' => 'Nama unit sekolah ini sudah terdaftar di sistem.'
        ]);

        $unit = Unit::create([
            'nama_unit' => $request->nama_unit,
            'alamat' => $request->alamat,
        ]);

        ActivityLogger::log('create_unit', 'Menambahkan unit sekolah baru: ' . $unit->nama_unit);
        return back()->with('success', 'Unit sekolah berhasil ditambahkan!');
    }

    // Mengambil data untuk dilempar ke modal edit via JSON
    public function edit(Unit $unit)
    {
        return response()->json(['unit' => $unit]);
    }

    // Memproses pembaruan data unit sekolah
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'nama_unit' => ['required', 'string', 'max:50', Rule::unique('units', 'nama_unit')->ignore($unit->id)],
            'alamat' => 'nullable|string',
        ], [
            'nama_unit.unique' => 'Nama unit sekolah ini sudah digunakan oleh unit lain.'
        ]);

        $unit->update([
            'nama_unit' => $request->nama_unit,
            'alamat' => $request->alamat,
        ]);

        ActivityLogger::log('update_unit', 'Mengubah data unit sekolah: ' . $unit->nama_unit);
        return back()->with('success', 'Data unit sekolah berhasil diperbarui!');
    }

    public function destroy(Unit $unit)
    {
        $nama = $unit->nama_unit;
        $unit->delete();

        ActivityLogger::log('delete_unit', 'Menghapus unit sekolah: ' . $nama);
        return back()->with('success', 'Unit sekolah berhasil dihapus!');
    }
}
