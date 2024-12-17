<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allFilm = Film::all();
        return response()->json($allFilm);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Mendapatkan pengguna yang sedang login
        $user = Auth::user();

        // Memastikan hanya admin yang bisa membuat film baru
        if (strtolower($user->role->value) !== 'admin') {
            return response()->json(['message' => 'Anda bukan admin', 'role' => $user->role], 403);
        }

        // Validasi input, termasuk file gambar dan trailer
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'durasi' => 'required|integer',
            'poster' => 'required|string', // Mengubah validasi poster menjadi string untuk base64
            'rating_usia' => 'required|string|max:5',
            'sinopsis' => 'required|string|max:1000',
            'trailer' => 'required|url|max:255',
        ]);

        // Memproses dan menyimpan gambar poster dari base64
        $posterPath = $this->saveBase64Image($validatedData['poster']);

        // Buat film baru
        $film = Film::create([
            'judul' => $validatedData['judul'],
            'genre' => $validatedData['genre'],
            'durasi' => $validatedData['durasi'],
            'poster' => $posterPath,
            'rating_usia' => $validatedData['rating_usia'],
            'sinopsis' => $validatedData['sinopsis'],
            'trailer' => $validatedData['trailer'],
        ]);

        return response()->json(['film' => $film], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Mendapatkan pengguna yang sedang login
        $user = Auth::user();

        // Memastikan hanya admin yang bisa memperbarui film
        if (strtolower($user->role->value) !== 'admin') {
            return response()->json(['message' => 'Anda bukan admin', 'role' => $user->role], 403);
        }

        // Validasi input
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'durasi' => 'required|integer',
            'rating_usia' => 'required|string|max:5',
            'sinopsis' => 'required|string|max:1000',
            'poster' => 'nullable|string', // Mengubah validasi poster menjadi string untuk base64
            'trailer' => 'nullable|url|max:255', // Validasi untuk link trailer
        ]);

        // Mencari film berdasarkan ID yang diberikan
        $film = Film::find($id);

        // Jika film tidak ditemukan
        if (!$film) {
            return response()->json(['message' => "Film tidak ditemukan"], 404);
        }

        // Jika ada file poster baru (dalam format base64), simpan file dan update path-nya
        if (!empty($validatedData['poster'])) {
            $posterPath = $this->saveBase64Image($validatedData['poster']);
            $validatedData['poster'] = $posterPath; // Mengupdate path file poster baru
        }

        // Update data film
        $film->update($validatedData);

        // Kembalikan data film yang telah diperbarui
        return response()->json($film);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Mendapatkan pengguna yang sedang login
        $user = Auth::user();

        // Memastikan hanya admin yang bisa menghapus film
        if (strtolower($user->role->value) !== 'admin') {
            return response()->json(['message' => 'Anda bukan admin', 'role' => $user->role], 403);
        }

        // Mencari film berdasarkan ID yang diberikan
        $film = Film::find($id);

        // Jika film tidak ditemukan
        if (!$film) {
            return response()->json(['message' => "Film tidak ditemukan"], 404);
        }

        // Hapus film
        $film->delete();

        // Kembalikan pesan bahwa film berhasil dihapus
        return response()->json(['message' => "Film berhasil dihapus."]);
    }

    /**
     * Search for a specific film.
     */
    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
        ]);

        $film = Film::find($validatedData['id']);

        if (!$film) {
            return response()->json(['message' => "Film tidak ditemukan"], 404);
        }

        return response()->json($film, 200);
    }

    /**
     * Helper function to save base64 image.
     */
    private function saveBase64Image($base64Image)
    {
        // Memisahkan data base64 (menghapus bagian prefix 'data:image/xxx;base64,')
        $imageParts = explode(';', $base64Image);
        $imageType = str_replace('data:image/', '', $imageParts[0]);
        $imageBase64 = explode(',', $base64Image)[1];

        // Membuat nama file acak untuk gambar
        $imageName = 'poster_' . Str::random(10) . '.' . $imageType;
        $imagePath = 'posters/' . $imageName;

        // Menyimpan gambar dalam format file
        Storage::disk('public')->put($imagePath, base64_decode($imageBase64));

        return $imagePath;
    }
}
