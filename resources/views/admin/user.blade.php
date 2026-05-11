@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <!--begin::Card Body-->
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <!--begin::Section-->
    <div>
      <!--begin::Heading-->
      <div class="col-12 d-flex">
        <h1 class="me-auto anchor fw-bolder mb-5" id="striped-rounded-bordered">Data pegawai</h1>
        <button class="btn btn-dark bg-purple" data-bs-toggle="modal" data-bs-target="#tambah" >Tambah</button>
      </div>
      <!--end::Heading-->
      <!--begin::Block-->
      <div class="my-5 table-responsive">
        <table id="myTable" class="table table-striped table-hover table-rounded border gs-7">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th class="width: 30px">No</th>
              <th>Nama</th>
              <th style="min-width: 120px">Email</th>
              <th style="min-width: 120px">Jabatan</th>
              <th style="min-width: 150px">Terakhir diupdate</th>
              <th style="min-width: 170px">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $u)
            @php
              $updated = date_create($u->updated_at);
            @endphp
            <tr>
              <td class="">{{$loop->iteration}}</td>
              <td style="min-width: 320px;">{{ $u->name }}  </td>
              <td>{{ $u->email }}  </td>
              <td>
                <span class="badge bg-purple">{{ $u->position }}</span>
              </td>
              <td>{{date_format($updated,"d F Y")}}</td>
              <td>
                <a href="/admin/user/{{ $u->id }}" class="btn btn-purple text-white py-1 px-3 fs-7" target="_blank">Lihat laporan</a>
                <a href="#" class="btn btn-icon btn-sm btn-primary bg-purple" data-bs-toggle="modal" data-bs-target="#edit" onclick="edit({{ $u->id }})"><i class="bi bi-pencil-fill"></i></a>
                <a href="#" class="btn btn-icon btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapus" onclick="hapus({{ $u->id }})"><i class="fa fa-times"></i></a>
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
        <h3 class="modal-title">Tambah pegawai</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>

      <form class="form" method="post" action="" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12">
              <label class="required fw-bold mb-2">Nama pegawai</label>
              <input type="text" class="form-control" name="name" required>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Jabatan</label>
              <input type="text" class="form-control" name="position" required>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Password</label>
              <input type="password" class="form-control" name="password" required>
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
          <h3 class="modal-title" id="et">Edit pegawai</h3>
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
                <label class="required fw-bold mb-2">Nama pegawai</label>
                <input type="text" class="form-control" name="name" required>
              </div>
              <div class="col-12">
                <label class="required fw-bold mb-2">Jabatan</label>
                <input type="text" class="form-control" name="position" required>
              </div>
              <div class="col-12">
                <label class="required fw-bold mb-2">Email</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="col-12">
                <label class="fw-bold mb-2">Password</label>
                <input type="password" class="form-control" name="password">
                <p class="mb-0 text-danger">*Kosongkan jika tidak ingin mengganti password</p>
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
          <h3 class="modal-title">Hapus pegawai</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="">
          @csrf
          <div class="modal-body text-center">
            <input type="hidden" class="d-none" id="hi" name="id">
            <p class="fw-bold mb-2 fs-4" id="hd">Apakah anda yakin ingin menghapus pegawai ini?</p>
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
      url: "/api/user/"+id,
      type: 'GET',
      dataType: 'json', // added data type
      success: function(response) {
        var mydata = response.data;
        $('#edit input[name="id"]').val(id);
        $('#edit input[name="name"]').val(mydata.name);
        $('#edit input[name="email"]').val(mydata.email);
        $('#edit input[name="position"]').val(mydata.position);
        $("#et").text("Edit "+mydata.name);
      }
    });
  }
  function hapus(id){
    $.ajax({
      url: "/api/user/"+id,
      type: 'GET',
      dataType: 'json', // added data type
      success: function(response) {
        //alert(JSON.stringify(mydata));
        var mydata = response.data;
        $("#hi").val(id);
        $("#hd").text("Apakah anda yakin ingin menghapus "+mydata.name+"?");
      }
    });
  }
</script>
@endsection