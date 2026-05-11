<div id="kt_docs_header" class="docs-header align-items-stretch mb-2 mb-lg-10">
            <!--begin::Container-->
            <div class="container">
              <div class="d-flex align-items-stretch justify-content-between py-3 h-75px">
                <!--begin::Aside toggle-->
                <div class="d-flex align-items-center d-lg-none me-3" title="Show aside menu">
                  <div class="btn btn-icon btn-flex btn-active-color-primary" id="kt_docs_aside_toggle">
                    <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                    <span class="svg-icon svg-icon-2tx mt-1">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
                      </svg>
                    </span>
                    <!--end::Svg Icon-->
                  </div>
                </div>
                <!--end::Aside toggle-->
                <!--begin::Logo-->
                <div class="d-flex d-lg-none align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15">
                  <a href="#">
                    <img alt="Logo" src="/assets/img/logo-horizontal.webp" class="h-30px" />
                  </a>
                </div>
                <!--end::Logo-->

                <!--begin::Wrapper-->
                <div class="d-flex align-items-center justify-content-between flex-lg-grow-1">
                  
                  <!--begin::Header title-->
                  <div class="d-flex align-items-center" id="kt_docs_header_title">
                    <div class="docs-page-title d-flex flex-column flex-lg-row align-items-lg-center py-5 mb-lg-0" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_docs_content_container', 'lg': '#kt_docs_header_title'}">
                      <h1 class="d-flex align-items-center text-dark my-1 fs-4">Admin
                      {{-- <a href="../dist/documentation/getting-started/changelog.html" class="badge fw-bold fs-9 px-2 ms-2 badge-white text-hover-primary shadow-sm">v1.0.0</a></h1> --}}
                      <span class="d-none d-lg-block bullet h-20px w-1px bg-secondary mx-4"></span>
                      <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-6 my-1">
                        <li class="breadcrumb-item text-dark">{{explode("|",$meta['title'])[1]}}</li>
                      </ul>
                    </div>
                  </div>
                  <!--end::Header title-->

                  <!--begin::Toolbar-->
                  <div class="d-flex align-items-center">
                    @php
                        $profile = auth()->user()
                    @endphp
                    <!--begin::User-->
                    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                      <!--begin::Menu wrapper-->
                      <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img alt="Pic" src="/assets/img/user/{{ $profile->image ? $profile->image : 'default.webp' }}" />
                      </div>
                      <!--begin::Menu-->
                      <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                          <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                              <img alt="Logo" src="/assets/img/user/{{ $profile->image ? $profile->image : 'default.webp' }}" />
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                              <div class="fw-bolder d-flex align-items-center fs-5">{{ $profile->name }}</div>
                              <a href="#" class="fw-bold text-muted text-hover-primary fs-7">{{ $profile->email }}</a>
                            </div>
                            <!--end::Username-->
                          </div>
                        </div>
     
                        <!--begin::Menu separator-->
                        <div class="separator my-2"></div>
                        <!--end::Menu separator-->

                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                          <a href="/admin/logout" class="menu-link px-5">Keluar</a>
                        </div>
                        <!--end::Menu item-->
                        
                      </div>
                      <!--end::Menu-->
                      <!--end::Menu wrapper-->
                    </div>
                    <!--end::User -->
                  </div>
                  <!--end::Toolbar-->
                </div>
                <!--end::Wrapper-->
              </div>
              <div class="border-gray-300 border-bottom border-bottom-dashed"></div>
            </div>
            <!--end::Container-->
          </div>