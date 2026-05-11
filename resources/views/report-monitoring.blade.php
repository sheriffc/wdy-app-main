@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
    @include('assets.highcharts-js')
    @include('assets.highmaps-js')
    @include('assets.moment-js')
@endsection

@section('content')

    <div class="container">
        
        <div class="alert alert-secondary">
            <h4>Welcome to the Report Monitoring page</h4>   
            <p> 
                This page is designed to help you make sure the school leaders are submitting data accurately and consistently in the Wi De Ya App.
                Use this information on this page to follow up and provide support to those who are not fulfilling their responsibilities.
            </p>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h4>Reporting for Date: <span id="attendance-date-label"></span></h4>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 mt-1">
                        <select id="district-filter" class="form-select form-select-sm"
                                aria-label=".form-select-sm example">
                            <option value="">All Districts</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-1">
                        <select id="attendance-dates" class="form-select form-select-sm"
                                aria-label=".form-select-sm example">
                            <option value="">Attendance Dates</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <h4 class="mt-4">Monitoring Attendance Submissions</h4>
        <div class="row mt-3">
            <p>
                1. By the end of the day, follow up with the school leaders who have not yet submitted today's attendance. Once the day has passed, school leaders can no longer edit today's attendance and the data will not be captured. 
            </p>
            <!--attendance map-->
            <div class="col-md-4">
                <div>
                    <div class="card">
                        <div id="school-map-chart-container" style="width:100%; height:400px;">
                            <div class="map">
                                <div class="text-center mt-4">
                                    <h5 class="card-title">Map loading...</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Schools Reporting Teachers Attendance</h5>
                            <h6 id="schools-teachers-participating"
                                class="card-subtitle mb-2 text-muted">Loading...</h6>
                            <div class="d-flex align-items-center">
                                <div class="flex-fill px-1 py-2">
                                    <p id="reported-teacher-schools">Loading...</p>
                                    <hr/>
                                    <p id="not-reported-teacher-schools">Loading...</p>
                                </div>
                                <div class="px-3">
                                <h1 id="reported-teacher-schools-percentage"></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Schools Reporting Learners Attendance</h5>
                            <h6 id="schools-learner-participating"
                                class="card-subtitle mb-2 text-muted">Loading...</h6>
                            <div class="d-flex align-items-center">
                                <div class="flex-fill px-1 py-2">
                                    <p id="reported-learner-schools">Loading...</p>
                                    <hr/>
                                    <p id="not-reported-learner-schools">Loading...</p>
                                </div>
                                <div class="px-3">
                                <h1 id="reported-learner-schools-percentage"></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--school reported card-->
            <div class="col-md-8 ">
                <div class="card">
                    <div class="card-body">
                        <h5>List of Schools Missing Attendance for <span id="dt-school-attendance-missing-date"></span></h5>
                        <table id="dt-school-attendance-missing" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                            <thead>
                                <tr class="align-middle">
                                    <th class="text-start">School Name</th>
                                    <th class="text-start">School Leader</th>
                                    <th class="text-start">Phone Number</th>
                                    <th class="text-start">Submission Status</th>
                                    <th class="text-start">Days with Complete Attendance Submissions from <span id="dt-school-attendance-missing-from-date"></span><br> <small class="text-mute">*In a normal 2 week period, 10 reports expected</small></th>
                                </tr>
                                </thead>
                        </table>
                    </div>
                </div>
            </div>
       
        </div>
        <div class="row">
            <p class="mt-5">
                2. Contact the schools who are not consistently submitting attendance data over the past two weeks.
                Make sure they are getting the support necessary to fulfill their responsibilities.
            </p>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Attendance Report Submissions</h5>
                        <div class="btn-group mb-2" role="group" aria-label="Basic example">
                            <button id="last-week-btn" type="button" class="btn btn-secondary">Last Week</button>
                            <button id="next-week-btn" type="button" class="btn btn-secondary">Next Week</button>
                          </div>
                        <table id="dt-school-attendance-monitoring" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                            <thead>
                                <tr class="align-middle">
                                    <th class="text-start">School Name</th>
                                    <th class="text-start">School Leader</th>
                                    <th class="text-start">Phone Number</th>
                                    <th class="text-start">Date 1</th>
                                    <th class="text-start">Date 2</th>
                                    <th class="text-start">Date 3</th>
                                    <th class="text-start">Date 4</th>
                                    <th class="text-start">Date 5</th>
                                    <th class="text-start">Date 6</th>
                                    <th class="text-start">Date 7</th>
                                    <th class="text-start">Days with Complete Attendance Submissions from <span id="dt-school-attendance-monitoring-from-date"></span><br> <small class="text-mute">*In a normal 2 week period, 10 reports expected</small></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mt-4">Monitoring Profile Completion</h4>
        <div class="mt-3">
            <p>
                1. Contact the school leaders to ensure at minimum, the required fields for teachers and learners are being completed for all teachers and learner profiles.
            </p>
            <p class="alert alert-info">
                Note that "Key Fields" on a teacher profile are: First Name, Last Name, Teacher Role, Start Date, Date of Birth, Sex, Photo Registration, and Address. 
            </p>
            <p class="alert alert-info">
                Note that "Key Fields" on the learner profile are: First Name, Last Name, Sex, Maternal Status (if female), Date of Birth, Learner Needs Assessment, Strongest Language, Parent/Guardian Name, Parent/Guardian Address    
            </p>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div id="teacher-profile-completion-barchart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div id="learner-profile-completion-barchart"></div>
                            <div class="text-center" ></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between ">
                                <h5>Schools With the Lowest Profile Completion Rates</h5>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-danger" disabled>0%-49%</button>
                                    <button type="button" class="btn btn-warning" disabled>50%-79%</button>
                                    <button type="button" class="btn btn-success" disabled>80%-100%</button>
                                </div>
                            </div>
                            
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                  <button class="nav-link active" id="teacher-profile-completion-table-tab" data-bs-toggle="tab" data-bs-target="#teacher-profile-completion-table" type="button" role="tab" aria-controls="teacher-profile-completion-table" aria-selected="true">Teacher Profile Completion</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                  <button class="nav-link" id="learner-profile-completion-table-tab" data-bs-toggle="tab" data-bs-target="#learner-profile-completion-table" type="button" role="tab" aria-controls="learner-profile-completion-table" aria-selected="false">Learner Profile Completion</button>
                                </li>
                            </ul>
                            
                            
                            <div class="tab-content mt-2" id="myTabContent">
                                <div class="tab-pane fade show  active" id="teacher-profile-completion-table" role="tabpanel" aria-labelledby="teacher-profile-completion-table-tab">
                                    <table id="dt-teacher-profile-completion-table" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                        <thead>
                                            <tr class="align-middle">
                                                <th class="text-start">School</th>
                                                <th class="text-start">School Leader</th>
                                                <th class="text-start">Key Fields*</th>
                                                <th class="text-start">Teacher Role*</th>
                                                <th class="text-start">Start Date*</th>
                                                <th class="text-start">Profile Photo*</th>
                                                <th class="text-start">Address*</th>
                                                <th class="text-start">Phone Number</th>
                                                <th class="text-start">Fingerprint Reg</th>
                                                <th class="text-start">Email</th>
                                                <th class="text-start">NIN</th>
                                                
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="tab-pane fade active" id="learner-profile-completion-table" role="tabpanel" aria-labelledby="learner-profile-completion-table-tab">
                                    <table id="dt-learner-profile-completion-table" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                                        <thead>
                                            <tr class="align-middle">
                                                <th class="text-start">School</th>
                                                <th class="text-start">School Leader</th>
                                                <th class="text-start">Key Fields*</th>
                                                <th class="text-start">Date of Birth*</th>
                                                <th class="text-start">Sex*</th>
                                                <th class="text-start">Strongest Language*</th>
                                                <th class="text-start">Maternal Status*</th>
                                                <th class="text-start">Complete Needs Assessment*</th>
                                                <th class="text-start">Guardian Name*</th>
                                                <th class="text-start">Guardian Phone</th>
                                                <th class="text-start">Guardian Address</th>
                                                <th class="text-start">NIN</th>
                                                <th class="text-start">Admission number</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        let tableAll = 'all';
        let attendanceMonitoringTable = 'attendanceMonitoringTable';
        let currentDate = moment().format('YYYY-MM-DD')
        let isDistrictOfficerOrAbove ="{{ $isDistrictOfficerOrAbove }}";
        let selectedDistrict = null;
        let selectedDate = currentDate;
        let dynamicColumns = [];
        let dtSchoolAttendanceMonitoringTableData = [];
        // let tableSchoolAttendanceMonitoring = null;
        let dateRange = [];
        let dtSchoolAttendanceMissingTableData = [];
        let dtSchoolLearnerAttendanceMissingTableData = [];
        let dtTeacherProfileCompletionTableData = [];
        let dtLearnerProfileCompletionTableData = [];

        $("#district-filter").bind("change", async function () {
            selectedDistrict = this.value;
            await requestSchoolDailyReportChartData();
            await requestSchoolAttendanceMonitoringTableData(tableAll);
            await requestTeacherProfileCompletionChartData();
        })

        $("#attendance-dates").bind("change", async function () {
            selectedDate = this.value;
            renameSchoolAttendanceMonitoringTableHeader();
            setAttendanceMonitoringTableFromDate('dt-school-attendance-missing-from-date',moment(selectedDate).subtract(13, 'days').format("D MMM"),moment(selectedDate).format("D MMM"))
            await requestSchoolDailyReportChartData();
            await requestSchoolAttendanceMonitoringTableData(tableAll);
        })

        $("#last-week-btn").click(function(){
            selectedDate = moment(selectedDate).subtract(6, 'days').format('YYYY-MM-DD');
            renameSchoolAttendanceMonitoringTableHeader();
            requestSchoolAttendanceMonitoringTableData(attendanceMonitoringTable);
            disableNextWeekBtn()
        })

        $("#next-week-btn").click(function(){
            selectedDate = moment(selectedDate).add(6, 'days').format('YYYY-MM-DD');
            renameSchoolAttendanceMonitoringTableHeader();
            requestSchoolAttendanceMonitoringTableData(attendanceMonitoringTable);
            disableNextWeekBtn()
        })

        function disableNextWeekBtn(){
            const todayDate = moment(currentDate)
            const selectedAttendanceWeekDate = moment(selectedDate)
            const daysBetween = todayDate.diff(selectedAttendanceWeekDate,"days");

            $("#next-week-btn").prop('disabled', (daysBetween <= 5))
          
        }

        function setAttendanceMonitoringTableFromDate(elementID,firstDate,secondDate){
            $(`#${elementID}`).text(`${firstDate} - ${secondDate}`);
        }


        $(async function () {
            disableNextWeekBtn()
            renameSchoolAttendanceMonitoringTableHeader();
            setAttendanceMonitoringTableFromDate('dt-school-attendance-missing-from-date',moment(selectedDate).subtract(13, 'days').format("D MMM"),moment(selectedDate).format("D MMM"))
            await setAttendanceDates();
            await setDistrictFilter();
            await requestSchoolDailyReportChartData();
            await requestSchoolAttendanceMonitoringTableData(tableAll);
            await requestTeacherProfileCompletionChartData();
            $("#learner-profile-completion-table").removeClass("active");
        })

        async function requestSchoolDailyReportChartData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/daily-school-report-data",
                type: "POST",
                dataType: "json",
                data: {selectedDate, districtId: selectedDistrict},
                success: function (response) {
                    if (response.status === true) {

                        let mapData = response.data.attendanceMapData;
                        let schoolsTeachersReportedCard = response.data.schoolsTeachersReportedCardData;
                        let schoolsLearnersReportedCardData = response.data.schoolsLearnersReportedCardData;

                        //set attendance date label
                        $("#attendance-date-label").text(response.data.date)

                        //schools teacher card
                        $("#schools-teachers-participating").text(`${schoolsTeachersReportedCard.schools_total} Participating`)
                        $("#reported-teacher-schools").text(`${schoolsTeachersReportedCard.schools_reported} reported`)
                        $("#not-reported-teacher-schools").text(`${parseInt(schoolsTeachersReportedCard.schools_total) - parseInt(schoolsTeachersReportedCard.schools_reported)} not reported`)
                        var reportedSchoolsPercetage = (parseInt(schoolsTeachersReportedCard.schools_reported) == 0) ? 0 : Math.round(parseInt(schoolsTeachersReportedCard.schools_reported) / parseInt(schoolsTeachersReportedCard.schools_total) * 100);
                        $("#reported-teacher-schools-percentage").text(`${reportedSchoolsPercetage}%`);

                        //schools learner card
                        $("#schools-learner-participating").text(`${schoolsLearnersReportedCardData.schools_total} Participating`)
                        $("#reported-learner-schools").text(`${schoolsLearnersReportedCardData.schools_reported} reported`)
                        $("#not-reported-learner-schools").text(`${parseInt(schoolsLearnersReportedCardData.schools_total) - parseInt(schoolsLearnersReportedCardData.schools_reported)} not reported`)
                        var reportedSchoolsPercetage = (parseInt(schoolsLearnersReportedCardData.schools_reported) == 0) ? 0 : Math.round(parseInt(schoolsLearnersReportedCardData.schools_reported) / parseInt(schoolsLearnersReportedCardData.schools_total) * 100);
                        $("#reported-learner-schools-percentage").text(`${reportedSchoolsPercetage}%`);

                        // attendance map
                        // supports decimal zoom values  : )
                        let viewConfigs = {
                            0: {name: "All", id: 0, lon: -11.5935, lat: 8.619, zoom: 9},
                            31: {name: "Bo", id: 31, lon: -11.7195, lat: 7.9621, zoom: 8.9},
                            21: {name: "Bombali", id: 21, lon: -12.0629, lat: 9.0407, zoom: 9},
                            32: {name: "Bonthe", id: 32, lon: -12.2826, lat: 7.5034, zoom: 9},
                            51: {name: "Falaba", id: 51, lon: -11.1714, lat: 9.5022, zoom: 8.8},
                            11: {name: "Kailahun", id: 11, lon: -10.6936, lat: 8.0879, zoom: 9},
                            22: {name: "Kambia", id: 22, lon: -12.81, lat: 9.1842, zoom: 9.2},
                            52: {name: "Karene", id: 52, lon: -12.3162, lat: 9.4144, zoom: 8.5},
                            12: {name: "Kenema", id: 12, lon: -11.1961, lat: 7.9456, zoom: 8.6},
                            23: {name: "Koinadugu", id: 23, lon: -11.5961, lat: 9.3727, zoom: 8.5},
                            13: {name: "Kono", id: 13, lon: -10.9397, lat: 8.6927, zoom: 9.3},
                            33: {name: "Moyamba", id: 33, lon: -12.4261, lat: 8.0638, zoom: 9.3},
                            24: {name: "Port Loko", id: 24, lon: -12.8021, lat: 8.6537, zoom: 9.3},
                            34: {name: "Pujehun", id: 34, lon: -11.573, lat: 7.3097, zoom: 9.3},
                            25: {name: "Tonkolili", id: 25, lon: -11.8754, lat: 8.6681, zoom: 8.7},
                            41: {name: "Western Area Rur", id: 41, lon: -13.0988, lat: 8.3231, zoom: 10.5},
                            42: {name: "Western Area Urb", id: 42, lon: -13.1985, lat: 8.4643, zoom: 11.9},
                        };

                        // zoom to district
                        if(selectedDistrict === undefined || selectedDistrict === null || selectedDistrict === ""){
                            //reset zoom
                            attendanceMap.mapView.setView([-11.5935,8.6190],0, true, false);
                        }else{
                            let districtId = parseInt(selectedDistrict);
                            let viewConfig = viewConfigs[districtId];

                            attendanceMap.mapView.setView([viewConfig.lon,viewConfig.lat],viewConfig.zoom, true, false);

                        }

                        //find the district and paint it
                        attendanceMap.series[0].data.forEach( (district, index) => {
                            if(district.properties.CS_Dis_Num == selectedDistrict){
                                console.log('district found')
                                attendanceMap.series[0].data[index].update({
                                    color: '#F1FCFF'
                                });
                            }else{
                                attendanceMap.series[0].data[index].update({
                                    color: '#F7F7F7'
                                });
                            }
                        })

                        let pointData = mapData;

                        pointData.forEach(function(el, i) {
                            let lineColor = "grey";
                            let fillColor = "#FFFFFF";

                            if(el['teachers_reported'] > 0 && el['learners_reported'] > 0){
                                lineColor = "#52BE80";
                                fillColor = "#6df1a4";
                            }else if(el['teachers_reported'] > 0 || el['learners_reported'] > 0){
                                lineColor = "#dcc709";
                                fillColor = "#F8E859";
                            }

                            el['marker'] = {
                                lineColor,
                                fillColor,
                            }

                        });

                        attendanceMap.series[1].setData(pointData)

                    }
                },
                cache: false
            });
        }

        async function requestSchoolAttendanceMonitoringTableData(table) {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/school-attendance-monitoring-data",
                type: "POST",
                dataType: "json",
                data:{selectedDate,districtId: selectedDistrict},
                success: function(response) {
                    if(response.status === true){
                        dtSchoolAttendanceMonitoringTableData = response.data.schoolAttendanceMonitoringData;
                        if(table == tableAll){
                            dtSchoolAttendanceMissingTableData = response.data.attendanceMissingTable;

                            //set attendance date label
                            $("#dt-school-attendance-missing-date").text(response.data.date)
                    
                            tableSchoolAttendanceMonitoring.clear().draw();
                            tableSchoolAttendanceMonitoring.rows.add(dtSchoolAttendanceMonitoringTableData);
                            tableSchoolAttendanceMonitoring.columns.adjust().draw();

                            tableSchoolAttendanceMissing.clear().draw();
                            tableSchoolAttendanceMissing.rows.add(dtSchoolAttendanceMissingTableData);
                            tableSchoolAttendanceMissing.columns.adjust().draw(); 

                            // tableSchoolLearnerAttendanceMissing.clear().draw();
                            // tableSchoolLearnerAttendanceMissing.rows.add(dtSchoolLearnerAttendanceMissingTableData);
                            // tableSchoolLearnerAttendanceMissing.columns.adjust().draw(); 
                        }else{
                            tableSchoolAttendanceMonitoring.clear().draw();
                            tableSchoolAttendanceMonitoring.rows.add(dtSchoolAttendanceMonitoringTableData);
                            tableSchoolAttendanceMonitoring.columns.adjust().draw();
                        }   
                    }
                }
            })  
        }

        let tableSchoolAttendanceMissing = $('#dt-school-attendance-missing').DataTable({
            rowCallback: function(row, data, index){
                setSubmissionCellColor(data['day_7'],row,3)
            },
            data : dtSchoolAttendanceMissingTableData,
            scrollX: true,
            columns:[
                {
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.uuid}">${row.school_name}</a>`;
                    },
                },
                {
                    data: "school_leader_name",
                    className: 'text-start',
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
                    data: "school_leader_phone_number",
                    className: 'text-start',
                    render: function ( data, type, row, meta ) {
                        if(isDistrictOfficerOrAbove){
                            if(data != null){
                                return '0'+data;
                            }else{
                                return ''
                            }
                        }else{
                            return '<span class="fa fa-lock" aria-hidden="true"></span>'
                        }
                    },
                },
                {
                    data: "day_7",
                    className: 'text-start',
                    render: (data, type, row, meta) => setSubmissionStatus( data )
                },
                {
                    data: "submitted_attendance_days",
                    className: 'text-center'
                },
            ],
            order:[]
        });

       let tableSchoolAttendanceMonitoring = $('#dt-school-attendance-monitoring').DataTable({
            rowCallback: function(row, data, index){
                for (let index = 1; index <= 7; index++) {
                    setSubmissionCellColor(data[`day_${index}`],row,2+index)      
                }
            },
            scrollX: true,
            data: dtSchoolAttendanceMonitoringTableData,
            columns:[
                {
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.uuid}">${row.school_name}</a>`;
                    },
                },
                {
                    data: "school_leader_name",
                    className: 'text-start',
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
                    data: "school_leader_phone_number",
                    className: 'text-start',
                    render: function ( data, type, row, meta ) {
                        if(isDistrictOfficerOrAbove){
                            if(data != null){
                                return '0'+data;
                            }else{
                                return ''
                            }
                        }else{
                            return '<span class="fa fa-lock" aria-hidden="true"></span>'
                        }
                    },
                },
                {
                    data: "day_1",
                    className: 'text-start',
                    render: (data, type, row, meta) => setSubmissionStatus( data )
                },
                {
                    data: "day_2",
                    className: 'text-start',
                    render: (data, type, row, meta) => setSubmissionStatus( data )
                },
                {
                    data: "day_3",
                    className: 'text-start',
                    render: (data, type, row, meta) => setSubmissionStatus( data )
                },
                {
                    data: "day_4",
                    className: 'text-start',
                    render: (data, type, row, meta) => setSubmissionStatus( data )
                },
                {
                    data: "day_5",
                    className: 'text-start',
                    render: (data, type, row, meta) => setSubmissionStatus( data )
                },
                {
                    data: "day_6",
                    className: 'text-start',
                    render: (data, type, row, meta) => setSubmissionStatus( data )
                },
                {
                    data: "day_7",
                    className: 'text-start',
                    render: (data, type, row, meta) => setSubmissionStatus( data )
                },
                {
                    data: "submitted_attendance_days",
                    className: 'text-center'
                },
            ],
            order:[]
        });

        async function requestTeacherProfileCompletionChartData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/profile-completion",
                type: "POST",
                dataType: "json",
                data:{selectedDate,districtId: selectedDistrict},
                success: function(response) {
                    if(response.status === true){

                        dtTeacherProfileCompletionTableData = response.data.schoolTeachersProfileCompletionTable;
                        dtLearnerProfileCompletionTableData = response.data.schoolLearnersProfileCompletionTable;

                        let teachersProfileCompletionChartData = response.data.teacherProfileCompletionBarChart;
                        let learnersProfileCompletionChartData = response.data.learnerProfileCompletionBarChart;

                        //set attendance date label
                        $("#attendance-date-label").text(response.data.date)               

                        teacherProfileCompletionDatatable.clear().draw();
                        teacherProfileCompletionDatatable.rows.add(dtTeacherProfileCompletionTableData);
                        teacherProfileCompletionDatatable.columns.adjust().draw();

                        learnerProfileCompletionDatatable.clear().draw();
                        learnerProfileCompletionDatatable.rows.add(dtLearnerProfileCompletionTableData);
                        learnerProfileCompletionDatatable.columns.adjust().draw();

                        //teacher profile completion bar chart
                        teacherProfileCompletionBarchart.series[0].setData([
                            teachersProfileCompletionChartData.required_fields_complete,
                            teachersProfileCompletionChartData.teacher_role,
                            teachersProfileCompletionChartData.start_date,
                            teachersProfileCompletionChartData.photo_registration,
                            teachersProfileCompletionChartData.address,
                            teachersProfileCompletionChartData.phone_number,
                            teachersProfileCompletionChartData.email,
                            teachersProfileCompletionChartData.nin,
                            teachersProfileCompletionChartData.fingerprint_registration,
                             
                        ]);

                        //learner profile completion bar chart
                        learnerProfileCompletionBarchart.series[0].setData([
                            learnersProfileCompletionChartData.required_fields_complete,
                            learnersProfileCompletionChartData.date_of_birth,
                            learnersProfileCompletionChartData.sex,
                            learnersProfileCompletionChartData.strongest_language,
                            learnersProfileCompletionChartData.maternal_status,
                            learnersProfileCompletionChartData.complete_needs_assessment,
                            learnersProfileCompletionChartData.guardian_name,
                            learnersProfileCompletionChartData.guardian_phone,
                            learnersProfileCompletionChartData.guardian_address,
                            learnersProfileCompletionChartData.nin,
                            learnersProfileCompletionChartData.admission_number,
                             
                        ]);

                    }

                },
                cache: false
            });
        }
        let teacherProfileCompletionBarchart = new Highcharts.Chart('teacher-profile-completion-barchart', (
            {
                chart: {
                    type: 'bar',
                },
                title: {
                    text: 'Teacher Profile Completion'
                },
                subtitle: {
                    text: '* Indicates a required field.'
                },
                xAxis: {
                    categories: [
                        'Key Fields *',
                        'Teacher Role *',
                        'Start Date *',
                        'Profile Photo *', 
                        'Address*', 
                        'Phone Number',
                        'Email',
                        'NIN', 
                        'Fingerprint Registration'
                    ],
                },
                yAxis: {
                    min: 0,
                    max:100,
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
                    pointFormat: 'Completed: {point.y}%'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            pointFormat: '{point.y}%',
                        }
                    }
                },

                series: [
                    {
                        showInLegend: false,
                    },
                ],

                credits: {
                    enabled: false
                }

            }

        ));

        var learnerProfileCompletionBarchart = new Highcharts.Chart('learner-profile-completion-barchart', (
            {
                chart: {
                    type: 'bar',
                },
                title: {
                    text: 'Learner Profile Completion'
                },
                subtitle: {
                    text: '* Indicates a required field.'
                },
                xAxis: {
                    categories: [
                        'Key Fields *',
                        'Date of Birth *',
                        'Sex *',
                        'Strongest Language*',
                        'Maternal Status *',
                        'Complete Needs Assesment *',
                        'Guardian Name *',
                        'Guardian Phone', 
                        'Guardian Address',
                        'NIN',
                        'Admission Number',
                    ],
                },
                yAxis: {
                    min: 0,
                    max:100,
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
                    pointFormat: 'Completed: {point.y}%'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            pointFormat: '{point.y}%',
                        }
                    }
                },

                series: [
                    {
                        showInLegend: false,
                    },
                ],

                credits: {
                    enabled: false
                }

            }

        ));

        let teacherProfileCompletionDatatable = $('#dt-teacher-profile-completion-table').DataTable({
            rowCallback: function(row, data, index){
                let teacherProfileCompletionTableColumns = [
                    "required_fields_complete",
                    "teacher_role",
                    "start_date",
                    "photo_registration",
                    "address",
                    "phone_number",
                    "fingerprint_registration",
                    "email",
                    "nin"
                ]
                setProfileCompletionCellColor(data,row,teacherProfileCompletionTableColumns);
            },
            data : dtTeacherProfileCompletionTableData,
            scrollX: true,
            columns: [
                {
                    data: "school_name",
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                    width:"80%"
                },
                {
                    data: "school_leader_name",
                    className: 'text-start',
                    render: function ( data, type, row, meta ) {
                        if(isDistrictOfficerOrAbove){
                            if(data != null){
                                return `<span>${row.school_leader_name}</span><br><span>${(row.school_leader_phone_number != null) ? '0'+row.school_leader_phone_number:''}</span>`;
                            }else{
                                return ''
                            }
                        }else{
                            return '********'
                        }
                    },
                },
                {
                    data: "required_fields_complete",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "teacher_role",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "start_date",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "photo_registration",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "address",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "phone_number",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "fingerprint_registration",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "email",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "nin",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
            ],
            select: false,
            order: [[3,'asc']],
        });

        let learnerProfileCompletionDatatable = $('#dt-learner-profile-completion-table').DataTable({
            rowCallback: function(row, data, index){
                let learnerProfileCompletionTableColumns = [
                    "required_fields_complete",
                    "date_of_birth",
                    "sex",
                    "strongest_language",
                    "maternal_status",
                    "complete_needs_assessment",
                    "guardian_name",
                    "guardian_phone",
                    "guardian_address",
                    "nin",
                    "admission_number"
                ]
                setProfileCompletionCellColor(data,row,learnerProfileCompletionTableColumns);
            },
            data : dtLearnerProfileCompletionTableData,
            scrollX: true,
            columns: [
                {
                    data: "school_name",
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                    width:"80%"
                },
                {
                    data: "school_leader_name",
                    className: 'text-start',
                    render: function ( data, type, row, meta ) {
                        if(isDistrictOfficerOrAbove){
                            if(data != null){
                                return `<span>${row.school_leader_name}</span><br><span>${(row.school_leader_phone_number != null) ? '0'+row.school_leader_phone_number:''}</span>`;
                            }else{
                                return ''
                            }
                        }else{
                            return '********'
                        }
                    },
                },
                {
                    data: "required_fields_complete",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "date_of_birth",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "sex",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "strongest_language",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "maternal_status",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "complete_needs_assessment",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "guardian_name",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "guardian_phone",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "guardian_address",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "nin",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
                {
                    data: "admission_number",
                    className: 'text-end',
                    render: function ( data, type, full, meta ) {
                       return setPercentageText(data)
                    },
                },
            ],
            select: false,
            order: [[3,'asc']],

        });

        function renameSchoolAttendanceMonitoringTableHeader(){
            let colNumber = 0;
            for (let index = 6; index >= 0; index--) {
                $(tableSchoolAttendanceMonitoring.column(3+colNumber).header()).text(moment(selectedDate).subtract(index, 'days').format("D MMM"));
                colNumber ++;
            }
            setAttendanceMonitoringTableFromDate('dt-school-attendance-monitoring-from-date',moment(selectedDate).subtract(13, 'days').format("D MMM"),moment(selectedDate).format("D MMM"))
        }

        async function setAttendanceDates() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/attendance-dates",
                type: "POST",
                dataType: "json",
                success: function (response) {
                    var sel = $("#attendance-dates");
                    sel.empty();
                    // sel.append('<option value="">All Districts</option>')
                    if (response.status === true) {
                        var dates = response.data;
                        let todayDate = dates.find((e)=> e.date == currentDate);
                        if(todayDate == null){
                            sel.append(`<option value='${currentDate}'>${moment(currentDate).format("dddd, Do MMM YYYY")}</option>`)
                        }
                        dates.forEach(element => {
                            sel.append(`<option value='${element.date}'>${element.format_date}</option>`)
                        });
                    }
                }
            })
        }

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
            // let color = "";
            // if(percentage <= 50){
            //     color = "text-danger"
            // }else if(percentage >=51 && percentage <= 79){
            //     color = "text-warning"
            // }
            return `<span >${percentage}%</span>`
        }

        function getYearMonthDate(date) {
            var day = String(date.getDate()).padStart(2, '0');
            var month = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
            var year = date.getFullYear();
            var newDate = `${year}-${month}-${day}`;
            return newDate;
        }

        function setSubmissionStatus( data ) {
            switch (data) {
                case 'missing_both':
                    return 'Missing both';
                    break;
                case 'missing_teacher':
                    return 'Missing teacher';
                    break;
                case 'missing_learner':
                    return 'Missing learner';
                    break;
                case 'complete':
                    return 'Complete';
                    break;
                default:
                    return '';
                    break;
            }
        }

        function setSubmissionCellColor(data,row,cellNumber){
                switch (data) {
                case 'missing_both':
                    $(row).find(`td:eq(${cellNumber})`).css('background-color', '#ffc4c4');
                    break;
                case 'missing_teacher':
                    $(row).find(`td:eq(${cellNumber})`).css('background-color', '#fcddbc');
                    break;
                case 'missing_learner':
                    $(row).find(`td:eq(${cellNumber})`).css('background-color', '#fff9b8');
                    break;
                case 'complete':
                    $(row).find(`td:eq(${cellNumber})`).css('background-color', '#cbe8c0');
                    break;
                default:
                    break;
            }
        }

        function setProfileCompletionCellColor(data,row,columnsArray){
            for (let index = 0; index <= columnsArray.length; index++) {
                    let cellNumber = 2 + index;
                    let colValue = data[columnsArray[index]];
                    if(colValue <= 50){
                        $(row).find(`td:eq(${cellNumber})`).css('background-color', '#ffc4c4');
                    }else if(colValue > 50 && colValue < 80){
                        $(row).find(`td:eq(${cellNumber})`).css('background-color', '#fff9b8');
                    }else if(colValue >= 80){
                        $(row).find(`td:eq(${cellNumber})`).css('background-color', '#cbe8c0');
                    }
                }
        }

    </script>

    <script>

        (function (H) {
            H.wrap(H.Chart.prototype, 'pan', function (proceed) {
                H.each(this.yAxis, function (axis) {
                    axis.fixTo = null;
                });
                proceed.apply(this, Array.prototype.slice.call(arguments, 1));
            });
        })(Highcharts);


        {{--var pointData = {!! json_encode($gpsSchools,JSON_NUMERIC_CHECK) !!};--}}
        let pointData = [];

        // pointData.forEach(function(el, i) {
        //     el['marker'] = {
        //         lineColor: el['teachers_reported'] > 0 ? "#52BE80" : "grey",
        //         fillColor: el['teachers_reported'] > 0 ? "#6df1a4" : "#FFFFFF",
        //     }
        //
        // });

        let attendanceMap = new Highcharts.mapChart('school-map-chart-container', {
            chart: {
                events: {
                    load: function () {
                        {{--
                        https://stackoverflow.com/questions/28933713/how-to-zoom-to-specific-point-in-highmaps
                        https://api.highcharts.com/class-reference/Highcharts.Chart#mapZoom
                        https://www.highcharts.com/forum/viewtopic.php?t=40287
                        https://www.highcharts.com/forum/viewtopic.php?t=37127
                        --}}
                        // this.mapZoom(0.4, -13.5, -50, 0, 0);
                        // this.xAxis[0].setExtremes(-13.205, -13.2005);
                        // chart.yAxis[0].startingExtremes = chart.yAxis[1].getExtremes();
                    }
                }
            },
            title: {
                text: 'Participating Schools: Daily Report Status'
            },
            legend: {
                itemHiddenStyle:{color : null},
            },
            mapNavigation: {
                enabled: true,
                //     enableButtons: true,
                //     buttonOptions: {
                //         verticalAlign: 'bottom'
                //     },
                    enableDoubleClickZoomTo:true,
                    enableMouseWheelZoom:true,
                    enableTouchZoom:true
            },

            mapView: {
                maxZoom: undefined
            },

            tooltip: {
                pointFormatter: function(){
                    // split the geo information in brackets at the end of the school name so it doesn't make the hover box huge
                    //     (otherwise long school name gets forced onto 1 line)
                    let nameSplit = this.name.split('(');
                    let first = (typeof nameSplit[0] === 'undefined') ? 'No Name Set' : nameSplit[0];
                    let second = (typeof nameSplit[1].split(')')[0] === 'undefined') ? '' : '<br>' + nameSplit[1].split(')')[0];

                    return '<strong>'+first+'</strong>'+second+'<br>' +
                        'EMIS Code: '+ ( (this.emis_id !== null) ? this.emis_id : 'None' ) +'<br>' +
                        'School Payroll ID: '+ ( (this.payroll_sid !== null) ? this.payroll_sid : 'None' ) +'<br>' +
                        '————'+'<br>' +
                        'Teachers Total: '+this.teachers_total +'<br>' +
                        'Teachers Present: '+this.teachers_present +'<br>' +
                        'Teachers Absent: '+this.teachers_absent +'<br>' +
                        // 'Unknown: '+this.unknown +'<br>' +
                        'Teachers Not Reported: '+this.teachers_not_reported +'<br>' +
                        '————'+'<br>' +
                        'Learners Total: '+this.learners_total +'<br>' +
                        'Learners Reported: '+this.learners_reported +'<br>' +
                        'Learners Not Reported: '+this.learners_not_reported +'<br>'
                        ;
                }
            },
            series: [
                {
                    mapData: {!! file_get_contents('json/sl_districts.geojson') !!},
                    name: 'Districts',
                    type: 'map',
                    showInLegend: false,
                    dataLabels: false
                },
                {
                    name: 'School',
                    type: 'mappoint',
                    title: false,
                    data: pointData,
                    {{--data: {!! json_encode($gpsSchools,JSON_NUMERIC_CHECK) !!},--}}
                    color: '#009ADE',
                    marker: {
                        fillColor: '#FFFFFF',
                        lineColor: '#009ADE',
                        lineWidth: 1,
                        radius: 3
                    },
                    dataLabels: {enabled: false},
                    // dataLabels: false,
                    animation: false,
                    tooltip: {
                        pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    showInLegend: false,
                    point: {
                        events: {
                            click: function () {
                                if (this.id) {
                                    window.open('/school/' + this.id);
                                }
                            }
                        },
                    },

                },
                { 
                    name: "Complete Submitted Attendance",
                    color: '#6df1a4',
                },
                { 
                    name: "Partial Submitted Attendance",
                    color: '#F8E859',
                },
                { 
                    name: "Not Submitted Attendance",
                    color:'#D3D3D3',
                },

            ],
            credits: {
                enabled: false
            },
        });
    </script>
@endsection
