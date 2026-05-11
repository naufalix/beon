@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <!--begin::Card Body-->
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <!--begin::Section-->
    <div>
      <!--begin::Heading-->
      <div class="col-12 d-flex align-items-center mb-5">
        <div class="me-auto">
          <a href="/admin/house" class="text-muted fs-7"><i class="bi bi-arrow-left me-1"></i>Kembali ke Data Rumah</a>
          <h1 class="anchor fw-bolder mb-0 mt-2">Penghuni Rumah {{ $house->house_number }}</h1>
          @if($house->address)
            <p class="text-muted mb-0">{{ $house->address }}</p>
          @endif
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">Tambah Penghuni</button>
      </div>
      <!--end::Heading-->
      <!--begin::Block-->
      <div class="my-5 table-responsive">
        <table id="myTable" class="table table-striped table-hover table-rounded border gs-7">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th style="width: 30px">No</th>
              <th style="min-width: 280px">Nama Lengkap</th>
              <th style="min-width: 80px">Status</th>
              <th style="min-width: 100px">No. HP</th>
              <th style="min-width: 70px">Menikah</th>
              <th style="min-width: 160px">Tanggal menetap</th>
              <th style="min-width: 70px">Aktif</th>
              <th style="min-width: 70px">KTP</th>
              <th style="min-width: 80px">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($residents as $r)
            @php
              $moveIn = $r->move_in_date ? date_format($r->move_in_date, "d/m/Y") : '-';
              $moveOut = $r->move_out_date ? date_format($r->move_out_date, "d/m/Y") : '-';
            @endphp
            <tr>
              <td>{{$loop->iteration}}</td>
              <td>
                {{ $r->full_name }}
                @if($r->is_head_of_family)
                  <span class="text-primary">(Kepala)</span>
                @endif
              </td>
              <td>
                @if($r->status == 'permanent') Tetap
                @else Kontrak
                @endif
              </td>
              <td>{{ $r->phone_number ?? '-' }}</td>
              <td>
                @if($r->is_married)
                  <span class="badge bg-primary">Ya</span>
                @else
                  <span class="badge bg-dark">Tidak</span>
                @endif
              </td>
              <td>
                {{ $moveIn }} -
                @if($moveOut!="-"){{ $moveOut }}
                @else Hari ini
                @endif
              </td>
              <td>
                @if($r->is_active_resident)
                  <span class="badge bg-success">Aktif</span>
                @else
                  <span class="badge bg-danger">Tidak</span>
                @endif
              </td>
              <td>
                @if($r->ktp_photo)
                  <a href="#" data-bs-toggle="modal" data-bs-target="#foto" onclick="foto('{{ $r->ktp_photo }}')">
                    <img src="/storage/img/ktp/{{ $r->ktp_photo }}" class="rounded" style="height: 30px; width: 45px; object-fit: cover;">
                  </a>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                <a href="#" class="btn btn-icon btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit" onclick="edit({{ $r->id }})"><i class="bi bi-pencil-fill"></i></a>
                <a href="#" class="btn btn-icon btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapus" onclick="hapus({{ $r->id }})"><i class="fa fa-times"></i></a>
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Tambah Penghuni</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>

      <form class="form" method="post" action="" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12 col-md-6">
              <label class="required fw-bold mb-2">Nama Lengkap</label>
              <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="required fw-bold mb-2">Status Hunian</label>
              <select class="form-select" name="status" required>
                <option value="permanent">Tetap</option>
                <option value="contract">Kontrak</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="fw-bold mb-2">No. HP</label>
              <input type="text" class="form-control" name="phone_number">
            </div>
            <div class="col-12 col-md-6">
              <label class="fw-bold mb-2">Tanggal Masuk</label>
              <input type="date" class="form-control" name="move_in_date">
            </div>
            <div class="col-12 col-md-6">
              <label class="fw-bold mb-2">Tanggal Keluar</label>
              <input type="date" class="form-control" name="move_out_date">
              <p class="mb-0 text-muted fs-7">*Kosongkan jika masih tinggal (otomatis aktif)</p>
            </div>
            <div class="col-12 col-md-6">
              <label class="fw-bold mb-2">Upload Foto KTP</label>
              <input type="file" class="form-control" name="ktp_photo" accept="image/*">
            </div>
            <div class="col-12 col-md-6 d-flex align-items-end">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_married" id="is_married_add">
                <label class="form-check-label fw-bold" for="is_married_add">Sudah Menikah</label>
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
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="et">Edit Penghuni</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id">
          <div class="modal-body">
            <div class="row g-9">
              <div class="col-12 col-md-6">
                <label class="required fw-bold mb-2">Nama Lengkap</label>
                <input type="text" class="form-control" name="full_name" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="required fw-bold mb-2">Status Hunian</label>
                <select class="form-select" name="status" required>
                  <option value="permanent">Tetap</option>
                  <option value="contract">Kontrak</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="fw-bold mb-2">No. HP</label>
                <input type="text" class="form-control" name="phone_number">
              </div>
              <div class="col-12 col-md-6">
                <label class="fw-bold mb-2">Tanggal Masuk</label>
                <input type="date" class="form-control" name="move_in_date">
              </div>
              <div class="col-12 col-md-6">
                <label class="fw-bold mb-2">Tanggal Keluar</label>
                <input type="date" class="form-control" name="move_out_date">
                <p class="mb-0 text-muted fs-7">*Kosongkan jika masih tinggal (otomatis aktif)</p>
              </div>
              <div class="col-12 col-md-6">
                <label class="fw-bold mb-2">Upload Foto KTP</label>
                <input type="file" class="form-control" name="ktp_photo" accept="image/*">
                <p class="mb-0 text-danger fs-7">*Kosongkan jika tidak ingin mengganti foto</p>
              </div>
              <div class="col-12 col-md-6 d-flex align-items-end">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_married" id="is_married_edit">
                  <label class="form-check-label fw-bold" for="is_married_edit">Sudah Menikah</label>
                </div>
              </div>
              <div class="col-12 col-md-6 d-flex align-items-end">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_head_of_family" id="is_head_of_family_edit">
                  <label class="form-check-label fw-bold" for="is_head_of_family_edit">Kepala Keluarga</label>
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
          <h3 class="modal-title">Hapus Penghuni</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="">
          @csrf
          <div class="modal-body text-center">
            <input type="hidden" class="d-none" id="hi" name="id">
            <p class="fw-bold mb-2 fs-4" id="hd">Apakah anda yakin ingin menghapus penghuni ini?</p>
          </div>
          <div class="modal-footer">
            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger" name="submit" value="destroy">Hapus</button>
          </div>
        </form>
      </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" id="foto">
  <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Foto KTP</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <div class="modal-body">
          <img class="w-100" id="img-view" src="">
        </div>
      </div>
  </div>
