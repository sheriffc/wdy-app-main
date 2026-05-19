@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
@endsection

@section('content')

<div class="container-fluid mt-2 px-3">

    {{-- Page header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Schools</h4>
            <small id="ay-label" class="text-muted">Loading…</small>
        </div>
        <div style="min-width:220px;">
            <select id="district-filter" class="form-select form-select-sm">
                <option value="">All Districts</option>
            </select>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row g-2 mb-3" id="summary-cards" style="display:none!important;">
        <div class="col-6 col-sm-3">
            <div class="card text-center py-2">
                <div class="fw-bold fs-4" id="sum-schools">—</div>
                <div class="text-muted small">Schools</div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card text-center py-2">
                <div class="fw-bold fs-4" id="sum-learners">—</div>
                <div class="text-muted small">Learners Enrolled</div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card text-center py-2">
                <div class="fw-bold fs-4" id="sum-teachers">—</div>
                <div class="text-muted small">Payroll / Non-Payroll Teachers</div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card text-center py-2">
                <div class="fw-bold fs-4" id="sum-classes">—</div>
                <div class="text-muted small">Classes</div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt-schools" data-csrf="{{ csrf_token() }}"
                               class="table table-bordered table-sm small-font align-middle" style="width:100%">
                            <thead>
                            <tr class="align-middle">
                                <th>School</th>
                                <th>EMIS ID</th>
                                <th>Education Level</th>
                                <th>District</th>
                                <th>Chiefdom</th>
                                <th>Head Teacher / Principal</th>
                                <th>WASH</th>
                                <th>Classroom Structure</th>
                                <th>Electricity</th>
                                <th>MNO</th>
                                <th>Learning Materials</th>
                                <th class="text-center">School Feeding</th>
                                <th class="text-center">Learners Enrolled</th>
                                <th class="text-center">Payroll Teachers</th>
                                <th class="text-center">Non-Payroll Teachers</th>
                                <th class="text-center">Classes</th>
                                <th class="text-center">Pupil : Payroll Teacher Ratio</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('script')
<script>
    var schoolsTable;
    var selectedDistrict = "";

    function loadTable() {
        if (schoolsTable) {
            schoolsTable.destroy();
            $("#dt-schools").empty();
        }

        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('#dt-schools').data("csrf") },
            url: "/api/schools/table",
            data: { districtId: selectedDistrict },
            dataType: 'json',
            success: function (response) {
                if (response.status !== true) return;
                var rows = response.data.rows;

                // Update header label
                if (rows.length > 0) {
                    $('#ay-label').text('Academic Year: ' + rows[0].academic_year_name);
                }

                // Summary totals
                var totalLearners = 0, totalPayroll = 0, totalNonPayroll = 0, totalClasses = 0;
                rows.forEach(function (r) {
                    totalLearners   += parseInt(r.learner_count)             || 0;
                    totalPayroll    += parseInt(r.payroll_teacher_count)     || 0;
                    totalNonPayroll += parseInt(r.non_payroll_teacher_count) || 0;
                    totalClasses    += parseInt(r.class_count)               || 0;
                });
                $('#sum-schools').text(rows.length.toLocaleString());
                $('#sum-learners').text(totalLearners.toLocaleString());
                $('#sum-teachers').text(totalPayroll.toLocaleString() + ' / ' + totalNonPayroll.toLocaleString());
                $('#sum-classes').text(totalClasses.toLocaleString());
                $('#summary-cards').css('display', '');

                schoolsTable = $('#dt-schools').DataTable({
                    data: rows,
                    destroy: true,
                    columns: [
                        {
                            data: "school_name",
                            width: "180px",
                            render: function (data, type, row) {
                                return '<a href="/school/' + row.school_uuid + '">' + data + '</a>';
                            }
                        },
                        { data: "emis_id",        defaultContent: '<span class="text-muted">—</span>' },
                        { data: "education_level", defaultContent: '<span class="text-muted">—</span>' },
                        { data: "district",        defaultContent: '<span class="text-muted">—</span>' },
                        { data: "chiefdom",        defaultContent: '<span class="text-muted">—</span>' },
                        { data: "head_teacher",    defaultContent: '<span class="text-muted">—</span>' },
                        { data: "wash",               defaultContent: '<span class="text-muted">—</span>' },
                        { data: "classrooms",         defaultContent: '<span class="text-muted">—</span>' },
                        { data: "electricity",         defaultContent: '<span class="text-muted">—</span>' },
                        { data: "mno",                 defaultContent: '<span class="text-muted">—</span>' },
                        { data: "learning_materials",  defaultContent: '<span class="text-muted">—</span>' },
                        {
                            data: "receives_feeding",
                            className: "text-center",
                            render: function (data) {
                                if (data == 1) return '<span class="badge bg-success">YES</span>';
                                if (data == 0) return '<span class="badge bg-secondary">NO</span>';
                                return '<span class="text-muted">—</span>';
                            }
                        },
                        { data: "learner_count",           className: "text-center" },
                        { data: "payroll_teacher_count",   className: "text-center" },
                        { data: "non_payroll_teacher_count", className: "text-center" },
                        { data: "class_count",             className: "text-center" },
                        {
                            data: null,
                            className: "text-center",
                            render: function (data, type, row) {
                                var pt = parseInt(row.payroll_teacher_count) || 0;
                                var l  = parseInt(row.learner_count) || 0;
                                if (pt === 0) return '<span class="text-muted">—</span>';
                                return (l / pt).toFixed(1) + ':1';
                            }
                        },
                    ],
                    order: [[3, "asc"], [0, "asc"]],
                    pageLength: 25,
                });
            }
        });
    }

    $(document).ready(function () {

        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: "/api/active-districts",
            dataType: 'json',
            success: function (response) {
                if (response.status === true) {
                    response.data.forEach(function (d) {
                        $("#district-filter").append(
                            '<option value="' + d.district_id + '">' + d.name + '</option>'
                        );
                    });
                }
            }
        });

        $("#district-filter").on("change", function () {
            selectedDistrict = this.value;
            loadTable();
        });

        loadTable();
    });
</script>
@endsection
