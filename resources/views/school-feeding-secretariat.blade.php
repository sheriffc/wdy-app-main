@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
    <style>
        #feeding-map { height: 480px; width: 100%; border-radius: 6px; }
        .legend-dot { display: inline-block; width: 14px; height: 14px; border-radius: 50%; margin-right: 6px; vertical-align: middle; }
        .map-card { position: relative; }
        .map-stats {
            position: absolute; top: 60px; right: 24px; z-index: 5;
            background: rgba(255,255,255,0.95); border-radius: 6px;
            padding: 10px 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.18);
            font-size: 0.85rem; min-width: 160px;
        }
    </style>
@endsection

@section('custom_js')
    @include('assets.datatables-js')
@endsection

@section('content')

<div class="container-fluid mt-2 px-3">

    {{-- Page header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">School Feeding Secretariat</h4>
        <div style="min-width:220px;">
            <select id="district-filter" class="form-select form-select-sm">
                <option value="">All Districts</option>
            </select>
        </div>
    </div>

    {{-- Map --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card map-card">
                <div class="card-body p-2">
                    <div id="feeding-map"></div>
                    <div class="map-stats" id="map-stats">
                        <div class="fw-semibold mb-1">Map Legend</div>
                        <div><span class="legend-dot" style="background:#28a745;"></span> Receiving support</div>
                        <hr class="my-1">
                        <div id="stat-count" class="text-muted">Loading…</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schools overview table --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-2">School Feeding Stock Available</h6>
                    <div class="table-responsive">
                        <table id="dt-secretariat" data-csrf="{{ csrf_token() }}"
                               class="table table-bordered table-sm small-font align-middle" style="width:100%">
                            <thead>
                            <tr class="align-middle">
                                <th>School</th>
                                <th>Town</th>
                                <th>District</th>
                                <th>Last Supply Date</th>
                                <th class="text-center">Rice (kg)</th>
                                <th class="text-center">Beans (kg)</th>
                                <th class="text-center">Gari (kg)</th>
                                <th class="text-center">Veg Oil (L)</th>
                                <th class="text-center">Salt (kg)</th>
                                <th class="text-center">Stock Month</th>
                                <th class="text-center">Stock Last Updated</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Supply submissions table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-2">Supply Submissions</h6>
                    <div class="table-responsive">
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

</div>

@endsection

@section('script')
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyBOWcZ_90o5xYRVyfU7cnLIoH-fF72re4E"></script>
<script>
    var secretariatTable;
    var feedingTable;
    var gmap;
    var markers = [];
    var infoWindow;
    var selectedDistrict = "";

    // ── Map init ──────────────────────────────────────────────────────────────
    function initMap() {
        gmap = new google.maps.Map(document.getElementById("feeding-map"), {
            zoom: 8,
            center: { lat: 8.4606, lng: -11.7799 },
            mapTypeId: "roadmap"
        });
        infoWindow = new google.maps.InfoWindow();
    }

    function clearMarkers() {
        markers.forEach(function (m) { m.setMap(null); });
        markers = [];
    }

    function addMarkers(rows) {
        clearMarkers();
        var validCount = 0;
        rows.forEach(function (row) {
            if (!row.lat || !row.lng) return;
            validCount++;
            var marker = new google.maps.Marker({
                position: { lat: parseFloat(row.lat), lng: parseFloat(row.lng) },
                map: gmap,
                title: row.school_name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 9,
                    fillColor: "#28a745",
                    fillOpacity: 0.9,
                    strokeColor: "#fff",
                    strokeWeight: 2
                }
            });

            marker.addListener("click", function () {
                var content =
                    '<div style="max-width:220px;">' +
                    '<strong>' + row.school_name + '</strong><br>' +
                    (row.district ? '<span class="text-muted">' + row.district + '</span><br>' : '') +
                    (row.town     ? row.town + '<br>' : '') +
                    (row.supplied_by    ? '<small>Supplied by: ' + row.supplied_by + '</small><br>' : '') +
                    (row.last_supply_date ? '<small>Last supply: ' + row.last_supply_date + '</small><br>' : '') +
                    (row.stock_month    ? '<small>Stock month: ' + row.stock_month + '</small>' : '') +
                    '</div>';
                infoWindow.setContent(content);
                infoWindow.open(gmap, marker);
            });

            markers.push(marker);
        });
        document.getElementById("stat-count").textContent =
            validCount + " school" + (validCount !== 1 ? "s" : "") + " on map";
    }

    // ── Secretariat overview table ────────────────────────────────────────────
    function loadSecretariatTable() {
        if (secretariatTable) {
            secretariatTable.destroy();
            $("#dt-secretariat").empty();
        }

        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('#dt-secretariat').data("csrf") },
            url: "/api/school-feeding-secretariat/table",
            data: { districtId: selectedDistrict },
            dataType: 'json',
            success: function (response) {
                if (response.status !== true) return;
                var rows = response.data.rows;

                addMarkers(rows);

                secretariatTable = $('#dt-secretariat').DataTable({
                    data: rows,
                    destroy: true,
                    columns: [
                        {
                            data: "school_name",
                            render: function (data, type, row) {
                                return '<a href="/school/' + row.school_uuid + '">' + data + '</a>';
                            }
                        },
                        { data: "town",     defaultContent: '<span class="text-muted">—</span>' },
                        { data: "district", defaultContent: '<span class="text-muted">—</span>' },
                        {
                            data: "last_supply_date",
                            className: "text-center",
                            defaultContent: '<span class="text-muted">—</span>',
                            render: function (data) {
                                return data ? data.substring(0, 10) : '<span class="text-muted">—</span>';
                            }
                        },
                        { data: "stock_rice",    className: "text-center", defaultContent: '<span class="text-muted">—</span>' },
                        { data: "stock_beans",   className: "text-center", defaultContent: '<span class="text-muted">—</span>' },
                        { data: "stock_gari",    className: "text-center", defaultContent: '<span class="text-muted">—</span>' },
                        { data: "stock_veg_oil", className: "text-center", defaultContent: '<span class="text-muted">—</span>' },
                        { data: "stock_salt",    className: "text-center", defaultContent: '<span class="text-muted">—</span>' },
                        { data: "stock_month",   className: "text-center", defaultContent: '<span class="text-muted">—</span>' },
                        {
                            data: "stock_updated_at",
                            className: "text-center",
                            defaultContent: '<span class="text-muted">—</span>',
                            render: function (data) {
                                return data ? data.substring(0, 10) : '<span class="text-muted">—</span>';
                            }
                        },
                    ],
                    order: [[2, "asc"], [0, "asc"]],
                    pageLength: 25,
                });
            }
        });
    }

    // ── Supply submissions table ──────────────────────────────────────────────
    function loadFeedingTable() {
        if (feedingTable) {
            feedingTable.destroy();
            $("#dt-feeding").empty();
        }

        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('#dt-feeding').data("csrf") },
            url: "/api/school-feeding/table",
            data: { districtId: selectedDistrict },
            dataType: 'json',
            success: function (response) {
                if (response.status !== true) return;

                feedingTable = $('#dt-feeding').DataTable({
                    data: response.data.feedingTable,
                    destroy: true,
                    columns: [
                        {
                            data: "school_name",
                            render: function (data, type, row) {
                                return '<a href="/school/' + row.school_uuid + '">' + data + '</a>';
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
                            render: function (data) { return data ? data.substring(0, 10) : ''; }
                        },
                    ],
                    order: [[0, "asc"]],
                    pageLength: 25,
                });
            }
        });
    }

    // ── Boot ─────────────────────────────────────────────────────────────────
    $(document).ready(function () {
        initMap();

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
            loadSecretariatTable();
            loadFeedingTable();
        });

        loadSecretariatTable();
        loadFeedingTable();
    });
</script>
@endsection
