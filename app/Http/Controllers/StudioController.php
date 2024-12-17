<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\Film;
use Illuminate\Http\Request;

class StudioController extends Controller
{
    /**
     * Menampilkan semua studio
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Ambil semua data studio
        $studios = Studio::all();
        
        // Mengembalikan respons dalam bentuk JSON
        return response()->json($studios);
    }

    /**
     * Menampilkan detail studio berdasarkan ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Cari studio berdasarkan ID
        $studio = Studio::find($id);

        // Jika studio tidak ditemukan
        if (!$studio) {
            return response()->json(['message' => 'Studio tidak ditemukan'], 404);
        }

        // Mengembalikan respons studio yang ditemukan
        return response()->json($studio);
    }

    /**
     * Membuat studio baru
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima dari request
        $validatedData = $request->validate([
            'nama_studio' => 'required|string|max:255',
            'jumlah_kursi' => 'required|integer',
            'jadwal_tayang' => 'required|string|max:255',
            'id_film' => 'required|exists:films,id', // Validasi ID Film yang terkait
        ]);

        // Membuat studio baru dengan data yang sudah tervalidasi
        $studio = Studio::create($validatedData);

        // Mengembalikan respons sukses setelah studio dibuat
        return response()->json($studio, 201);
    }

    /**
     * Memperbarui studio berdasarkan ID
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Cari studio berdasarkan ID
        $studio = Studio::find($id);

        // Jika studio tidak ditemukan
        if (!$studio) {
            return response()->json(['message' => 'Studio tidak ditemukan'], 404);
        }

        // Validasi data yang diterima untuk pembaruan
        $validatedData = $request->validate([
            'nama_studio' => 'sometimes|required|string|max:255',
            'jumlah_kursi' => 'sometimes|required|integer',
            'jadwal_tayang' => 'sometimes|required|string|max:255',
        ]);

        // Update data studio
        $studio->update($validatedData);

        // Mengembalikan respons sukses setelah studio diperbarui
        return response()->json($studio);
    }

    /**
     * Menghapus studio berdasarkan ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Cari studio berdasarkan ID
        $studio = Studio::find($id);

        // Jika studio tidak ditemukan
        if (!$studio) {
            return response()->json(['message' => 'Studio tidak ditemukan'], 404);
        }

        // Hapus studio
        $studio->delete();

        // Mengembalikan respons sukses setelah studio dihapus
        return response()->json(['message' => 'Studio berhasil dihapus']);
    }

    /**
     * Menampilkan studio berdasarkan filmId
     *
     * @param int $filmId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showStudiosByFilm($filmId)
    {
        // Cari film berdasarkan filmId
        $film = Film::find($filmId);

        // Jika film tidak ditemukan
        if (!$film) {
            return response()->json(['message' => 'Film tidak ditemukan'], 404);
        }

        // Ambil studio yang terkait dengan film
        $studios = $film->studios; // Pastikan relasi sudah benar di model Film

        // Mengembalikan respons studio yang terkait dengan film
        return response()->json($studios);
    }
}
