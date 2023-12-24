@extends('layouts.app')

@push('style')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js" integrity="sha256-5+4UA0RwHxrLdxuo+/LioZkIerSs8F/VDnV4Js9ZdwQ=" crossorigin="anonymous"></script>
@endpush

@section('section')
    <div class="container mt-5">
        <a href="{{ route('roads.create') }}" class="btn btn-primary">Tambah Ruas Jalan</a>
        <br><br>
        <table class="table table-dark">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Desa Id</th>
                <th scope="col">Kode Ruas</th>
                <th scope="col">Nama Ruas</th>
                <th scope="col">Keterangan</th>
                <th scope="col">Aksi</th>
            </tr>
            </thead>
            <tbody>
                @forelse($roads as $road)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $road->desa_id }}</td>
                        <td>{{ $road->kode_ruas }}</td>
                        <td>{{ $road->nama_ruas }}</td>
                        <td>{{ $road->keterangan }}</td>
                        <td>
                            <a href="{{ route('roads.edit', $road) }}" class="btn btn-warning">Edit</a>
                            <button data-action="{{ route('roads.destroy', $road) }}" class="btn btn-danger btn-delete">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Data Kosong</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <form action="" class="" id="form" method="post">@csrf @method('DELETE')</form>
@endsection

@push('script')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" integrity="sha256-ZCK10swXv9CN059AmZf9UzWpJS34XvilDMJ79K+WOgc=" crossorigin="anonymous">
    <script>
        $('.btn-delete').on('click', function () {
            Swal.fire({
                title: "Yakin hapus jalan?",
                showDenyButton: true,
                confirmButtonText: "Iya",
                denyButtonText: `Batal`
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('#form');

                    form.attr('action', $(this).data('action'));
                    form.submit();
                }
            });
        })
    </script>
@endpush
