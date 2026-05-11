@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
    @include('assets.highcharts-js')
@endsection

@section('content')

        <div class="container mt-2">
            <div class="d-md-flex justify-content-between">
                <h4>Reporting for Date: <span id="attendance-date-label"></span></h4>
                <div class="col-md-3">
                    <select id="attendance-dates" class="form-select form-select-sm" aria-label=".form-select-sm example">
                        <option value="">Attendance Dates</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $schoolInfo->school_name }}</h5>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-lg-7">EMIS ID:</dt><dd class="col-lg-5">{{ $schoolInfo->emis_id ?? 'None' }}</dd>
                                    <dt class="col-lg-7">Payroll School ID:</dt><dd class="col-lg-5">{{ $schoolInfo->payroll_sid ?? 'None' }}</dd>
                                    <dt class="col-lg-7">WAEC ID:</dt><dd class="col-lg-5">{{ $schoolInfo->waec_id ?? 'None' }}</dd>
                                    <dt class="col-lg-7">District Office:</dt><dd class="col-lg-5">{{ $schoolInfo->district_name ?? 'None' }}</dd>
                                    <dt class="col-lg-7">Education Level:</dt><dd class="col-lg-5">{{ $schoolInfo->education_level ?? 'None' }}</dd>
                                    <dt class="col-lg-7">{{ $schoolInfo->head_teacher_label ?? 'Head Teacher' }}:</dt><dd class="col-lg-5">{{ $schoolInfo->head_teacher_name ?? 'Not Assigned' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div id="teacher-attendance-barchart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="text-left"><h5>Teachers Assigned to School</h5></div>

                            <table id="dt-teacher-table" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                <tr class="align-middle">
                                    <th class="text-start">Name</th>
                                    <th class="text-start">Gender</th>
                                    @if(Auth::check() && Auth::user()->user_type_id >= 40)
                                        <th class="text-start">Payroll PIN</th>
                                    @else
                                        <th class="text-start">Payroll Status</th>
                                    @endif
                                    <th class="text-start">Teacher Role</th>
                                    <th class="text-start">Attendance Status</th>
                                    <th class="text-start">Absent Reason</th>
                                    <th class="text-start">Biometric Method</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="text-left"><h5>Learners Assigned to School</h5></div>

                            <table id="dt-learner-table" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                <tr class="align-middle">
                                    <th class="text-start">Name</th>
                                    <th class="text-start">Gender</th>
                                    <th class="text-start">Date of Birth</th>
                                    <th class="text-start">Year Group</th>
                                    <th class="text-start">Attendance Status</th>
                                    <th class="text-start">Absent Reason</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="text-left"><h5>School Classrooms</h5></div>

                            <table id="dt-classroom-table" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                <tr class="align-middle">
                                    <th class="text-start">Year Group</th>
                                    <th class="text-start">Classroom Name</th>
                                    <th class="text-start">Number of Learners</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                @if(Auth::check() && Auth::user()->user_type_id >= 40)
                    <div class="col-md-6">
                        <div class="card mt-4">
                            <div class="card-body">
                                <div class="text-left"><h5>Payroll teachers removed from this school</h5></div>

                                <table id="dt-teachers-payroll-removed-table" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                    <thead>
                                    <tr class="align-middle">
                                        <th class="text-start">Name</th>
                                        <th class="text-start">PIN</th>
                                        <th class="text-start">Reason</th>
                                        <th class="text-start">Reason Detail</th>
                                        <th class="text-start">Date Ended</th>
                                        <th class="text-start">Date Removed</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

@endsection

@section('script')
        <script>
            var selectedDate = null;
            var startDate = null;
            var endDate = null;
            var isDistrictOfficerOrAbove ="{{ $isDistrictOfficerOrAbove }}";
            var dtTeacherTableData = [];
            var dtLearnerTableData = [];
            var dtClassroomTableData = [];
            var dtTeachersRemovedFromPayrollTableData = [];


            $(function(){
                 setAttendanceDates();
            })

            $("#attendance-dates").bind("change", async function(){
                selectedDate = this.value;
                endDate = this.value;
                var todaysDate = new Date(endDate);
                var tmpStartDate = todaysDate.setDate(todaysDate.getDate() - 6);
                startDate = getYearMonthDate(new Date(tmpStartDate))
                await requestAllChartData();
            })

            async function requestAllChartData() {

                await $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/api/school/{{ $schoolInfo->uuid }}",
                    type: "POST",
                    dataType: "json",
                    data:{startDate,endDate},
                    success: function(response) {
                        if(response.status === true){

                            var teacherAttendanceBarchartData = response.data.teacherAttendanceBarchart;
                            dtTeacherTableData = response.data.teacherTable;
                            dtLearnerTableData = response.data.learnerTable;
                            dtClassroomTableData = response.data.classroomTable;
                            dtTeachersRemovedFromPayrollTableData = response.data.teachersRemovedFromPayroll;
                            var categories = [];
                            var absentList = [];
                            var presentList = [];
                            var lateList = [];
                            var notReportedList =[];

                            //set attendance date label
                            $("#attendance-date-label").text(response.data.date)

                            //attendance chart
                            teacherAttendanceBarchartData.forEach(element => {
                                categories.push(element.date)
                                let totalPresent = parseInt(element.teachers_absent) + parseInt(element.teachers_on_time) + parseInt(element.teachers_late)
                                absentList.push(parseInt(element.teachers_absent))
                                presentList.push(parseInt(element.teachers_on_time))
                                lateList.push(parseInt(element.teachers_late))
                                notReportedList.push(parseInt(element.teachers_total) - totalPresent)
                            });

                            teacherAttendanceBarchart.xAxis[0].setCategories( categories );
                            // teacherAttendanceBarchart.series[0].setData(notReportedList);
                            teacherAttendanceBarchart.series[0].setData(absentList);
                            teacherAttendanceBarchart.series[1].setData(lateList);
                            teacherAttendanceBarchart.series[2].setData(presentList);

                            teacherAttendanceDatatable.clear().draw();
                            teacherAttendanceDatatable.rows.add(dtTeacherTableData);
                            teacherAttendanceDatatable.columns.adjust().draw();

                            learnerAttendanceDatatable.clear().draw();
                            learnerAttendanceDatatable.rows.add(dtLearnerTableData);
                            learnerAttendanceDatatable.columns.adjust().draw();

                            classroomAttendanceDatatable.clear().draw();
                            classroomAttendanceDatatable.rows.add(dtClassroomTableData);
                            classroomAttendanceDatatable.columns.adjust().draw();

                            teachersPayrollRemovedTable.clear().draw();
                            teachersPayrollRemovedTable.rows.add(dtTeachersRemovedFromPayrollTableData);
                            teachersPayrollRemovedTable.columns.adjust().draw();
                        }

                    },
                    cache: false
                });
            }

            var teacherAttendanceBarchart = new Highcharts.Chart('teacher-attendance-barchart',(

                {
                    chart: {
                        type: 'column',
                        height: 300,
                        events: {
                            load: requestAllChartData
                        }
                    },
                    title: {
                        text: 'Teacher Attendance Reporting'
                    },
                    subtitle: {
                        text: 'Period: Last Week'
                    },
                    xAxis: {
                        categories:{},
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Teachers'
                        },
                        stackLabels: {
                            enabled: true,
                            style: {
                                fontWeight: 'bold',
                                color: (
                                    Highcharts.defaultOptions.title.style &&
                                    Highcharts.defaultOptions.title.style.color
                                ) || 'gray'
                            }
                        },
                    },
                    legend: {
                        align: 'center',
                        verticalAlign: 'bottom',
                        backgroundColor:
                            Highcharts.defaultOptions.legend.backgroundColor || 'white',
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },

                    series: [
                        // {name: 'Not Submitted', color: '#8D99AE'},
                        {name: 'Absent', color: '#EE6352'},
                        {name: 'Late', color: '#F8E859'},
                        {name: 'On Time', color: '#9FD356'},
                    ],

                    credits: {
                        enabled: false
                    }

                }

            ));

            let teacherAttendanceDatatable = $('#dt-teacher-table').DataTable({
                data : dtTeacherTableData,
                fnRowCallback: function( row, data, dataIndex ) {
                    setTableRowColor(row,data['attendance_status_oid'])
                },
                columns: [
                    {
                        data: "teacher_name",
                        className: 'text-start',
                        render: function ( data, type, row, meta ) {
                            if(isDistrictOfficerOrAbove){
                                if(data != null){
                                    return `<a href="/teacher-profile/${row.uuid}">${row.teacher_name}</a>`;
                                }else{
                                    return ''
                                }
                            }else{
                                return `<a href="/teacher-profile/${row.uuid}">'********'</a>`;
                            }
                        },
                    },
                    {
                        data: "gender",
                        className: 'text-center'
                    },
                    {
                        data: "pin",
                        className: 'text-center'
                    },
                    {
                        data: "teacher_role",
                        className: 'text-center'
                    },
                    {
                        data: "attendance_status",
                        className: 'text-center',
                        render: function ( data, type, row, meta ) {
                            if (row.attendance_status_oid == 'present' || row.attendance_status_oid == 'late') {
                                return data;
                            } else if (row.attendance_status_oid == 'absent') {
                                return data;
                            }else{
                                return ''
                            }
                        }
                    },
                    {
                        data: "absent_reason",
                        className: 'text-start',
                        defaultContent: '',
                        render: function ( data, type, row, meta ) {
                            return data || '';
                        }
                    },
                    {
                        data: "biometric_method_oid",
                        className: 'text-center',
                        render: function ( data, type, full, meta ) {
                            let timeStr = '';
                            if ((full.attendance_status_oid === 'present' || full.attendance_status_oid === 'late') && full.attendance_created_at) {
                                let d = new Date(full.attendance_created_at);
                                timeStr = ` <small class="text-muted">${d.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}</small>`;
                            }
                            if (data === 'fingerprint') {
                                return '<span class="fas fa-fingerprint" aria-hidden="true"></span>' + timeStr;
                            } else if (data === 'photo') {
                                return '<span class="fas fa-camera" aria-hidden="true"></span>' + timeStr;
                            } else {
                                return timeStr;
                            }
                        }
                    },
                ],
                select: false,
                order: [], // by default use the custom ordering set in the SQL query
            });

            let learnerAttendanceDatatable = $('#dt-learner-table').DataTable({
                data : dtLearnerTableData,
                fnRowCallback: function( row, data, dataIndex ) {
                    setTableRowColor(row,data['attendance_status'])
                },
                columns: [
                    {
                        data: "learner_name",
                        className: (isDistrictOfficerOrAbove) ? 'text-left' : 'text-center',
                        searchable: (isDistrictOfficerOrAbove) ? true : false,
                        render: function ( data, type, full, meta ) {
                            if (isDistrictOfficerOrAbove) {
                                if (data && full.learner_uuid) {
                                    return '<a href="/learner-profile/' + full.learner_uuid + '">' + data + '</a>'
                                } else {
                                    return data || ''
                                }
                            } else {
                                return '********'
                            }
                        }
                    },
                    {
                        data: "gender",
                        className: 'text-center'
                    },
                    {
                        data: "date_of_birth",
                        className: 'text-center',
                        searchable: (isDistrictOfficerOrAbove) ? true : false,
                        render: function ( data, type, full, meta ) {
                            if (isDistrictOfficerOrAbove) {
                                if (data) {
                                    return data
                                } else {
                                    return ''
                                }
                            } else {
                                return '<span class="fa fa-lock" aria-hidden="true"></span>'
                            }
                        }
                    },
                    {
                        data: "year_group",
                        className: 'text-center'
                    },
                    {
                        data: "attendance_status",
                        className: 'text-center',
                        searchable: true,
                        render: function ( data, type, full, meta ) {
                            if (data) {
                                let status = "";
                                switch (data) {
                                    case 'present':
                                            status = "Present";
                                        break;
                                    case 'half_day':
                                        status = "Half Day";
                                    break;
                                    case 'absent':
                                        status = "Absent";
                                    break;
                                    default:
                                            status = "";
                                        break;
                                }
                                return status
                            } else {
                                return ''
                            }
                        }
                    },
                    {
                        data: "absent_reason",
                        className: 'text-start',
                        defaultContent: '',
                        render: function ( data, type, full, meta ) {
                            return data || '';
                        }
                    },
                ],
                select: false,
                order: [], // by default use the custom ordering set in the SQL query

            });

            let classroomAttendanceDatatable = $('#dt-classroom-table').DataTable({
                data : dtClassroomTableData,
                columns: [
                    {
                        data: "year_group",
                        className: 'text-center'
                    },
                    {
                        data: "classroom_name",
                        className: 'text-center'
                    },
                    {
                        data: "learner_count",
                        className: 'text-center'
                    },


                ],
                select: false,
                order: [], // by default use the custom ordering set in the SQL query

            });

            let teachersPayrollRemovedTable = $('#dt-teachers-payroll-removed-table').DataTable({
                data : dtTeachersRemovedFromPayrollTableData,
                columns: [
                    {
                        data: "teacher_name",
                        className: 'text-start',
                        render: function ( data, type, row, meta ) {
                            if(isDistrictOfficerOrAbove){
                                if(data != null){
                                    return `<a href="/teacher-profile/${row.uuid}">${row.teacher_name}</a>`;
                                }else{
                                    return ''
                                }
                            }else{
                                return `<a href="/teacher-profile/${row.uuid}">'********'</a>`;
                            }
                        },
                    },
                    {
                        data: "pin",
                        className: 'text-center'
                    },
                    {
                        data: "reason",
                        className: 'text-left'
                    },
                    {
                        data: "reason_detail",
                        className: 'text-left'
                    },
                    {
                        data: "end_date_at_school",
                        className: 'text-center'
                    },
                    {
                        data: "date_removed",
                        className: 'text-center'
                    },

                ],
                select: false,
                order: [], // by default use the custom ordering set in the SQL query

            });


            async function setAttendanceDates(){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/api/school/attendance-dates/{{ $schoolInfo->uuid }}",
                    type: "POST",
                    dataType: "json",
                    success: function(response) {
                        var sel = $("#attendance-dates");
                        sel.empty();
                        // sel.append('<option value="">All Districts</option>')
                        if(response.status === true){
                            var districts = response.data;
                            districts.forEach(element => {
                                sel.append(`<option value='${element.date}'>${element.format_date}</option>`)
                            });
                        }
                    }
                })
            }

            function getYearMonthDate(date){
                var day = String(date.getDate()).padStart(2, '0');
                var month = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
                var year = date.getFullYear();
                var newDate = `${year}-${month}-${day}`;
                return newDate;
            }

            function setTableRowColor(row,data){
                switch (data) {
                    case 'present':
                            $('td', row).css('background-color', '#c7eed8')
                        break;
                    case 'late':
                        $('td', row).css('background-color', '#fffacc')
                        break;
                    case 'absent':
                        $('td', row).css('background-color', '#f7c6c5')
                        break;
                    case 'half_day':
                            $('td', row).css('background-color', '#FAC898')
                        break;
                    default:
                            $('td', row).css('background-color', '#d6d8db')
                        break;
                }
            }

        </script>
@endsection
