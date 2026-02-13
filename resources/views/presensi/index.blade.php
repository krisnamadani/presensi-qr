@extends('layouts.app')

@section('title', 'Data Presensi')

@section('content')
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kabupaten</th>
                        <th>Total Peserta</th>
                        <th>Hadir</th>
                        <th>Belum Hadir</th>
                        <th>Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->kabupaten }}</td>
                            <td>{{ $row->total_peserta }}</td>
                            <td>{{ $row->jumlah_hadir }}</td>
                            <td>{{ $row->total_peserta - $row->jumlah_hadir }}</td>
                            <td>
                                @if ($row->total_peserta > 0)
                                    {{ round(($row->jumlah_hadir / $row->total_peserta) * 100, 1) }} %
                                @else
                                    0 %
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tr class="table-primary">
                    <th colspan="2">TOTAL</th>
                    <th>{{ $data->sum('total_peserta') }}</th>
                    <th>{{ $data->sum('jumlah_hadir') }}</th>
                    <th>{{ $data->sum('total_peserta') - $data->sum('jumlah_hadir') }}</th>
                    <th>
                        {{ round(($data->sum('jumlah_hadir') / $data->sum('total_peserta')) * 100, 1) }} %
                    </th>
                </tr>
            </table>
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-table"></i> Data Presensi</h5>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="card mb-3 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-filter"></i> Filter Data
                            </h6>
                            <form method="GET" action="{{ route('data.presensi.index') }}" id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="sesi" class="form-label">Filter Sesi</label>
                                        <select class="form-select" id="sesi" name="sesi">
                                            <option value="">Semua Sesi</option>
                                            @foreach ($sesis as $sesi)
                                                <option value="{{ $sesi }}"
                                                    {{ request('sesi') == $sesi ? 'selected' : '' }}>
                                                    {{ $sesi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="kabupaten" class="form-label">Filter Kabupaten</label>
                                        <select class="form-select" id="kabupaten" name="kabupaten">
                                            <option value="">Semua Kabupaten</option>
                                            @foreach ($kabupatens as $kab)
                                                <option value="{{ $kab }}"
                                                    {{ request('kabupaten') == $kab ? 'selected' : '' }}>
                                                    {{ $kab }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search"></i> Tampilkan
                                        </button>
                                        <a href="{{ route('data.presensi.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Info Filter Aktif -->
                    @if (request('nip') || request('kabupaten'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i> <strong>Filter Aktif:</strong>
                            @if (request('nip'))
                                <span class="badge bg-primary">NIP: {{ request('nip') }}</span>
                            @endif
                            @if (request('kabupaten'))
                                <span class="badge bg-success">Kabupaten: {{ request('kabupaten') }}</span>
                            @endif
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table id="presensiTable" class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Sesi</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Kabupaten</th>
                                    <th>Jam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensis as $presensi)
                                    <tr>
                                        <td>{{ $presensi->id }}</td>
                                        <td><span class="badge bg-success">{{ $presensi->sesi }}</span></td>
                                        <td>{{ $presensi->nip }}</td>
                                        <td>{{ $presensi->nama }}</td>
                                        <td>{{ $presensi->kabupaten }}</td>
                                        <td><span class="badge bg-info">{{ $presensi->jam }}</span></td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Jumlah Data -->
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Total: <strong>{{ $presensis->count() }}</strong> data
                            @if (request('nip') || request('kabupaten'))
                                (terfilter)
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#presensiTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    zeroRecords: "Data tidak ditemukan",
                    emptyTable: "Tidak ada data di tabel"
                },
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "Semua"]
                ],
                order: [
                    [0, 'desc']
                ], // Urutkan berdasarkan ID descending
                responsive: true
            });
        });
    </script>
@endpush
