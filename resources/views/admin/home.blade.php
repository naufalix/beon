@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <div>
      <div class="row mb-5">
        <div class="col-12 col-md-6">
          <h1 class="anchor fw-bolder mb-5">Selamat datang, {{auth()->user()->name}}</h1>
        </div>
      </div>

      {{-- Summary Cards --}}
      <div class="row mb-5">
        <div class="col-12 col-md-3 mb-3">
          <div class="card bg-light-primary">
            <div class="card-body text-center">
              <h6 class="text-muted">Saldo Kas</h6>
              <h2 class="fw-bolder h1 {{ $saldoKas >= 0 ? 'text-dark' : 'text-danger' }}">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
          <div class="card bg-light-success">
            <div class="card-body text-center">
              <h6 class="text-muted">Pemasukan Bulan Ini</h6>
              <h2 class="fw-bolder text-success h1">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
          <div class="card bg-light-danger">
            <div class="card-body text-center">
              <h6 class="text-muted">Pengeluaran Bulan Ini</h6>
              <h2 class="fw-bolder text-danger h1">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
          <div class="card bg-light-warning">
            <div class="card-body text-center">
              <h6 class="text-muted">Tagihan Belum Lunas</h6>
              <h2 class="fw-bolder text-warning h1">{{ number_format($unpaidCount, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
      </div>

      {{-- Chart --}}
      <h3 class="fw-bold mb-4">Grafik Pemasukan vs Pengeluaran (12 Bulan)</h3>
      <canvas id="grafik" class="mh-400px"></canvas>

    </div>
  </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  var ctx = document.getElementById('grafik').getContext('2d');
  var grafik = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: {!! $chartLabels !!},
      datasets: [
        {
          label: 'Pemasukan',
          data: {!! $chartIncome !!},
          backgroundColor: 'rgba(80, 205, 137, 0.7)',
          borderColor: '#50cd89',
          borderWidth: 2,
          borderRadius: 4,
        },
        {
          label: 'Pengeluaran',
          data: {!! $chartExpense !!},
          backgroundColor: 'rgba(241, 65, 108, 0.7)',
          borderColor: '#f1416c',
          borderWidth: 2,
          borderRadius: 4,
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
            }
          }
        }
      }
    }
  });
</script>
@endsection