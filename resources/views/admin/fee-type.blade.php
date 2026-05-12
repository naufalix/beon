@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <!--begin::Card Body-->
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <!--begin::Section-->
    <div>
      <!--begin::Heading-->
      <div class="col-12 d-flex">
        <h1 class="me-auto anchor fw-bolder mb-5" id="striped-rounded-bordered">Jenis Iuran</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah" >Tambah</button>
      </div>
      <!--end::Heading-->
      <!--begin::Block-->
      <div class="my-5 table-responsive">
        <table id="myTable" class="table table-striped table-hover table-rounded border gs-7">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th style="min-width: 30px">No</th>
              <th style="min-width: 100px">Nama Iuran</th>
              <th style="min-width: 150px">Nominal</th>
              <th style="min-width: 100px">Status</th>
              <th style="min-width: 140px">Terakhir diupdate</th>
              <th style="min-width: 80px">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($feeTypes as $f)
            @php
              $updated = date_create($f->updated_at);
            @endphp
            <tr>
              <td class="">{{$loop->iteration}}</td>
              <td>{{ $f->name }}</td>
              <td>Rp {{ number_format($f->amount, 0, ',', '.') }}</td>
              <td>
                @if($f->is_active)
                  <span class="badge bg-success">Aktif</span>
                @else
                  <span class="badge bg-secondary">Nonaktif</span>
                @endif
              </td>
              <td>{{date_format($updated,"d F Y")}}</td>
              <td>
                <a href="#" class="btn btn-icon btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit" onclick="edit({{ $f->id }})"><i class="bi bi-pencil-fill"></i></a>
                <a href="#" class="btn btn-icon btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapus" onclick="hapus({{ $f->id }})"><i class="fa fa-times"></i></a>
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

<div class="modal fade" tabindex="-1" id="tambah">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Tambah Jenis Iuran</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>

      <form class="form" method="post" action="">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <label class="required fw-bold mb-2">Nama Iuran</label>
              <input type="text" class="form-control" name="name" required>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Nominal (Rp)</label>
              <input type="number" class="form-control" name="amount" min="0" required>
            </div>
            <div class="col-12">
              <label class="fw-bold mb-2">Status</label>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" checked>
                <label class="form-check-label">Aktif</label>
              </div>
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

<div class="modal fade" tabindex="-1" id="edit">
  <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="et">Edit Jenis Iuran</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="">
          @csrf
          <input type="hidden" id="eid" name="id">
          <div class="modal-body">
            <div class="row g-9">
              <div class="col-12">
                <label class="required fw-bold mb-2">Nama Iuran</label>
                <input type="text" class="form-control" name="name" required>
              </div>
              <div class="col-12">
                <label class="required fw-bold mb-2">Nominal (Rp)</label>
                <input type="number" class="form-control" name="amount" min="0" required>
              </div>
              <div class="col-12">
                <label class="fw-bold mb-2">Status</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                  <label class="form-check-label">Aktif</label>
                </div>
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

<div class="modal fade" tabindex="-1" id="hapus">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Hapus Jenis Iuran</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="">
          @csrf
          <div class="modal-body text-center">
            <input type="hidden" class="d-none" id="hi" name="id">
            <p class="fw-bold mb-2 fs-4" id="hd">Apakah anda yakin ingin menghapus jenis iuran ini?</p>
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
      url: "/api/fee-type/"+id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        var mydata = response.data;
        $('#edit input[name="id"]').val(id);
        $('#edit input[name="name"]').val(mydata.name);
        $('#edit input[name="amount"]').val(mydata.amount);
        $('#edit_is_active').prop('checked', mydata.is_active);
        $("#et").text("Edit "+mydata.name);
      }
    });
  }
  function hapus(id){
    $.ajax({
      url: "/api/fee-type/"+id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        var mydata = response.data;
        $("#hi").val(id);
        $("#hd").text("Apakah anda yakin ingin menghapus "+mydata.name+"?");
      }
    });
  }
</script>
@endsection
