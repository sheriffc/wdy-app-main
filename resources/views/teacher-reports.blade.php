@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
    @include('assets.moment-js')
@endsection

@section('content')

    <div class="container">
        <div class="d-sm-flex justify-content-between">
            <div class="col-md-6">
                {{-- <h4>Reporting for Date: <span id="attendance-date-label"></span></h4> --}}
            </div>
            <div class="row col-md-6">
                <div class="col-md-6 ">
                
                    <div class=" mt-1">
                        <select id="district-filter" class="form-select form-select-sm"
                                aria-label=".form-select-sm example">
                            <option value="">All Districts</option>
                        </select>
                    </div>
             
            </div>
            <div class="col-md-6">
                
                    <div class=" mt-1">
                        <select disabled id="chiefdom-filter" class="form-select form-select-sm"
                                aria-label=".form-select-sm example">
                            <option value="">All Chiefdoms</option>
                        </select>
                    </div>
             
            </div>
            </div>
            
        </div>

        <div class="col-md-12 mt-4">
          
            <h5 class="col-md-6 card-title">Teachers Liable to Attendance Sanctions for <span id="teacher-sanction-selected-date"></span></h5>
                
            <p>
                These teachers have received 3 or more absences without a valid reason during the indicated month.
            </p>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-2 mt-1">
                            <label for="">Month:</label>
                            <select id="teacher-sanctions-month" class="form-select form-select-sm"
                                    aria-label=".form-select-sm example">
                            </select>
                        </div>
                        <div class="col-md-2 mt-1">
                            <label for="">Year:</label>
                            <select id="teacher-sanctions-year" class="form-select form-select-sm"
                                    aria-label=".form-select-sm example">
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for=""></label><br>
                            <button id="teacher-sanctions-search-btn" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                    <table id="dt-teacher-sanctions" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">PIN</th>
                                <th class="text-start">School</th>
                                <th class="text-start">School Leader</th>
                                <th class="text-start">Phone Number</th>
                                <th class="text-start">Number Unauthorised Absences</th>
                            </tr>
                            </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <h5 class="card-title">Removable Teachers</h5>
            <p>
                These teachers have been removed from their assigned school. Once the teacher has been removed from the payroll, they will be removed from this table.
            </p>
            <div class="card">
                <div class="card-body">
                    <table id="dt-removed-teachers" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">PIN</th>
                                <th class="text-start">School Removed From</th>
                                <th class="text-start">Removed By</th>
                                <th class="text-start">School Leader Phone</th>
                                <th class="text-start">Reason Teacher Left</th>
                                <th class="text-start">Notes</th>
                                <th class="text-start">Date Left</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <h5 class="card-title">Teachers Requiring Investigation</h5>
            <p>
                These teachers were removed by head teachers citing either that they did not know the person or what they are doing, or “other” reasons. Once a teacher has been removed from the payroll or added to another school, they will be removed from this table.
            </p>
            <div class="card">
                <div class="card-body">
                    <table id="dt-further-investigation" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">PIN</th>
                                <th class="text-start">School Removed From</th>
                                <th class="text-start">Reason For Removal</th>
                                <th class="text-start">Reason Teacher Left</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
      
        <div class="col-md-12 mt-4">
            <h5 class="card-title">Unauthorised Teacher Transfers</h5>
            <p>
                These teachers were either (1) removed by the head teacher from the school they were assigned to on a payroll or (2) added to a school they were not initially assigned to. Once their payroll SID matches the SID of the school they are actively assigned to on WDY, they will be removed from the list.
            </p>
            <div class="card">
                <div class="card-body">
                    <table id="dt-unauthorised-transfers" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">PIN</th>
                                <th class="text-start">Phone Number</th>
                                <th class="text-start">Current School Assignment</th>
                                <th class="text-start">Current SID</th>
                                <th class="text-start">Payroll School Assignment</th>
                                <th class="text-start">Payroll SID</th>
                                <th class="text-start">Transfer Notes</th>
                            </tr>
                            </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <h5 class="card-title">Active Teachers</h5>
            <p>
                This is a list of all teachers (payroll and non-payroll) that are currently enrolled in a WDY school.
            </p>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="">Employment Status:</label>
                            <select id="employment-status-filter" class="form-select form-select-sm"
                                    aria-label=".form-select-sm example">
                                <option value="">All</option>
                                <option value="payroll">Payroll</option>
                                <option value="non-payroll">Non-Payroll</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="">Gender:</label>
                            <select id="gender-filter" class="form-select form-select-sm"
                                    aria-label=".form-select-sm example">
                                <option value="">All</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="">Teacher Role:</label>
                            <select id="teacher-role-filter" class="form-select form-select-sm"
                                    aria-label=".form-select-sm example">
                                <option value="">All</option>
                                <option value="teacher">Teacher</option>
                                <option value="senior_teacher">Senior Teacher</option>
                                <option value="head_of_department">Head of Department</option>
                                <option value="deputy_head_teacher">Deputy of Department</option>
                                <option value="head_teacher">Head-Teacher</option>
                                <option value="vice_principal">Vice-Principal</option>
                            </select>
                        </div>
                    </div>
                    <table id="dt-active-teachers" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">PIN</th>
                                <th class="text-start">Phone Number</th>
                                <th class="text-start">Gender</th>
                                <th class="text-start">Employment Status</th>
                                <th class="text-start">Teacher Role</th>
                                <th class="text-start">Current School</th>
                                {{-- <th class="text-start">Current District</th>
                                <th class="text-start">Current Chiefdom</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let selectedDistrict = null;
        let selectedChiefdom = null;
        let isDistrictOfficerOrAbove ="{{ $isDistrictOfficerOrAbove }}";
        let dtTeacherSanctionsTableData = []
        let dtUnauthorisedTransfersTableData = []
        let dtRemovedTeachersTableData = []
        let dtFurtherInvestigationTableData = []
        let dtActiveTeachersTableData = []
        let selectedTeacherSanctionMonth = moment().subtract(1, 'months').format('YYYY-MM')

        $(async function () {
            setTabelSanctionsMonthYearDropdown();
            setTeacherSanctionsTableDate(selectedTeacherSanctionMonth);
            await setDistrictFilter();
            await requestTeacherSanctionsData();
            await requestUnauthorizedTransfersData();
            await requestRemovedTeachersData();
            await requestFurtherInvestigationData();
            await requestActiveTeachersData();
        });

        $("#district-filter").bind("change", async function () {
            selectedDistrict = this.value;
            selectedChiefdom = null;
            
            let chiefdomDropdown = $("#chiefdom-filter");
            chiefdomDropdown.prop( "disabled", (selectedDistrict == '') );
            chiefdomDropdown.empty();
            chiefdomDropdown.append('<option value="">All Chiefdoms</option>')

            await setChiefdomFilter();
            await requestTeacherSanctionsData();
            await requestRemovedTeachersData();
            await requestFurtherInvestigationData();
            await requestUnauthorizedTransfersData();
            await requestActiveTeachersData();
        })

        $("#chiefdom-filter").bind("change", async function () {
            selectedChiefdom = this.value;
            await requestTeacherSanctionsData();
            await requestRemovedTeachersData();
            await requestFurtherInvestigationData();
            await requestUnauthorizedTransfersData();
            await requestActiveTeachersData();
        })

        $("#teacher-sanctions-search-btn").click(async function(){
            let month = $('#teacher-sanctions-month').find(":selected").val();
            let year = $('#teacher-sanctions-year').find(":selected").val();
            let yearMonth = year+"-"+month;
            selectedTeacherSanctionMonth = yearMonth;
            setTeacherSanctionsTableDate(yearMonth);
            await requestTeacherSanctionsData();
        })

        $("#employment-status-filter").bind("change", async function () {
            filterTableActiveTeachers();
        })

        $("#gender-filter").bind("change", async function () {
            filterTableActiveTeachers();
        })

        $("#teacher-role-filter").bind("change", async function () {
            filterTableActiveTeachers();
        })

        async function requestTeacherSanctionsData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-reports/teacher-sanctions",
                type: "POST",
                dataType: "json",
                data: { 
                    month:selectedTeacherSanctionMonth, 
                    districtId: selectedDistrict,
                    chiefdomId: selectedChiefdom
                },
                success: function (response) {
                    if (response.status === true) {
                        dtTeacherSanctionsTableData = response.data.teachersEligbleForSanctionsTable;

                        tableTeacherSanctions.clear().draw();
                        tableTeacherSanctions.rows.add(dtTeacherSanctionsTableData);
                        tableTeacherSanctions.columns.adjust().draw();
        
                    }
                },
                cache: false
            });
        }

        async function requestUnauthorizedTransfersData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-reports/unauthorised-transfers",
                type: "POST",
                dataType: "json",
                data: { 
                        districtId: selectedDistrict,
                        chiefdomId: selectedChiefdom
                    },
                success: function (response) {
                    if (response.status === true) {
                        dtUnauthorisedTransfersTableData = response.data.unauthorisedTeacherTransfersTable;

                        tableUnauthorisedTransfers.clear().draw();
                        tableUnauthorisedTransfers.rows.add(dtUnauthorisedTransfersTableData);
                        tableUnauthorisedTransfers.columns.adjust().draw();
                    }
                },
                cache: false
            });
        }

        async function requestRemovedTeachersData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-reports/removed-teachers",
                type: "POST",
                dataType: "json",
                data: { 
                        districtId: selectedDistrict,
                        chiefdomId: selectedChiefdom
                    },
                success: function (response) {
                    if (response.status === true) {
                        dtRemovedTeachersTableData = response.data.removableTeachersTable;

                        tableRemovedTeachers.clear().draw();
                        tableRemovedTeachers.rows.add(dtRemovedTeachersTableData);
                        tableRemovedTeachers.columns.adjust().draw();
        
                    }
                },
                cache: false
            });
        }

        async function requestFurtherInvestigationData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-reports/further-investigation",
                type: "POST",
                dataType: "json",
                data: { 
                        districtId: selectedDistrict,
                        chiefdomId: selectedChiefdom
                    },
                success: function (response) {
                    if (response.status === true) {
                        dtFurtherInvestigationTableData = response.data.teacherRequiringInvestigationTable;

                        tableFurtherInvestigation.clear().draw();
                        tableFurtherInvestigation.rows.add(dtFurtherInvestigationTableData);
                        tableFurtherInvestigation.columns.adjust().draw();
        
                    }
                },
                cache: false
            });
        }

        async function requestActiveTeachersData() {

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-reports/active-teachers",
                type: "POST",
                dataType: "json",
                data: { 
                        districtId: selectedDistrict,
                        chiefdomId: selectedChiefdom
                    },
                success: function (response) {
                    if (response.status === true) {
                        dtActiveTeachersTableData = response.data.activeTeachersTable;

                        redrawTableActiveTeachers(dtActiveTeachersTableData);
                    }
                },
                cache: false
            });
        }

        let tableTeacherSanctions = $('#dt-teacher-sanctions').DataTable({
            data : dtTeacherSanctionsTableData,
            scrollX: true,
            columns:[
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
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
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
                    data: "number_unauthorised_absences",
                    className: 'text-center',
                },
            ],
            order:[]
        });

        let tableUnauthorisedTransfers = $('#dt-unauthorised-transfers').DataTable({
            data : dtUnauthorisedTransfersTableData,
            scrollX: true,
            columns:[
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
                    data: "phone_number",
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
                    data: 'current_school_assignment',
                    render: function ( data, type, row, meta ) {
                        if(row.current_school_uuid != null){
                            return `<a href="/school/${row.current_school_uuid}">${row.current_school_assignment}</a>`;
                        }else{
                            return data
                        }
                       
                    },
                },
                {
                    data: "current_sid",
                    className: 'text-start',
                },
               
                {
                    data: "payroll_school_assignment",
                    className: 'text-start',
                    render: function ( data, type, row, meta ) {
                        if(row.payroll_school_uuid != null){
                            return `<a href="/school/${row.payroll_school_uuid}">${row.payroll_school_assignment}</a>`;
                        }else{
                            return data
                        }
                       
                    },
                },
                {
                    data: "payroll_sid",
                    className: 'text-center',
                },
               
                {
                    data: "transfer_notes",
                    className: 'text-start',
                },
            ],
            order:[]
        });

        let tableRemovedTeachers = $('#dt-removed-teachers').DataTable({
            data : dtRemovedTeachersTableData,
            scrollX: true,
            columns:[
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
                    data: 'school_removed_from',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_removed_from}</a>`;
                    },
                },
                {
                    data: "removed_by",
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
                    data: "school_leader_phone",
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
                    data: "end_reason",
                    className: 'text-start',
                },
                {
                    data: "comments",
                    className: 'text-start',
                },
                {
                    data: "date_left_school",
                    className: 'text-start',
                },
            ],
            order:[]
        });

        let tableFurtherInvestigation = $('#dt-further-investigation').DataTable({
            data : dtFurtherInvestigationTableData,
            scrollX: true,
            columns:[
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
                    data: 'school_removed_from',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_removed_from}</a>`;
                    },
                },
                {
                    data: "reason_for_removal",
                    className: 'text-start',
                },
                {
                    data: "comments",
                    className: 'text-start',
                },
         
            ],
            order:[]
        });

        let tableActiveTeachers = $('#dt-active-teachers').DataTable({
            data : dtActiveTeachersTableData,
            scrollX: true,
            columns:[
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
                    data: "teacher_phone_number",
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
                    data: "gender",
                    className: 'text-start',
                },
                {
                    data: 'employment_status',
                    className: 'text-start',
                },
                {
                    data: "teacher_role",
                    className: 'text-start',
                },
                {
                    data: "current_school",
                    className: 'text-start',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.current_school}</a>`;
                    },
                },
                // {
                //     data: "current_district",
                //     className: 'text-start',
                // },
                // {
                //     data: "current_chiefdom",
                //     className: 'text-start',
                // },
                
         
            ],
            order:[]
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
       
        function setChiefdomFilter(){
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/district-chiefdoms",
                dataType: 'json',
                data: { districtId: selectedDistrict},
                success: function (response) {
                    let sel = $("#chiefdom-filter");
                    sel.empty();
                    sel.append('<option value="">All Chiefdoms</option>')
                    if (response.status === true) {
                        var records = response.data;
                        records.forEach(element => {
                            sel.append(`<option value='${element.id}'>${element.name}</option>`)
                        });
                    }
                }
            });
        }

        function filterTableActiveTeachers(){
            
            let filterData = dtActiveTeachersTableData;
            let employmentStatusFilterValue = $('#employment-status-filter').find(":selected").val()
            let genderFilterValue = $('#gender-filter').find(":selected").val()
            let teacherRoleStatusFilterValue = $('#teacher-role-filter').find(":selected").val()

            if(employmentStatusFilterValue != ""){
                filterData = filterActiveTeachersRecords(filterData,'employment_status',employmentStatusFilterValue)
            }

            if(genderFilterValue != ""){
                filterData = filterActiveTeachersRecords(filterData,'gender',genderFilterValue)
            }

            if(teacherRoleStatusFilterValue != ""){
                filterData = filterActiveTeachersRecords(filterData,'teacher_role_oid',teacherRoleStatusFilterValue)
            }

            redrawTableActiveTeachers(filterData);
           
        }

        function filterActiveTeachersRecords(records,column,value){
           return records.filter((e)=>{
                switch (column) {
                    case 'employment_status':
                            return e.employment_status.toLowerCase() == value.toLowerCase()
                        break;
                    case 'gender':
                            return e.gender.toLowerCase() == value.toLowerCase()
                        break;
                    case 'teacher_role_oid':
                            return e.teacher_role_oid == value.toLowerCase()
                        break;
                    default:
                        break;
                }
            })
        }

        function redrawTableActiveTeachers(data){
            tableActiveTeachers.clear().draw();
            tableActiveTeachers.rows.add(data);
            tableActiveTeachers.columns.adjust().draw();
        }

        function setTeacherSanctionsTableDate(date){
            $('#teacher-sanction-selected-date').text(moment(date).format('MMM YYYY'));
        }

        function setTabelSanctionsMonthYearDropdown(){
            let teacherSanctionMonthSelect = $("#teacher-sanctions-month");
            let teacherSanctionYearSelect = $("#teacher-sanctions-year");

            let months = moment.months();
            let previousMonth = moment().subtract(1, 'months').format('M')
            let currentYear = moment().format('YYYY')

            months.forEach((element,index) => {
                let value = index+1;
                let selected = (value == previousMonth) ? 'selected' : '';
                teacherSanctionMonthSelect.append(`<option value='${('0' + value).slice(-2)}' ${selected}>${element}</option>`)
            });

            for (let index = 0; index < 20; index++) {
                let value = moment().subtract(index, 'years').format('YYYY');
                let selected = (value == currentYear) ? 'selected' : '';
                teacherSanctionYearSelect.append(`<option value='${value}' ${selected}>${value}</option>`)
                if(value == 2022){break}
            }
        }

    </script>
@endsection