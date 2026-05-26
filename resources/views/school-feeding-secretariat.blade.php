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
    @include('assets.highcharts-js')
    @include('assets.highmaps-js')
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
    @include('_partials.district-geo-location')
<script>
    let slDistrictsGeoJson = {!! file_get_contents('json/sl_districts_simple.geojson') !!};

    var feedingMap;
    var secretariatTable;
    var feedingTable;
    var selectedDistrict = "";

    function updateMap(rows) {
        if (!feedingMap) return;
        var pointData = [];
        rows.forEach(function (row) {
            if (!row.lat || !row.lng) return;
            pointData.push({
                id: row.school_uuid,
                name: row.school_name,
                lat: parseFloat(row.lat),
                lon: parseFloat(row.lng),
                district: row.district,
                town: row.town,
                supplied_by: row.supplied_by,
                last_supply_date: row.last_supply_date ? row.last_supply_date.substring(0, 10) : null,
                stock_month: row.stock_month
            });
        });
        feedingMap.series[1].setData(pointData);
        document.getElementById("stat-count").textContent =
            pointData.length + " school" + (pointData.length !== 1 ? "s" : "") + " on map";
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

                updateMap(rows);

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
        loadSecretariatTable();
        loadFeedingTable();

        try { feedingMap = new Highcharts.mapChart('feeding-map', {
            chart: { animation: false },
            title: { text: null },
            mapNavigation: {
                enabled: true,
                enableDoubleClickZoomTo: true,
                enableMouseWheelZoom: true,
                enableTouchZoom: true
            },
            mapView: { maxZoom: 14 },
            legend: { enabled: false },
            tooltip: {
                pointFormatter: function () {
                    return '<strong>' + this.name + '</strong><br>' +
                        (this.district ? this.district + '<br>' : '') +
                        (this.town ? this.town + '<br>' : '') +
                        '————<br>' +
                        (this.supplied_by     ? 'Supplied by: ' + this.supplied_by + '<br>' : '') +
                        (this.last_supply_date ? 'Last supply: ' + this.last_supply_date + '<br>' : '') +
                        (this.stock_month     ? 'Stock month: ' + this.stock_month : '');
                }
            },
            series: [
                {
                    mapData: slDistrictsGeoJson,
                    name: 'Districts',
                    type: 'map',
                    showInLegend: false,
                    dataLabels: false
                },
                {
                    name: 'School',
                    type: 'mappoint',
                    data: [],
                    color: '#28a745',
                    marker: { fillColor: '#28a745', lineColor: '#fff', lineWidth: 2, radius: 5 },
                    dataLabels: { enabled: false },
                    animation: false,
                    turboThreshold: 0,
                    showInLegend: false,
                    point: {
                        events: {
                            click: function () {
                                if (this.id) { window.open('/school/' + this.id); }
                            }
                        }
                    }
                }
            ],
            credits: { enabled: false }
        }); } catch(e) { console.error('Map init error:', e); }

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

            if (feedingMap && slDistrictsGeoJson) {
                if (!selectedDistrict) {
                    feedingMap.series[0].update({ mapData: slDistrictsGeoJson }, true);
                    feedingMap.mapView.setView([-11.5935, 8.6190], 0, true, false);
                } else {
                    var districtId = parseInt(selectedDistrict);
                    var viewConfig = districtGeoLocation[districtId];
                    var filteredGeoJson = {
                        type: 'FeatureCollection',
                        features: slDistrictsGeoJson.features.filter(function (f) {
                            return f.properties.CS_Dis_Num == viewConfig.geoJsonId;
                        })
                    };
                    feedingMap.series[0].update({ mapData: filteredGeoJson }, true);
                    feedingMap.mapView.setView([viewConfig.lon, viewConfig.lat], viewConfig.zoom, true, false);
                }
            }

            loadSecretariatTable();
            loadFeedingTable();
        });
    });
</script>
@endsection
