@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <!--begin::Card Body-->
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <!--begin::Section-->
    <div>
      <!--begin::Heading-->
      <div class="col-12 d-flex flex-wrap align-items-center gap-2 mb-5">
        <h1 class="me-auto anchor fw-bolder mb-0" id="striped-rounded-bordered">Data Pengeluaran</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">Tambah</button>
      </div>
      <!--end::Heading-->

      <div class="row mb-5">
        <div class="col-12 mb-3">
          <div class="card bg-light-danger">
            <div class="card-body text-center">
              <h6 class="text-muted">Total Pengeluaran Bulan Ini</h6>
              <h2 class="fw-bolder text-danger h1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h2>
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
              <th style="width: 30px">No</th>
              <th>Keterangan</th>
              <th style="min-width: 160px">Kategori</th>
              <th style="min-width: 100px">Nominal</th>
              <th style="min-width: 150px">Tanggal</th>
              <th style="min-width: 90px">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($expenses as $e)
            <tr>
              <td>{{$loop->iteration}}</td>
              <td>{{ $e->description ?? '-' }}</td>
              <td>
                @if($e->category->is_recurring ?? false)
                  <span class="badge bg-primary">{{ $e->category->name ?? '-' }}</span>
                @else
                  <span class="badge bg-warning">{{ $e->category->name ?? '-' }}</span>
                @endif
              </td>
              <td>Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
              <td>{{ $e->expense_date->format('d F Y') }}</td>
              <td>
                <a href="#" class="btn btn-icon btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit" onclick="edit({{ $e->id }})"><i class="bi bi-pencil-fill"></i></a>
                <a href="#" class="btn btn-icon btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapus" onclick="hapus({{ $e->id }})"><i class="fa fa-times"></i></a>
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

{{-- Modal Tambah --}}
<div class="modal fade" tabindex="-1" id="tambah">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Tambah Pengeluaran</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>

      <form class="form" method="post" action="">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <label class="required fw-bold mb-2">Kategori</label>
              <select class="form-select" name="category_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $c)
                  <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Nominal (Rp)</label>
              <input type="number" class="form-control" name="amount" min="0" required>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Tanggal</label>
              <input type="date" class="form-control" name="expense_date" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-12">
              <label class="fw-bold mb-2">Keterangan</label>
              <textarea class="form-control" name="description" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" name="submit" value="store">Submit</button>
        </div>
      </form>  
    </div>
  </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" tabindex="-1" id="edit">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="et">Edit Pengeluaran</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>
      <form class="form" method="post" action="">
        @csrf
        <input type="hidden" name="id" id="eid">
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <label class="required fw-bold mb-2">Kategori</label>
              <select class="form-select" name="category_id" id="edit_category_id" required>
                @foreach ($categories as $c)
                  <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Nominal (Rp)</label>
              <input type="number" class="form-control" name="amount" min="0" required>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Tanggal</label>
              <input type="date" class="form-control" name="expense_date" required>
            </div>
            <div class="col-12">
              <label class="fw-bold mb-2">Keterangan</label>
              <textarea class="form-control" name="description" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" name="submit" value="update">Simpan</button>
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
        <h3 class="modal-title">Hapus Pengeluaran</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>
      <form class="form" method="post" action="">
        @csrf
        <div class="modal-body text-center">
          <input type="hidden" class="d-none" id="hi" name="id">
          <p class="fw-bold mb-2 fs-4" id="hd">Apakah anda yakin ingin menghapus pengeluaran ini?</p>
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
  function edit(id){
    $.ajax({
      url: "/api/expense/"+id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        var mydata = response.data;
        $('#edit input[name="id"]').val(id);
        $('#edit select[name="category_id"]').val(mydata.category_id);
        $('#edit input[name="amount"]').val(mydata.amount);
        $('#edit input[name="expense_date"]').val(mydata.expense_date ? mydata.expense_date.substring(0, 10) : '');
        $('#edit textarea[name="description"]').val(mydata.description);
        $("#et").text("Edit Pengeluaran");
      }
    });
  }
  function hapus(id){
    $("#hi").val(id);
    $("#hd").text("Apakah anda yakin ingin menghapus pengeluaran ini?");
  }
</script>
@endsection
