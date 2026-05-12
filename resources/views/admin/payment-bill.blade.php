@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <!--begin::Card Body-->
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <!--begin::Section-->
    <div>
      <!--begin::Heading-->
      <div class="col-12 d-flex flex-wrap align-items-center gap-2 mb-5">
        <h1 class="me-auto anchor fw-bolder mb-0" id="striped-rounded-bordered">Tagihan Bulanan</h1>
        <div class="col-12 col-md-6 d-flex" style="zoom: 80%;">
          <button class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#tambah">Tambah</button>
          <button class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#generate"><i class="mdi mdi-refresh me-1"></i>Generate Tagihan</button>
          <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#payBulk"><i class="mdi mdi-cash-multiple me-1"></i>Bayar Bulk</button>
        </div>
      </div>
      <!--end::Heading-->

      @php
        $totalTagihan = $bills->sum('amount');
        $totalLunas = $bills->where('status','paid')->sum('amount');
        $totalBelum = $bills->where('status','unpaid')->sum('amount');
      @endphp
      <div class="row mb-5">
        <div class="col-12 col-md-4 mb-3">
          <div class="card bg-light-primary">
            <div class="card-body text-center">
              <h6 class="text-muted">Total Tagihan</h6>
              <h2 class="fw-bolder text-info h1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
          <div class="card bg-light-success">
            <div class="card-body text-center">
              <h6 class="text-muted">Sudah Lunas</h6>
              <h2 class="fw-bolder text-success h1">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
          <div class="card bg-light-danger">
            <div class="card-body text-center">
              <h6 class="text-muted">Belum Lunas</h6>
              <h2 class="fw-bolder text-danger h1">Rp {{ number_format($totalBelum, 0, ',', '.') }}</h2>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex">
        <span class="my-auto me-2">Filter : </span>
        <form method="get" action="" class="d-flex align-items-center gap-2">
          <input type="month" class="form-control form-control-sm" name="month" value="{{ $month }}" onchange="this.form.submit()">
        </form>
      </div>

      <!--begin::Block-->
      <div class="my-5 table-responsive">
        <table id="myTable" class="table table-striped table-hover table-rounded border gs-7">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th style="min-width: 30px">No</th>
              <th style="min-width: 100px">Rumah</th>
              <th style="min-width: 200px">Penghuni</th>
              <th style="min-width: 90px">Jenis Iuran</th>
              <th style="min-width: 100px">Bulan</th>
              <th style="min-width: 90px">Nominal</th>
              <th style="min-width: 100px">Status</th>
              <th style="min-width: 130px">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($bills as $b)
            <tr>
              <td>{{$loop->iteration}}</td>
              <td>{{ $b->house->house_number ?? '-' }}</td>
              <td>
                {{ $b->resident->full_name ?? '-' }} 
                {{ (isset($b->resident) && $b->resident->is_head_of_family) ? '(Kepala)' : '' }}
              </td>
              <td>{{ $b->feeType->name ?? '-' }}</td>
              <td>{{ \Carbon\Carbon::parse($b->billing_month)->translatedFormat('F Y') }}</td>
              <td>Rp {{ number_format($b->amount, 0, ',', '.') }}</td>
              <td>
                @if($b->status == 'paid')
                  <span class="badge bg-success">Lunas ({{ $b->payment->payment_method }})</span>
                @else
                  <span class="badge bg-danger">Belum Bayar</span>
                @endif
              </td>
              <td>
                @if($b->status == 'unpaid')
                  <button class="btn btn-sm btn-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#bayar" onclick="bayar({{ $b->id }})" style="font-size: 11px !important; height: 25.3px;"><i class="mdi mdi-cash"></i>Bayar</button>
                  <button class="btn btn-icon btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapus" onclick="hapus({{ $b->id }})"><i class="fa fa-times"></i></button>
                @else
                  <small class="text-muted">
                    <i class="mdi mdi-check-circle text-success me-1"></i>
                    {{ $b->payment ? $b->payment->paid_at->format('d/m/Y H:i') : '-' }}
                  </small>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!--end::Block-->
    </div>
    <!--end::Section-->
  </div>
  <!--end::Card Body-->
</div>

