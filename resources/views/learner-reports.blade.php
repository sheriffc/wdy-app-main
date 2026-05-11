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
                <div class="col-md-6">

                </div>
                <div class="col-md-6 ">
                
                    <div class=" mt-1">
                        <select id="district-filter" class="form-select form-select-sm"
                                aria-label=".form-select-sm example">
                            <option value="">All Districts</option>
                        </select>
                    </div>
             
                </div>
            </div>
            
        </div>

        <div class="col-md-12 mt-4">
            <h5 class="card-title">Learners with Duplicate Active Enrolment</h5>
            <p>
                These learners have active admissions at more than one school in the same academic year.
                Each school they appear in is listed as a separate row. This likely indicates a data entry error
                or an incomplete transfer — the old admission should be closed before enrolling elsewhere.
            </p>
            <div class="card">
                <div class="card-body">
                    <table id="dt-duplicate-enrollment" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Learner UID</th>
                                <th class="text-start">Name</th>
                                <th class="text-start">School</th>
                                <th class="text-center">Academic Year</th>
                                <th class="text-start">Year Group</th>
                                <th class="text-start">Classroom</th>
                                <th class="text-center">Admitted</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <h5 class="card-title">Learners With Reported Disabilities</h5>
            <p>
                Below are the children currently assessed to have the most severe needs, as reported by the school leader. 
                To find guidance on the appropriate referral please contact the National Commission for People with Disability.
            </p>
            <div class="card">
                <div class="card-body">
                    <table id="dt-disability-learners" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">School</th>
                                <th class="text-start">Vision</th>
                                <th class="text-start">Hearing</th>
                                <th class="text-start">Mobility</th>
                                <th class="text-start">Cognition</th>
                                <th class="text-start">Self Care</th>
                                <th class="text-start">Communication</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <h5 class="card-title">Unassigned Learners</h5>
            <p>
                These learners have no classroom assigned in the current academic year and are not considered enroled in school. They have had profiles created by the school leaders and no attendance reports are being submitted for them.
            </p>
            <div class="card">
                <div class="card-body">
                    <table id="dt-unassigned-learners" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">School</th>
                                <th class="text-start">Last Assigned Academic year</th>
                                <th class="text-start">Previous Class</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <h5 class="card-title">Persistently Absent Learners</h5>
            <p>These learners are defined to be absent between 10% to 49% of the time.</p>
            <div class="card">
                <div class="card-body">
                    <table id="dt-persistent-absent-learners" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">School</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <h5 class="card-title">Severely Absent Learners</h5>
            <p>These learners are defined to be absent at least 50% of the time</p>
            <div class="card">
                <div class="card-body">
                    <table id="dt-severe-absent-learners" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr class="align-middle">
                                <th class="text-start">Name</th>
                                <th class="text-start">School</th>
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
        let dtDuplicateEnrollmentTableData = [];
        let dtDisabilityLearnersTableData = [];
        let dtUnassignedLearnersTableData = [];
        let dtPersistentAbsentLearnersTableData = [];
        let dtSeverAbsentLearnersTableData = [];

        let isDistrictOfficerOrAbove ="{{ $isDistrictOfficerOrAbove }}";

        $(async function () {
            await setDistrictFilter();
            await requestDuplicateEnrollmentTableData();
            await requestDisabilityLearnerTableData();
            await requestUnassignedLearnerTableData();
            await requestAtRiskLearnerTableData();
        })

        $("#district-filter").bind("change", async function () {
            selectedDistrict = this.value;

            await requestDuplicateEnrollmentTableData();
            await requestDisabilityLearnerTableData();
            await requestUnassignedLearnerTableData();
            await requestAtRiskLearnerTableData();
        })

        async function requestDuplicateEnrollmentTableData(){
            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learner-reports/duplicate-enrollment-table",
                type: "POST",
                dataType: "json",
                data: {
                    districtId: selectedDistrict
                },
                success: function (response) {
                    dtDuplicateEnrollmentTableData = response.data.duplicateEnrollmentTable;
                    tableDuplicateEnrollment.clear().draw();
                    tableDuplicateEnrollment.rows.add(dtDuplicateEnrollmentTableData);
                    tableDuplicateEnrollment.columns.adjust().draw();
                },
                cache: false
            });
        }

        async function requestAtRiskLearnerTableData(){
            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learner-reports/at-risk-table",
                type: "POST",
                dataType: "json",
                data: { 
                    districtId: selectedDistrict
                },
                success: function (response) {
                    
                },
                cache: false
            });
        }

        async function requestDisabilityLearnerTableData(){
            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learner-reports/disability-table",
                type: "POST",
                dataType: "json",
                data: { 
                    districtId: selectedDistrict
                },
                success: function (response) {
                    dtDisabilityLearnersTableData = response.data.disabilityLearnersTable;

                    tableDisabilityLearners.clear().draw();
                    tableDisabilityLearners.rows.add(dtDisabilityLearnersTableData);
                    tableDisabilityLearners.columns.adjust().draw();
                },
                cache: false
            });
        }

        async function requestUnassignedLearnerTableData(){
            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learner-reports/unassigned-table",
                type: "POST",
                dataType: "json",
                data: { 
                    districtId: selectedDistrict
                },
                success: function (response) {
                    dtUnassignedLearnersTableData = response.data.unassignedLearnersTable;

                    tableUnassignedLearners.clear().draw();
                    tableUnassignedLearners.rows.add(dtUnassignedLearnersTableData);
                    tableUnassignedLearners.columns.adjust().draw();
                },
                cache: false
            });
        }

        async function requestAtRiskLearnerTableData(){
            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learner-reports/at-risk-learner-data",
                type: "POST",
                dataType: "json",
                data: { 
                    districtId: selectedDistrict
                },
                success: function (response) {
                    let tableData = response.data.atRiskLearnersTable;
                    dtPersistentAbsentLearnersTableData = tableData.persistentAbsentTable;
                    dtSeverAbsentLearnersTableData = tableData.severlyAbsentTable;

                    tablePersistentAbsentLearners.clear().draw();
                    tablePersistentAbsentLearners.rows.add(dtPersistentAbsentLearnersTableData);
                    tablePersistentAbsentLearners.columns.adjust().draw();

                    tableSevereAbsentLearners.clear().draw();
                    tableSevereAbsentLearners.rows.add(dtSeverAbsentLearnersTableData);
                    tableSevereAbsentLearners.columns.adjust().draw();
                },
                cache: false
            });
        }

        let tableDuplicateEnrollment = $('#dt-duplicate-enrollment').DataTable({
            data: dtDuplicateEnrollmentTableData,
            scrollX: true,
            order: [[0, 'asc']],
            rowCallback: function(row, data) {
                $(row).addClass('table-warning');
            },
            columns: [
                {
                    data: 'learner_id',
                    className: 'text-start fw-bold',
                    defaultContent: '—'
                },
                {
                    data: 'learner_name',
                    className: 'text-start',
                    render: function(data, type, row) {
                        if (isDistrictOfficerOrAbove) {
                            return data || '—';
                        }
                        return '<span class="text-muted fst-italic">Restricted</span>';
                    }
                },
                {
                    data: 'school_name',
                    render: function(data, type, row) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    }
                },
                {
                    data: 'academic_year',
                    className: 'text-center',
                    defaultContent: '—'
                },
                {
                    data: 'year_group',
                    className: 'text-start',
                    defaultContent: '—'
                },
                {
                    data: 'classroom',
                    className: 'text-start',
                    defaultContent: '—'
                },
                {
                    data: 'start_date',
                    className: 'text-center',
                    defaultContent: '—'
                },
            ],
        });

        let tableDisabilityLearners = $('#dt-disability-learners').DataTable({
            data : dtDisabilityLearnersTableData,
            scrollX: true,
            columns:[
                {
                    data: "learner_name",
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
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                },
                {
                    data: "vision",
                    className: 'text-center',
                },
                {
                    data: "hearing",
                    className: 'text-center',
                },
                {
                    data: "mobility",
                    className: 'text-center',
                },
                {
                    data: "cognition",
                    className: 'text-center',
                },
                {
                    data: "selfcare",
                    className: 'text-center',
                },
                {
                    data: "communication",
                    className: 'text-center',
                },
            ],
            order:[]
        });

        let tableUnassignedLearners = $('#dt-unassigned-learners').DataTable({
            data : dtUnassignedLearnersTableData,
            scrollX: true,
            columns:[
                {
                    data: "learner_name",
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
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                },
                {
                    data: "academic_year",
                    className: 'text-center',
                },
                {
                    data: "prev_class",
                    className: 'text-start',
                    render: function ( data, type, row, meta ) {
                        if (!data) return '';
                        let year = row.prev_academic_year ? ` (${row.prev_academic_year})` : '';
                        return data + year;
                    },
                }
            ],
            order:[]
        });

        let tablePersistentAbsentLearners = $('#dt-persistent-absent-learners').DataTable({
            data : dtPersistentAbsentLearnersTableData,
            scrollX: true,
            columns:[
                {
                    data: "learner_name",
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
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                },
            ],
            order:[]
        });

        let tableSevereAbsentLearners = $('#dt-severe-absent-learners').DataTable({
            data : dtSeverAbsentLearnersTableData,
            scrollX: true,
            columns:[
                {
                    data: "learner_name",
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
                    data: 'school_name',
                    render: function ( data, type, row, meta ) {
                        return `<a href="/school/${row.school_uuid}">${row.school_name}</a>`;
                    },
                },
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
    </script>
@endsection