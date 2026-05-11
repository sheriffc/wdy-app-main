@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
@endsection

@section('content')

        <div class="container mt-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center"><h3>School List</h3></div>
                            <div class="d-flex justify-content-between mb-2 mt-2">
                                <div>
                                    <h4>{{ $currentDate }}</h4>
                                </div>
                                <div class="col-md-3">
                                    <select id="district-filter" class="form-select form-select-sm" aria-label=".form-select-sm example">
                                        <option value="">All Districts</option>
                                    </select>
                                </div>
                            </div>
                            <table id="dt-school-list" data-csrf="{{csrf_token()}}" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                <tr class="align-middle">
                                    <th class="text-start">School</th>
                                    <th class="text-start">District, Chiefdom</th>
                                    <th class="text-start">Teachers</th>
                                    <th class="text-start">Learners</th>
                                    <th class="text-start">Classrooms</th>
                                    <th class="text-start">Last Submitted Teacher Attendance</th>
                                    <th class="text-start">Last Submitted Learner Attendance</th>
                                    <th class="text-start">Tablet Phone Number</th>
                                    <th class="text-start">School Leader Phone Number</th>
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
        <script>
            var selectedDistrict = "";
            var isDistrictOfficerOrAbove ="{{ $isDistrictOfficerOrAbove }}";
            $("#district-filter").bind("change", function(){
                selectedDistrict = this.value;
                tableSchoolMonitoring.draw();
            })

            $(document).ready(function(){
                $.ajax({
                type: "POST",
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                url: "/api/active-districts",
                dataType: 'json',
                success:function(response){
                    var sel = $("#district-filter");
                    // sel.empty();
                    // sel.append('<option value="">All Districts</option>')
                    if(response.status === true){
                        var districts = response.data;
                        districts.forEach(element => {
                            sel.append(`<option value='${element.district_id}'>${element.name}</option>`)
                        });
                    }
                }
                });

            })

            let tableSchoolMonitoring = $('#dt-school-list').DataTable({
                ajax: {
                    url: "/api/dte/dt-school-list",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('#dt-school-list').data("csrf")
                    },
                    data: function (d) {
                        d.districtId = $('#district-filter').val();
                        // d.custom = $('#myInput').val();
                        // etc
                    }
                },
                serverSide: true,

                columns: [
                    {
                        data: "school",
                        render: function ( data, type, row, meta ) {
                            return `<a href="/school/${row.school_uuid}">${row.school}</a>`;
                        },
                    },
                    {
                        data: 'district',
                        render: function ( data, type, row, meta ) {
                            return (row.district) ? row.district + ', ' + row.chiefdom : '';
                        },
                        className: 'text-start'
                    },
                    {
                        data: "count_teachers",
                        className: 'text-center'
                    },
                    {
                        data: "count_learners",
                        className: 'text-center'
                    },
                    {
                        data: "count_classrooms",
                        className: 'text-center'
                    },
                    {
                        data: "teacher_attendance_date",
                        render: function ( data, type, row, meta ) {
                            return (row.teacher_attendance_date) ? row.last_teacher_submitted_date : '';
                        },
                        className: 'text-center'
                    },
                    {
                        data: "last_learner_submitted_date",
                        className: 'text-center'
                    },
                    {
                        data: (isDistrictOfficerOrAbove) ? "tablet_phone_number" : null,
                        className: 'text-center',
                        searchable: false,
                        orderable: {{(Auth::check()) ? 'true' : 'false'}},
                        render: function ( data, type, full, meta ) {
                            if (isDistrictOfficerOrAbove) {
                                if (data) {
                                    return '0'+data
                                } else {
                                    return ''
                                }
                            } else {
                                return '<span class="fa fa-lock" aria-hidden="true"></span>'
                            }
                        },
                        // visible: isDistrictOfficerOrAbove,
                    },
                    {
                        data: (isDistrictOfficerOrAbove) ? "school_leader_phone_number" : null,
                        className: 'text-center',
                        searchable: false,
                        orderable: {{(Auth::check()) ? 'true' : 'false'}},
                        render: function ( data, type, full, meta ) {
                            if (isDistrictOfficerOrAbove) {
                                if (data) {
                                return '0'+data
                                } else {
                                    return ''
                                }
                            } else {
                                return '<span class="fa fa-lock" aria-hidden="true"></span>'
                            }
                        },
                        // visible: isDistrictOfficerOrAbove,
                    },
                    {
                        data: "chiefdom",
                        visible: false,
                    },
                ],
                order: isDistrictOfficerOrAbove ? [5, "asc"] : [5, "desc"],
                select: false,
            });

        </script>
@endsection
