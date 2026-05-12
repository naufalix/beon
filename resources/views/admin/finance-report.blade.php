@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <div>
      <div class="col-12 d-flex flex-wrap align-items-center gap-2 mb-5">
        <h1 class="me-auto anchor fw-bolder mb-0">Laporan Keuangan</h1>
        <form method="get" action="" class="d-flex align-items-center gap-2">
          <input type="month" class="form-control form-control-sm" name="month" value="{{ $month }}" onchange="this.form.submit()">
        </form>
      </div>

      {{-- Summary Cards --}}
      <div class="row mb-5">
        <div class="col-12 col-md-4 mb-3">
          <div class="card bg-light-success">
            <div class="card-body text-center">
              <h6 class="text-muted">Total Pemasukan</h6>
              <h2 class="fw-bolder text-success h1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
          <div class="card bg-light-danger">
            <div class="card-body text-center">
              <h6 class="text-muted">Total Pengeluaran</h6>
              <h2 class="fw-bolder text-danger h1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
          <div class="card bg-light-primary">
            <div class="card-body text-center">
              <h6 class="text-muted">Selisih</h6>
              <h2 class="fw-bolder h1 {{ $selisih >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($selisih, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
      </div>

      {{-- Tabel Pemasukan --}}
      <h3 class="fw-bold mb-3"><i class="mdi mdi-arrow-down-bold text-success me-1"></i>Pemasukan</h3>
      <div class="mb-5 table-responsive">
        <table class="table table-striped table-hover table-rounded border gs-7">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th style="min-width: 30px">No</th>
              <th style="min-width: 100px">Rumah</th>
              <th style="min-width: 120px">Jenis Iuran</th>
              <th style="min-width: 120px">Nominal</th>
              <th style="min-width: 160px">Tgl Bayar</th>
              <th style="min-width: 100px">Metode</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($income as $i)
            <tr>
              <td>{{$loop->iteration}}</td>
              <td>{{ $i->house->house_number ?? '-' }}</td>
              <td>{{ $i->feeType->name ?? '-' }}</td>
              <td>Rp {{ number_format($i->amount, 0, ',', '.') }}</td>
              <td>{{ $i->payment ? $i->payment->paid_at->format('d F Y') : '-' }}</td>
              <td>
                @if($i->payment && $i->payment->payment_method)
                  <span class="badge bg-primary">{{ $i->payment->payment_method }}</span>
                @else
                  -
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center text-muted">Belum ada pemasukan bulan ini</td>
            </tr>
            @endforelse
          </tbody>
          @if($income->count() > 0)
          <tfoot>
            <tr class="fw-bold">
              <td colspan="3" class="text-end">Total Pemasukan:</td>
              <td colspan="3" class="text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>

      {{-- Tabel Pengeluaran --}}
      <h3 class="fw-bold mb-3"><i class="mdi mdi-arrow-up-bold text-danger me-1"></i>Pengeluaran</h3>
      <div class="mb-5 table-responsive">
        <table class="table table-striped table-hover table-rounded border gs-7">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th style="min-width: 30px">No</th>
              <th style="min-width: 120px">Kategori</th>
              <th style="min-width: 120px">Nominal</th>
              <th style="min-width: 160px">Tanggal</th>
              <th style="min-width: 250px">Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($expenses as $e)
            <tr>
              <td>{{$loop->iteration}}</td>
              <td>
                @if($e->category->is_recurring ?? false)
                  <span class="badge bg-primary">{{ $e->category->name ?? '-' }}</span>
                @else
                  <span class="badge bg-warning">{{ $e->category->name ?? '-' }}</span>
                @endif
              </td>
              <td>Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
              <td>{{ $e->expense_date->format('d F Y') }}</td>
              <td>{{ $e->description ?? '-' }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-muted">Belum ada pengeluaran bulan ini</td>
            </tr>
            @endforelse
          </tbody>
          @if($expenses->count() > 0)
          <tfoot>
            <tr class="fw-bold">
              <td colspan="2" class="text-end">Total Pengeluaran:</td>
              <td colspan="3" class="text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>

    </div>
  </div>
</div>

@endsection
