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
                <h4>Learners Profile Completion</h4>
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
                            <table id="dt-learner-profile-completion-table" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                <tr class="align-middle">
                                    <th class="text-start">School</th>
                                    <th class="text-start">Need Asses</th>
                                    <th class="text-start">Maternal Status</th>
                                    <th class="text-start">Parent Name</th>
                                    <th class="text-start">Parent Phone</th>
                                    <th class="text-start">Parent Address</th>
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
        var dtLearnerProfileCompletionTableData = [];


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
                url: "/api/profile-completion/learners",
                type: "POST",
                dataType: "json",
                data:{districtId: selectedDistrict},
                success: function(response) {
                    if(response.status === true){

                        dtLearnerProfileCompletionTableData = response.data.schoolLearnersProfileCompletionTable;

                        //set attendance date label
                        $("#attendance-date-label").text(response.data.date)               

                        learnerProfileCompletionDatatable.clear().draw();
                        learnerProfileCompletionDatatable.rows.add(dtLearnerProfileCompletionTableData);
                        learnerProfileCompletionDatatable.columns.adjust().draw();

                    }

                },
                cache: false
            });
        }

        let learnerProfileCompletionDatatable = $('#dt-learner-profile-completion-table').DataTable({
            data : dtLearnerProfileCompletionTableData,
            columns: [
                {
                    data: "school_name",
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                },
                {
                    data: "complete_needs_assessment",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "maternal_status",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "guardian_name",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "guardian_phone",
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "guardian_address",
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
