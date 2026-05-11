@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
    @include('assets.moment-js')
@endsection

@section('content')

        <div class="container mt-2">
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center"><h3>School Attendance Monitoring Report</h3></div>
                            <table id="dt-school-attendance-monitoring" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                <tr class="align-middle">
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
            var selectedAttendanceStatus = "missing";
            var isDistrictOfficerOrAbove ="{{ $isDistrictOfficerOrAbove }}";
            var dynamicColumns = [];
            var dynamicRows = [];
            let tableSchoolAttendanceMonitoring = null;
            let dateRange = [];

            $("#district-filter").bind("change", function(){
                selectedDistrict = this.value;
                requestAllChartData()
            })

           

            $(document).ready(function(){

                for (let index = 6; index >= 0; index--) {
                    let currentDate = moment().subtract(index, 'days').format('YYYY-MM-DD'); 
                    dateRange.push({dateValue: currentDate,dateTitle: moment(currentDate).format("MMM D")})
                }
                loadSchoolAttendanceMonitoringTable();

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

                requestAllChartData();
            })

            async function requestAllChartData() {

                await $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/api/school-attendance-monitoring",
                    type: "POST",
                    dataType: "json",
                    data:{districtId: selectedDistrict,attendanceStatus: selectedAttendanceStatus},
                    success: function(response) {
                        if(response.status === true){
                            let schoolAttendanceMonitoringData = response.data.schoolAttendanceMonitoringData;
                            dynamicRows = schoolAttendanceMonitoringData;
        
                            tableSchoolAttendanceMonitoring.clear().draw();
                            tableSchoolAttendanceMonitoring.rows.add(dynamicRows);
                            tableSchoolAttendanceMonitoring.columns.adjust().draw();      
                            
                        }
                    }
                })  
             }

            function loadSchoolAttendanceMonitoringTable(){  
    
                generateSchoolAttendanceMonitoringTableColumns();
                
                tableSchoolAttendanceMonitoring = $('#dt-school-attendance-monitoring').DataTable({
                    dom: 'Bfrtip',
                    data: dynamicRows,
                    columns: dynamicColumns
                });
                $( "#dt-school-attendance-monitoring_filter" ).addClass('d-md-flex justify-content-between')
                $('label:contains("Search:")').addClass('order-md-2');
                $( "#dt-school-attendance-monitoring_filter" ).append( `
                    <div class='col-md-2 order-md-1 d-flex justify-content-center align-items-center'>
                        <label>Filter: </label>
                        <select ml-2 id='attendance-status-filter' onchange='setAttendanceStatus()' class="form-select form-select-sm" aria-label=".form-select-sm example">
                            <option value="">All</option>
                            <option selected value="missing">Missing</option>
                            <option value="submitted">Submitted</option>
                        </select>
                    </div>
                ` );
                
            }

            function generateSchoolAttendanceMonitoringTableColumns(){
                
                let schoolNameColumn = {
                    data: 'school_name',
                    title: 'School Name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.uuid}">${row.school_name}</a>`;
                    },
                };

                let avgReportRateColumn = {
                    data: 'avg_report_rate',
                    title: 'Avg Report Rate',
                };
                
                dynamicColumns.push(schoolNameColumn);
                dateRange.forEach(element => {
                    dynamicColumns.push({data:element.dateValue,title:element.dateTitle});
                });
                dynamicColumns.push(avgReportRateColumn);
            }

            function setAttendanceStatus(){
                selectedAttendanceStatus = $('#attendance-status-filter').val();
                requestAllChartData()
            }

        </script>
@endsection
