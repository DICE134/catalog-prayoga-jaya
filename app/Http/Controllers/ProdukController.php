<?php

namespace App\Http\Controllers;

use App\Models\FotoProduk;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Controller produk.
 *
 * Bagian ADMIN (backend, butuh login): index, create, store, edit, update, destroy,
 * storeFoto, destroyFoto — route bernama backend.produk.*
 *
 * Bagian PUBLIK (frontend): produkAll, produkKategori, detail — mengikuti Modul III.
 *
 * Satu controller dipakai untuk keduanya seperti di modul. Route-nya tetap terpisah.
 */
class ProdukController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN (backend)
    |--------------------------------------------------------------------------
    */

    /**
     * READ — daftar semua produk.
     * GET /backend/produk
     */
    public function index(): View
    {
        // with('kategori') = EAGER LOADING: data kategori untuk SEMUA produk diambil
        // sekaligus dalam 1 query tambahan (setara JOIN), bukan 1 query per produk.
        // latest() = urut dari yang terbaru (created_at DESC).
        $produk = Produk::with('kategori')->latest()->get();

        return view('backend.v_produk.index', [
            'judul' => 'Data Produk',
            'produk' => $produk,
        ]);
    }

    /**
     * Form tambah produk.
     * GET /backend/produk/create
     */
    public function create(): View
    {
        return view('backend.v_produk.create', [
            'judul' => 'Tambah Produk',
            // Data master kategori untuk dropdown
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    /**
     * CREATE — simpan produk baru.
     * POST /backend/produk
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturanValidasi());

        // Checkbox yang TIDAK dicentang tidak terkirim sama sekali,
        // jadi nilainya diambil dengan boolean() → true/false.
        $data['unggulan'] = $request->boolean('unggulan');

        // Foto utama opsional. Jika ada, simpan file lalu catat NAMA FILE-nya saja di database.
        unset($data['foto']);
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request->file('foto'));
        }

        $produk = Produk::create($data);

        return redirect()->route('backend.produk.index')
            ->with('success', 'Produk "'.$produk->nama_produk.'" berhasil ditambahkan. Foto tambahan bisa diunggah lewat tombol Ubah.');
    }

    /**
     * Form ubah produk + kelola foto tambahan.
     * GET /backend/produk/{produk}/edit
     */
    public function edit(Produk $produk): View
    {
        // load('fotoProduk') = ambil foto tambahan milik produk ini lewat relasi hasMany
        $produk->load('fotoProduk');

        return view('backend.v_produk.edit', [
            'judul' => 'Ubah Produk',
            'produk' => $produk,
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    /**
     * UPDATE — simpan perubahan produk.
     * PUT /backend/produk/{produk}
     */
    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $data = $request->validate($this->aturanValidasi());
        $data['unggulan'] = $request->boolean('unggulan');

        unset($data['foto']);
        if ($request->hasFile('foto')) {
            // Ada foto baru: hapus file lama dari storage supaya tidak menumpuk, lalu simpan yang baru.
            $this->hapusFoto($produk->foto);
            $data['foto'] = $this->simpanFoto($request->file('foto'));
        }

        $produk->update($data);

        return redirect()->route('backend.produk.index')
            ->with('success', 'Produk "'.$produk->nama_produk.'" berhasil diubah.');
    }

    /**
     * DELETE — hapus produk beserta semua file fotonya.
     * DELETE /backend/produk/{produk}
     */
    public function destroy(Produk $produk): RedirectResponse
    {
        // Baris di tabel foto_produk ikut terhapus otomatis (cascadeOnDelete di migration),
        // tetapi FILE-nya tidak. Jadi file dihapus manual terlebih dahulu.
        foreach ($produk->fotoProduk as $foto) {
            $this->hapusFoto($foto->foto);
        }
        $this->hapusFoto($produk->foto);

        $produk->delete();

        return redirect()->route('backend.produk.index')
            ->with('success', 'Produk "'.$produk->nama_produk.'" berhasil dihapus.');
    }

    /**
     * Unggah satu atau beberapa foto tambahan.
     * POST /backend/produk/{produk}/foto
     */
    public function storeFoto(Request $request, Produk $produk): RedirectResponse
    {
        $request->validate([
            // foto_tambahan[] = beberapa file sekaligus (input <input type="file" multiple>)
            'foto_tambahan' => ['required', 'array', 'max:5'],
            'foto_tambahan.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        foreach ($request->file('foto_tambahan') as $file) {
            // Simpan lewat relasi: produk_id terisi otomatis
            $produk->fotoProduk()->create([
                'foto' => $this->simpanFoto($file),
            ]);
        }

        return redirect()->route('backend.produk.edit', $produk)
            ->with('success', 'Foto tambahan berhasil diunggah.');
    }

    /**
     * Hapus satu foto tambahan.
     * DELETE /backend/foto-produk/{fotoProduk}
     */
    public function destroyFoto(FotoProduk $fotoProduk): RedirectResponse
    {
        $this->hapusFoto($fotoProduk->foto);
        $fotoProduk->delete();

        return redirect()->route('backend.produk.edit', $fotoProduk->produk_id)
            ->with('success', 'Foto tambahan berhasil dihapus.');
    }

    /**
     * Aturan validasi form produk (dipakai store dan update).
     * Pesan error Bahasa Indonesia diambil dari lang/id/validation.php.
     *
     * @return array<string, array<int, mixed>>
     */
    private function aturanValidasi(): array
    {
        return [
            // exists: id kategori harus benar-benar ada di tabel kategori
            'kategori_id' => ['required', 'exists:kategori,id'],
            'nama_produk' => ['required', 'string', 'max:150'],
            'merek' => ['nullable', 'string', 'max:100'],
            'spesifikasi' => ['nullable', 'string', 'max:150'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga' => ['required', 'integer', 'min:0', 'max:999999999'],
            'detail' => ['nullable', 'string'],
            'status' => ['required', Rule::in([0, 1])],
            'unggulan' => ['nullable', 'boolean'],
            // image + mimes: isi file diperiksa benar-benar gambar, bukan sekadar nama berakhiran .jpg
            // max:2048 = maksimal 2048 KB (2 MB)
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Simpan file foto ke storage/app/public/img-produk dengan nama acak,
     * lalu kembalikan NAMA FILE-nya (contoh: "aB3x...9.jpg").
     */
    private function simpanFoto(UploadedFile $file): string
    {
        // store() membuat nama file acak yang unik, sehingga foto dengan nama sama tidak saling menimpa.
        $path = $file->store(Produk::FOLDER_FOTO, 'public');

        return basename($path);
    }

    /**
     * Hapus file foto dari storage (jika ada).
     */
    private function hapusFoto(?string $namaFile): void
    {
        if ($namaFile) {
            Storage::disk('public')->delete(Produk::FOLDER_FOTO.'/'.$namaFile);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIK (frontend)
    |--------------------------------------------------------------------------
    */

    /** Jumlah produk per halaman katalog */
    private const PER_HALAMAN = 9;

    /**
     * Katalog semua produk.
     * Route: 'produk.all' (URL: /produk/all, bisa ditambah ?q=kata-kunci).
     */
    public function produkAll(Request $request): View
    {
        return $this->tampilkanKatalog($request, null);
    }

    /**
     * Katalog produk dalam satu kategori.
     * Route: 'produk.kategori' (URL: /produk/kategori/{id}, bisa ditambah ?q=kata-kunci).
     *
     * $id otomatis diisi Laravel dari bagian {id} pada URL. Contoh: /produk/kategori/3 → $id = '3'.
     */
    public function produkKategori(Request $request, string $id): View
    {
        // findOrFail: jika kategori dengan id itu tidak ada → halaman 404 (bukan error).
        $kategoriAktif = Kategori::findOrFail($id);

        return $this->tampilkanKatalog($request, $kategoriAktif);
    }

    /**
     * Logika katalog yang dipakai BERSAMA oleh produkAll dan produkKategori.
     * Pencarian (?q=) dan filter kategori bisa digabung.
     */
    private function tampilkanKatalog(Request $request, ?Kategori $kategoriAktif): View
    {
        // trim() membuang spasi di awal/akhir. Kata kunci kosong dianggap tidak mencari.
        $cari = trim((string) $request->query('q', ''));

        $produk = Produk::with('kategori')
            // 1. Hanya produk Publis yang boleh dilihat pelanggan
            ->where('status', 1)
            // 2. Filter kategori — when(): klausa where ini HANYA dipasang jika $kategoriAktif tidak null
            ->when($kategoriAktif, fn ($query) => $query->where('kategori_id', $kategoriAktif->id))
            // 3. Pencarian di nama_produk ATAU merek.
            //    orWhere dibungkus fungsi supaya SQL-nya menjadi:
            //      status = 1 AND kategori_id = ? AND (nama_produk LIKE ? OR merek LIKE ?)
            //    Tanpa kurung, OR akan "lolos" dan produk Blok/kategori lain ikut tampil.
            ->when($cari !== '', function ($query) use ($cari) {
                $query->where(function ($query) use ($cari) {
                    $query->where('nama_produk', 'like', '%'.$cari.'%')
                        ->orWhere('merek', 'like', '%'.$cari.'%');
                });
            })
            ->orderBy('nama_produk')
            // paginate(9): ambil 9 produk sesuai ?page= di URL
            ->paginate(self::PER_HALAMAN)
            // appends(): tautan halaman 2, 3, ... tetap membawa ?q= sehingga pencarian tidak hilang
            ->appends($request->query());

        return view('v_produk.index', [
            'judul' => $kategoriAktif ? 'Kategori '.$kategoriAktif->nama_kategori : 'Katalog Produk',
            'produk' => $produk,
            'cari' => $cari,
            'kategoriAktif' => $kategoriAktif,
            // Daftar kategori untuk sidebar filter, beserta jumlah produk Publis
            'daftarKategori' => Kategori::withCount(['produk' => fn ($query) => $query->where('status', 1)])
                ->orderBy('nama_kategori')
                ->get(),
        ]);
    }

    /**
     * Menampilkan detail satu produk.
     * Route: 'produk.detail' (URL: /produk/detail/{id}).
     */
    public function detail(string $id): View
    {
        // firstOrFail(): jika produk tidak ada ATAU berstatus Blok → halaman 404.
        // with([...]) mengambil kategori dan foto tambahan sekaligus.
        $produk = Produk::with(['kategori', 'fotoProduk'])
            ->where('status', 1)
            ->where('id', $id)
            ->firstOrFail();

        // Tautan WhatsApp: https://wa.me/{nomor}?text={pesan}
        // rawurlencode() mengubah spasi dan karakter khusus agar aman dipakai di URL
        // (spasi → %20, & → %26). Tanpa ini, pesan bisa terpotong.
        $pesan = 'Halo Prayoga Jaya, saya ingin bertanya tentang '.$produk->nama_produk;
        $linkWhatsapp = 'https://wa.me/'.config('toko.wa').'?text='.rawurlencode($pesan);

        // Produk serupa: kategori sama, Publis, bukan produk ini sendiri, maksimal 4.
        $produkSerupa = Produk::with('kategori')
            ->where('status', 1)
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->latest()
            ->take(4)
            ->get();

        return view('v_produk.detail', [
            'judul' => $produk->nama_produk,
            'produk' => $produk,
            'linkWhatsapp' => $linkWhatsapp,
            'produkSerupa' => $produkSerupa,
        ]);
    }
}
