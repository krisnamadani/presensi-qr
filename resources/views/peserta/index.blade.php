@extends('layouts.app')

@section('title', 'Data Presensi')

@section('content')
    <div class="container">
        <h4 class="mb-3">Rekap Kehadiran per Sesi</h4>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Jumlah Peserta</h6>
                        <h4>{{ $jumlahPeserta }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Jumlah Hadir</h6>
                        <h4>{{ $jumlahHadir }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6>Jumlah Tidak Hadir</h6>
                        <h4>{{ $jumlahTidakHadir }}</h4>
                    </div>
                </div>
            </div>
        </div>


        <!-- FILTER -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <select name="kabupaten" class="form-select">
                        <option value="">-- Semua Kabupaten --</option>
                        @foreach ($kabupatens as $kab)
                            <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>
                                {{ $kab }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <!-- TABEL -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Kabupaten</th>
                    @foreach ($sesis as $sesi)
                        <th class="text-center">Sesi {{ $sesi }}</th>
                    @endforeach
                    <th class="text-center">Total Hadir</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $totalPerSesi = [];
                @endphp

                @foreach ($pesertas as $nip => $rows)
                    @php
                        $peserta = $rows->first();
                        $hadirSesi = $rows->pluck('sesi')->toArray();
                        $totalHadir = count(array_filter($hadirSesi));
                    @endphp

                    <tr>
                        <td>{{ $peserta->nama }}</td>
                        <td>{{ $peserta->kabupaten }}</td>

                        @foreach ($sesis as $sesi)
                            @php
                                $hadir = in_array($sesi, $hadirSesi);
                                if ($hadir) {
                                    $totalPerSesi[$sesi] = ($totalPerSesi[$sesi] ?? 0) + 1;
                                }
                            @endphp

                            <td class="text-center">
                                @if ($hadir)
                                    <span class="badge bg-success">✔</span>
                                @else
                                    <span class="badge bg-danger">✘</span>
                                @endif
                            </td>
                        @endforeach

                        <td class="text-center fw-bold">
                            {{ $totalHadir }}
                        </td>
                    </tr>
                @endforeach

            </tbody>

            <!-- TOTAL ROW -->
            <tfoot class="table-primary">
                <tr>
                    <th colspan="2">TOTAL HADIR</th>

                    @foreach ($sesis as $sesi)
                        <th class="text-center">
                            {{ $totalPerSesi[$sesi] ?? 0 }}
                        </th>
                    @endforeach

                    <th></th>
                </tr>
            </tfoot>

        </table>

    </div>

@endsection
