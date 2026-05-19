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

                        <div class="text-center"><h3>School Feeding</h3></div>

                        <div class="d-flex justify-content-between mb-2 mt-2 align-items-center">
                            <div>
                                <span class="text-muted small">Schools receiving school feeding support</span>
                            </div>
                            <div class="col-md-3">
                                <select id="district-filter" class="form-select form-select-sm">
                                    <option value="">All Districts</option>
                                </select>
                            </div>
                        </div>

                        <table id="dt-feeding" data-csrf="{{ csrf_token() }}"
                               class="table table-bordered table-sm small-font align-middle" style="width:100%">
                            <thead>
                            <tr class="align-middle">
                                <th class="text-start">School</th>
                                <th class="text-start">District, Chiefdom</th>
                                <th class="text-center">Supply Period</th>
                                <th class="text-center">Received At</th>
                                <th class="text-center">Supplied By</th>
                                <th class="text-center">Rice (bags)</th>
                                <th class="text-center">Beans (bags)</th>
                                <th class="text-center">Gari (bags)</th>
                                <th class="text-center">Veg Oil (L)</th>
                                <th class="text-center">Salt (kg)</th>
                                <th class="text-center">Last Updated</th>
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
                            `<option value="${d.district_id}">${d.name}</option>`
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

    var feedingTable;

    function loadTable() {
        if (feedingTable) {
            feedingTable.destroy();
        }

        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('#dt-feeding').data("csrf") },
            url: "/api/school-feeding/table",
            data: { districtId: selectedDistrict },
            dataType: 'json',
            success: function (response) {
                if (response.status === true) {
                    feedingTable = $('#dt-feeding').DataTable({
                        data: response.data.feedingTable,
                        destroy: true,
                        columns: [
                            {
                                data: "school_name",
                                render: function (data, type, row) {
                                    return `<a href="/school/${row.school_uuid}">${data}</a>`;
                                }
                            },
                            {
                                data: "district",
                                render: function (data, type, row) {
                                    return (row.district ? row.district : '') +
                                           (row.chiefdom ? ', ' + row.chiefdom : '');
                                }
                            },
                            { data: "supply_period",  className: "text-center", defaultContent: "" },
                            { data: "received_at",    className: "text-center", defaultContent: "" },
                            { data: "supplied_by",    className: "text-center", defaultContent: "" },
                            {
                                data: "qty_rice",
                                className: "text-center",
                                defaultContent: '<span class="text-muted">—</span>',
                                render: function (data) { return data !== null ? data : '<span class="text-muted">—</span>'; }
                            },
                            {
                                data: "qty_beans",
                                className: "text-center",
                                defaultContent: '<span class="text-muted">—</span>',
                                render: function (data) { return data !== null ? data : '<span class="text-muted">—</span>'; }
                            },
                            {
                                data: "qty_gari",
                                className: "text-center",
                                defaultContent: '<span class="text-muted">—</span>',
                                render: function (data) { return data !== null ? data : '<span class="text-muted">—</span>'; }
                            },
                            {
                                data: "qty_veg_oil",
                                className: "text-center",
                                defaultContent: '<span class="text-muted">—</span>',
                                render: function (data) { return data !== null ? data : '<span class="text-muted">—</span>'; }
                            },
                            {
                                data: "qty_salt",
                                className: "text-center",
                                defaultContent: '<span class="text-muted">—</span>',
                                render: function (data) { return data !== null ? data : '<span class="text-muted">—</span>'; }
                            },
                            {
                                data: "updated_at",
                                className: "text-center",
                                render: function (data) {
                                    return data ? data.substring(0, 10) : '';
                                }
                            },
                        ],
                        order: [[0, "asc"]],
                        pageLength: 25,
                    });
                }
            }
        });
    }
</script>
@endsection
