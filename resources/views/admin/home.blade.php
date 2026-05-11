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
      <div class="row g-4 mb-8">
        <div class="col-md-3 col-6">
          <div class="border rounded p-4 text-center h-100" style="border-left: 4px solid #8e62a9 !important;">
            <p class="text-muted mb-1 fs-7">Saldo Kas</p>
            <h4 class="fw-bold mb-0 {{ $saldoKas >= 0 ? 'text-dark' : 'text-danger' }}">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h4>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="border rounded p-4 text-center h-100" style="border-left: 4px solid #50cd89 !important;">
            <p class="text-muted mb-1 fs-7">Pemasukan Bulan Ini</p>
            <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</h4>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="border rounded p-4 text-center h-100" style="border-left: 4px solid #f1416c !important;">
            <p class="text-muted mb-1 fs-7">Pengeluaran Bulan Ini</p>
            <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</h4>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="border rounded p-4 text-center h-100" style="border-left: 4px solid #ffc700 !important;">
            <p class="text-muted mb-1 fs-7">Tagihan Belum Lunas</p>
            <h4 class="fw-bold mb-0">{{ $unpaidCount }}</h4>
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