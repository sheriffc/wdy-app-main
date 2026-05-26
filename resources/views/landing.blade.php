@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
    @include('assets.highcharts-js')
    @include('assets.highmaps-js')
@endsection

@section('content')

    <div class="container mt-2">
        <div class="row">
            <div class="col-md-6">
                <h4>Reporting for Date: <span id="attendance-date-label"></span></h4>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-3 mt-1">
                        <select id="academic-year-filter" class="form-select form-select-sm">
                            <option value="">Academic Year</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <select id="month-filter" class="form-select form-select-sm" disabled>
                            <option value="">All Months</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <select id="district-filter" class="form-select form-select-sm">
                            <option value="">All Districts</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-1">
                        <select id="attendance-dates" class="form-select form-select-sm">
                            <option value="">Attendance Date</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <div class="row mt-3">
            <div class="col-md-3 mt-1">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Schools Reported</h5>
                        <h6 id="schools-participating"
                            class="card-subtitle mb-2 text-muted">Loading...</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-fill px-1 py-2">
                                <p id="reported-schools">Loading...</p>
                                <hr/>
                                <p id="not-reported-schools">Loading...</p>
                            </div>
                            <div class="px-3">
                            <h1 id="reported-schools-percentage"></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-1">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Teachers Reported</h5>
                        <h6 id="registered-teachers"
                            class="card-subtitle mb-2 text-muted">Loading...</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-fill px-1 py-2">
                                <p id="reported-teachers">Loading...</p>
                                <hr/>
                                <p id="not-reported-teachers">Loading...</p>
                            </div>
                            <div class="px-3">
                            <h1 id="reported-teachers-percentage"></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-1">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Learners Reported</h5>
                        <h6 id="registered-learners"
                            class="card-subtitle mb-2 text-muted">Loading...</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-fill px-1 py-2">
                                <p id="reported-learners">Loading...</p>
                                <hr/>
                                <p id="not-reported-learners">Loading...</p>
                            </div>
                            <div class="px-3">
                            <h1 id="reported-learners-percentage"></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mt-1">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Teacher Attendance</h5>
                        <h6 id="attendance-reported-teachers"
                            class="card-subtitle mb-2 text-muted">Loading...</h6>
                        <div class="d-flex align-items-center">
                            <div class="flex-fill px-1 py-2">
                                <p id="teachers-present">Loading...</p>
                                <hr/>
                                <p id="teachers-absent">Loading...</p>
                            </div>
                            <div class="px-3">
                            <h1 id="attendance-teachers-percentage"></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!--attendance map-->
            <div class="col-md-6 mt-4">
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
            <!--attendance chart-->
            <div class="col-md-6 mt-4">
                <div class="card">
                    <div class="card-body">
                        <div id="teacher-attendance-barchart"></div>
                    </div>
                </div>
            </div>
    </div>

    <div class="mt-4">
        <h3>Learner Vulnerabilities Analysis</h3>
        <ul class="nav nav-tabs flex-wrap" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
            <button class="nav-link active" id="gender-analysis-tab" data-bs-toggle="tab" data-bs-target="#gender-analysis" type="button" role="tab" aria-controls="gender-analysis" aria-selected="true"><h4>Gender</h4></button>
            </li>
            <li class="nav-item" role="presentation">
            <button class="nav-link" id="special-needs-analysis-tab" data-bs-toggle="tab" data-bs-target="#special-needs-analysis" type="button" role="tab" aria-controls="special-needs-analysis" aria-selected="false"><h4>Special Needs</h4></button>
            </li>
            <li class="nav-item" role="presentation">
            <button class="nav-link" id="at-risk-analysis-tab" data-bs-toggle="tab" data-bs-target="#at-risk-analysis" type="button" role="tab" aria-controls="at-risk-analysis" aria-selected="false"><h4>At Risk</h4></button>
            </li>
            <li class="nav-item" role="presentation">
            <button class="nav-link" id="performance-analysis-tab" data-bs-toggle="tab" data-bs-target="#performance-analysis" type="button" role="tab" aria-controls="performance-analysis" aria-selected="false"><h4>Learner Performance</h4></button>
            </li>
        </ul>

        <div class="tab-content mt-2" id="myTabContent">
            <div class="tab-pane fade show  active" id="gender-analysis" role="tabpanel" aria-labelledby="gender-analysis-tab">
                <div class="row mt-3 ">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-center">Highlights</h5>
                                <p><span id="enrolled-girls-percentage" class="fw-bold h4">Loading..</span> of enrolled learners are <span style="color: #E79595">girls</span></p>
                                <p><span id="girls-maternal-status-count" class="fw-bold h4">Loading..</span> enrolled girls have been identified as <span style="color: #8AC1D8">pregnant</span> and/or a <span style="color: #76AC8B">mother</span></p>
                                <p>Girls are reported as absent <span id="girls-not-school-percentage" class="fw-bold h4">Loading..</span> of the time</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Gender Ratio</h5>
                                <div id="gender-ratio-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div id="class-enrolment-chart"></div>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-body">
                                <div id="absenteeism-rate-barchart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Learners with Maternal Status</h5>
                                <div id="maternal-status-chart"></div>
                                <div id="maternal-status-map-container">
                                    <p class="text-center pt-2 h6">Where are mothers and pregnant girls learners located?</p>
                                    <div class="d-flex">
                                        <div class="card-body col-12">
                                            <div id="maternal-status-map"></div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex h-50">
                                                <div>
                                                    <h6 class="fw-bold">Learners with maternal status</h4>
                                                    <div class="d-flex h-50">
                                                        <canvas class="col-2" id="maternal-status-map-learner-legend"></canvas>
                                                        <div class="d-flex justify-content-between flex-column">
                                                            <div><span id="maternal-status-map-learner-legend-max-count">20</span> Learners</div>
                                                            <span>0 Learners</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="special-needs-analysis" role="tabpanel" aria-labelledby="special-needs-analysis-tab">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">How are special needs defined?</h5>
                                <p>Based on the <a href="https://www.washingtongroup-disability.com/">Washington Group Short Set</a></p>

                                <h6 class="text-success fw-bold">Vision</h6>
                                <p>
                                    The learner struggles to read their exercise books, or the board unless they sit close to it.
                                </p>

                                <h6 class="text-success fw-bold">Communication</h6>
                                <p>
                                    The learner struggles to read their exercise books, or the board unless they sit close to it.
                                    Using their usual language, others often struggle to understand what the learner is telling them,
                                    and/or the learner struggles to understand them.
                                </p>

                                <h6 class="text-success fw-bold">Hearing</h6>
                                <p>
                                    The learner does not respond to others or others have to use a louder voice or go closer to them so they can hear.
                                </p>

                                <h6 class="text-success fw-bold">Mobility</h6>
                                <p>
                                    The learner struggles to move around the school.
                                </p>

                                <h6 class="text-success fw-bold">Cognition</h6>
                                <p>
                                    The learner needs more time for simple tasks,
                                    does not remember tasks and/or what they have learnt, and/or cannot concentrate in class.
                                </p>

                                <h6 class="text-success fw-bold">Self Care</h6>
                                <p>
                                    The learner struggles in daily self-care, such as using the bathroom and feeding themselves.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">What are Other Common Conditions?</h5>
                                <h6 class="text-danger fw-bold">Epilepsy</h6>
                                <p>
                                    A neurological disorder marked by sudden recurrent episodes of sensory disturbance,
                                    loss of consciousness, or convulsions, associated with abnormal electrical activity in the brain.
                                </p>

                                <h6 class="text-danger fw-bold">Dwarfism</h6>
                                <p>
                                    The condition of being of unusually short stature or small size,
                                    especially on account of a genetic or medical condition such as achondroplasia.
                                </p>

                                <h6 class="text-danger fw-bold">Albinism</h6>
                                <p>
                                    A congenital absence of pigment in the skin and hair (which are white) and the eyes (which are usually pink).
                                </p>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">Highlights</h5>
                                <p><span id="screened-learners-percentage" class="fw-bold h4">Loading...</span> of learners in WDY have been assessed for special needs.</p>
                                <p><span id="learner-difficulty-count" class="fw-bold h4">Loading...</span> learners have <span class="fw-bold">at least some difficulty</span> in one of the six special needs categories.</p>
                                <p><span id="common-conditions-count" class="fw-bold h4">Loading...</span> learners have <span class="fw-bold"> epilepsy, albinism, and/or dwarfism.</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div id="disability-severity-barchart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div id="common-conditions-barchart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <h5 class="text-center pt-3">Where are the learners with special needs located?</h5>
                            <ul class="nav nav-tabs" id="special-needs-located-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="special-needs-located-barchart-tab" data-bs-toggle="tab" data-bs-target="#special-needs-located-barchart-section" type="button" role="tab" aria-controls="special-needs-located-barchart-section" aria-selected="true">Bar</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                <button class="nav-link" id="special-needs-located-map-tab" data-bs-toggle="tab" data-bs-target="#special-needs-located-map-section" type="button" role="tab" aria-controls="special-needs-located-map-section" aria-selected="false">Map</button>
                                </li>
                            </ul>
                            <div class="card-body">
                                <div class="tab-content mt-2" id="special-needs-located-content">
                                    <div class="tab-pane fade show  active" id="special-needs-located-barchart-section" role="tabpanel" aria-labelledby="special-needs-located-barchart-tab">
                                        <div id="special-needs-located-barchart"></div>
                                    </div>
                                    <div class="tab-pane fade" id="special-needs-located-map-section" role="tabpanel" aria-labelledby="special-needs-located-map-tab">
                                        <div class="d-flex">
                                            <div class="col-8">
                                                <div id="special-needs-located-map"></div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex h-50">
                                                    <div>
                                                        <h6 class="fw-bold">Learners with special needs</h4>
                                                        <div class="d-flex h-50">
                                                            <canvas class="col-2" id="special-needs-located-map-legend"></canvas>
                                                            <div class="d-flex justify-content-between flex-column">
                                                                <div><span id="special-needs-located-map-legend-max-count">20</span> Learners</div>
                                                                <span>0 Learners</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <h5 class="text-center pt-3">How do special needs affect attendance?</h5>
                            <ul class="nav nav-tabs" id="special-needs-absenteeism-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="special-needs-absenteeism-barchart-tab" data-bs-toggle="tab" data-bs-target="#special-needs-absenteeism-barchart-section" type="button" role="tab" aria-controls="special-needs-absenteeism-barchart-section" aria-selected="true">Bar</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                <button class="nav-link" id="special-needs-absenteeism-map-tab" data-bs-toggle="tab" data-bs-target="#special-needs-absenteeism-map-section" type="button" role="tab" aria-controls="special-needs-absenteeism-map-section" aria-selected="false">Map</button>
                                </li>
                            </ul>


                            <div class="card-body">
                                <div class="tab-content mt-2" id="special-needs-located-content">
                                    <div class="tab-pane fade show  active" id="special-needs-absenteeism-barchart-section" role="tabpanel" aria-labelledby="special-needs-absenteeism-barchart-tab">
                                        <div id="special-needs-absenteeism-barchart"></div>
                                    </div>
                                    <div class="tab-pane fade" id="special-needs-absenteeism-map-section" role="tabpanel" aria-labelledby="special-needs-absenteeism-map-tab">
                                        <div class="d-flex">
                                            <div class="col-8">
                                                <div id="special-needs-absenteeism-map"></div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex h-50">
                                                    <div>
                                                        <h6 class="fw-bold">Learner Absenteeism Rate</h4>
                                                        <div class="d-flex h-50">
                                                            <canvas class="col-2" id="special-needs-absenteeism-map-legend"></canvas>
                                                            <div class="d-flex justify-content-between flex-column">
                                                                <div><span id="special-needs-absenteeism-map-legend-max-count">100</span>%</div>
                                                                <span>0%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="tab-pane fade" id="at-risk-analysis" role="tabpanel" aria-labelledby="at-risk-analysis-tab">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <table>
                                    <thead>
                                        <th>Learner Category</th>
                                        <th>Count of Learners</th>
                                        <th>% of School Population</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h5 class="pt-3" style="color: #8AC1D8">Persistently Absent</h5>
                                                <p>These learners are defined to be absent between 10% to 49% of the time.</p>
                                            </td>
                                            <td>
                                                <h3 class="text-center" id="persistentAbsentCount">Loading...</h3>
                                            </td>
                                            <td>
                                                <h3 class="text-center" id="persistentAbsentPercentage">Loading...</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><h5 style="color: #E79595">Out of School Children</h5></td>
                                        </tr>
                                        <tr class="">
                                            <td class="d-flex p-2">
                                                <div class="border-start border border-3 border-danger" ></div><p>  </p>
                                                <div class="p-1">
                                                    <h6 style="color: #E79595">Severely Absent</h6>
                                                    <p>These learners are defined to be absent at least 50% of the time</p>
                                                </div>    
                                            </td>
                                            <td><h3 class="text-center" id="severlyAbsentCount">Loading...</h3></td>
                                            <td><h3 class="text-center" id="severlyAbsentPercentage">Loading...</h3></td>
                                        </tr>
                                        <tr>
                                            <td class="d-flex p-2">
                                                <div class="border-start border border-3" ></div>
                                                <div class="p-1">
                                                    <h6>Not Enroled</h6>
                                                    <p>These children are of school going age but have not been enroled in school</p>
                                                </div>
                                            </td>
                                            <td><h3 class="text-center">N/A</h3></td>
                                            <td><h3 class="text-center">N/A</h3></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Who has a higher rate of absenteeism?</h5>
                                <div class="col-md-4 mt-3">
                                    <select class="form-control" id="higher-absenteeism-rate-select" id="">
                                        <option value="gender">Gender</option>
                                        <option value="age">Age</option>
                                        <option value="district">District</option>
                                        <option value="severity">Needs Severity</option>
                                    </select>
                                </div>
                                <div class="mt-3" id="higher-absenteeism-rate-barchart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Why are learners removed from school?</h5>
                                <div id="reasons-learners-removed-barchart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Absenteeism Distribution</h5>
                                <div id="absenteeism-distribution-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div id="at-risk-learners-map-container" class="card">
                            <div class="card-body">
                                <div id="at-risk-learners-map"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-center">Trends over Time</h5>
                                <div id="trends-over-time-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Learner Performance Tab ─────────────────────────────────────── --}}
            <div class="tab-pane fade" id="performance-analysis" role="tabpanel" aria-labelledby="performance-analysis-tab">

                {{-- Filters --}}
                <div class="d-flex gap-2 mt-2 mb-3 flex-wrap">
                    <div>
                        <label class="form-label small mb-1">Term</label>
                        <select id="perf-term-filter" class="form-select form-select-sm" style="min-width:150px;">
                            <option value="">All Terms</option>
                            <option value="first_term">First Term</option>
                            <option value="second_term">Second Term</option>
                            <option value="third_term">Third Term</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small mb-1">School Level</label>
                        <select id="perf-level-filter" class="form-select form-select-sm" style="min-width:150px;">
                            <option value="">All Levels</option>
                            <option value="Primary">Primary</option>
                            <option value="JSS">JSS</option>
                            <option value="SSS">SSS</option>
                        </select>
                    </div>
                </div>

                {{-- Summary table --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Poor Performance Summary <small class="text-muted">(avg score &lt; 50%)</small></h5>
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>School Level</th>
                                            <th class="text-center">Total Assessed</th>
                                            <th class="text-center">Poor Performance</th>
                                            <th class="text-center">% Poor</th>
                                        </tr>
                                    </thead>
                                    <tbody id="perf-summary-body">
                                        <tr><td colspan="4" class="text-center text-muted">Loading…</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-center">Performance Trends by Term</h5>
                                <div id="perf-trends-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subject comparison --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-center">Subject Performance Comparison</h5>
                                <p class="text-muted small text-center mb-2">
                                    <span style="color:#E74C3C;">&#9632;</span> Poor (&lt;50%)&ensp;
                                    <span style="color:#F39C12;">&#9632;</span> Good (50–69%)&ensp;
                                    <span style="color:#27AE60;">&#9632;</span> Very Good (≥70%)
                                </p>
                                <div id="perf-subject-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    @include('_partials.highchart-config')
    @include('_partials.district-geo-location')
    <script>
        let slDistrictsGeoJson = {!! file_get_contents('json/sl_districts.geojson') !!};

        var selectedDistrict = null;
        var selectedDate = null;
        var startDate = null;
        var endDate = null;
        var selectedAcademicYear = null;
        var academicYearsData = [];
        var isDistrictOfficerOrAbove ="{{ $isDistrictOfficerOrAbove }}";
        let maternalStatusMapPointData = [];
        let noMaternalStatusMapPointData = [];
        let genderAbsenteeismRatesData = [];
        let severityAbsenteeismRatesData = [];
        let districtAbsenteeismRatesData = [];
        let ageAbsenteeismRatesData = [];

        // ── Academic year filter ─────────────────────────────────────────────────
        function populateMonthFilter(dateFrom, dateTo) {
            const sel = $("#month-filter");
            sel.empty().append('<option value="">All Months</option>');
            if (!dateFrom || !dateTo) { sel.prop('disabled', true); return; }

            const start = new Date(dateFrom + 'T00:00:00');
            const end   = new Date(dateTo   + 'T00:00:00');
            const today = new Date();
            const cursor = new Date(start.getFullYear(), start.getMonth(), 1);

            const monthNames = ["January","February","March","April","May","June",
                                "July","August","September","October","November","December"];

            while (cursor <= end && cursor <= today) {
                const y = cursor.getFullYear();
                const m = cursor.getMonth();
                const val = `${y}-${String(m+1).padStart(2,'0')}`;
                sel.append(`<option value="${val}">${monthNames[m]} ${y}</option>`);
                cursor.setMonth(cursor.getMonth() + 1);
            }
            sel.prop('disabled', false);
        }

        function setDateRangeFromMonth(yearMonth) {
            if (!yearMonth) {
                // No month selected — reset to default 7-day window
                startDate = null;
                endDate   = null;
                return;
            }
            const [y, m] = yearMonth.split('-').map(Number);
            const first = new Date(y, m - 1, 1);
            const last  = new Date(y, m, 0); // last day of month
            const today = new Date();
            const clampedLast = last < today ? last : today;
            startDate = getYearMonthDate(first);
            endDate   = getYearMonthDate(clampedLast);
        }

        $("#academic-year-filter").bind("change", async function () {
            selectedAcademicYear = this.value ? parseInt(this.value) : null;
            const year = academicYearsData.find(y => y.academic_year == this.value);
            if (year) {
                populateMonthFilter(year.date_from, year.date_to);
                // Default the month to the current month within this year, or last available
                const latestOption = $("#month-filter option:last").val();
                if (latestOption) {
                    $("#month-filter").val(latestOption);
                    setDateRangeFromMonth(latestOption);
                } else {
                    startDate = null; endDate = null;
                }
            } else {
                populateMonthFilter(null, null);
                startDate = null; endDate = null;
            }
            await setAttendanceDates();
            await requestAllChartData();
            await getclassGenderRatio();
        });

        $("#month-filter").bind("change", async function () {
            setDateRangeFromMonth(this.value);
            await setAttendanceDates();
            await requestAllChartData();
        });

        // ── District filter ──────────────────────────────────────────────────────
        $("#district-filter").bind("change", async function () {
            selectedDistrict = this.value;
            await requestAllChartData();
            await getclassGenderRatio();
            await getMaternalStatusChartData();
            await getAbsenteeismRates();
            await getLearnersWithSpecialNeeds();
            await getLearnersWithSpecialNeedsAbsteeismChart();
            await getRemovedLearnersChart();
            await getAtRiskLearnersChart();
            await getAtRiskSchoolsChart();
            await getGenderSeverityAbsenteeismChart();
            await getDistrictAbsenteeismChart();
            await getAgeAbsenteeismChart();
            if ($('#performance-analysis-tab').hasClass('active')) getLearnerPerformanceData();
        })

        $("#attendance-dates").bind("change", async function () {
            selectedDate = this.value;
            endDate = this.value;
            var todaysDate = new Date(endDate);
            var tmpStartDate = todaysDate.setDate(todaysDate.getDate() - 6);
            startDate = getYearMonthDate(new Date(tmpStartDate))
            // startDate =
            await requestAllChartData();
        })

        //hide maps if not district officer or above
        if(!isDistrictOfficerOrAbove){
            document.getElementById('maternal-status-map-container').style.display = 'none';
            document.getElementById('special-needs-located-map-tab').style.display = 'none';
            document.getElementById('special-needs-absenteeism-map-tab').style.display = 'none';
            document.getElementById('at-risk-learners-map-container').style.display = 'none';
            }

        $(async function () {
            // let learnerMartenalStatusLegend = $('#maternal-status-map-learner-legend');
            if(isDistrictOfficerOrAbove){
                drawMapLegend("maternal-status-map-learner-legend","#25A0D4","#EBF5FA")
                drawMapLegend("special-needs-located-map-legend","#E87676","#f8f9fa")
                drawMapLegend("special-needs-absenteeism-map-legend","#25A0D4","#f8f9fa")
            }
            // Load academic years
            await $.ajax({
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: "/api/academic-years-filter",
                dataType: "json",
                success: function (response) {
                    if (response.status !== true) return;
                    academicYearsData = response.data;
                    const sel = $("#academic-year-filter");
                    let activeYear = null;
                    academicYearsData.forEach(y => {
                        const label = y.academic_year_name || y.academic_year;
                        const opt = `<option value="${y.academic_year}"${y.active == 1 ? ' selected' : ''}>${label}</option>`;
                        sel.append(opt);
                        if (y.active == 1) activeYear = y;
                    });
                    if (activeYear) {
                        selectedAcademicYear = activeYear.academic_year;
                        populateMonthFilter(activeYear.date_from, activeYear.date_to);
                        // default to latest available month
                        const latestOpt = $("#month-filter option:last").val();
                        if (latestOpt) {
                            $("#month-filter").val(latestOpt);
                            setDateRangeFromMonth(latestOpt);
                        }
                    }
                }
            });

            await setAttendanceDates();
            await $.ajax({
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
            
            await getclassGenderRatio();
            await getMaternalStatusChartData();
            await getAbsenteeismRates();
            await getLearnersWithSpecialNeeds();
            await getLearnersWithSpecialNeedsAbsteeismChart();
            await getRemovedLearnersChart();
            await getAtRiskLearnersChart();
            await getAtRiskSchoolsChart();
            await getGenderSeverityAbsenteeismChart();
            await getDistrictAbsenteeismChart();
            await getAgeAbsenteeismChart();
            await getAbsenteeismDistributionChart();
            await getTrendsOverTimeChart();
        })

        function reduceHexColor(color1, color2, ratio) {
            var hex = function(x) {
                x = x.toString(16);
                return (x.length === 1) ? '0' + x : x;
            };

            var r1 = parseInt(color1.substring(1, 3), 16);
            var g1 = parseInt(color1.substring(3, 5), 16);
            var b1 = parseInt(color1.substring(5, 7), 16);

            var r2 = parseInt(color2.substring(1, 3), 16);
            var g2 = parseInt(color2.substring(3, 5), 16);
            var b2 = parseInt(color2.substring(5, 7), 16);

            var r = Math.round(r1 + (r2 - r1) * ratio);
            var g = Math.round(g1 + (g2 - g1) * ratio);
            var b = Math.round(b1 + (b2 - b1) * ratio);

            return '#' + hex(r) + hex(g) + hex(b);
        }

        function drawMapLegend(elementId,startColor,endColor){
            const c = document.getElementById(elementId);
            const ctx = c.getContext("2d");

            // Create gradient
            var grd = ctx.createLinearGradient(0, 0, 0, 80);
            grd.addColorStop(0, startColor);
            grd.addColorStop(1, endColor);

            // Fill with gradient
            ctx.fillStyle = grd;
            ctx.fillRect(5, 5, 50, 150);
        }

        $("#higher-absenteeism-rate-select").bind("change", async function () {
            switch (this.value) {
                case 'gender':
                    setGenderHigherAbsenteeismChart();
                    break;
                case 'severity':
                    setSeverityHigherAbsenteeismChart();
                    break;
                case 'age':
                    setAgeHigherAbsenteeismChart();
                    break;
                case 'district':
                    setDistrictHigherAbsenteeismChart();
                    break;
            
                default:
                    break;
            }
            
        })

       

        function setGenderHigherAbsenteeismChart(){
            higherAbsenteeismRateBarchart.xAxis[0].setCategories(Object.keys(genderAbsenteeismRatesData))
            higherAbsenteeismRateBarchart.series[0].setData(Object.values(genderAbsenteeismRatesData))
        }

        function setSeverityHigherAbsenteeismChart(){
            let severityLabels = [
                "0 - No difficulty",
                "1 - Some difficulty",
                "2 - A lot of difficulty",
                "3 - Cannot do at all"
            ]

            higherAbsenteeismRateBarchart.xAxis[0].setCategories(Object.values(severityLabels))
            higherAbsenteeismRateBarchart.series[0].setData(Object.values(severityAbsenteeismRatesData))
        }

        function setDistrictHigherAbsenteeismChart(){
            higherAbsenteeismRateBarchart.xAxis[0].setCategories(Object.values(districtAbsenteeismRatesData.districts))
            higherAbsenteeismRateBarchart.series[0].setData(Object.values(districtAbsenteeismRatesData.rate))
        }

        function setAgeHigherAbsenteeismChart(){
            higherAbsenteeismRateBarchart.xAxis[0].setCategories(Object.values(ageAbsenteeismRatesData.age))
            higherAbsenteeismRateBarchart.series[0].setData(Object.values(ageAbsenteeismRatesData.rate))
        }

        var teacherAttendanceBarchart = new Highcharts.Chart('teacher-attendance-barchart', (

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
                    categories: {},
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

        async function requestAllChartData() {
            // if(startDate == null && endDate == null){
            //     var todaysDate = new Date();
            //     var tmpStartDate   = todaysDate.setDate(todaysDate.getDate() - 6);
            //     startDate = getYearMonthDate(new Date(tmpStartDate))
            //     endDate = getYearMonthDate(new Date());
            // }

            await $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/teacher-attendance",
                type: "POST",
                dataType: "json",
                data: {startDate, endDate, districtId: selectedDistrict, academicYear: selectedAcademicYear},
                success: function (response) {
                    if (response.status === true) {

                        var teacherAttendanceBarchartData = response.data.teacherAttendanceBarchart;
                        let mapData = response.data.attendanceMap;
                        var todayHeadlineTotals = response.data.todayHeadlineTotals;
                        var categories = [];
                        var absentList = [];
                        var onTimeList = [];
                        var lateList = [];
                        var notReportedList = [];

                        //set attendance date label
                        $("#attendance-date-label").text(response.data.date)

                        //teacher report summary
                        //school card
                        $("#schools-participating").text(`${todayHeadlineTotals.schools_total} Participating`)
                        $("#reported-schools").text(`${todayHeadlineTotals.schools_reported} reported`)
                        $("#not-reported-schools").text(`${parseInt(todayHeadlineTotals.schools_total) - parseInt(todayHeadlineTotals.schools_reported)} not reported`)
                        var reportedSchoolsPercetage = (parseInt(todayHeadlineTotals.schools_reported) == 0) ? 0 : Math.round(parseInt(todayHeadlineTotals.schools_reported) / parseInt(todayHeadlineTotals.schools_total) * 100);
                        $("#reported-schools-percentage").text(`${reportedSchoolsPercetage}%`);

                        //teachers card
                        $("#registered-teachers").text(`${todayHeadlineTotals.teachers_total.toLocaleString('en-US')} Registered`)
                        $("#reported-teachers").text(`${todayHeadlineTotals.teachers_reported.toLocaleString('en-US')} reported`)
                        const notReportedTeachers = parseInt(todayHeadlineTotals.teachers_total) - parseInt(todayHeadlineTotals.teachers_reported);
                        $("#not-reported-teachers").text(`${notReportedTeachers.toLocaleString('en-US')} not reported`)
                        var reportedTeachersPercetage = (parseInt(todayHeadlineTotals.teachers_reported) == 0) ? 0 : Math.round(parseInt(todayHeadlineTotals.teachers_reported) / parseInt(todayHeadlineTotals.teachers_total) * 100);
                        $("#reported-teachers-percentage").text(`${reportedTeachersPercetage}%`);

                        //learners card
                        $("#registered-learners").text(`${todayHeadlineTotals.learners_total.toLocaleString('en-US')} Registered`)
                        $("#reported-learners").text(`${todayHeadlineTotals.learners_reported.toLocaleString('en-US')} reported`)
                        const notReportedLearners = parseInt(todayHeadlineTotals.learners_total) - parseInt(todayHeadlineTotals.learners_reported);
                        $("#not-reported-learners").text(`${notReportedLearners.toLocaleString('en-US')} not reported`)
                        var reportedLearnersPercetage = (parseInt(todayHeadlineTotals.learners_reported) == 0) ? 0 : Math.round(parseInt(todayHeadlineTotals.learners_reported) / parseInt(todayHeadlineTotals.learners_total) * 100);
                        $("#reported-learners-percentage").text(`${reportedLearnersPercetage}%`);

                        //teacher attendance
                        $("#attendance-reported-teachers").text(`${todayHeadlineTotals.teachers_reported.toLocaleString('en-US')} Reported`)
                        var totalPresent = parseInt(todayHeadlineTotals.teachers_present);
                        $("#teachers-present").text(`${totalPresent.toLocaleString('en-US')} present`)
                        $("#teachers-absent").text(`${parseInt(todayHeadlineTotals.teachers_absent).toLocaleString('en-US')} absent`)
                        var reportedTeacherAttendancePercetage = (totalPresent == 0) ? 0 : Math.round(totalPresent / parseInt(todayHeadlineTotals.teachers_reported) * 100);
                        $("#attendance-teachers-percentage").text(`${reportedTeacherAttendancePercetage}%`);


                        //attendance chart
                        teacherAttendanceBarchartData.forEach(element => {
                            var teachersReported = parseInt(element.teachers_on_time) + parseInt(element.teachers_absent) + parseInt(element.teachers_late);
                            categories.push(element.date)

                            absentList.push(parseInt(element.teachers_absent))
                            onTimeList.push(parseInt(element.teachers_on_time))
                            lateList.push(parseInt(element.teachers_late))
                            notReportedList.push(parseInt(element.teachers_total) - teachersReported)
                        });

                        teacherAttendanceBarchart.xAxis[0].setCategories(categories);

                        // teacherAttendanceBarchart.series[0].setData(notReportedList);
                        teacherAttendanceBarchart.series[0].setData(absentList);
                        teacherAttendanceBarchart.series[1].setData(lateList);
                        teacherAttendanceBarchart.series[2].setData(onTimeList);

                        // zoom to district and filter map boundaries
                        if(selectedDistrict === undefined || selectedDistrict === null || selectedDistrict === ""){
                            // restore all district boundaries and reset zoom
                            attendanceMap.series[0].update({ mapData: slDistrictsGeoJson }, true);
                            attendanceMap.mapView.setView([-11.5935,8.6190],0, true, false);
                        }else{
                            let districtId = parseInt(selectedDistrict);
                            let viewConfig = districtGeoLocation[districtId];

                            // show only the selected district boundary
                            let filteredGeoJson = {
                                type: 'FeatureCollection',
                                features: slDistrictsGeoJson.features.filter(function(f){
                                    return f.properties.CS_Dis_Num == viewConfig.geoJsonId;
                                })
                            };
                            attendanceMap.series[0].update({ mapData: filteredGeoJson }, true);
                            attendanceMap.mapView.setView([viewConfig.lon, viewConfig.lat], viewConfig.zoom, true, false);
                        }

                        let pointData = mapData;

                        pointData.forEach(function(el, i) {
                            el['marker'] = {
                                lineColor: el['teachers_reported'] > 0 ? "#52BE80" : "grey",
                                fillColor: el['teachers_reported'] > 0 ? "#6df1a4" : "#FFFFFF",
                            }

                        });

                        attendanceMap.series[1].setData(pointData)

                    }
                },
                cache: false
            });
        }

        async function setAttendanceDates() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/attendance-dates",
                type: "POST",
                dataType: "json",
                data: { selectedDate: endDate },
                success: function (response) {
                    var sel = $("#attendance-dates");
                    sel.empty();
                    if (response.status === true) {
                        var dates = response.data;
                        dates.forEach(element => {
                            sel.append(`<option value='${element.date}'>${element.format_date}</option>`)
                        });
                        // keep endDate in sync with the dropdown selection
                        if (dates.length && !endDate) {
                            endDate = dates[0].date;
                        }
                    }
                }
            })
        }

        async function getclassGenderRatio() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/class-gender-ratio-data",
                type: "POST",
                dataType: "json",
                data: {districtId: selectedDistrict, academicYear: selectedAcademicYear},
                success: function (response) {
                    let genderRatioChartData = response.data.genderRatio;
                    let classCategories = response.data.classCategories;
                    let classEnrolmentByGender = response.data.classEnrolmentByGender;

                    let maleCount = parseInt(genderRatioChartData.male);
                    let femaleCount = parseInt(genderRatioChartData.female);
                    let totalLearners = parseInt(genderRatioChartData.totalLearners);

                    let femalePercentage = Math.round(femaleCount / totalLearners * 100);

                    $("#enrolled-girls-percentage").text(femalePercentage+"%");

                    genderRatioChart.setTitle({text: totalLearners.toLocaleString("en-US")+"<br> <small>TOTAL</small>"});
                    genderRatioChart.series[0].update({
                        data: [
                            {y:femalePercentage,count:femaleCount.toLocaleString("en-US")},
                            {y:Math.round(maleCount / totalLearners * 100),count:maleCount.toLocaleString("en-US")}
                        ]
                    });

                    classCategories = moveElement(classCategories,6)
                    let femaleClassEnrolement = moveElement(classEnrolmentByGender.females,6)
                    let maleClassEnrolment = moveElement(classEnrolmentByGender.males,6)

                    const ayData = academicYearsData.find(y => y.academic_year == selectedAcademicYear);
                    const ayLabel = ayData ? (ayData.academic_year_name || ayData.academic_year) : selectedAcademicYear;
                    classEnrolmentChart.setTitle({ text: 'Class Enrolment for AY ' + ayLabel });

                    classEnrolmentChart.xAxis[0].setCategories(classCategories);

                    classEnrolmentChart.series[0].setData(femaleClassEnrolement);
                    classEnrolmentChart.series[1].setData(maleClassEnrolment);
                }
            })
        }

        function moveElement(array, fromIndex) {
            for (let toIndex = 0; fromIndex <= 9; toIndex++) {
                const element = array.splice(fromIndex, 1)[0];
                array.splice(toIndex, 0, element);
                fromIndex++;
            }
            return array;
        }

        function getSubtitle(value) {
            return `<span class="text-center">${value}</span>`;
        }

        let genderRatioChart = new Highcharts.Chart('gender-ratio-chart', (
            {
                chart:{
                    height: "33%"
                },
                title: {
                    text: '0',
                    verticalAlign: 'middle',
                    floating: true,
                    x:-90,
                    style: {
                        fontWeight: 'bold',
                        fontSize: '18px'
                    },
                },
                // subtitle: {
                //     useHTML: true,
                //     text: getSubtitle(0),
                //     floating: true,
                //     verticalAlign: 'middle',
                //     // y: 30,
                //     // x:-90
                // },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        colorByPoint: true,
                        type: 'pie',
                        size: '130%',
                        innerSize: '80%',
                        dataLabels: {
                            enabled: false,
                        },
                        showInLegend: true
                    }
                },
                legend: {
                    align: 'right',
                    verticalAlign: 'top',
                    layout: 'vertical',
                    x: -50,
                    y: 50,
                    labelFormatter: function() {
                         return `${this.name}: ${this.y}%`
                    },
                    itemStyle: {
                        fontWeight: 'bold',
                        fontSize: '15px'
                    },
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: `<b>{point.name} Learners</b><br/>
                                    <span>Percentage: {point.y}% </span><br>
                                    <span>Count: {point.count}</span>`
                },
                series: [
                    {
                        type: 'pie',
                        data: [
                            {name:"Female",y:0,color:'#FEBBBB',count:0},
                            {name:"Male",y:0,color:'#8AC1D8',count:0}
                        ]
                    }
                ],
                credits: {
                    enabled: false
                }
            })
        );

        let classEnrolmentChart = new Highcharts.Chart('class-enrolment-chart', (
            {
                chart:{
                    height:"73%"
                },
                title: {
                    text: 'Class Enrolment',
                    align: 'center'
                },
                yAxis: {
                    title: {
                        text: 'Count Learners Enrolled'
                    }
                },
                xAxis:{
                    categories: {},
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },
                series: [
                    {name:"Female",color:'#FEBBBB'},
                    {name:"Male",color:'#8AC1D8'},
                ],
                credits: {
                    enabled: false
                }
            })
        );

        async function getMaternalStatusChartData() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/maternal-status-chart",
                type: "POST",
                dataType: "json",
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let maternalStatusData = response.data.maternalStatusRates;
                    let mapData = response.data.maternalStatusGeoLocation;

                    let noStatus = parseInt(maternalStatusData.no_status);
                    let none = parseInt(maternalStatusData.none);
                    let mothers = parseInt(maternalStatusData.mother);
                    let pregnant = parseInt(maternalStatusData.pregnant);
                    let pregnantMothers = parseInt(maternalStatusData.pregnant_mother);

                    let totalMaternalLearners = mothers + pregnant + pregnantMothers;
                    let totalFemaleLearners = noStatus + none + totalMaternalLearners;

                    let girlsWithMaternalStatusPercentage = Math.round(totalMaternalLearners / totalFemaleLearners * 100);

                    $("#girls-maternal-status-percentage").text(girlsWithMaternalStatusPercentage+"%");
                    $("#girls-maternal-status-count").text(totalMaternalLearners);

                    maternalStatusChart.setTitle({text: totalMaternalLearners.toLocaleString("en-US")+"<br> <small>TOTAL</small>"});
                    maternalStatusChart.update(
                        {
                            subtitle: {
                                text: `${totalMaternalLearners.toLocaleString("en-US")} out of ${totalFemaleLearners.toLocaleString("en-US")} female learners have maternal status`
                            }
                        },
                        false,
                        false,
                        false
                    );


                    let mothersPercentage = Math.round(mothers / totalMaternalLearners * 100);
                    let pregnantPercentage = Math.round(pregnant / totalMaternalLearners * 100);
                    let pregnantMothersPercentage = Math.round(pregnantMothers / totalMaternalLearners * 100);
                    
                    maternalStatusChart.series[0].update({
                        data: [
                            {y:isNaN(mothersPercentage) ? 0 : mothersPercentage,count:mothers.toLocaleString("en-US")},
                            {y:isNaN(pregnantPercentage) ? 0 : pregnantPercentage,count:pregnant.toLocaleString("en-US")},
                            {y:isNaN(pregnantMothersPercentage) ? 0 : pregnantMothersPercentage,count:pregnantMothers.toLocaleString("en-US")}
                        ]
                    });

                    if(isDistrictOfficerOrAbove){
                        $("#maternal-status-map-learner-legend-max-count").text(totalMaternalLearners.toLocaleString("en-US"));
                    

                        let pointData = mapData;
                        let maternalStatusMapPointData = [];
                        let noMaternalStatusMapPointData = [];

                        // zoom to district
                        if(selectedDistrict === undefined || selectedDistrict === null || selectedDistrict === ""){
                            //reset zoom
                            martenalStatusMap.series[0].update({ mapData: slDistrictsGeoJson }, true);
                            martenalStatusMap.mapView.setView([-11.5935,8.6190],0, true, false);
                        }else{
                            let districtId = parseInt(selectedDistrict);
                            let viewConfig = districtGeoLocation[districtId];
                            let filteredGeoJson = {
                                type: 'FeatureCollection',
                                features: slDistrictsGeoJson.features.filter(function(f){
                                    return f.properties.CS_Dis_Num == viewConfig.geoJsonId;
                                })
                            };
                            martenalStatusMap.series[0].update({ mapData: filteredGeoJson }, true);
                            martenalStatusMap.mapView.setView([viewConfig.lon,viewConfig.lat],viewConfig.zoom, true, false);
                        }

                        let maternalDistricts = []
                        for(var i in districtGeoLocation){

                            let filterData = mapData.filter((e) =>{
                                if(parseInt(e.district_id) == districtGeoLocation[i].id){
                                    return parseInt(e.maternal_status_mother) > 0  ||  parseInt(e.maternal_status_pregnant) > 0
                                }
                            })
                            maternalDistricts.push({districId:districtGeoLocation[i].id, count: filterData.length})
                        }

                        //find the district and paint it
                        martenalStatusMap.series[0].data.forEach( (district, index) => {
                            let res = maternalDistricts.find((e)=> e.districId == district.properties.CS_Dis_Num)

                            let color = '#f8f9fa';
                            if(res != null && res.count > 0){
                                const ratio = res.count/totalMaternalLearners;
                                color = reduceHexColor("#f8f9fa","#25A0D4",ratio.toFixed(2));
                            }

                            if(district.properties.CS_Dis_Num == selectedDistrict){
                                martenalStatusMap.series[0].data[index].update({
                                    color
                                });
                            }else{
                                martenalStatusMap.series[0].data[index].update({
                                    color
                                });
                            }
                        })

                        pointData.forEach(function(el, i) {
                            if(parseInt(el['maternal_status_mother']) > 0 || parseInt(el['maternal_status_pregnant']) > 0){
                                el['marker'] = {
                                    lineColor: parseInt(el['maternal_status_mother']) > 0 || parseInt(el['maternal_status_pregnant']) > 0  ? "#52BE80" : "grey",
                                    fillColor: parseInt(el['maternal_status_mother']) > 0 || parseInt(el['maternal_status_pregnant']) > 0 ? "#6df1a4" : "#FFFFFF",
                                }
                                maternalStatusMapPointData.push(el)
                            }else{
                                el['marker'] = {
                                    lineColor: "grey",
                                    fillColor: "#FFFFFF",
                                }
                                noMaternalStatusMapPointData.push(el)
                            }

                        });


                        martenalStatusMap.series[1].setData(noMaternalStatusMapPointData)
                        martenalStatusMap.series[2].setData(maternalStatusMapPointData)
                    }
                }
            })
        }

        let maternalStatusChart = new Highcharts.Chart('maternal-status-chart', (
            {
                chart:{
                    height: "50%"
                },
                title: {
                    text: 0,
                    verticalAlign: 'middle',
                    floating: true,
                    x: -110,
                    style: {
                        fontWeight: 'bold',
                        fontSize: '23px'
                    },
                },
                subtitle: {
                    text: 'Loading... of female learners have maternal status'
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        colorByPoint: true,
                        type: 'pie',
                        size: '100%',
                        innerSize: '80%',
                        dataLabels: {
                            enabled: false,
                        },
                        showInLegend: true
                    }
                },
                legend: {
                    align: 'right',
                    verticalAlign: 'top',
                    layout: 'vertical',
                    x: -10,
                    y: 80,
                    labelFormatter: function() {
                        return `${this.name}: ${this.y}%`
                    },
                    itemStyle: {
                        fontWeight: 'bold',
                        fontSize: '15px'
                    },
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    pointFormat: `<b>{point.name}</b><br/>
                                    <span>Percentage: {point.y}% </span><br>
                                    <span>Count: {point.count}</span>`
                },
                series: [
                    {
                        type: 'pie',
                        data: [
                            {name:"Mother",y:0,color:'#66A7F3',count:0},
                            {name:"Pregnant",y:0,color:'#81D6A3',count:0},
                            {name:"Mother and Pregnant",y:0,color:'#E79595',count:0},
                        ]
                    }
                ],
                credits: {
                    enabled: false
                }
            })
        );

        let martenalStatusMap = new Highcharts.mapChart('maternal-status-map', {
            chart: {
                animation: false,
            },
            title: {
                text:""
            },
            legend: {
                itemHiddenStyle:{color : null},
                symbolRadius: 20
            },
            mapNavigation: {
                enabled: true,
                    enableDoubleClickZoomTo:true,
                    enableMouseWheelZoom:true,
                    enableTouchZoom:true
            },

            mapView: {
                maxZoom: 14
            },

            tooltip: {
                pointFormatter: function(){
                    // split the geo information in brackets at the end of the school name so it doesn't make the hover box huge
                    //     (otherwise long school name gets forced onto 1 line)
                    let nameSplit = this.school_name.split('(');
                    let first = (typeof nameSplit[0] === 'undefined') ? 'No Name Set' : nameSplit[0];
                    let second = (typeof nameSplit[1].split(')')[0] === 'undefined') ? '' : '<br>' + nameSplit[1].split(')')[0];

                    return '<strong>'+first+'</strong>'+second+'<br>'+
                    'Pregnant: '+this.maternal_status_pregnant +'<br>' +
                    'Mother: '+this.maternal_status_mother +'<br>' +
                    'Pregant Mothers: '+this.maternal_status_pregnant_mother +'<br>' +
                    'None: '+this.maternal_status_none +'<br>';
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
                    name: 'No learners with maternal status',
                    type: 'mappoint',
                    title: false,
                    data: noMaternalStatusMapPointData,
                    color: '#D3D3D3',
                    marker: {
                        fillColor: '#FFFFFF',
                        lineColor: '#D3D3D3',
                        lineWidth: 1,
                        radius: 3,
                        symbol : 'circle'
                    },
                    animation: false,
                    tooltip: {
                        // pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    point: {
                        events: {
                            click: function () {
                                if (this.uuid) {
                                    window.open('/school/' + this.uuid);
                                }
                            }
                        },
                    },

                },
                {
                    name: 'Has learners with maternal status',
                    type: 'mappoint',
                    title: false,
                    data: maternalStatusMapPointData,
                    {{--data: {!! json_encode($gpsSchools,JSON_NUMERIC_CHECK) !!},--}}
                    color: '#6df1a4',
                    dataLabels: {enabled: false},
                    animation: false,
                    tooltip: {
                        // pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    showInLegend: true,
                    point: {
                        events: {
                            click: function () {
                                if (this.uuid) {
                                    window.open('/school/' + this.uuid);
                                }
                            }
                        },
                    },

                },

            ],
            credits: {
                enabled: false
            },
        });

        let absenteeismRateBarchart = new Highcharts.Chart('absenteeism-rate-barchart', (

            {
                chart: {
                    type: 'column',
                    height: 300,
                },
                title: {
                    text: 'Absenteeism Rates'
                },

                xAxis: {
                    categories: ['Male','Female <br> (All)','Female <br> (Mother)','Female <br> (Pregnant)','Female <br> (None)'],
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '% Marked Absent'
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
                    enabled:false,
                    align: 'center',
                    verticalAlign: 'bottom',
                    backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },

                series: [
                    {
                        data: [
                            {color:'#8AC1D8',y:0},
                            {color:'#FEBBBB',y:0},
                            {color:'#FEBBBB',y:0},
                            {color:'#FEBBBB',y:0},
                            {color:'#FEBBBB',y:0},
                        ]
                    }
                ],

                credits: {
                    enabled: false
                }

            }

        ));

        async function getAbsenteeismRates(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learner-absenteeism-rates",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let learnerAbsenteeismRateData = response.data.absenteeismRates;
                    $("#girls-not-school-percentage").text(learnerAbsenteeismRateData.females * 100 +"%")
                    absenteeismRateBarchart.series[0].setData([
                        {y:parseInt(learnerAbsenteeismRateData.males*100)},
                        {y:parseInt(learnerAbsenteeismRateData.females*100)},
                        {y:parseInt(learnerAbsenteeismRateData.mother*100)},
                        {y:parseInt(learnerAbsenteeismRateData.pregnant*100)},
                        {y:parseInt(learnerAbsenteeismRateData.none*100)},
                    ]);
                }
            });
        }

        async function getLearnersWithSpecialNeeds(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learners-special-needs",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let disabilitySeverity = response.data.disabilitySeverity;
                    let commonConditions = response.data.commonConditions;
                    let specialNeedsSchools = response.data.specialNeedsSchools;
                    let highlights = response.data.highlights;

                    $("#screened-learners-percentage").text(highlights.learnerScreenedRate+"%");
                    $("#learner-difficulty-percentage").text(highlights.learnerDifficultyRate+"%");
                    $("#learner-difficulty-count").text(highlights.learnerDifficultyCount);
                    $("#common-conditions-count").text(highlights.commonConditionsCount);
                    $("#common-conditions-percentage").text(highlights.commonConditionsRate);
                    $("#total-learners-count").text(highlights.learnerTotal);


                    disabilitySeverityBarchart.series[0].setData(disabilitySeverity.cannot_do);
                    disabilitySeverityBarchart.series[1].setData(disabilitySeverity.lot_of_difficulty);
                    disabilitySeverityBarchart.series[2].setData(disabilitySeverity.some_difficulty);

                    commonConditionsBarchart.series[0].setData([
                        {y:commonConditions.epilepsy},
                        {y:commonConditions.dwarfism},
                        {y:commonConditions.albinism}
                    ])

                    let specialNeedsByDistrict = [];
                    // districtGeoLocation.forEach(district => {
                    //     let specialNeedsCount = specialNeedsSchools.filter((e)=>{
                    //         if(e.district_id = district.id && e.learners_disability_vision){
                    //             return true;
                    //         }
                    //     })
                    //     specialNeedsByDistrict.push({districtId:district.id,count:specialNeedsCount.length})
                    // });

                    let districtCategories = [];
                    let specialNeedsLearnersList = [];
                    let allLearnersList = [];
                    let specialNeedsLearnersCount = 0;
                    for (let index in districtGeoLocation) {
                        let learnerAll = 0;
                        let learnerSpecialNeeds = 0;
                        let specialNeedsCount = specialNeedsSchools.filter((e)=>{
                                if( parseInt(e.district_id) == districtGeoLocation[index].id){
                                    learnerAll += parseInt(e.learners_total);
                                    if(e.disability_learners_total > 0 ){
                                        learnerSpecialNeeds += parseInt(e.disability_learners_total)
                                    }
                                }
                            })
                        if(districtGeoLocation[index].id != 0){
                            specialNeedsByDistrict.push({
                                districtId:districtGeoLocation[index].id,
                                name:districtGeoLocation[index].name,
                                learnerAll,learnerSpecialNeeds
                            })
                            districtCategories.push(districtGeoLocation[index].name)
                            allLearnersList.push(learnerAll)
                            specialNeedsLearnersList.push(learnerSpecialNeeds)
                        }
                        specialNeedsLearnersCount+=learnerSpecialNeeds;
                    }

                    $("#special-needs-located-map-legend-max-count").text(specialNeedsLearnersCount.toLocaleString('en-US'))
                   specialNeedsLocatedBarchart.xAxis[0].setCategories(districtCategories)
                   specialNeedsLocatedBarchart.series[0].setData(allLearnersList)
                   specialNeedsLocatedBarchart.series[1].setData(specialNeedsLearnersList)

                    // zoom to district
                    if(selectedDistrict === undefined || selectedDistrict === null || selectedDistrict === ""){
                        //reset zoom
                        specialNeedsLocatedMap.series[0].update({ mapData: slDistrictsGeoJson }, true);
                        specialNeedsLocatedMap.mapView.setView([-11.5935,8.6190],0, true, false);
                    }else{
                        let districtId = parseInt(selectedDistrict);
                        let viewConfig = districtGeoLocation[districtId];
                        let filteredGeoJson = {
                            type: 'FeatureCollection',
                            features: slDistrictsGeoJson.features.filter(function(f){
                                return f.properties.CS_Dis_Num == viewConfig.geoJsonId;
                            })
                        };
                        specialNeedsLocatedMap.series[0].update({ mapData: filteredGeoJson }, true);
                        specialNeedsLocatedMap.mapView.setView([viewConfig.lon,viewConfig.lat],viewConfig.zoom, true, false);
                    }

                    //find the district and paint it
                    specialNeedsLocatedMap.series[0].data.forEach( (district, index) => {
                        let res = specialNeedsByDistrict.find((e)=> e.districtId == district.properties.CS_Dis_Num)

                        let color = '';
                        if(res != null && res.learnerSpecialNeeds > 0){
                            const ratio = res.learnerSpecialNeeds/specialNeedsLearnersCount;
                            color = reduceHexColor("#f8f9fa","#E87676",ratio.toFixed(2))
                        }

                        if(district.properties.CS_Dis_Num == selectedDistrict){
                            specialNeedsLocatedMap.series[0].data[index].update({
                                color
                            });
                        }else{
                            specialNeedsLocatedMap.series[0].data[index].update({
                                color
                            });
                        }
                    })

                    let schoolsWithSpecialNeedsLearners = [];
                    let schoolsWithNoSpecialNeedsLearners = []
                    specialNeedsSchools.forEach(function(el, i) {
                        if(parseInt(el['disability_learners_total']) > 0){
                            el['marker'] = {
                                lineColor: parseInt(el['disability_learners_total']) > 0 ? "#52BE80" : "grey",
                                fillColor: parseInt(el['disability_learners_total']) > 0 ? "#6df1a4" : "#FFFFFF",
                            }
                            schoolsWithSpecialNeedsLearners.push(el)
                        }else{
                            el['marker'] = {
                                lineColor: "grey",
                                fillColor: "#FFFFFF",
                            }
                            schoolsWithNoSpecialNeedsLearners.push(el)
                        }

                    });

                   specialNeedsLocatedMap.series[1].setData(schoolsWithSpecialNeedsLearners)
                   specialNeedsLocatedMap.series[2].setData(schoolsWithNoSpecialNeedsLearners)
                }
            });
        }

        let disabilitySeverityBarchart = new Highcharts.Chart('disability-severity-barchart', (

            {
                chart: {
                    type: 'column',
                    height: 300,
                },
                title: {
                    text: 'What needs are the most prevalent?'
                },
                xAxis: {
                    categories: [
                        "Vision",
                        "Hearing",
                        "Cognition",
                        "Self Care",
                        "Communication",
                        "Mobility"
                    ],
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Count of Learners'
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
                    verticalAlign: 'top',
                    backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    // pointFormat: "{series.name}: {point.y.toLocaleString('en-US')}<br/>Total: {point.stackTotal}"
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: false
                        }
                    }
                },

                series: [
                    {name:"3 - Cannot do at all",color:"#E87676",data:[0,0,0,0,0,0],},
                    {name:"2 - a lot of difficulty",color:"#FF9D9D",data:[0,0,0,0,0,0]},
                    {name:"1 - Some difficulty",color:"#FEBBBB",data:[0,0,0,0,0,0]},
                  
                ],

                credits: {
                    enabled: false
                }

            }

        ));

        let commonConditionsBarchart = new Highcharts.Chart('common-conditions-barchart', (

            {
                chart: {
                    type: 'column',
                    height: 300,
                },
                title: {
                    text: 'How prevalent are other common conditions?'
                },

                xAxis: {
                    categories: ['Epilepsy','Dwarfism','Albinism'],
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Count of Learners'
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
                    enabled:false,
                    align: 'center',
                    verticalAlign: 'bottom',
                    backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                },
                tooltip: {
                    // headerFormat: '<b>{point.x}</b><br/>',
                    // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },

                series: [
                    {
                        color:'#AED1BC',
                        data: [
                            {y:0},
                            {y:0},
                            {y:0},
                        ]
                    }
                ],

                credits: {
                    enabled: false
                }

            }

        ));

        let specialNeedsLocatedBarchart = new Highcharts.Chart('special-needs-located-barchart', (

            {
                chart: {
                    type: 'column',
                    height: 400,
                },
                title: {
                    text: ''
                },

                xAxis: {
                    categories: {},
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Count of Learners'
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
                    verticalAlign: 'top',
                    backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: false
                        }
                    }
                },

                series: [
                    {name:"All Learners",color:"#FF9D9D"},
                    {name:"Learners with needs",color:"#E87676"}
                ],

                credits: {
                    enabled: false
                }

            }

        ));

        let specialNeedsLocatedMap = new Highcharts.mapChart('special-needs-located-map', {
            chart: {
                animation: false,
            },
            title: {
                text:""
            },
            legend: {
                itemHiddenStyle:{color : null},
                symbolRadius: 20
            },
            mapNavigation: {
                enabled: true,
                    enableDoubleClickZoomTo:true,
                    enableMouseWheelZoom:true,
                    enableTouchZoom:true
            },

            mapView: {
                maxZoom: 14
            },

            tooltip: {
                pointFormatter: function(){
                    // split the geo information in brackets at the end of the school name so it doesn't make the hover box huge
                    //     (otherwise long school name gets forced onto 1 line)
                    let nameSplit = this.school_name.split('(');
                    let first = (typeof nameSplit[0] === 'undefined') ? 'No Name Set' : nameSplit[0];
                    let second = (typeof nameSplit[1].split(')')[0] === 'undefined') ? '' : '<br>' + nameSplit[1].split(')')[0];
                   
                    return '<strong>'+first+'</strong>'+second+'<br>'+
                        '————'+'<br>' +
                        'Vision: '+this.learners_disability_vision+'<br>'+
                        'Hearing: '+this.learners_disability_hearing+'<br>'+
                        'Cognition: '+this.learners_disability_cognition+'<br>'+
                        'Self Care: '+this.learners_disability_selfcare+'<br>'+
                        'Communication: '+this.learners_disability_communication+'<br>'+
                        'Mobility: '+this.learners_disability_mobility+'<br>'+
                        '————'+'<br>' +
                        'Epilepsy: '+this.learners_condition_epilepsy+'<br>'+
                        'Dwarfism: '+this.learners_condition_dwarfism+'<br>'+
                        'Albinism: '+this.learners_condition_albinism+'<br>';
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
                    name: 'Has special needs',
                    type: 'mappoint',
                    title: false,
                    data: maternalStatusMapPointData,
                    {{--data: {!! json_encode($gpsSchools,JSON_NUMERIC_CHECK) !!},--}}
                    color: '#6df1a4',
                    // marker: {
                    //     fillColor: '#FFFFFF',
                    //     lineColor: '#6df1a4',
                    //     lineWidth: 1,
                    //     radius: 3
                    // },
                    dataLabels: {enabled: false},
                    // dataLabels: false,
                    animation: false,
                    tooltip: {
                        // pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    showInLegend: true,
                    point: {
                        events: {
                            click: function () {
                                if (this.uuid) {
                                    window.open('/school/' + this.uuid);
                                }
                            }
                        },
                    },

                },
                {
                    name: 'No learners with special needs',
                    type: 'mappoint',
                    title: false,
                    data: noMaternalStatusMapPointData,
                    color: '#D3D3D3',
                    marker: {
                        fillColor: '#FFFFFF',
                        lineColor: '#D3D3D3',
                        lineWidth: 1,
                        radius: 3,
                        symbol : 'circle'
                    },
                    // dataLabels: {enabled: true},
                    // dataLabels: false,
                    animation: false,
                    tooltip: {
                        // pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    // showInLegend: true,
                    // legend: {
                        // symbolRadius: 20
                    // },
                    point: {
                        events: {
                            click: function () {
                                if (this.uuid) {
                                    window.open('/school/' + this.uuid);
                                }
                            }
                        },
                    },

                },

            ],
            credits: {
                enabled: false
            },
        });

        async function getLearnersWithSpecialNeedsAbsteeismChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learners-special-needs-absteeism-data",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let chartData = response.data.specialNeedsAttendanceAbsenteeismChart;
                    let schoolMapData = response.data.specialNeedsAbsenteeismTable;

                    specialNeedsAbsenteeismBarchart.series[0].setData([
                        {y:chartData.hearing},
                        {y:chartData.selfcare},
                        {y:chartData.cognition},
                        {y:chartData.communication},
                        {y:chartData.vision},
                        {y:chartData.mobility}
                    ]);


                    // zoom to district
                    if(selectedDistrict === undefined || selectedDistrict === null || selectedDistrict === ""){
                        //reset zoom
                        specialNeedsAbsenteeismMap.series[0].update({ mapData: slDistrictsGeoJson }, true);
                        specialNeedsAbsenteeismMap.mapView.setView([-11.5935,8.6190],0, true, false);
                    }else{
                        let districtId = parseInt(selectedDistrict);
                        let viewConfig = districtGeoLocation[districtId];
                        let filteredGeoJson = {
                            type: 'FeatureCollection',
                            features: slDistrictsGeoJson.features.filter(function(f){
                                return f.properties.CS_Dis_Num == viewConfig.geoJsonId;
                            })
                        };
                        specialNeedsAbsenteeismMap.series[0].update({ mapData: filteredGeoJson }, true);
                        specialNeedsAbsenteeismMap.mapView.setView([viewConfig.lon,viewConfig.lat],viewConfig.zoom, true, false);
                    }

                    let specialNeedsByDistrict = [];
                    let specialNeedsTotal = 0;
                    for (let index in districtGeoLocation) {

                        let specialNeedsCount = schoolMapData.filter((e)=>{
                                if( parseInt(e.district_id) == districtGeoLocation[index].id){
                                    if(
                                        parseInt(e.vision) > 0 ||
                                        parseInt(e.communication) > 0 ||
                                        parseInt(e.hearing) > 0 ||
                                        parseInt(e.cognition) > 0 ||
                                        parseInt(e.mobility) > 0 ||
                                        parseInt(e.selfcare) > 0
                                    ){
                                        return true;
                                    }
                                }
                            })
                        if(districtGeoLocation[index].id != 0){
                            specialNeedsByDistrict.push({
                                districtId:districtGeoLocation[index].id,
                                count: specialNeedsCount.length
                            })
                           specialNeedsTotal+= specialNeedsCount.length
                        }
                    }

                    //find the district and paint it
                    specialNeedsAbsenteeismMap.series[0].data.forEach( (district, index) => {
                        let res = specialNeedsByDistrict.find((e)=> e.districtId == district.properties.CS_Dis_Num)

                        let color = '';
                        if(res != null && res.count > 0){
                            const ratio = res.count / specialNeedsTotal;
                            color = reduceHexColor("#f8f9fa","#25A0D4",ratio.toFixed(2))
                        }

                        if(district.properties.CS_Dis_Num == selectedDistrict){
                            specialNeedsAbsenteeismMap.series[0].data[index].update({
                                color
                            });
                        }else{
                            specialNeedsAbsenteeismMap.series[0].data[index].update({
                                color
                            });
                        }
                    })

                    let schoolsWithSpecialNeedsLearners = [];
                    let schoolsWithNoSpecialNeedsLearners = []
                    schoolMapData.forEach(function(el, i) {
                        let hasSpecialNeeds = false;

                        if(parseInt(el['vision']) > 0 ||
                            parseInt(el['communication']) > 0 ||
                            parseInt(el['hearing']) > 0 ||
                            parseInt(el['cognition']) > 0 ||
                            parseInt(el['mobility']) > 0 ||
                            parseInt(el['selfcare']) > 0
                        ){
                            hasSpecialNeeds = true;
                        }

                        if(hasSpecialNeeds){
                            el['marker'] = {
                                lineColor: "#52BE80",
                                fillColor: "#6df1a4",
                            }
                            schoolsWithSpecialNeedsLearners.push(el)
                        }else{
                            el['marker'] = {
                                lineColor: "grey",
                                fillColor: "#FFFFFF",
                            }
                            schoolsWithNoSpecialNeedsLearners.push(el)
                        }

                    });

                    specialNeedsAbsenteeismMap.series[1].setData(schoolsWithSpecialNeedsLearners)
                    specialNeedsAbsenteeismMap.series[2].setData(schoolsWithNoSpecialNeedsLearners)


                }
            })
        }

        let specialNeedsAbsenteeismBarchart = new Highcharts.Chart('special-needs-absenteeism-barchart', (

            {
                chart: {
                    type: 'column',
                    height: 400,
                },
                title: {
                    text: ''
                },

                xAxis: {
                    categories: ['Hearing','Self Care','Cognition','Communication','Vision','Mobility'],
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '%Absenteeism Rate'
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
                    enabled: false,
                    align: 'center',
                    verticalAlign: 'top',
                    backgroundColor:
                        Highcharts.defaultOptions.legend.backgroundColor || 'white',
                },
                tooltip: {
                    headerFormat: '<b>{point.x}</b><br/>',
                    // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
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
                    {
                        data: [
                            {y:0},
                            {y:0},
                            {y:0},
                            {y:0},
                            {y:0},
                            {y:0},
                        ]
                    }
                ],

                credits: {
                    enabled: false
                }

            }

        ));

        let specialNeedsAbsenteeismMap = new Highcharts.mapChart('special-needs-absenteeism-map', {
            chart: {
                animation: false,
            },
            title: {
                text:""
            },
            legend: {
                itemHiddenStyle:{color : null},
                symbolRadius: 20
            },
            mapNavigation: {
                enabled: true,
                    enableDoubleClickZoomTo:true,
                    enableMouseWheelZoom:true,
                    enableTouchZoom:true
            },

            mapView: {
                maxZoom: 14
            },

            tooltip: {
                pointFormatter: function(){
                    // split the geo information in brackets at the end of the school name so it doesn't make the hover box huge
                    //     (otherwise long school name gets forced onto 1 line)
                    let nameSplit = this.school_name.split('(');
                    let first = (typeof nameSplit[0] === 'undefined') ? 'No Name Set' : nameSplit[0];
                    let second = (typeof nameSplit[1].split(')')[0] === 'undefined') ? '' : '<br>' + nameSplit[1].split(')')[0];

                    return '<strong>'+first+'</strong>'+second+'<br>'
                        // 'EMIS Code: '+ ( (this.emis_id !== null) ? this.emis_id : 'None' ) +'<br>' +
                        // 'School Payroll ID: '+ ( (this.payroll_sid !== null) ? this.payroll_sid : 'None' ) +'<br>' +
                        ;
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
                    name: 'WDY School Reporting Learner(s) With Needs',
                    type: 'mappoint',
                    title: false,
                    data: maternalStatusMapPointData,
                    {{--data: {!! json_encode($gpsSchools,JSON_NUMERIC_CHECK) !!},--}}
                    color: '#6df1a4',
                    // marker: {
                    //     fillColor: '#FFFFFF',
                    //     lineColor: '#6df1a4',
                    //     lineWidth: 1,
                    //     radius: 3
                    // },
                    dataLabels: {enabled: false},
                    // dataLabels: false,
                    animation: false,
                    tooltip: {
                        // pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    showInLegend: true,
                    point: {
                        events: {
                            click: function () {
                                if (this.school_uuid) {
                                    window.open('/school/' + this.school_uuid);
                                }
                            }
                        },
                    },

                },
                {
                    name: 'WDY School Reporting No Learners With Needs',
                    type: 'mappoint',
                    title: false,
                    data: noMaternalStatusMapPointData,
                    color: '#D3D3D3',
                    marker: {
                        fillColor: '#FFFFFF',
                        lineColor: '#D3D3D3',
                        lineWidth: 1,
                        radius: 3,
                        symbol : 'circle'
                    },
                    // dataLabels: {enabled: true},
                    // dataLabels: false,
                    animation: false,
                    tooltip: {
                        // pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    // showInLegend: true,
                    // legend: {
                        // symbolRadius: 20
                    // },
                    point: {
                        events: {
                            click: function () {
                                if (this.school_uuid) {
                                    window.open('/school/' + this.school_uuid);
                                }
                            }
                        },
                    },

                },

            ],
            credits: {
                enabled: false
            },
        });

        async function getAtRiskLearnersChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learners-at-risk-data",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let persistentAbsentCount = response.data.persistent_absent
                    let severlyAbsentCount = response.data.severly_absent
                    let persistentAbsentPercentage = response.data.persistent_percentage
                    let severlyAbsentPercentage = response.data.serverly_percentage

                    $("#persistentAbsentCount").text(persistentAbsentCount.toLocaleString("en-US"))
                    $("#persistentAbsentPercentage").text(persistentAbsentPercentage+"%")
                    $("#severlyAbsentCount").text(severlyAbsentCount.toLocaleString("en-US"))
                    $("#severlyAbsentPercentage").text(severlyAbsentPercentage+"%")
                }
            })
        }

        async function getAtRiskSchoolsChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learners-at-risk-school-data",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let schools = response.data.schools

                    // zoom to district
                    if(selectedDistrict === undefined || selectedDistrict === null || selectedDistrict === ""){
                        //reset zoom
                        atRiskLearnersMap.series[0].update({ mapData: slDistrictsGeoJson }, true);
                        atRiskLearnersMap.mapView.setView([-11.5935,8.6190],0, true, false);
                    }else{
                        let districtId = parseInt(selectedDistrict);
                        let viewConfig = districtGeoLocation[districtId];
                        let filteredGeoJson = {
                            type: 'FeatureCollection',
                            features: slDistrictsGeoJson.features.filter(function(f){
                                return f.properties.CS_Dis_Num == viewConfig.geoJsonId;
                            })
                        };
                        atRiskLearnersMap.series[0].update({ mapData: filteredGeoJson }, true);
                        atRiskLearnersMap.mapView.setView([viewConfig.lon,viewConfig.lat],viewConfig.zoom, true, false);
                    }

                    // let specialNeedsByDistrict = [];
                    // let specialNeedsTotal = 0;
                    // for (let index in districtGeoLocation) {

                    //     let specialNeedsCount = schoolMapData.filter((e)=>{
                    //             if( parseInt(e.district_id) == districtGeoLocation[index].id){
                    //                 if(
                    //                     parseInt(e.vision) > 0 ||
                    //                     parseInt(e.communication) > 0 ||
                    //                     parseInt(e.hearing) > 0 ||
                    //                     parseInt(e.cognition) > 0 ||
                    //                     parseInt(e.mobility) > 0 ||
                    //                     parseInt(e.selfcare) > 0
                    //                 ){
                    //                     return true;
                    //                 }
                    //             }
                    //         })
                    //     if(districtGeoLocation[index].id != 0){
                    //         specialNeedsByDistrict.push({
                    //             districtId:districtGeoLocation[index].id,
                    //             count: specialNeedsCount.length
                    //         })
                    //        specialNeedsTotal+= specialNeedsCount.length
                    //     }
                    // }

                    // //find the district and paint it
                    atRiskLearnersMap.series[0].data.forEach( (district, index) => {
                        // let res = specialNeedsByDistrict.find((e)=> e.districtId == district.properties.CS_Dis_Num)

                        let color = '#f8f9fa';
                        // if(res != null && res.count > 0){
                        //     const ratio = res.count / specialNeedsTotal;
                        //     color = reduceHexColor("#f8f9fa","#25A0D4",ratio.toFixed(2))
                        // }

                        if(district.properties.CS_Dis_Num == selectedDistrict){
                            atRiskLearnersMap.series[0].data[index].update({
                                color
                            });
                        }else{
                            atRiskLearnersMap.series[0].data[index].update({
                                color
                            });
                        }
                    })

                    let schoolsWithAtRiskLearner = [];
                    let schoolsWithNoAtRiskLearner = []
                    schools.forEach(function(el, i) {
                        let hasAtRiskLearners = false;

                        if(el.absent_learners_count > 0){
                            hasAtRiskLearners = true;
                        }

                        if(hasAtRiskLearners){
                            el['marker'] = {
                                lineColor: "#52BE80",
                                fillColor: "#6df1a4",
                            }
                            schoolsWithAtRiskLearner.push(el)
                        }else{
                            el['marker'] = {
                                lineColor: "grey",
                                fillColor: "#FFFFFF",
                            }
                            schoolsWithNoAtRiskLearner.push(el)
                        }

                    });

                    atRiskLearnersMap.series[1].setData(schoolsWithAtRiskLearner)
                    atRiskLearnersMap.series[2].setData(schoolsWithNoAtRiskLearner)
                }
            })
        }

        async function getRemovedLearnersChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/learners-removed-data",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let chartData = response.data.endReasonsChart;
                    let removedLearners = response.data.removedLearners;

                    reasonsLearnersRemovedBarchart.xAxis[0].setCategories(Object.keys(chartData))
                    reasonsLearnersRemovedBarchart.series[0].setData(Object.values(chartData))


                }
            })
        }

        async function getGenderSeverityAbsenteeismChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/gender-severity-absenteeism-rate",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let chartData = response.data[0];
                    let genderRates = {male:parseInt(chartData.males_rate),female:parseInt(chartData.females_rate)};
                    let severityRates = [
                        parseInt(chartData.no_difficulty_rate),
                        parseInt(chartData.some_difficulty_rate),
                        parseInt(chartData.lot_of_difficulty_rate),
                        parseInt(chartData.cannot_do_rate)
                    ]
                    let severityLabels = [
                        "0 - No difficulty",
                        "1 - Some difficulty",
                        "2 - A lot of difficulty",
                        "3 - Cannot do at all"
                    ]

                    genderAbsenteeismRatesData = genderRates;
                    severityAbsenteeismRatesData = severityRates;

                    // let removedLearners = response.data.removedLearners;
                    higherAbsenteeismRateBarchart.xAxis[0].setCategories(Object.keys(genderRates))
                    higherAbsenteeismRateBarchart.series[0].setData(Object.values(genderRates))

                    //gender
                    // higherAbsenteeismRateBarchart.xAxis[0].setCategories(Object.values(severityLabels))
                    // higherAbsenteeismRateBarchart.series[0].setData(Object.values(severityRates))


                }
            })
        }

        async function getAgeAbsenteeismChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/age-absenteeism-rate",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let chartData = response.data;
                    let ageRange = [];
                    let rateRange = [];
                    chartData.forEach(element => {
                        if(element.age >= 2){
                            ageRange.push(element.age+' years old');
                            rateRange.push(parseInt(element.rate))
                        }
                    });
                    ageAbsenteeismRatesData ={
                        age:ageRange,
                        rate: rateRange
                    }
                }
            })
        }

        async function getDistrictAbsenteeismChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/district-absenteeism-rate",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    // let chartData = response.data[0];
                    let chartData = response.data;
                    let districtsList = [];
                    let rateRange = [];
                    chartData.forEach(element => {
                        districtsList.push(element.name);
                        rateRange.push(parseInt(element.rate))
                    });
                    districtAbsenteeismRatesData ={
                        districts:districtsList,
                        rate: rateRange
                    }
                }
            })
        }

        function groupDatesAndSum(data) {
    // Calculate the total number of days
    const totalDays = (new Date(data[data.length - 1].date) - new Date(data[0].date)) / (1000 * 60 * 60 * 24) + 1;

    // Calculate the interval size in days
    const intervalSize = Math.ceil(totalDays * 0.1);

    const groupedData = [];
    let currentIntervalStart = new Date(data[0].date);
    let currentIntervalEnd = new Date(currentIntervalStart);
    currentIntervalEnd.setDate(currentIntervalStart.getDate() + intervalSize - 1);
    let currentSum = 0;
    let currentPercentage = 10; // Start from 10

    for (const entry of data) {
        const entryDate = new Date(entry.date);

        while (entryDate > currentIntervalEnd) {
            groupedData.push({
                intervalStart: currentIntervalStart.toISOString().split('T')[0],
                intervalEnd: currentIntervalEnd.toISOString().split('T')[0],
                sum: currentSum,
                percentage: currentPercentage.toFixed(2)
            });

            currentIntervalStart = new Date(currentIntervalEnd);
            currentIntervalStart.setDate(currentIntervalStart.getDate() + 1);
            currentIntervalEnd = new Date(currentIntervalStart);
            currentIntervalEnd.setDate(currentIntervalStart.getDate() + intervalSize - 1);

            currentPercentage += 10; // Increment by 10
            currentSum = 0;
        }

        currentSum += entry.count_learners;
    }

    // Push the last interval
    const percentage = (currentSum / data.reduce((sum, entry) => sum + entry.count_learners, 0)) * 100;
    groupedData.push({
        intervalStart: currentIntervalStart.toISOString().split('T')[0],
        intervalEnd: currentIntervalEnd.toISOString().split('T')[0],
        sum: currentSum,
        percentage: percentage.toFixed(2)
    });

    return groupedData;
}


        async function getAbsenteeismDistributionChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/absenteeism-distribution-chart",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    let chartData = response.data.learnerData;
                    let totalAttendanceDays = response.data.maxAttendanceDay;
                    // let chartData = response.data;
                    let malesData = {name:"Male",color:'#8AC1D8',data:[]}
                    let femalesData = {name:"Female",color:'#FEBBBB',data:[]}

                    let noDifficultyData = {name:"Special Need = 0",data:[]}
                    let someDifficultyData = {name:"Special Need = 1",data:[]}
                    let lotDifficultyData = {name:"Special Need = 2",data:[]}
                    let cannotDifficultyData = {name:"Special Need = 3",data:[]}

                    let motherMaternalData = {name:"Mother",data:[]}
                    let pregnantMaternalData = {name:"Pregnant",data:[]}
                    let noMaternalData = {name:"No Maternal Status",data:[]}

                    // const groupedAndSummedData = groupDatesAndSum(chartData);
                    // console.log(groupedAndSummedData);
                    // let percentageCounter = 10;
                    // groupedAndSummedData.forEach(element => {
                        // monthLabels.push(percentageCounter);
                        // console.log(element.count_learners)
                        malesData.data = getPercentageIntervals(chartData.males,totalAttendanceDays)
                        femalesData.data = getPercentageIntervals(chartData.females,totalAttendanceDays)

                        noDifficultyData.data = getPercentageIntervals(chartData.no_difficulty,totalAttendanceDays)
                        someDifficultyData.data = getPercentageIntervals(chartData.some_difficulty,totalAttendanceDays)
                        lotDifficultyData.data = getPercentageIntervals(chartData.alot_difficulty,totalAttendanceDays)
                        cannotDifficultyData.data = getPercentageIntervals(chartData.cannot_difficulty,totalAttendanceDays)
                        
                        pregnantMaternalData.data = getPercentageIntervals(chartData.pregnant,totalAttendanceDays)
                        motherMaternalData.data = getPercentageIntervals(chartData.mother,totalAttendanceDays)
                        noMaternalData.data = getPercentageIntervals(chartData.maternal_none,totalAttendanceDays)

                    //     percentageCounter+=10
                    // });
                    // console.log(malesData)

                    // let temp = []
                    //  temp = Object.values(chartData.males)
                    // temp.forEach(element => {
                    //     malesData.data.push(parseInt(element))
                    // });

                    //  temp = Object.values(chartData.females)
                    // temp.forEach(element => {
                    //     femalesData.data.push(parseInt(element))
                    // });
                    
                    //  temp = Object.values(chartData.alot_difficulty)
                    // temp.forEach(element => {
                    //     lotDifficultyData.data.push(parseInt(element))
                    // });

                    //  temp = Object.values(chartData.no_difficulty)
                    // temp.forEach(element => {
                    //     noDifficultyData.data.push(parseInt(element))
                    // });

                    //  temp = Object.values(chartData.some_difficulty)
                    // temp.forEach(element => {
                    //     someDifficultyData.data.push(parseInt(element))
                    // });

                    //  temp = Object.values(chartData.cannot_difficulty)
                    // temp.forEach(element => {
                    //     cannotDifficultyData.data.push(parseInt(element))
                    // });

                    //  temp = Object.values(chartData.pregnant)
                    // temp.forEach(element => {
                    //     pregnantMaternalData.data.push(parseInt(element))
                    // });
                    //  temp = Object.values(chartData.mother)
                    // temp.forEach(element => {
                    //     motherMaternalData.data.push(parseInt(element))
                    // });
                    //  temp = Object.values(chartData.maternal_none)
                    // temp.forEach(element => {
                    //     noMaternalData.data.push(parseInt(element))
                    // });

                    // trendsOverTimeChart.xAxis[0].setCategories()

                    // absenteeismDistributionChart.xAxis[0].setCategories(monthLabels)

                    while (absenteeismDistributionChart.series.length > 0) {
                        absenteeismDistributionChart.series[0].remove();
                    }

                    absenteeismDistributionChart.addSeries(malesData)
                    absenteeismDistributionChart.addSeries(femalesData)

                    absenteeismDistributionChart.addSeries(motherMaternalData)
                    absenteeismDistributionChart.addSeries(pregnantMaternalData)
                    absenteeismDistributionChart.addSeries(noMaternalData)

                    absenteeismDistributionChart.addSeries(noDifficultyData)
                    absenteeismDistributionChart.addSeries(someDifficultyData)
                    absenteeismDistributionChart.addSeries(lotDifficultyData)
                    absenteeismDistributionChart.addSeries(cannotDifficultyData)
                    // absenteeismDistributionChart.series[0].setData(malesData);
                }
            })
        }

        async function getTrendsOverTimeChart(){
            await $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/api/trends-over-time-chart",
                dataType: 'json',
                data: {districtId: selectedDistrict},
                success: function (response) {
                    // let chartData = response.data[0];
                    let chartData = response.data;
                    let monthLabels = [];
                    let severlyData = {name:"Out of School",color:' #FF0000',data:[]}
                    let persistentData = {name:"Persistent Absent",data:[]}
                    let atRiskData = {name:"At Risk",color:'#AED1BC',data:[]}

                    chartData.forEach(element => {
                        monthLabels.push(element.month_name);
                        severlyData.data.push(parseInt(element.count_serverly_absent))
                        persistentData.data.push(parseInt(element.count_persistent_absent))
                        atRiskData.data.push(parseInt(element.count_at_risk))
                    });
                 
                    trendsOverTimeChart.xAxis[0].setCategories(monthLabels)

                    trendsOverTimeChart.addSeries(persistentData)
                    trendsOverTimeChart.addSeries(atRiskData)
                    trendsOverTimeChart.addSeries(severlyData)
                }
            })
        }

        let reasonsLearnersRemovedBarchart = new Highcharts.Chart('reasons-learners-removed-barchart', (
        {
            chart: {
                type: 'column',
                height: 300,
            },
            title: {
                text: ''
            },

            xAxis: {
                categories: ['Epilepsy','Dwarfism','Albinism'],
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Count of Learners'
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
                enabled:false,
                align: 'center',
                verticalAlign: 'bottom',
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
            },
            tooltip: {
                // headerFormat: '<b>{point.x}</b><br/>',
                // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },

            series: [
                {
                    color:'#AED1BC',
                    data: [
                        {y:0},
                        {y:0},
                        {y:0},
                    ]
                }
            ],

            credits: {
                enabled: false
            }

        }
        ));

        let atRiskLearnersMap = new Highcharts.mapChart('at-risk-learners-map', {
            chart: {
                animation: false,
            },
            title: {
                text:"Where are at risk learners located?"
            },
            legend: {
                itemHiddenStyle:{color : null},
                symbolRadius: 20
            },
            mapNavigation: {
                enabled: true,
                    enableDoubleClickZoomTo:true,
                    enableMouseWheelZoom:true,
                    enableTouchZoom:true
            },

            mapView: {
                maxZoom: 14
            },
            tooltip: {
                pointFormatter: function(){
                    // split the geo information in brackets at the end of the school name so it doesn't make the hover box huge
                    //     (otherwise long school name gets forced onto 1 line)
                    let nameSplit = this.name.split('(');
                    let first = (typeof nameSplit[0] === 'undefined') ? 'No Name Set' : nameSplit[0];
                    let second = (typeof nameSplit[1].split(')')[0] === 'undefined') ? '' : '<br>' + nameSplit[1].split(')')[0];

                    return '<strong>'+first+'</strong>'+second+'<br>'
                        'Absent Learners: '+ this.absent_learners_count +'<br>' 
                        // 'School Payroll ID: '+ ( (this.payroll_sid !== null) ? this.payroll_sid : 'None' ) +'<br>' +
                        ;
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
                    name: 'Schools With At Risk Learners',
                    type: 'mappoint',
                    title: false,
                    {{--data: {!! json_encode($gpsSchools,JSON_NUMERIC_CHECK) !!},--}}
                    color: '#6df1a4',
                    // marker: {
                    //     fillColor: '#FFFFFF',
                    //     lineColor: '#6df1a4',
                    //     lineWidth: 1,
                    //     radius: 3
                    // },
                    dataLabels: {enabled: false},
                    // dataLabels: false,
                    animation: false,
                    tooltip: {
                        // pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    showInLegend: true,
                    point: {
                        events: {
                            click: function () {
                                if (this.school_uuid) {
                                    window.open('/school/' + this.school_uuid);
                                }
                            }
                        },
                    },

                },
                {
                    name: 'Schools With No At Risk Learners',
                    type: 'mappoint',
                    title: false,
                    color: '#D3D3D3',
                    marker: {
                        fillColor: '#FFFFFF',
                        lineColor: '#D3D3D3',
                        lineWidth: 1,
                        radius: 3,
                        symbol : 'circle'
                    },
                    animation: false,
                    tooltip: {
                        pointFormat: '{point.name}'
                    },
                    turboThreshold: 0,
                    point: {
                        events: {
                            click: function () {
                                if (this.school_uuid) {
                                    window.open('/school/' + this.uuid);
                                }
                            }
                        },
                    },

                },

            ],
            credits: {
                enabled: false
            },
        });

        let higherAbsenteeismRateBarchart = new Highcharts.Chart('higher-absenteeism-rate-barchart', (
        {
            chart: {
                type: 'column',
                height: 350
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: [],
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Absenteeism Rate %'
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
                enabled:false,
                align: 'center',
                verticalAlign: 'bottom',
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
            },
            tooltip: {
                // headerFormat: '<b>{point.x}</b><br/>',
                // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },

            series: [
                {
                    color:'#AED1BC',
                    data: []
                }
            ],

            credits: {
                enabled: false
            }

        }
        ));

        let absenteeismDistributionChart = new Highcharts.Chart('absenteeism-distribution-chart', ({
            chart: {
                type: 'column',
            },
            title: {
                text: ''
            },
            subtitle:{
                text:'Click on a series to display'
            },
            xAxis: {
                categories: ["10","20","30","40","50","60","70","80","90","100"],
                crosshair: true,
                accessibility: {
                    description: '% of days attended this year'
                },
                title:{
                    text:'% of days attended this year '
                } 
            },
            yAxis: {
                title: {
                    text: 'Count of learners'
                }
            },
            legend: {
                align: 'center',
                verticalAlign: 'top',
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
            },
            tooltip: {
                // headerFormat: '<b>{point.x}</b><br/>',
                // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
            },
            plotOptions: {
                column: {
                    pointPadding: 0,
                    borderWidth: 0,
                    groupPadding: 0,
                    shadow: false
                }
            },
            series: [],

            credits: {
                enabled: false
            }

        }
        ));

        let trendsOverTimeChart = new Highcharts.Chart('trends-over-time-chart', ({
           
            title: {
                text: ''
            },
            xAxis: {
            },
            yAxis: {
                title: {
                    text: 'Count of learners'
                }
            },
            legend: {
                align: 'center',
                verticalAlign: 'top',
                backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || 'white',
            },
            tooltip: {
                // headerFormat: '<b>{point.x}</b><br/>',
                // pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
            },
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    // pointStart: 2010
                }
            },
            series: [
                // {name:"Persistent Absent",data:[20,10,30]},
                // {name:"Out of School",data:[10,40,60]}
            ],

            credits: {
                enabled: false
            }

        }
        ));

        function getYearMonthDate(date) {
            var day = String(date.getDate()).padStart(2, '0');
            var month = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
            var year = date.getFullYear();
            var newDate = `${year}-${month}-${day}`;
            return newDate;
        }

        function generateKeyValueArray(object){
            let keys = Object.keys(object)
            let values = Object.values(object)

            let newCollection = [] 
            for(let i=0; i<keys.length; i++){
                newCollection.push({
                key: parseInt(keys[i]),
                value: values[i]
                })
            }
            return newCollection
        }

        function getPercentageIntervals(data,totalCount){
            let range = generateKeyValueArray(data);
            let outRange = [];
            
            range.forEach((element)=>{
                element.percentage = parseInt((element.key/totalCount *100).toFixed(0))
            })
            
            let intervalPercentage = 10;
            let currentPercentage = 0;
            let percentageAttendanceList = []
            for(let i=10;i <= 100;i+=intervalPercentage){
            let sum = 0;
                range.forEach((e)=>{
                if(e.percentage >= currentPercentage && e.percentage <= i){
                    sum+= e.value
                    }
                })
            currentPercentage += intervalPercentage; 
            percentageAttendanceList.push(sum)
            }
            return percentageAttendanceList
        }
  


        // ── Learner Performance Tab ───────────────────────────────────────────────
        var selectedPerfTerm  = "";
        var selectedPerfLevel = "";
        var perfTrendsChart, perfSubjectChart;
        var perfDataCache = null;

        const TERM_LABELS = {
            first_term:  'First Term',
            second_term: 'Second Term',
            third_term:  'Third Term'
        };
        const LEVEL_COLORS = { Primary: '#5B9BD5', JSS: '#ED7D31', SSS: '#70AD47' };

        function initPerfCharts() {
            perfTrendsChart = new Highcharts.Chart('perf-trends-chart', {
                chart: { type: 'column', height: 280, animation: false },
                title: { text: null },
                xAxis: { categories: ['First Term', 'Second Term', 'Third Term'] },
                yAxis: {
                    min: 0, max: 100,
                    title: { text: 'Avg Score (%)' },
                    plotLines: [{ value: 50, color: '#E74C3C', dashStyle: 'ShortDash', width: 1,
                                  label: { text: '50% threshold', style: { color: '#E74C3C', fontSize: '10px' } } }]
                },
                tooltip: { valueSuffix: '%', shared: true },
                legend: { enabled: true },
                series: [],
                credits: { enabled: false }
            });

            perfSubjectChart = new Highcharts.Chart('perf-subject-chart', {
                chart: { type: 'bar', height: 380, animation: false },
                title: { text: null },
                xAxis: { categories: [], title: { text: null } },
                yAxis: {
                    min: 0, max: 100,
                    title: { text: 'Avg Score (%)' },
                    plotLines: [{ value: 50, color: '#E74C3C', dashStyle: 'ShortDash', width: 1 }]
                },
                tooltip: { valueSuffix: '%' },
                legend: { enabled: false },
                series: [{ name: 'Avg Score', data: [], colorByPoint: true, showInLegend: false }],
                credits: { enabled: false }
            });
        }

        function renderPerfSummary(summary) {
            const levels = ['Primary', 'JSS', 'SSS'];
            let html = '';
            levels.forEach(function(lvl) {
                const row = summary.find(function(r) { return r.level_label === lvl; });
                if (!row) return;
                const pct = row.total_assessed > 0 ? Math.round(row.poor_performers / row.total_assessed * 100) : 0;
                const badge = pct >= 50 ? 'danger' : pct >= 30 ? 'warning' : 'success';
                html += '<tr>' +
                    '<td><strong>' + lvl + '</strong></td>' +
                    '<td class="text-center">' + row.total_assessed + '</td>' +
                    '<td class="text-center">' + row.poor_performers + '</td>' +
                    '<td class="text-center"><span class="badge bg-' + badge + '">' + pct + '%</span></td>' +
                    '</tr>';
            });
            document.getElementById('perf-summary-body').innerHTML =
                html || '<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>';
        }

        function renderPerfTrends(trends) {
            const terms   = ['first_term', 'second_term', 'third_term'];
            const levels  = selectedPerfLevel ? [selectedPerfLevel] : ['Primary', 'JSS', 'SSS'];
            const present = levels.filter(function(l) { return trends.some(function(t) { return t.level_label === l; }); });

            while (perfTrendsChart.series.length) perfTrendsChart.series[0].remove(false);

            present.forEach(function(level) {
                perfTrendsChart.addSeries({
                    name:  level,
                    color: LEVEL_COLORS[level],
                    data:  terms.map(function(term) {
                        const row = trends.find(function(t) { return t.level_label === level && t.term_oid === term; });
                        return row ? parseFloat(row.avg_score) : null;
                    })
                }, false);
            });
            perfTrendsChart.redraw();
        }

        function renderPerfSubjects(subjects) {
            let filtered = subjects.slice();
            if (selectedPerfLevel) filtered = filtered.filter(function(s) { return s.level_label === selectedPerfLevel; });
            if (selectedPerfTerm)  filtered = filtered.filter(function(s) { return s.term_oid === selectedPerfTerm; });

            const subjectMap = {};
            filtered.forEach(function(row) {
                const key = row.subject_oid;
                if (!subjectMap[key]) subjectMap[key] = { name: row.subject_name, total: 0, count: 0 };
                subjectMap[key].total += parseFloat(row.avg_score);
                subjectMap[key].count += 1;
            });

            const data = Object.values(subjectMap)
                .map(function(s) { return { name: s.name, y: Math.round(s.total / s.count * 10) / 10 }; })
                .sort(function(a, b) { return a.y - b.y; });

            const height = Math.max(280, data.length * 28 + 80);
            perfSubjectChart.setSize(null, height, false);
            perfSubjectChart.xAxis[0].setCategories(data.map(function(d) { return d.name; }), false);
            perfSubjectChart.series[0].setData(data.map(function(d) {
                return { y: d.y, color: d.y < 50 ? '#E74C3C' : d.y < 70 ? '#F39C12' : '#27AE60' };
            }), true);
        }

        function getLearnerPerformanceData() {
            document.getElementById('perf-summary-body').innerHTML =
                '<tr><td colspan="4" class="text-center text-muted">Loading…</td></tr>';

            $.ajax({
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: "/api/learner-performance/dashboard",
                data: {
                    districtId: selectedDistrict,
                    termOid:    selectedPerfTerm,
                    levelLabel: selectedPerfLevel
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status !== true) return;
                    perfDataCache = response.data;
                    renderPerfSummary(response.data.summary);
                    renderPerfTrends(response.data.trends);
                    renderPerfSubjects(response.data.subjects);
                }
            });
        }

        $('#perf-term-filter').on('change', function() {
            selectedPerfTerm = this.value;
            if (perfDataCache) {
                renderPerfTrends(perfDataCache.trends);
                renderPerfSubjects(perfDataCache.subjects);
            } else {
                getLearnerPerformanceData();
            }
        });

        $('#perf-level-filter').on('change', function() {
            selectedPerfLevel = this.value;
            getLearnerPerformanceData();
        });

        $('#performance-analysis-tab').on('shown.bs.tab', function() {
            if (!perfTrendsChart) initPerfCharts();
            getLearnerPerformanceData();
        });

    </script>

    @include('_partials.landing-map-config')
@endsection
