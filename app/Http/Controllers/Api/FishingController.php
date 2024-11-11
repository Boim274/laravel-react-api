<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\FishingResource;
use App\Models\Fishing;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FishingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fishings = Fishing::latest()->paginate(5);
        return new FishingResource(true, 'List Data Fishing', $fishings);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Validator = Validator ::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'harga' => 'required',
            'lokasi' => 'required',
        ]);

        if ($Validator->fails()) {
            return response()->json($Validator->errors(), 422);
        }

        $image = $request->file('gambar');
        $image->storeAs('public/fishings', $image->hashName());

        $fishing = Fishing::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'gambar' => $image->hashName(),
            'harga' => $request->harga,
            'lokasi' => $request->lokasi
        ]);
        return new FishingResource(true, 'Data Fishing Berhasil Di Tambahkan', $fishing);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fishing = Fishing::find($id);
        return new FishingResource(true, 'Detail Data Ikan', $fishing);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $Validator = Validator ::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required',
            'lokasi' => 'required',
        ]);

        if ($Validator->fails()) {
            return response()->json($Validator->errors(), 422);
        }

        $fishing = Fishing::find($id);

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $image->storeAs('public/fishings', $image->hashName());
            Storage::delete('public/fishings/' . basename($fishing->gambar));
            $fishing->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'gambar' => $image->hashName(),
                'harga' => $request->harga,
                'lokasi' => $request->lokasi
            ]);
        } else {
            $fishing->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'harga' => $request->harga,
                'lokasi' => $request->lokasi
            ]);
        }

        return new FishingResource(true, 'Data Ikan Berhasil Di Update', $fishing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fishing = Fishing::find($id);
        Storage::delete('public/fishings/' . basename($fishing->gambar));
        $fishing->delete();

        return new FishingResource(true,'Data Ikan Berhasil Di Hapus!', null);

    }
}
