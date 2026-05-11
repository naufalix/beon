@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <!--begin::Card Body-->
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <!--begin::Section-->
    <div>
      <!--begin::Heading-->
      <div class="col-12 d-flex">
        <h1 class="me-auto anchor fw-bolder mb-5" id="striped-rounded-bordered">Data Rumah</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah" >Tambah</button>
      </div>
      <!--end::Heading-->
      <!--begin::Block-->
      <div class="my-5 table-responsive">
        <table id="myTable" class="table table-striped table-hover table-rounded border gs-7">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th style="width: 30px">No</th>
              <th>Nomor dan alamat</th>
              <th style="min-width: 150px">Kepala Keluarga</th>
              <th style="width: 100px">Status</th>
              <th style="width: 120px">Penghuni Aktif</th>
              <th style="width: 140px">Terakhir diupdate</th>
              <th style="width: 90px">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($houses as $h)
            @php
              $updated = date_create($h->updated_at);
            @endphp
            <tr>
              <td class="">{{$loop->iteration}}</td>
              <td>
                <b>{{ $h->house_number }}</b> ({{ $h->address ?? '-' }})
              </td>
              <td>
                @if($h->headOfFamily)
                  {{ $h->headOfFamily->full_name }}
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                @if($h->status == 'occupied')
                  <span class="badge bg-primary fs-7">Dihuni</span>
                @else
                  <span class="badge bg-danger fs-7">Kosong</span>
                @endif
              </td>
              <td>
                <span class="badge bg-primary fs-7">{{ $h->activeResidents->count() }} orang</span>
              </td>
              <td>{{date_format($updated,"d F Y")}}</td>
              <td>
                <a href="/admin/house/{{ $h->id }}/resident" class="btn btn-icon btn-sm btn-primary" title="Manage penghuni"><i class="mdi mdi-account fs-3"></i></a>
                <a href="#" class="btn btn-icon btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit" onclick="edit({{ $h->id }})"><i class="bi bi-pencil-fill"></i></a>
                <a href="#" class="btn btn-icon btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapus" onclick="hapus({{ $h->id }})"><i class="fa fa-times"></i></a>
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
        <h3 class="modal-title">Tambah Rumah</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>

      <form class="form" method="post" action="" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <label class="required fw-bold mb-2">Nomor Rumah</label>
              <input type="text" class="form-control" name="house_number" required>
            </div>
            <div class="col-12">
              <label class="fw-bold mb-2">Alamat</label>
              <input type="text" class="form-control" name="address">
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Status</label>
              <select class="form-select" name="status" required>
                <option value="vacant">Kosong</option>
                <option value="occupied">Dihuni</option>
              </select>
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
          <h3 class="modal-title" id="et">Edit Rumah</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="" enctype="multipart/form-data">
          @csrf
          <input type="hidden" id="eid" name="id">
          <div class="modal-body">
            <div class="row g-9">
              <div class="col-12">
                <label class="required fw-bold mb-2">Nomor Rumah</label>
                <input type="text" class="form-control" name="house_number" required>
              </div>
              <div class="col-12">
                <label class="fw-bold mb-2">Alamat</label>
                <input type="text" class="form-control" name="address">
              </div>
              <div class="col-12">
                <label class="required fw-bold mb-2">Status</label>
                <select class="form-select" name="status" required>
                  <option value="vacant">Kosong</option>
                  <option value="occupied">Dihuni</option>
                </select>
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
          <h3 class="modal-title">Hapus Rumah</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="">
          @csrf
          <div class="modal-body text-center">
            <input type="hidden" class="d-none" id="hi" name="id">
            <p class="fw-bold mb-2 fs-4" id="hd">Apakah anda yakin ingin menghapus rumah ini?</p>
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
      url: "/api/house/"+id,
      type: 'GET',
      dataType: 'json', // added data type
      success: function(response) {
        var mydata = response.data;
        $('#edit input[name="id"]').val(id);
        $('#edit input[name="house_number"]').val(mydata.house_number);
        $('#edit input[name="address"]').val(mydata.address);
        $('#edit select[name="status"]').val(mydata.status);
        $("#et").text("Edit Rumah "+mydata.house_number);
      }
    });
  }
  function hapus(id){
    $.ajax({
      url: "/api/house/"+id,
      type: 'GET',
      dataType: 'json', // added data type
      success: function(response) {
        var mydata = response.data;
        $("#hi").val(id);
        $("#hd").text("Apakah anda yakin ingin menghapus rumah "+mydata.house_number+"?");
      }
    });
  }
</script>
@endsection
