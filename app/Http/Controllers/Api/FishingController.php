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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'harga' => 'required',
            'lokasi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/fishings', $image->hashName());

        $fishing = Fishing::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'image' => $image->hashName(),
            'harga' => $request->harga,
            'lokasi' => $request->lokasi,
        ]);

        return new FishingResource(true, 'Data Fishing Berhasil Ditambahkan', $fishing);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fishing = Fishing::find($id);

        if (!$fishing) {
            return response()->json(['message' => 'Data Ikan tidak ditemukan'], 404);
        }

        return new FishingResource(true, 'Detail Data Ikan', $fishing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required',
            'lokasi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $fishing = Fishing::find($id);

        if (!$fishing) {
            return response()->json(['message' => 'Data Ikan tidak ditemukan'], 404);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/fishings', $image->hashName());

            Storage::delete('public/fishings/' . $fishing->image);

            $fishing->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'image' => $image->hashName(),
                'harga' => $request->harga,
                'lokasi' => $request->lokasi,
            ]);
        } else {
            $fishing->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'harga' => $request->harga,
                'lokasi' => $request->lokasi,
            ]);
        }

        return new FishingResource(true, 'Data Ikan Berhasil Diupdate', $fishing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fishing = Fishing::find($id);

        if (!$fishing) {
            return response()->json(['message' => 'Data Ikan tidak ditemukan'], 404);
        }

        Storage::delete('public/fishings/' . $fishing->image);
        $fishing->delete();

        return new FishingResource(true, 'Data Ikan Berhasil Dihapus!', null);
    }
}
