@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
@endsection

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center"><h3>Manage Schools</h3></div>
                    <table id="dt-schools" data-csrf="{{csrf_token()}}" class="table table-sm small-font" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-left">Name</th>
{{--                                                <th class="text-left">EMIS ID</th>--}}
{{--                                                <th class="text-left">Payroll SID</th>--}}
                            <th class="text-left">Education Level</th>
                            <th class="text-left">District Office</th>
                            <th class="text-left">Chiefdom</th>
                            <th class="text-left">Council</th>
                            <th class="text-left">Section</th>
                            <th class="text-left">Town</th>
                            <th class="text-left">Created at</th>
                            <th class="text-left">Updated at</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

    @include('_partials.gmaps-dt-init');

    <script>

        let editorDtSchools = new $.fn.dataTable.Editor({
            ajax: {
                url: "/api/dte/dt-schools",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-schools').data("csrf")
                }
            },
            table: "#dt-schools",
            fields: [
                {
                    label: "UUID:",
                    name: "school.uuid",
                    type: 'readonly'
                }, {
                    label: "District Office:",
                    name: "school.district_office_uuid",
                    type: "select",
                    placeholderDisabled: false,
                    placeholder: ""
                }, {
                    label: "School Name:",
                    name: "school.name"
                }, {
                    label: "EMIS ID:",
                    name: "school.emis_id"
                }, {
                    label: "Payroll SID:",
                    name: "school.payroll_sid"
                }, {
                    label: "Education Level:",
                    name: "school.school_education_level_oid",
                    type: "select",
                    placeholderDisabled: false,
                    placeholder: ""
                }, {
                    label: "Chiefdom:",
                    name: "chiefdom.name",
                    type: 'readonly'
                }, {
                    label: "Council:",
                    name: "school.council_name"
                }, {
                    label: "Section:",
                    name: "school.section_name"
                }, {
                    label: "Town:",
                    name: "school.town_name"
                }, {
                    label: "Address:",
                    name: "school.address"
                }, {
                    label: "Picker",
                    name: '',
                    type: "gmap"
                }, {
                    label: "Latitude",
                    name: "school.lat",
                    type: "text"
                }, {
                    label: "Longitude",
                    name: "school.lng",
                    type: "text"
                }, {
                    label: "Last Updated:",
                    name: "school.updated_at",
                    type: 'readonly'
                }, {
                    label: "Date Registered:",
                    name: "school.created_at",
                    type: 'readonly'
                }
            ]
        });

        editorDtSchools.on('open', function (e, json, data) {

            for (var key in e.currentTarget.s.editData['school.lat']) {
            }
            ;

            var lat = parseFloat(e.currentTarget.s.editData['school.lat'][key]);
            var lng = parseFloat(e.currentTarget.s.editData['school.lng'][key]);

            //default val for user
            if (!lat) lat = 8.472266;
            if (!lng) lng = -13.245376;

            setTimeout(function () {
                if_gmap_init(lat, lng)
            }, 500);

        });

        let tableDtSchools = $('#dt-schools').DataTable({
            // lengthChange: true,
            // lengthMenu: [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
            // pageLength: 5,
            // dom: "Bfrtip",
            ajax: {
                url: "/api/dte/dt-schools",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-schools').data("csrf")
                }
            },
            serverSide: true,
            // order: [[1,'asc'],[0,'asc']],
            columns: [
                {data: "school.name"},
                // {data: "school.emis_id"},
                // {data: "school.payroll_sid"},
                {data: "education_level.item_name"},
                {data: "district_office.name"},
                {data: "chiefdom.name"},
                {data: "school.council_name"},
                {data: "school.section_name"},
                {data: "school.town_name"},
                {data: "school.created_at"},
                {data: "school.updated_at"},
            ],
            select: true,
        });

        new $.fn.dataTable.Buttons(tableDtSchools, [
            {extend: "create", editor: editorDtSchools, className: 'btn-sm'},
            {extend: "edit", editor: editorDtSchools, className: 'btn-sm'},
            {extend: "remove", editor: editorDtSchools, className: 'btn-sm'}
        ]);
        //
        tableDtSchools.buttons().container()
            .appendTo($('.col-md-6:eq(0)', tableDtSchools.table().container()));

    </script>

    <script>
        var gmapdata;
        var gmapmarker;
        var infoWindow;

        var def_zoomval = 15;

        function if_gmap_init(def_latval, def_longval)
        {
            var curpoint = new google.maps.LatLng(def_latval,def_longval);

            gmapdata = new google.maps.Map(document.getElementById("mapitems"), {
                center: curpoint,
                zoom: def_zoomval,
                mapTypeId: 'roadmap',
            });

            gmapmarker = new google.maps.Marker({
                map: gmapdata,
                position: curpoint
            });

            infoWindow = new google.maps.InfoWindow;
            google.maps.event.addListener(gmapdata, 'click', function(event) {
                gmapmarker.setPosition(event.latLng);
                editorDtSchools.field('district_office.lng').set(event.latLng.lng().toFixed(6));
                editorDtSchools.field('district_office.lat').set(event.latLng.lat().toFixed(6));
            });

            google.maps.event.addListener(gmapmarker, 'click', function() {
                infoWindow.open(gmapdata, gmapmarker);
            });

            return false;
        }
    </script>

@endsection
