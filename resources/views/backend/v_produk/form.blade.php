{{--
    ISIAN FORM PRODUK — dipakai bersama oleh create.blade.php dan edit.blade.php lewat @include.
    Variabel:
      $kategori : daftar kategori untuk dropdown (data master)
      $produk   : produk yang diubah (edit), atau null (tambah)

    Pola nilai awal setiap kolom:  old('kolom', $produk?->kolom)
      - old('kolom')     → ketikan terakhir jika validasi gagal
      - $produk?->kolom  → nilai dari database (edit). Tanda ?-> = jika $produk null, hasilnya null.
--}}
@php($produk = $produk ?? null)

<div class="row g-3">
    {{-- KATEGORI: dropdown dari tabel kategori --}}
    <div class="col-md-6">
        <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
        <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror">
            <option value="">— Pilih kategori —</option>
            @foreach ($kategori as $k)
                {{-- @selected(kondisi) → menulis atribut "selected" jika kondisi benar --}}
                <option value="{{ $k->id }}" @selected(old('kategori_id', $produk?->kategori_id) == $k->id)>
                    {{ $k->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('kategori_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
        <input type="text" name="nama_produk" id="nama_produk" maxlength="150"
               class="form-control @error('nama_produk') is-invalid @enderror"
               value="{{ old('nama_produk', $produk?->nama_produk) }}" placeholder="Contoh: Semen Portland">
        @error('nama_produk')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="merek" class="form-label">Merek</label>
        <input type="text" name="merek" id="merek" maxlength="100"
               class="form-control @error('merek') is-invalid @enderror"
               value="{{ old('merek', $produk?->merek) }}" placeholder="Boleh dikosongkan">
        @error('merek')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="spesifikasi" class="form-label">Spesifikasi / Ukuran</label>
        <input type="text" name="spesifikasi" id="spesifikasi" maxlength="150"
               class="form-control @error('spesifikasi') is-invalid @enderror"
               value="{{ old('spesifikasi', $produk?->spesifikasi) }}" placeholder="Contoh: 50 kg, 1/2 inch, 40 x 40 cm">
        @error('spesifikasi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
        {{-- list="daftar-satuan": saran pilihan, tapi admin tetap boleh mengetik satuan lain --}}
        <input type="text" name="satuan" id="satuan" maxlength="30" list="daftar-satuan"
               class="form-control @error('satuan') is-invalid @enderror"
               value="{{ old('satuan', $produk?->satuan) }}" placeholder="sak, batang, ...">
        <datalist id="daftar-satuan">
            @foreach (['sak', 'batang', 'kaleng', 'galon', 'lembar', 'dus', 'roll', 'buah', 'kg', 'meter', 'm²', 'm³'] as $satuan)
                <option value="{{ $satuan }}">
            @endforeach
        </datalist>
        @error('satuan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-8">
        <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
        <div class="input-group has-validation">
            <span class="input-group-text">Rp</span>
            <input type="number" name="harga" id="harga" min="0" step="1"
                   class="form-control @error('harga') is-invalid @enderror"
                   value="{{ old('harga', $produk?->harga) }}" placeholder="Contoh: 68000 (tanpa titik)">
            @error('harga')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text">Ketik angka saja tanpa titik, misalnya 68000.</div>
    </div>

    <div class="col-12">
        <label for="detail" class="form-label">Deskripsi</label>
        <textarea name="detail" id="detail" rows="4"
                  class="form-control @error('detail') is-invalid @enderror">{{ old('detail', $produk?->detail) }}</textarea>
        @error('detail')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- FOTO UTAMA --}}
    <div class="col-md-8">
        <label for="foto" class="form-label">Foto Utama</label>
        @if ($produk)
            <div class="mb-2">
                <img src="{{ $produk->url_foto }}" alt="Foto {{ $produk->nama_produk }}"
                     class="img-thumbnail" style="max-width: 160px">
                @unless ($produk->foto)
                    <div class="form-text">Belum ada foto, sementara tampil gambar placeholder.</div>
                @endunless
            </div>
        @endif
        {{-- accept: browser hanya menawarkan file gambar (validasi tetap dilakukan di server) --}}
        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp"
               class="form-control @error('foto') is-invalid @enderror">
        @error('foto')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">
            JPG, PNG, atau WEBP, maksimal 2 MB.
            @if ($produk) Kosongkan jika tidak ingin mengganti foto. @endif
        </div>
    </div>

    {{-- STATUS dan UNGGULAN --}}
    <div class="col-md-4">
        <span class="form-label d-block">Status <span class="text-danger">*</span></span>
        @php($statusTerpilih = (string) old('status', $produk?->status ?? 1))
        <div class="form-check">
            <input type="radio" name="status" id="status_publis" value="1" class="form-check-input @error('status') is-invalid @enderror"
                   @checked($statusTerpilih === '1')>
            <label for="status_publis" class="form-check-label">Publis (tampil di website)</label>
        </div>
        <div class="form-check">
            <input type="radio" name="status" id="status_blok" value="0" class="form-check-input @error('status') is-invalid @enderror"
                   @checked($statusTerpilih === '0')>
            <label for="status_blok" class="form-check-label">Blok (disembunyikan)</label>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check form-switch mt-3">
            {{-- Jika validasi gagal ($errors->any()), pakai centangan terakhir; jika tidak, nilai dari database --}}
            <input type="checkbox" name="unggulan" id="unggulan" value="1" class="form-check-input" role="switch"
                   @checked($errors->any() ? old('unggulan') : $produk?->unggulan)>
            <label for="unggulan" class="form-check-label">Produk unggulan (tampil di beranda)</label>
        </div>
    </div>
</div>
