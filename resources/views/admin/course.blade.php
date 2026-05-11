@extends('layouts.admin')

@section('content')
@php
  $admin = Auth::guard('admin')->user();
  $role = $admin->role;
@endphp
<div class="card mb-2">
  <!--begin::Card Body-->
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <!--begin::Section-->
    <div>
      <!--begin::Heading-->
      <div class="col-12 d-flex">
        <h1 class="anchor fw-bolder mb-5" id="striped-rounded-bordered">Course</h1>
        <button class="ms-auto btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">Tambah</button>
      </div>
      <!--end::Heading-->
      <!--begin::Block-->
      <div class="my-5 table-responsive">
        <table id="myTablePrint" class="table table-striped table-hover table-rounded border gs-7" style="zoom: 90%">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th style="width: 30px">No.</th>
              <th style="min-width: 320px">Nama course</th>
              <th style="min-width: 100px">Kategori</th>
              <th style="min-width: 250px">Keynote speaker</th>
              <th style="min-width: 80px">Enrolled</th>
              <th style="min-width: 190px">Tanggal event</th>
              <th style="min-width: 70px">Status</th>
              <th style="min-width: 100px">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($courses as $c)
            @php
              $sd = date_create($c->start_date);
              $ed = date_create($c->end_date);
            @endphp
            <tr>
              <td>{{$loop->iteration}}</td>
              <td class="d-flex">
                <div class="symbol symbol-30px me-5" data-bs-toggle="modal" data-bs-target="#foto" onclick="foto('{{ $c->image }}')">
                  <img src="/storage/img/course/{{ $c->image }}" class="align-self-center of-cover rounded" style="height: 40px; width: 60px;">
                </div>
                <div>
                  {{ $c->name }}
                </div>
              </td>
              <td>
                <span class="badge badge-success">{{ $c->category->name }}</span>
              </td>
              <td>{{ $c->speaker }}</td>
              <td>
                <p class="text-primary fw-bold fs-3">{{$c->userCourse()->count()}}/{{ $c->enrolled }}</p>
              </td>
              <td>
                <p class="mb-0"><b>Tanggal mulai : </b> {{date_format($sd,"d/m/Y")}}</p>
                <p class="mb-0"><b>Tanggal selesai : </b> {{date_format($ed,"d/m/Y")}}</p>  
              </td>
              <td>
                @if ($c->status)
                  <span class="badge badge-success">Aktif</span>
                @else
                  <span class="badge badge-danger">Tidak aktif</span>
                @endif
              </td>
              <td>
                <a href="/admin/course-detail/{{ $c->id }}" class="btn btn-icon btn-sm btn-primary" target="_blank"><i class="mdi mdi-eye-outline fs-3"></i></a>
                <a href="#" class="btn btn-icon btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit" onclick="edit({{ $c->id }})"><i class="bi bi-pencil-fill"></i></a>
                <a href="#" class="btn btn-icon btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapus" onclick="hapus({{ $c->id }})"><i class="fa fa-times"></i></a>
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
        <h3 class="modal-title">Tambah course</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>

      <form class="form" method="post" action="" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-9">
            <div class="col-12 col-md-8">
              <label class="required fw-bold mb-2">Nama course</label>
              <input type="text" class="form-control" name="name" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Kode</label>
              <input type="text" class="form-control" name="code" required>
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Deskripsi</label>
              <input type="text" class="form-control" name="description" required>
            </div>
            
            <div class="col-12 col-md-4">
              <label class="fw-bold mb-2">Keynote speaker</label>
              <input type="text" class="form-control" name="speaker">
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Partisipan</label>
              <select class="form-select" name="profession_id">
                @foreach ($professions as $p)
                  <option value="{{$p->id}}">{{$p->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Instruktor</label>
              <input type="text" class="form-control" name="instructor" required>
            </div>
            
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Kategori</label>
              <select class="form-select" name="category_id">
                @foreach ($categories as $c)
                  <option value="{{$c->id}}">{{$c->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Durasi</label>
              <input type="text" class="form-control" name="duration" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Enrolled (Max Student)</label>
              <input type="number" class="form-control" name="enrolled" required>
            </div>

            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Price Include Discount</label>
              <input type="number" class="form-control" name="price" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Discount</label>
              <input type="number" class="form-control" name="discount" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Currency</label>
              <select class="form-select" name="currency">
                <option value="IDR">IDR</option>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Tanggal post</label>
              <input type="date" class="form-control" name="post_date" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Tanggal mulai</label>
              <input type="date" class="form-control" name="start_date" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="required fw-bold mb-2">Tanggal selesai</label>
              <input type="date" class="form-control" name="end_date" required>
            </div>

            <div class="col-12">
              <label class="required fw-bold mb-2">Link YouTube</label>
              <input type="text" class="form-control" name="url_youtube">
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Link Zoom</label>
              <input type="text" class="form-control" name="url_zoom">
            </div>
            <div class="col-12">
              <label class="required fw-bold mb-2">Link materi</label>
              <input type="text" class="form-control" name="url_material">
            </div>

            <div class="col-12 col-md-6">
              <label class="required fw-bold mb-2">Lokasi</label>
              <input type="text" class="form-control" name="location" required>
            </div>
            <div class="col-12 col-md-3">
              <label class="required fw-bold mb-2">Posisi</label>
              <select class="form-select" name="position">
                <option value="article">Article</option>
                <option value="recommendation">Recommendation</option>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <label class="required fw-bold mb-2">Sifat</label>
              <select class="form-select" name="type">
                <option value="online">Daring</option>
                <option value="offline">Luring</option>
              </select>
            </div>

            <div class="col-12">
              <label class="required fw-bold mb-2">Artikel</label>
              <textarea class="form-control" name="article" rows="3" required></textarea>
            </div>

            <div class="col-6 col-md-3">
              <label class="required fw-bold mb-2">Pinned</label>
              <select class="form-select" name="pinned">
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
              </select>
            </div>
            <div class="col-6 col-md-3">
              <label class="required fw-bold mb-2">Status</label>
              <select class="form-select" name="status">
                <option value="1">Aktif</option>
                <option value="0">Tidak aktif</option>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="required fw-bold mb-2">Upload foto</label>
              <input type="file" class="form-control" name="image" required>
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
          <h3 class="modal-title" id="et">Edit course</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="id">
          <div class="modal-body">
            <div class="row g-9">
              <div class="col-12 col-md-8">
                <label class="required fw-bold mb-2">Nama course</label>
                <input type="text" class="form-control" name="name" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Kode</label>
                <input type="text" class="form-control" name="code" required>
              </div>
              <div class="col-12">
                <label class="required fw-bold mb-2">Deskripsi</label>
                <input type="text" class="form-control" name="description" required>
              </div>
  
              <div class="col-12 col-md-4">
                <label class="fw-bold mb-2">Keynote speaker</label>
                <input type="text" class="form-control" name="speaker">
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Partisipan</label>
                <select class="form-select" name="profession_id">
                  @foreach ($professions as $p)
                    <option value="{{$p->id}}">{{$p->name}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Instruktor</label>
                <input type="text" class="form-control" name="instructor" required>
              </div>
              
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Kategori</label>
                <select class="form-select" name="category_id">
                  @foreach ($categories as $c)
                    <option value="{{$c->id}}">{{$c->name}}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Durasi</label>
                <input type="text" class="form-control" name="duration" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Enrolled (Max Student)</label>
                <input type="number" class="form-control" name="enrolled" required>
              </div>
  
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Price Include Discount</label>
                <input type="number" class="form-control" name="price" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Discount</label>
                <input type="number" class="form-control" name="discount" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Currency</label>
                <select class="form-select" name="currency">
                  <option value="IDR">IDR</option>
                </select>
              </div>
  
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Tanggal post</label>
                <input type="date" class="form-control" name="post_date" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Tanggal mulai</label>
                <input type="date" class="form-control" name="start_date" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="required fw-bold mb-2">Tanggal selesai</label>
                <input type="date" class="form-control" name="end_date" required>
              </div>
  
              <div class="col-12">
                <label class="required fw-bold mb-2">Link YouTube</label>
                <input type="text" class="form-control" name="url_youtube">
              </div>
              <div class="col-12">
                <label class="required fw-bold mb-2">Link Zoom</label>
                <input type="text" class="form-control" name="url_zoom">
              </div>
              <div class="col-12">
                <label class="required fw-bold mb-2">Link materi</label>
                <input type="text" class="form-control" name="url_material">
              </div>
  
              <div class="col-12 col-md-6">
                <label class="required fw-bold mb-2">Lokasi</label>
                <input type="text" class="form-control" name="location" required>
              </div>
              <div class="col-12 col-md-3">
                <label class="required fw-bold mb-2">Posisi</label>
                <select class="form-select" name="position">
                  <option value="article">Article</option>
                  <option value="recommendation">Recommendation</option>
                </select>
              </div>
              <div class="col-12 col-md-3">
                <label class="required fw-bold mb-2">Sifat</label>
                <select class="form-select" name="type">
                  <option value="online">Daring</option>
                  <option value="offline">Luring</option>
                </select>
              </div>
  
              <div class="col-12">
                <label class="required fw-bold mb-2">Artikel</label>
                <textarea class="form-control" name="article" rows="3" required></textarea>
              </div>
  
              <div class="col-6 col-md-3">
                <label class="required fw-bold mb-2">Pinned</label>
                <select class="form-select" name="pinned">
                  <option value="0">Tidak</option>
                  <option value="1">Ya</option>
                </select>
              </div>
              <div class="col-6 col-md-3">
                <label class="required fw-bold mb-2">Status</label>
                <select class="form-select" name="status">
                  <option value="1">Aktif</option>
                  <option value="0">Tidak aktif</option>
                </select>
              </div>
              <div class="col-12 col-md-6">
                <label class="fw-bold mb-2">Upload foto</label>
                <input type="file" class="form-control" name="image">
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
          <h3 class="modal-title">Hapus course</h3>
          <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </div>
        </div>
        <form class="form" method="post" action="">
          @csrf
          <div class="modal-body text-center">
            <input type="hidden" class="d-none" id="hi" name="id">
            <p class="fw-bold mb-2 fs-3">"<span id="hd"></span>"</p>
            <p class="mb-2 fs-4">Apakah anda yakin ingin menghapus course ini?</p>
            <p class="mb-2 fs-5">*Course yang memiliki peserta tidak bisa dihapus</p>
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
          <h3 class="modal-title" id="ft">View image</h3>
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
    $("#img-view").attr("src","/storage/img/course/"+image);
  }
  function edit(id) {
    $.ajax({
      url: "/api/course/" + id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        var mydata = response.data;
        $('#edit input[name="id"]').val(id);
        $('#edit input[name="name"]').val(mydata.name);
        $('#edit input[name="code"]').val(mydata.code);
        $('#edit input[name="description"]').val(mydata.description);
        $('#edit input[name="speaker"]').val(mydata.speaker);
        $('#edit select[name="profession_id"]').val(mydata.profession_id);
        $('#edit input[name="instructor"]').val(mydata.instructor);
        $('#edit select[name="category_id"]').val(mydata.category_id);
        $('#edit input[name="duration"]').val(mydata.duration);
        $('#edit input[name="enrolled"]').val(mydata.enrolled);
        $('#edit input[name="price"]').val(mydata.price);
        $('#edit input[name="discount"]').val(mydata.discount);
        $('#edit select[name="currency"]').val(mydata.currency);
        $('#edit input[name="post_date"]').val(mydata.post_date);
        $('#edit input[name="start_date"]').val(mydata.start_date);
        $('#edit input[name="end_date"]').val(mydata.end_date);
        $('#edit input[name="url_youtube"]').val(mydata.url_youtube);
        $('#edit input[name="url_zoom"]').val(mydata.url_zoom);
        $('#edit input[name="url_material"]').val(mydata.url_material);
        $('#edit input[name="location"]').val(mydata.location);
        $('#edit select[name="position"]').val(mydata.position);
        $('#edit select[name="type"]').val(mydata.type);
        $('#edit textarea[name="article"]').val(mydata.article);
        $('#edit select[name="pinned"]').val(mydata.pinned);
        $('#edit select[name="status"]').val(mydata.status);
      }
    });
  }
  function hapus(id){
    $.ajax({
      url: "/api/course/"+id,
      type: 'GET',
      dataType: 'json', // added data type
      success: function(response) {
        //alert(JSON.stringify(mydata));
        var mydata = response.data;
        $("#hi").val(id);
        $("#hd").text(mydata.name);
      }
    });
  }
</script>
@endsection