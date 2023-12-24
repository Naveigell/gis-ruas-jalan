@extends('layouts.app')

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
@endsection
