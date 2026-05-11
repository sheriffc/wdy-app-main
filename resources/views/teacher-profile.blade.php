@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
    @include('assets.jquery-calendar-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
    @include('assets.highcharts-js')
    @include('assets.jquery-calendar-js')
    @include('assets.moment-js')
@endsection

@section('content')
    <div class="container">

        <h2>Teacher Profile</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            @if ($isDistrictOfficerOrAbove && $teacherProfileDetails->portrait_photo != "")
                                <img src="data:image/jpg;base64,{{ $teacherProfileDetails->portrait_photo }}" alt="unverified portrait" class="img-thumbnail border border-warning" data-toggle="tooltip" data-placement="top" title="portrait unverified">
                            @else
                                <div class="border border-secondary p-2" >
                                    <span style="color: Grey;" data-toggle="tooltip" data-placement="right" title="Portrait captured but not yet verified"> <i class="fas fa-user fa-5x"></i> </span>
                                </div>
                            @endif
                            <div class="p-2">
                                <h3>
                                    @if ($isDistrictOfficerOrAbove)
                                        {{ $teacherProfileDetails->teacher_name }}
                                    @else
                                        *****
                                    @endif
                                </h3>
                                <h5>
                                    {{ $teacherProfileDetails->employment_status}} 
                                    @if ($teacherProfileDetails->pin)
                                        @if ($isDistrictOfficerOrAbove)
                                        , {{ $teacherProfileDetails->pin }}
                                        @else
                                            *****
                                        @endif 
                                    @endif
                                </h5>
                            </div>
                        </div>
                        <table class="mt-3">
                            <thead>
                                <th style="width:60%"></th>
                                <th></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Nassit:</td>
                                    <td>
                                        @if ($isDistrictOfficerOrAbove)
                                            {{ $teacherProfileDetails->nassit_number ?? 'None' }}
                                        @else
                                            *****
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>NIN:</td>
                                    <td>
                                        @if ($isDistrictOfficerOrAbove)
                                            {{ $teacherProfileDetails->nin ?? 'None' }}
                                        @else
                                            *****
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Phone Number:</td>
                                    <td>
                                        @if ($isDistrictOfficerOrAbove)
                                            @if($teacherProfileDetails->phone_number != "")
                                                {{ $teacherProfileDetails->phone_number }}
                                            @else
                                                {{ "None" }}
                                            @endif
                                        @else
                                            *****
                                        @endif 
                                    </td>
                                </tr>
                                <tr>
                                    <td>Address:</td>
                                    <td>
                                        @if ($isDistrictOfficerOrAbove)
                                            {{ $teacherProfileDetails->address ?? 'None' }}
                                        @else
                                            *****
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Fingerprints Registered?:</td>
                                    <td>{{ $teacherProfileDetails->fingerprint_registered ?? 'None' }}</td>
                                   
                                </tr>
                                <tr>
                                    <td>Date Of Birth:</td>
                                    <td>
                                        @if ($isDistrictOfficerOrAbove)
                                            {{ $teacherProfileDetails->date_of_birth ?? 'Not Assigned' }}
                                        @else
                                            *****
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sex:</td>
                                    <td>
                                        {{ $teacherProfileDetails->sex ?? 'Not Assigned' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h3 class="card-title">School Assignment History</h3>
                        <h5>Payroll School(s)</h5>
                        <table id="dt-payroll-schools" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                            <thead>
                                <tr class="align-middle">
                                    {{-- <th class="text-start">Dates at School</th> --}}
                                    <th class="text-start">School Name</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="mt-3">
                            <h5>Wi De Ya School(s)</h5>
                            <table id="dt-wideya-schools" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                    <tr class="align-middle">
                                        {{-- <th class="text-start">Dates at School</th> --}}
                                        <th class="text-start">School Name</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h3>Classes Assigned To Teacher</h3>
                        <div class="mt-3">
                            <h5>Current Classes</h5>
                            <table id="dt-current-classes" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                    <tr class="align-middle">
                                        <th class="text-start">Class Name</th>
                                        <th class="text-start">Class Level</th>
                                        <th class="text-start">No. Students</th>
                                        <th class="text-start">Academic Year</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <div class="mt-3">
                            <h5>Previous Classes</h5>
                            <table id="dt-previous-classes" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                    <tr class="align-middle">
                                        <th class="text-start">Class Name</th>
                                        <th class="text-start">Class Level</th>
                                        <th class="text-start">No. Students</th>
                                        <th class="text-start">Academic Year</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h3>Learners Assigned To Teacher</h3>
                        <table id="dt-learners-assigned" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                            <thead>
                                <tr class="align-middle">
                                    <th class="text-start">Name</th>
                                    <th class="text-start">Maternal Status</th>
                                    <th class="text-start">Vision</th>
                                    <th class="text-start">Hearing</th>
                                    <th class="text-start">Cognition</th>
                                    <th class="text-start">Selfcare</th>
                                    <th class="text-start">Communication</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3>Attendance Summary</h3>
                        <div class="alert alert-primary">
                           <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="">
                                            <div class="d-flex pb-4">
                                                In the last 30 days:
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div class="">
                                                <h3 id="excused-absences">0</h3>
                                                <small>excused <br>absences</small>
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div>
                                                <h3 id="unauthorised-absences">0</h3>
                                                <small>unauthorised <br>absences</small>
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div>
                                                <h3 id="late-arrivals">0</h3>
                                                <small>late <br>arrivals</small>
                                            </div>
                                        </th>
                                        <th class="text-center">
                                            <div>
                                                <h3 id="absenteeism-rate">0%</h3>
                                                <small>absenteeism <br>rate</small>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                           </table>
                        </div>
                        <div class="mt-4">
                            <h5>Weekly Attendance Trends</h5>
                            <div id="weekly-attendacne-trends-barchart"></div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h3>Attendance Records</h3>
                        <div class="d-md-flex justify-content-around align-items-center mt-3">
                          
                            <h5 for="">Date Range:</h5>
                    
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <label for="">From:</label>
                                    <input type="date" name="" class="form-control" id="start-date">
                                </div>
                            </div>
                           <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <label for="">To:</label>
                                    <input type="date" name="" class="form-control" id="end-date">
                                </div>
                           </div>
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    <button id="search-attendance-record-btn" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <label for="">Attendance Status:</label>
                                    <select class="form-control"  name="" id="attendance-status-filter">
                                        <option value="">All</option>
                                        <option value="present">On Time</option>
                                        <option value="late">Late</option>
                                        <option value="absent">Absent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <label for="">Reason of Absence:</label>
                                    <select class="form-control"  name="" id="absence-reason-filter">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <table id="dt-attendance-records" class="table table-bordered table-sm small-font align-middle mt-4" style="width:100%">
                            <thead>
                                <tr class="align-middle">
                                    <th class="text-start">Dates</th>
                                    <th class="text-start">Attendance Status</th>
                                    <th class="text-start">Reason for Absence</th>
                                    <th class="text-start">Biometric</th>
                                    <th class="text-start">School Name</th>
                                </tr>
                            </thead>
                        </table>

                        <div class="mt-4">
                            <h4>Summary</h4>
                            <table id="dt-attendance-records-summary" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                <thead>
                                    <tr class="align-middle">
                                        <th class="text-start">Reports Submitted</th>
                                        <th class="text-start">Present: On-Time</th>
                                        <th class="text-start">Present: Late</th>
                                        <th class="text-start">Absent (Total)</th>
                                        <th class="text-start">Absent (Unauthorised)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h4>Teacher Timetable</h4>
                        <div id="timetable"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('script')
    <script>
        let isDistrictOfficerOrAbove ="{{ $isDistrictOfficerOrAbove }}";
        let dtPayrollSchoolTableData = [];
        let dtWideyaSchoolTableData = [];
        let dtCurrentClassesTableData = [];
        let dtPreviousClassesTableData = [];
        let dtLearnersAssignedTableData = [];
        let dtAttendanceRecordsTableData = [];
        let timetableEvents = [];

        moment.locale('fr');
        var now = moment();

        $(async function () {
           await requestTeacherSanctionsData();
           await requestTeacherAssignedLearners();
           await requestTeacherAttendanceSummaryData();
           await requestTeacherTimeTable();
           await requestTeacherAbsentReasons();

           var calendar = $('#timetable').Calendar({
            locale: 'en',
            enableKeyboard: false,
            weekday: {
                timeline: {
                    intervalMinutes: 60,
                    fromHour: 8,
                    toHour:17,
                    heightPx:30
                },
                dayline: {
                    weekdays: [1,2,3,4,5,7],
                    format: 'ddd',
                    month:{
                        // format:"x",
                        // heightPx:-1
                    },

                }
            },
            events: timetableEvents,
            defaultView: {
                largeScreen:'week',
                smallScreen:'week'
            },
            now:{
                enable:true,
                heightPx:3
            }
            // daynotes: daynotes
        }).init();
        })

        $("#search-attendance-record-btn").click(async function(){
            $startDate = $("#start-date").val();
            $endDate = $("#end-date").val();

            await requestTeacherAttendanceRecordsData($startDate,$endDate)
        })

        $("#attendance-status-filter").bind("change", async function () {
            setTeacherAttendanceRecords();
        })

        $("#absence-reason-filter").bind("change", async function () {
            setTeacherAttendanceRecords();
        })

        async function requestTeacherSanctionsData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-profile/school-data/{{ $teacherProfileDetails->uuid }}",
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.status === true) {
                       const data = response.data;
                       dtPayrollSchoolTableData = data.payrollSchools;
                       dtWideyaSchoolTableData = data.wideyaSchools;
                       dtCurrentClassesTableData = data.currentClasses;
                       dtPreviousClassesTableData = data.previousClasses;



                       payrollSchoolsTable.clear().draw();
                       payrollSchoolsTable.rows.add(dtPayrollSchoolTableData);
                       payrollSchoolsTable.columns.adjust().draw();

                       wideyachoolsTable.clear().draw();
                       wideyachoolsTable.rows.add(dtWideyaSchoolTableData);
                       wideyachoolsTable.columns.adjust().draw();

                       currentClassesTable.clear().draw();
                       currentClassesTable.rows.add(dtCurrentClassesTableData);
                       currentClassesTable.columns.adjust().draw();

                       previousClassesTable.clear().draw();
                       previousClassesTable.rows.add(dtPreviousClassesTableData);
                       previousClassesTable.columns.adjust().draw();
                    }
                },
                cache: false
            });
        }

        async function requestTeacherAssignedLearners() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-profile/assigned-learners/{{ $teacherProfileDetails->uuid }}",
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.status === true) {
                        dtLearnersAssignedTableData = response.data;

                        learnersAssignedTable.clear().draw();
                        learnersAssignedTable.rows.add(dtLearnersAssignedTableData);
                        learnersAssignedTable.columns.adjust().draw();
                    }
                },
                cache: false
            });
        }

        async function requestTeacherAttendanceSummaryData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-profile/attendance-summary/{{ $teacherProfileDetails->uuid }}",
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.status === true) {
                        const data = response.data;
                        let summary = data.attendanceSummaryChart;

                        let absentList = data.weeklyAttendance.absentAttendanceList;
                        let lateList = data.weeklyAttendance.lateAttendanceList;
                        let presentList = data.weeklyAttendance.presentAttendanceList;
                        let categories = data.weeklyAttendance.weekLabels;
                        
                        $("#excused-absences").text(summary.excusedAbsences)
                        $("#unauthorised-absences").text(summary.unauthorisedAbsences)
                        $("#late-arrivals").text(summary.lateArrivals)
                        $("#absenteeism-rate").text(summary.absenteeismRate+"%")
                       
                        weeklyAttendanceTrendsBarchart.xAxis[0].setCategories( categories );
                        // teacherAttendanceBarchart.series[0].setData(notReportedList);
                        weeklyAttendanceTrendsBarchart.series[0].setData(absentList);
                        weeklyAttendanceTrendsBarchart.series[1].setData(lateList);
                        weeklyAttendanceTrendsBarchart.series[2].setData(presentList);


                    }
                },
                cache: false
            });
        }

        async function requestTeacherAttendanceRecordsData(startDate,endDate) {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-profile/attendance-records/{{ $teacherProfileDetails->uuid }}",
                type: "POST",
                data:{
                    startDate,endDate
                },
                dataType: "json",
                success: function (response) {
                    if (response.status === true) {
                        dtAttendanceRecordsTableData = response.data.attendanceRecords;
                        const summary = response.data.summary;

                        attendanceRecordsTable.clear().draw();
                        attendanceRecordsTable.rows.add(dtAttendanceRecordsTableData);
                        attendanceRecordsTable.columns.adjust().draw();

                        $("#dt-attendance-records-summary > tbody").empty();

                        $('#dt-attendance-records-summary > tbody:last-child').append(`
                            <tr>
                                <td class='text-center'>${summary.reportsSubmitted}</td>
                                <td class='text-center'>${summary.present}</td>
                                <td class='text-center'>${summary.late}</td>
                                <td class='text-center'>${summary.absent}</td>
                                <td class='text-center'>${summary.unauthorisedAbsences}</td>
                            </tr>
                        `);
                    }
                },
                cache: false
            });
        }

        async function requestTeacherTimeTable() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-profile/time-table/{{ $teacherProfileDetails->uuid }}",
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.status === true) {
                        const teacherTimeTable = response.data;

                        teacherTimeTable.forEach(element => {
                            const startTime = element.start_time.split(":");
                            const endTime = element.end_time.split(":");
                    
                            timetableEvents.push({
                                start: now.startOf('week').add(moment().day(element.day_of_the_week_oid).weekday(), 'days').add(startTime[0], 'h').add(startTime[1], 'm').format('X'),
                                end: now.startOf('week').add(moment().day(element.day_of_the_week_oid).weekday(), 'days').add(endTime[0], 'h').add(endTime[1], 'm').format('X'),
                                title: `${element.start_time}-${element.end_time}`,
                                content: `Subject: ${element.school_subject}`,
                                // category:'Religious Moral Education'
                            })
                        });
                    }
                },
                cache: false
            });
        }

        async function requestTeacherAbsentReasons() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-absent-reasons",
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.status === true) {
                        let sel = $("#absence-reason-filter");
                        sel.empty();
                        sel.append('<option value="">All</option>')
                    
                        var records = response.data;
                        records.forEach(element => {
                            sel.append(`<option value='${element.item_id}'>${element.item_name}</option>`)
                        });
                    
                    }
                },
                cache: false
            });
        }

        let payrollSchoolsTable = $('#dt-payroll-schools').DataTable({
            data : dtPayrollSchoolTableData,
            scrollX: true,
            columns:[
                //Todo: add date for teacher duration to school
                // {
                //     data: 'school_name',
                //     render: function ( data, type, row, meta ) {
                //         return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                //     },
                // },
                {
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                }
            ],
            order:[]
        });

        let wideyachoolsTable = $('#dt-wideya-schools').DataTable({
            data : dtWideyaSchoolTableData,
            scrollX: true,
            columns:[
                //Todo: add date for teacher duration to school
                // {
                //     data: 'school_name',
                //     render: function ( data, type, row, meta ) {
                //         return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                //     },
                // },
                {
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                }
            ],
            order:[]
        });

        let currentClassesTable = $('#dt-current-classes').DataTable({
            data : dtCurrentClassesTableData,
            scrollX: true,
            columns:[
                {
                    data: 'school_group_name',
                    className: 'text-center',
                },
                {
                    data: 'school_group_level',
                    className: 'text-center',
                },
                {
                    data: 'count_learners',
                    className: 'text-center',
                },
                {
                    data: 'academic_year',
                    className: 'text-center',
                },
            ],
            order:[]
        });

        let previousClassesTable = $('#dt-previous-classes').DataTable({
            data : dtPreviousClassesTableData,
            scrollX: true,
            columns:[
                {
                    data: 'school_group_name',
                    className: 'text-center',
                },
                {
                    data: 'school_group_level',
                    className: 'text-center',
                },
                {
                    data: 'count_learners',
                    className: 'text-center',
                },
                {
                    data: 'academic_year',
                    className: 'text-center',
                },
            ],
            order:[]
        });

        let learnersAssignedTable = $('#dt-learners-assigned').DataTable({
            data : dtLearnersAssignedTableData,
            scrollX: true,
            columns:[
                {
                    data: 'learner_name',
                    className: 'text-center',
                    render: function ( data, type, row, meta ) {
                        if(isDistrictOfficerOrAbove){
                            if(data != null){
                                return data;
                            }else{
                                return ''
                            }
                        }else{
                            return '********'
                        }
                    },
                },
                {
                    data: 'maternal_status',
                    className: 'text-center',
                },
                {
                    data: 'vision',
                    className: 'text-center',
                },
                {
                    data: 'hearing',
                    className: 'text-center',
                },
                {
                    data: 'cognition',
                    className: 'text-center',
                },
                {
                    data: 'selfcare',
                    className: 'text-center',
                },
                {
                    data: 'communication',
                    className: 'text-center',
                },
            ],
            order:[]
        });

        var weeklyAttendanceTrendsBarchart = new Highcharts.Chart('weekly-attendacne-trends-barchart',(

            {
                chart: {
                    type: 'column',
                    height: 300,
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories:{},
                },
                yAxis: {
                    minPadding: 0,
                    maxPadding: 0,
                    softMax: 10,
                    title: {
                        text: ''
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
   
        let attendanceRecordsTable = $('#dt-attendance-records').DataTable({
            data : dtAttendanceRecordsTableData,
            fnRowCallback: function( row, data, dataIndex ) {
                setTableRowColor(row,data['attendance_status_oid'])
            },
            columns:[
                {
                    data: 'format_date',
                    className: 'text-center',
                },
                {
                    data: 'attendance_status',
                    className: 'text-center',
                },
                {
                    data: 'absent_reason',
                    className: 'text-center',
                },
                {
                    data: "biometric_method_oid",
                    className: 'text-center',
                    render: function ( data, type, full, meta ) {
                        if (data == 'fingerprint') {
                            return '<span class="fas fa-fingerprint" aria-hidden="true"></span>'
                        } else if (data == 'photo') {
                            return '<span class="fas fa-camera" aria-hidden="true"></span>'
                        }else{
                            return ''
                        }
                    }
                },
                {
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                }
            ],
            order:[]
        });
   
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



        function setTeacherAttendanceRecords(){
            
            let filterData = dtAttendanceRecordsTableData;

            let attendanceStatusFilterValue = $('#attendance-status-filter').find(":selected").val()
            let absenceReasonFilterValue = $('#absence-reason-filter').find(":selected").val()

            if(attendanceStatusFilterValue != ""){
                filterData = filterAttendanceRecordsTable(filterData,'attendance_status',attendanceStatusFilterValue)
            }

            if(absenceReasonFilterValue != ""){
                filterData = filterAttendanceRecordsTable(filterData,'absent_reason',absenceReasonFilterValue)
            }

            redrawAttendanceRecordsTable(filterData);
           
        }

        function filterAttendanceRecordsTable(records,column,value){
           return records.filter((e)=>{
                switch (column) {
                    case 'attendance_status':
                            return e.attendance_status_oid == value
                        break;
                    case 'absent_reason':
                            return e.absent_reason_oid == value
                        break;
                    default:
                        break;
                }
            })
        }

        function redrawAttendanceRecordsTable(data){
            attendanceRecordsTable.clear().draw();
            attendanceRecordsTable.rows.add(data);
            attendanceRecordsTable.columns.adjust().draw();
        }
   </script>
@endsection