{{-- Modal Generate --}}
<div class="modal fade" tabindex="-1" id="generate">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Generate Tagihan</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>
      <form method="post" action="/admin/payment-bill/generate">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <label class="required fw-bold mb-2">Bulan Tagihan</label>
              <input type="month" class="form-control" name="billing_month" value="{{ $month }}" required>
            </div>
            <div class="col-12">
              <div class="alert alert-info py-3 mb-0">
                <i class="mdi mdi-information me-1"></i>
                Tagihan akan dibuat untuk semua rumah yang memiliki penghuni aktif. Resident dipilih dari kepala keluarga, jika tidak ada maka ambil resident pertama. Tagihan yang sudah ada akan di-skip.
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Generate</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Bayar --}}
<div class="modal fade" tabindex="-1" id="bayar">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Konfirmasi Pembayaran</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>
      <form method="post" action="/admin/payment-bill/pay">
        @csrf
        <input type="hidden" name="bill_id" id="bayar_bill_id">
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <p class="fw-bold fs-5" id="bayar_info"></p>
            </div>
            <div class="col-12">
              <label class="fw-bold mb-2">Metode Pembayaran</label>
              <select class="form-select" name="payment_method">
                <option value="cash">Cash</option>
                <option value="transfer">Transfer Bank</option>
              </select>
            </div>
            <div class="col-12">
              <label class="fw-bold mb-2">Catatan</label>
              <textarea class="form-control" name="note" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Bayar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Pay Bulk --}}
