@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
@endsection

@section('content')

        <div class="container mt-2">
            <div class="d-md-flex justify-content-between">
                <h4>Teachers Profile Completion</h4>
                <div class="col-md-3">
                    <select id="district-filter" class="form-select form-select-sm" aria-label=".form-select-sm example">
                        <option value="">All Districts</option>
                    </select>
                </div>
            </div>
          
            <div class="row">
                <div class="col-md-12">
                    <div class="card mt-4">
                        <div class="card-body">
                            <table id="dt-teacher-profile-completion-table" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                <tr class="align-middle">
                                    <th class="text-start">School</th>
                                    <th class="text-start">Teaching Position</th>
                                    <th class="text-start">Photo Reg</th>
                                    <th class="text-start">Phone Number</th>
                                    <th class="text-start">Address</th>
                                    <th class="text-start">NIN</th>
                                    <th class="text-start">Req. Fields</th>
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
        var selectedDistrict = null;
        var dtTeacherProfileCompletionTableData = [];


        $( async function(){
            await setDistrictFilter();
            await requestAllChartData();
        })

        $("#district-filter").bind("change", async function () {
            selectedDistrict = this.value;
            await requestAllChartData();
        })

        async function requestAllChartData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/profile-completion/teachers",
                type: "POST",
                dataType: "json",
                data:{districtId: selectedDistrict},
                success: function(response) {
                    if(response.status === true){

                        dtTeacherProfileCompletionTableData = response.data.schoolTeachersProfileCompletionTable;

                        //set attendance date label
                        $("#attendance-date-label").text(response.data.date)               

                        teacherProfileCompletionDatatable.clear().draw();
                        teacherProfileCompletionDatatable.rows.add(dtTeacherProfileCompletionTableData);
                        teacherProfileCompletionDatatable.columns.adjust().draw();

                    }

                },
                cache: false
            });
        }

        let teacherProfileCompletionDatatable = $('#dt-teacher-profile-completion-table').DataTable({
            data : dtTeacherProfileCompletionTableData,
            columns: [
                {
                    data: "school_name",
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                },
                {
                    data: "teacher_role",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "photo_registration",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "phone_number",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "address",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "nin",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "all_required_fields_complete",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },


            ],
            select: false,
            order: [], // by default use the custom ordering set in the SQL query

        });

        function setDistrictFilter(){
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/active-districts",
                dataType: 'json',
                success: function (response) {
                    var sel = $("#district-filter");
                    // sel.empty();
                    // sel.append('<option value="">All Districts</option>')
                    if (response.status === true) {
                        var districts = response.data;
                        districts.forEach(element => {
                            sel.append(`<option value='${element.district_id}'>${element.name}</option>`)
                        });
                    }
                }
            });
        }

        function setPercentageText(percentage){
            let color = "";
            if(percentage <= 50){
                color = "text-danger"
            }else if(percentage >=51 && percentage <= 79){
                color = "text-warning"
            }
            return `<span class="${color}">${parseFloat(percentage).toFixed(0)}%</span>`
        }

    </script>

@endsection
