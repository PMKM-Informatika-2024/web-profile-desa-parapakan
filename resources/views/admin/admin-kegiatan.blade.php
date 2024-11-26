@extends('layouts.adminlayout')
@section('child')
    <main class="content px-3 py-2">
        <div class="container-fluid" id="admin-kegiatan">
            <div class="mt-3 mb-3">
                <h4>Kelola Kegiatan</h4>
            </div>
            <div class="row">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="card" id="kelolaKegiatanCard">
                                <div class="card-body">
                                    <h5>Kelola Kegiatan
                                        <hr>
                                    </h5>
                                    <!-- Form Input Kegiatan -->
                                    <form id="tambahKegiatanForm" method="POST" action="{{ route('kegiatan.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row mb-3">
                                            <label for="namaKegiatan" class="col-lg-2 col-md-3 col-sm-4 form-label">Nama
                                                Kegiatan:</label>
                                            <div class="col-lg-10 col-md-9 col-sm-8">
                                                <input name="nama_kegiatan" type="text" class="form-control"
                                                    id="namaKegiatan" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label for="fotoKegiatan"
                                                class="col-lg-2 col-md-3 col-sm-4 form-label">Foto:</label>
                                            <div class="col-lg-10 col-md-9 col-sm-8">
                                                <input name="gambar_kegiatan" type="file" class="form-control"
                                                    id="fotoKegiatan" accept="image/*" required>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="submit" class="btn btn-simpan">Simpan</button>
                                            <button type="button" onclick="resetForm()"
                                                class="btn btn-batal ms-2">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Kegiatan -->
                    <table class="table table-bordered" id="kegiatanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kegiatan</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kegiatanTableBody">
                            @foreach ($kelolakegiatans as $kelolakegiatan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $kelolakegiatan->nama_kegiatan }}</td>
                                    <td>
                                        @if ($kelolakegiatan->gambar_kegiatan)
                                            <img src="{{ asset('storage/' . $kelolakegiatan->gambar_kegiatan) }}" alt="Gambar Kegiatan" class="img-thumbnail" style="width: 50px; height: 50px;">
                                        @else
                                            Tidak Ada Gambar
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editKegiatanModal"
                                            onclick="loadEditData({{ $kelolakegiatan }})">Edit</button>
                                        <form action="/kegiatan/{{ $kelolakegiatan->id }}" method="POST" class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <input type="text" name="id" value="{{ $kelolakegiatan->id }}" hidden>
                                            <input type="text" name="gambar_kegiatan" value="{{ $kelolakegiatan->gambar_kegiatan }}" hidden>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kegiatan {{ $kelolakegiatan->nama_kegiatan }}?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>                        
                    </table>

                    <!-- Modal untuk Edit Kegiatan -->
                    <div class="modal fade" id="editKegiatanModal" tabindex="-1" aria-labelledby="editKegiatanModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editKegiatanModalLabel">Edit Kegiatan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editKegiatanForm" method="POST" enctype="multipart/form-data">
                                        @method('put')
                                        @csrf
                                        <input type="text" id="editId" hidden name="id">
                                        <input type="hidden" name="oldImage" id="editFotoKegiatan">
                                        <div class="mb-3">
                                            <label for="editNamaKegiatan" class="form-label">Nama Kegiatan</label>
                                            <input name="nama_kegiatan" type="text" class="form-control"
                                                id="editNamaKegiatan" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editFotoKegiatan" class="form-label">Foto</label>
                                            <img alt="" id="previewImage" class="img-thumbnail"
                                                style="width: 50px; height: 50px;">
                                            <input name="gambar_kegiatan" type="file" class="form-control"
                                                accept="image/*" onchange="changeImage(event)">
                                        </div>
                                        <button type="submit" class="btn btn-edit">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('kodejs')
    <script>
        function loadEditData(kelolakegiatan) {
            console.log(kelolakegiatan);
            
            // Isi nilai input dengan data dari parameter
            document.getElementById('editId').value = kelolakegiatan.id;
            document.getElementById('editFotoKegiatan').value = kelolakegiatan.gambar_kegiatan;
            document.getElementById('editNamaKegiatan').value = kelolakegiatan.nama_kegiatan;
            const previewImage = document.getElementById('previewImage');
            if (kelolakegiatan.gambar_kegiatan) {
                previewImage.src = `/storage/${kelolakegiatan.gambar_kegiatan}`;
            } else {
                previewImage.src = ''; // Kosongkan jika tidak ada foto
            }

            // Ubah action form untuk mengarahkan ke route update yang sesuai
            const editForm = document.getElementById('editKegiatanForm');
            editForm.action = `/kegiatan/${kelolakegiatan.id}`;
        }

        function changeImage(event) {
            const previewImage = document.getElementById('previewImage');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log(e.target.result);

                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function resetForm() {
            document.getElementById("tambahKegiatanForm").reset();
        }
    </script>
@endsection