<div class="modal fade" tabindex="-1" id="payBulk">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Bayar Bulk (Beberapa Bulan)</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>
      <form method="post" action="/admin/payment-bill/pay-bulk">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <label class="required fw-bold mb-2">Rumah</label>
              <select class="form-select" name="house_id" id="house_id_bulk" required onchange="loadResidentsBulk(this.value)">
                <option value="">-- Pilih Rumah --</option>
                @foreach ($houses as $h)
                  <option value="{{ $h->id }}">{{ $h->house_number }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Penghuni</label>
              <select class="form-select" name="resident_id" id="resident_id_bulk" required disabled>
                <option value="">-- Pilih Rumah Terlebih Dahulu --</option>
              </select>
              <small class="text-muted">Pilih penghuni yang akan ditagih</small>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Jenis Iuran</label>
              <select class="form-select" name="fee_type_id" required>
                <option value="">-- Pilih Jenis Iuran --</option>
                @foreach ($feeTypes as $ft)
                  <option value="{{ $ft->id }}">{{ $ft->name }} (Rp {{ number_format($ft->amount, 0, ',', '.') }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-6">
              <label class="required fw-bold mb-2">Bulan Mulai</label>
              <input type="month" class="form-control" name="start_month" value="{{ $month }}" required>
            </div>
            <div class="col-6">
              <label class="required fw-bold mb-2">Jumlah Bulan</label>
              <input type="number" class="form-control" name="months" min="1" max="12" value="12" required>
            </div>
            <div class="col-12">
              <label class="fw-bold mb-2">Metode Pembayaran</label>
              <select class="form-select" name="payment_method">
                <option value="cash">Cash</option>
                <option value="transfer">Transfer Bank</option>
              </select>
            </div>
            <div class="col-12">
              <div class="alert alert-info py-3 mb-0">
                <i class="mdi mdi-information me-1"></i>
                Fitur ini akan menambahkan tagihan baru jika belum ada. Jika tagihan sudah ada dan sudah dibayar, akan di-skip.
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-info text-white">Bayar Bulk</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" tabindex="-1" id="tambah">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Tambah Tagihan Satuan</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>
      <form method="post" action="">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <label class="required fw-bold mb-2">Rumah</label>
              <select class="form-select" name="house_id" id="house_id_tambah" required onchange="loadResidents(this.value)">
                <option value="">-- Pilih Rumah --</option>
                @foreach ($houses as $h)
                  <option value="{{ $h->id }}">{{ $h->house_number }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Penghuni</label>
              <select class="form-select" name="resident_id" id="resident_id_tambah" required disabled>
                <option value="">-- Pilih Rumah Terlebih Dahulu --</option>
              </select>
              <small class="text-muted">Pilih penghuni yang akan ditagih</small>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Jenis Iuran</label>
              <select class="form-select" name="fee_type_id" required>
                <option value="">-- Pilih Jenis Iuran --</option>
                @foreach ($feeTypes as $ft)
                  <option value="{{ $ft->id }}">{{ $ft->name }} (Rp {{ number_format($ft->amount, 0, ',', '.') }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Bulan Tagihan</label>
              <input type="month" class="form-control" name="billing_month" value="{{ $month }}" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" name="submit" value="store">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Hapus --}}
<div class="modal fade" tabindex="-1" id="hapus">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Hapus Tagihan</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>
      <form class="form" method="post" action="">
        @csrf
        <div class="modal-body text-center">
          <input type="hidden" class="d-none" id="hi" name="id">
          <p class="fw-bold mb-2 fs-4" id="hd">Apakah anda yakin ingin menghapus tagihan ini?</p>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger" name="submit" value="destroy">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script type="text/javascript">
  // Reset form when modal is closed
  $('#tambah').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    $('#resident_id_tambah').html('<option value="">-- Pilih Rumah Terlebih Dahulu --</option>');
    $('#resident_id_tambah').prop('disabled', true);
  });

  $('#payBulk').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    $('#resident_id_bulk').html('<option value="">-- Pilih Rumah Terlebih Dahulu --</option>');
    $('#resident_id_bulk').prop('disabled', true);
  });

  function loadResidents(houseId) {
    const residentSelect = $('#resident_id_tambah');
    
    if (!houseId) {
      residentSelect.html('<option value="">-- Pilih Rumah Terlebih Dahulu --</option>');
      residentSelect.prop('disabled', true);
      return;
    }
    
    // Show loading
    residentSelect.html('<option value="">Loading...</option>');
    residentSelect.prop('disabled', true);
    
    // Fetch residents
    $.ajax({
      url: "/api/house/" + houseId + "/residents",
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        const residents = response.data;
        
        if (residents.length === 0) {
          residentSelect.html('<option value="">-- Tidak Ada Penghuni Aktif --</option>');
          residentSelect.prop('disabled', true);
        } else {
          let options = '<option value="">-- Pilih Penghuni --</option>';
          residents.forEach(function(resident) {
            const badge = resident.is_head_of_family ? ' (Kepala Keluarga)' : '';
            options += `<option value="${resident.id}">${resident.full_name}${badge}</option>`;
          });
          residentSelect.html(options);
          residentSelect.prop('disabled', false);
        }
      },
      error: function() {
        residentSelect.html('<option value="">-- Error Loading Data --</option>');
        residentSelect.prop('disabled', true);
      }
    });
  }

  function loadResidentsBulk(houseId) {
    const residentSelect = $('#resident_id_bulk');
    
    if (!houseId) {
      residentSelect.html('<option value="">-- Pilih Rumah Terlebih Dahulu --</option>');
      residentSelect.prop('disabled', true);
      return;
    }
    
    // Show loading
    residentSelect.html('<option value="">Loading...</option>');
    residentSelect.prop('disabled', true);
    
    // Fetch residents
    $.ajax({
      url: "/api/house/" + houseId + "/residents",
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        const residents = response.data;
        
        if (residents.length === 0) {
          residentSelect.html('<option value="">-- Tidak Ada Penghuni Aktif --</option>');
          residentSelect.prop('disabled', true);
        } else {
          let options = '<option value="">-- Pilih Penghuni --</option>';
          residents.forEach(function(resident) {
            const badge = resident.is_head_of_family ? ' (Kepala Keluarga)' : '';
            options += `<option value="${resident.id}">${resident.full_name}${badge}</option>`;
          });
          residentSelect.html(options);
          residentSelect.prop('disabled', false);
        }
      },
      error: function() {
        residentSelect.html('<option value="">-- Error Loading Data --</option>');
        residentSelect.prop('disabled', true);
      }
    });
  }

  function bayar(id){
    $.ajax({
      url: "/api/payment-bill/"+id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        var mydata = response.data;
        $('#bayar_bill_id').val(id);
        $('#bayar_info').text("Bayar tagihan Rp " + new Intl.NumberFormat('id-ID').format(mydata.amount));
      }
    });
  }
  function hapus(id){
    $("#hi").val(id);
    $("#hd").text("Apakah anda yakin ingin menghapus tagihan ini?");
  }
</script>
@endsection
