<script>var hostUrl = "assets/";</script>
    <!--begin::Javascript-->
    
    <!--begin::Page Vendors Javascript(used by this page)-->
    <script src="/assets/plugins/custom/datatables.net-bs4/jquery.dataTables.js"></script>
    <script src="/assets/plugins/custom/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <script src="/assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
    <script src="/assets/plugins/custom/select2/select2.min.js"></script>
    {{-- <script src="/assets/plugins/global/plugins.bundle.js"></script> --}}
    <!--end::Page Vendors Javascript-->
    <!--begin::Page Custom Javascript(used by this page)-->
    <script src="/assets/js/custom/widgets.js"></script>
    <script src="/assets/js/custom/documentation/charts/chartjs.js"></script>
    <script src="/assets/js/custom/documentation/documentation.js"></script>
    <script src="/assets/js/custom/documentation/search.js"></script>
    <script src="/assets/js/custom/authentication/sign-in/general.js"></script>
		<script src="/assets/js/custom/dataexport.js?v=1"></script>

    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <!--end::Page Custom Javascript-->
    <script>
      $(document).ready(function () {
        $('#myTable').DataTable({
          "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All Pages"]],
          "pageLength": 35,
          "language": {
            "paginate": {
              "previous": "<",
              "next": ">"
            }
          },
					"dom": 'Bfrtip',
					"buttons": [
						'copy', 'csv', 'excel', 'pdf', 'print'
					]
        });
      });
    </script>
    <!--end::Javascript-->