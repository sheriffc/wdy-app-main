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
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center"><h3>Manage District Offices</h3></div>
                    <table id="dt-district-offices" data-csrf="{{csrf_token()}}" class="table table-sm small-font" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-left">District Name</th>
                            <th class="text-left">District ID</th>
                            <th class="text-left">District Code</th>
{{--                                                <th class="text-left">Latitude</th>--}}
{{--                                                <th class="text-left">Longitude</th>--}}
                            <th class="text-left">Active</th>
                            <th class="text-left">Created At</th>
                            <th class="text-left">Updated At</th>
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
        let editorDtDistrictOffices = new $.fn.dataTable.Editor({
            ajax: {
                url: "/api/dte/dt-district-offices",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-district-offices').data("csrf")
                }
            },
            table: "#dt-district-offices",
            fields: [
                {
                    label: "ID:",
                    name: "district_office.uuid",
                    type: 'readonly'
                }, {
                    label: "District Name:",
                    name: "district_office.name"
                }, {
                    label: "District ID:",
                    name: "district_office.district_id",
                    type: 'readonly'
                }, {
                    label: "District Code:",
                    name: "district_office.district_code"
                }, {
                    label: "Active:",
                    name: "district_office.active",
                    type:      "checkbox",
                    separator: "|",
                    options:   [
                        { label: '', value: 1 }
                    ]
                }, {
                    label: "Picker",
                    name: 'district_office._map_picker',
                    type:  "gmap",
                    submit: false
                }, {
                    label: "Latitude",
                    name: "district_office.lat",
                    type:  "text"
                }, {
                    label: "Longitude",
                    name: "district_office.lng",
                    type:  "text"
                }, {
                    label: "Last Updated:",
                    name: "district_office.updated_at",
                    type: 'readonly'
                }, {
                    label: "Date Registered:",
                    name: "district_office.created_at",
                    type: 'readonly'
                }
            ]
        });

        editorDtDistrictOffices.on( 'open', function ( e, json, data ) {

            var latData = e.currentTarget.s.editData['district_office.lat'];
            var lngData = e.currentTarget.s.editData['district_office.lng'];
            var key;
            if (latData) {
                for (var k in latData) { key = k; }
            }

            var lat = latData ? parseFloat(latData[key]) : NaN;
            var lng = lngData ? parseFloat(lngData[key]) : NaN;

            //default val for user
            if (!lat) lat = 8.472266;
            if (!lng) lng = -13.245376;

            setTimeout(function() {
                if_gmap_init(lat,lng)}, 500);

        } );

        let tableDtDistrictOffices = $('#dt-district-offices').DataTable({
            // lengthChange: true,
            // lengthMenu: [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
            // pageLength: 5,
            // dom: "Bfrtip",
            ajax: {
                url: "/api/dte/dt-district-offices",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-district-offices').data("csrf")
                }
            },
            serverSide: true,
            // order: [[1,'asc'],[0,'asc']],
            columns: [
                {data: "district_office.name"},
                {data: "district_office.district_id"},
                {data: "district_office.district_code"},
                // {data: "district_office.lat"},
                // {data: "district_office.lng"},
                {
                    data: "district_office.active",
                    render: function ( data, type, row ) {
                        if ( data ) {
                            return '<input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked disabled>';
                        }else{
                            return '<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" disabled>';
                        }
                    },
                },
                {data: "district_office.created_at"},
                {data: "district_office.updated_at"},
            ],
            select: true,
        });

        new $.fn.dataTable.Buttons( tableDtDistrictOffices, [
            { extend: "create", editor: editorDtDistrictOffices, className: 'btn-sm' },
            { extend: "edit",   editor: editorDtDistrictOffices, className: 'btn-sm' },
            { extend: "remove", editor: editorDtDistrictOffices, className: 'btn-sm' }
        ] );
        //
        tableDtDistrictOffices.buttons().container()
            .appendTo( $('.col-md-6:eq(0)', tableDtDistrictOffices.table().container() ) );
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
                editorDtDistrictOffices.field('district_office.lng').set(event.latLng.lng().toFixed(6));
                editorDtDistrictOffices.field('district_office.lat').set(event.latLng.lat().toFixed(6));
            });

            google.maps.event.addListener(gmapmarker, 'click', function() {
                infoWindow.open(gmapdata, gmapmarker);
            });

            return false;
        }
    </script>

@endsection