</div>


<script type="text/javascript">
  function foto(image){
    $("#img-view").attr("src","/storage/img/ktp/"+image);
  }
  function edit(id){
    $.ajax({
      url: "/api/resident/"+id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        var mydata = response.data;
        $('#edit input[name="id"]').val(id);
        $('#edit input[name="full_name"]').val(mydata.full_name);
        $('#edit select[name="status"]').val(mydata.status);
        $('#edit input[name="phone_number"]').val(mydata.phone_number);
        $('#edit input[name="is_married"]').prop('checked', mydata.is_married == 1);
        $('#edit input[name="is_head_of_family"]').prop('checked', mydata.is_head_of_family == 1);
        $('#edit input[name="move_in_date"]').val(mydata.move_in_date ? mydata.move_in_date.substring(0,10) : '');
        $('#edit input[name="move_out_date"]').val(mydata.move_out_date ? mydata.move_out_date.substring(0,10) : '');
        $("#et").text("Edit "+mydata.full_name);
      }
    });
  }
  function hapus(id){
    $.ajax({
      url: "/api/resident/"+id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        var mydata = response.data;
        $("#hi").val(id);
        $("#hd").text("Apakah anda yakin ingin menghapus "+mydata.full_name+"?");
      }
    });
  }
</script>
@endsection
