@extends('layouts.admin')

@section('content')

<div class="card mb-2">
  <!--begin::Card Body-->
  <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
    <!--begin::Section-->
    <div>
      <!--begin::Heading-->
      <div class="col-12 d-flex">
        <h1 class="anchor fw-bolder mb-5">Laporan Presensi : {{$user->name}} ({{ $activeMonth->translatedFormat('F Y') }})</h1>
      </div>
      <!--end::Heading-->
      <!--begin::Block-->
      <div class="my-5 table-responsive">

        <div class="row mb-5">
          <!-- Hadir -->
          <div class="col-12 col-md-4 mb-3">
              <div class="card bg-light-success">
                  <div class="card-body text-center">
                      <h6 class="text-muted">Total Hadir</h6>
                      <h2 class="fw-bolder text-success h1">{{ $totalHadir }} Hari</h2>
                  </div>
              </div>
          </div>
      
          <!-- Tidak Hadir -->
          <div class="col-12 col-md-4 mb-3">
              <div class="card bg-light-danger">
                  <div class="card-body text-center">
                      <h6 class="text-muted">Tidak Hadir <small class="text-muted">(kecuali Minggu)</small></h6>
                      <h2 class="fw-bolder text-danger h1">{{ $totalTidakHadir }} Hari</h2>
                  </div>
              </div>
          </div>
      
          <!-- Persentase -->
          <div class="col-12 col-md-4 mb-3">
              <div class="card bg-light-info">
                  <div class="card-body text-center">
                      <h6 class="text-muted">Persentase Kehadiran</h6>
                      <h2 class="fw-bolder text-info h1">{{ $persentaseHadir }}%</h2>
                  </div>
              </div>
          </div>
        </div>
        
        <form method="POST" class="row g-3 mb-5">
          @csrf
          <div class="col-auto">
            <input type="month" name="month" class="form-control" value="{{ old('month', $activeMonth->format('Y-m')) }}">
          </div>
          <div class="col-auto">
            <button class="btn btn-info btn-purple">Filter</button>
          </div>
        </form>
        
        <table id="myTable" class="table table-striped table-hover table-rounded border gs-7">
          <thead>
            <tr class="fw-bold fs-6 text-gray-800 border-bottom border-gray-200">
              <th class="width: 30px">No</th>
              <th style="min-width: 160px">Tanggal</th>
              <th style="min-width: 200px">Scan Masuk</th>
              <th style="min-width: 200px">Scan Pulang</th>
              <th>Satus</th>
              <th style="min-width: 160px">Action</th>
            </tr>
          </thead>
          <tbody>
            @php
              $hour_in = $office->hour_in;
            @endphp
            @foreach ($daysInMonth as $i => $day)
            @php
              $p = $presences[$day] ?? null;
              $carbonDay = \Carbon\Carbon::parse($day);
              $isWeekend = $carbonDay->isSunday();
            @endphp
            <tr class="{{ $isWeekend ? 'table-info' : '' }}">
                <td>{{ $i + 1 }}</td>
                <td>{{ $carbonDay->translatedFormat('l, j F Y') }}</td>
                <td>
                  @if($p)
                    <div class="symbol symbol-30px me-5" data-bs-toggle="modal" data-bs-target="#foto" onclick="foto('{{ $p->image_in }}')">
                      <img src="/storage/img/absent/{{ $p->image_in }}" class="h-30 of-cover rounded-0">
                    </div>
                    <span class="align-middle me-3">{{ $p->time_in }}</span>
                    @if($p->time_in < $hour_in)
                      <span class="badge bg-purple">Tepat waktu</span>
                    @else
                      <span class="badge bg-danger align-middle">Terlambat</span>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($p)
                    <div class="symbol symbol-30px me-5" data-bs-toggle="modal" data-bs-target="#foto" onclick="foto('{{ $p->image_out ?: 'default.jpg' }}')">
                      <img src="/storage/img/absent/{{ $p->image_out ?: 'default.jpg' }}" class="h-30 of-cover rounded-0">
                    </div>
                    <span class="align-middle me-3">{{ $p->time_out }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if(!$p)
                    <span class="badge badge-danger">Tidak hadir</span>
                  @else
                    <span class="badge badge-info">Hadir</span>
                  @endif
                </td>
                <td>
                  @if($p && $p->location_in)
                    <a href="#" class="btn btn-purple text-white py-1 ps-2 pe-3 fs-7" data-bs-toggle="modal" data-bs-target="#map" onclick="map({{ $p->location_in }})">
                      <i class="bi bi-geo-alt-fill text-white"></i> Masuk
                    </a>
                  @endif
                  @if($p && $p->location_out)
                    <a href="#" class="btn btn-purple text-white py-1 ps-2 pe-3 fs-7" data-bs-toggle="modal" data-bs-target="#map" onclick="map({{ $p->location_out }})">
                      <i class="bi bi-geo-alt-fill text-white"></i> Pulang
                    </a>
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

<div class="modal fade" tabindex="-1" id="edit">
  <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="et">Edit persetujuan</h3>
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
                <div class="d-flex">
                  <p class="badge badge-primary fs-4 mx-auto">
                    Anda sebagai approver level <span id="level"></span>
                  </p>
                </div>
                <br>
                <label class="required fw-bold mb-2">Status verivikasi</label>
                <select class="form-control form-select" name="status" required>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div class="col-12">
                <label class="required fw-bold mb-2">Catatan</label>
                <input type="text" class="form-control" name="comment" required>
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
          <img class="rounded" id="img-view" src="" style="width:100%">
        </div>
      </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" id="map">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Lokasi</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
             data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </div>
      </div>

      <div class="modal-body p-4">
        <div id="map-view" class="rounded" style="width:100%; height:400px;"></div>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script type="text/javascript">
  function foto(image){
    $("#img-view").attr("src","/storage/img/absent/"+image);
  }
  function edit(id){
    $.ajax({
      url: "/api/reservation_approval/"+id,
      type: 'GET',
      dataType: 'json', // added data type
      success: function(response) {
        var mydata = response.data;
        $('#edit input[name="id"]').val(id);
        $('#edit input[name="comment"]').val(mydata.comment);
        $('#edit select[name="status"]').val(mydata.status);
        $("#level").text(mydata.approval_level);
      }
    });
  }

  let leafletMap;
  let marker;

  function map(lat, lng) {
    // buka modal
    $('#map').modal('show');

    // delay dikit biar modal kebuka dulu
    setTimeout(() => {
      if (!leafletMap) {
        leafletMap = L.map('map-view').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap'
        }).addTo(leafletMap);

        marker = L.marker([lat, lng]).addTo(leafletMap);
      } else {
        leafletMap.setView([lat, lng], 15);
        marker.setLatLng([lat, lng]);
      }

      leafletMap.invalidateSize();
    }, 300);
  }
</script>

@endsection