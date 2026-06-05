@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
@endsection

@section('content')
<div class="container">

    {{-- Back link --}}
    @if($details && $details->school_uuid)
        <a href="{{ url('/school/' . $details->school_uuid) }}" class="btn btn-sm btn-outline-secondary mb-3">
            &larr; Back to {{ $details->school_name ?? 'School' }}
        </a>
    @endif

    <h2 class="mb-1">Learner Profile</h2>
    @if($details)
        <p class="text-muted mb-3">UID: <strong>{{ $details->learner_id ?? '—' }}</strong></p>
    @endif

    @if(!$details)
        <div class="alert alert-warning">Learner not found.</div>
    @else

    <div class="row g-3">

        {{-- ── PERSONAL INFORMATION ── --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">Personal Information</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width:45%">Full Name</td>
                                <td>
                                    @if($isDistrictOfficerOrAbove)
                                        {{ $details->full_name ?? '—' }}
                                    @else
                                        <span class="text-muted fst-italic">Restricted</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIN</td>
                                <td>
                                    @if($isDistrictOfficerOrAbove)
                                        {{ $details->nin ?? '—' }}
                                    @else
                                        <i class="fas fa-lock"></i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date of Birth</td>
                                <td>
                                    @if($isDistrictOfficerOrAbove)
                                        {{ $details->date_of_birth ?? '—' }}
                                    @else
                                        <i class="fas fa-lock"></i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sex</td>
                                <td>{{ $details->sex ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Language Spoken</td>
                                <td>{{ $details->language_name ?? '—' }}</td>
                            </tr>
                            @if($details->maternal_status)
                            <tr>
                                <td class="text-muted">Maternal Status</td>
                                <td>{{ $details->maternal_status }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── SCHOOL & ENROLMENT ── --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">Current School &amp; Enrolment</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width:45%">School</td>
                                <td>
                                    @if($details->school_uuid)
                                        <a href="{{ url('/school/' . $details->school_uuid) }}">{{ $details->school_name }}</a>
                                    @else
                                        <span class="text-muted fst-italic">Not currently enrolled</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Year Group</td>
                                <td>{{ $details->school_group_level ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Classroom</td>
                                <td>{{ $details->school_group_name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Academic Year</td>
                                <td>{{ $details->academic_year ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Start Date</td>
                                <td>{{ $details->start_date ?? '—' }}</td>
                            </tr>
                            @if($details->end_date)
                            <tr>
                                <td class="text-muted">End Date</td>
                                <td>{{ $details->end_date }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">End Reason</td>
                                <td>{{ $details->end_reason_name ?? '—' }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── GUARDIAN / PARENT ── --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">Parent / Guardian</div>
                <div class="card-body">
                    @if($details->guardian_uuid)
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width:45%">Name</td>
                                <td>
                                    @if($isDistrictOfficerOrAbove)
                                        {{ $details->guardian_full_name ?? '—' }}
                                    @else
                                        <span class="text-muted fst-italic">Restricted</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Relation</td>
                                <td>{{ $details->guardian_relation ?? ($details->guardian_relation_to_learner_other ?? '—') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sex</td>
                                <td>{{ $details->guardian_sex ?? '—' }}</td>
                            </tr>
                            @if($isDistrictOfficerOrAbove)
                            <tr>
                                <td class="text-muted">NIN</td>
                                <td>{{ $details->guardian_nin ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Date of Birth</td>
                                <td>{{ $details->guardian_date_of_birth ?? '—' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted">Phone 1</td>
                                <td>{{ $details->guardian_phone_1 ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Phone 2</td>
                                <td>{{ $details->guardian_phone_2 ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $details->guardian_email ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Address</td>
                                <td>{{ $details->guardian_address ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @else
                        <p class="text-muted fst-italic mb-0">No guardian recorded.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── SPECIAL NEEDS ── --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-bold">Special Needs</div>
                <div class="card-body">
                    @php
                        $disabilities = [
                            'Vision'         => $details->disability_vision,
                            'Hearing'        => $details->disability_hearing,
                            'Mobility'       => $details->disability_mobility,
                            'Cognition'      => $details->disability_cognition,
                            'Self-Care'      => $details->disability_selfcare,
                            'Communication'  => $details->disability_communication,
                            'Other Condition'=> $details->disability_other_condition,
                        ];
                        $hasDisability = array_filter($disabilities);
                    @endphp
                    @if($hasDisability)
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            @foreach($disabilities as $label => $value)
                                @if($value)
                                <tr>
                                    <td class="text-muted" style="width:50%">{{ $label }}</td>
                                    <td>{{ $value }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <p class="text-muted fst-italic mb-0">No special needs recorded.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── ATTENDANCE SUMMARY ── --}}
        @if($attendanceSummary && $attendanceSummary->total_days > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-bold">Attendance Summary</div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <div class="fs-4 fw-bold">{{ $attendanceSummary->total_days }}</div>
                            <div class="text-muted small">Total Days</div>
                        </div>
                        <div class="col">
                            <div class="fs-4 fw-bold text-success">{{ $attendanceSummary->present_full }}</div>
                            <div class="text-muted small">Present</div>
                        </div>
                        <div class="col">
                            <div class="fs-4 fw-bold text-warning">{{ $attendanceSummary->late }}</div>
                            <div class="text-muted small">Late</div>
                        </div>
                        <div class="col">
                            <div class="fs-4 fw-bold text-info">{{ $attendanceSummary->half_day }}</div>
                            <div class="text-muted small">Half Day</div>
                        </div>
                        <div class="col">
                            <div class="fs-4 fw-bold text-danger">{{ $attendanceSummary->absent }}</div>
                            <div class="text-muted small">Absent</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── REPORT CARD ── --}}
        @if(count($reportCard) > 0)
        @php
            $gradeColour = fn($g) => match($g) {
                'A'     => 'bg-success text-white',
                'B'     => 'bg-info text-white',
                'C'     => 'bg-warning text-dark',
                'D'     => 'bg-secondary text-white',
                'F'     => 'bg-danger text-white',
                default => 'bg-light text-muted border',
            };
            $calcGrade = function($avg) {
                if ($avg === null) return '—';
                if ($avg >= 75) return 'A';
                if ($avg >= 65) return 'B';
                if ($avg >= 50) return 'C';
                if ($avg >= 40) return 'D';
                return 'F';
            };
            $termAvg = function($t) {
                if (!$t || $t->assessment_1_score === null || $t->assessment_2_score === null) return null;
                return ($t->assessment_1_score + $t->assessment_2_score) / 2;
            };
            $fmtScore = fn($v) => $v !== null
                ? ($v == floor($v) ? (int)$v : number_format($v, 1))
                : '—';
        @endphp

        {{-- Year filter --}}
        <div class="col-12">
            <div class="d-flex align-items-center gap-2">
                <label for="yearFilter" class="fw-semibold mb-0 text-nowrap">Filter by Academic Year:</label>
                <select id="yearFilter" class="form-select form-select-sm" style="width:auto">
                    @foreach($reportCard as $year => $_)
                    <option value="{{ $year }}" {{ $loop->first ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @foreach($reportCard as $year => $yearData)
        <div class="col-12 report-card-year" data-year="{{ $year }}">
            <div class="card">
                <div class="card-header fw-bold d-flex align-items-baseline gap-3">
                    <span>Academic Performance &mdash; {{ $year }}</span>
                    @if($yearData['class_label'])
                        <span class="text-muted fw-normal small">{{ $yearData['class_label'] }}</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0" style="min-width:900px">
                            <thead>
                                <tr class="table-light">
                                    <th rowspan="2" class="align-middle" style="min-width:160px">Subject</th>
                                    <th colspan="4" class="text-center border-start">First Term</th>
                                    <th colspan="4" class="text-center border-start">Second Term</th>
                                    <th colspan="4" class="text-center border-start">Third Term</th>
                                    <th rowspan="2" class="text-center align-middle border-start fw-bold table-primary" style="width:90px">Yearly Average</th>
                                </tr>
                                <tr class="table-light">
                                    {{-- First Term sub-headers --}}
                                    <th class="text-center small border-start" style="width:75px">Test 1</th>
                                    <th class="text-center small" style="width:75px">Test 2</th>
                                    <th class="text-center small" style="width:60px">Avg</th>
                                    <th class="text-center small" style="width:55px">Grade</th>
                                    {{-- Second Term sub-headers --}}
                                    <th class="text-center small border-start" style="width:75px">Test 1</th>
                                    <th class="text-center small" style="width:75px">Test 2</th>
                                    <th class="text-center small" style="width:60px">Avg</th>
                                    <th class="text-center small" style="width:55px">Grade</th>
                                    {{-- Third Term sub-headers --}}
                                    <th class="text-center small border-start" style="width:75px">Test 1</th>
                                    <th class="text-center small" style="width:75px">Final Exam</th>
                                    <th class="text-center small" style="width:60px">Avg</th>
                                    <th class="text-center small" style="width:55px">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($yearData['subjects'] as $subject)
                                @php
                                    $ft = $subject['first_term'];
                                    $st = $subject['second_term'];
                                    $tt = $subject['third_term'];
                                    $ftAvg = $termAvg($ft);
                                    $stAvg = $termAvg($st);
                                    $ttAvg = $termAvg($tt);
                                    $termAvgs = array_filter([$ftAvg, $stAvg, $ttAvg], fn($a) => $a !== null);
                                    $yearlyAvg = count($termAvgs) > 0 ? array_sum($termAvgs) / count($termAvgs) : null;
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $subject['subject_name'] }}</td>
                                    {{-- First Term --}}
                                    <td class="text-center border-start">{{ $fmtScore($ft->assessment_1_score ?? null) }}</td>
                                    <td class="text-center">{{ $fmtScore($ft->assessment_2_score ?? null) }}</td>
                                    <td class="text-center">{{ $ftAvg !== null ? round($ftAvg) : '—' }}</td>
                                    <td class="text-center">
                                        @php $g = $calcGrade($ftAvg); @endphp
                                        @if($ftAvg !== null)<span class="badge {{ $gradeColour($g) }}">{{ $g }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                    {{-- Second Term --}}
                                    <td class="text-center border-start">{{ $fmtScore($st->assessment_1_score ?? null) }}</td>
                                    <td class="text-center">{{ $fmtScore($st->assessment_2_score ?? null) }}</td>
                                    <td class="text-center">{{ $stAvg !== null ? round($stAvg) : '—' }}</td>
                                    <td class="text-center">
                                        @php $g = $calcGrade($stAvg); @endphp
                                        @if($stAvg !== null)<span class="badge {{ $gradeColour($g) }}">{{ $g }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                    {{-- Third Term --}}
                                    <td class="text-center border-start">{{ $fmtScore($tt->assessment_1_score ?? null) }}</td>
                                    <td class="text-center">{{ $fmtScore($tt->assessment_2_score ?? null) }}</td>
                                    <td class="text-center">{{ $ttAvg !== null ? round($ttAvg) : '—' }}</td>
                                    <td class="text-center">
                                        @php $g = $calcGrade($ttAvg); @endphp
                                        @if($ttAvg !== null)<span class="badge {{ $gradeColour($g) }}">{{ $g }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                    {{-- Yearly Average --}}
                                    <td class="text-center fw-bold border-start table-primary">{{ $yearlyAvg !== null ? round($yearlyAvg) : '—' }}</td>
                                </tr>
                                @endforeach

                                @php
                                    $ftSum = null; $stSum = null; $ttSum = null;
                                    $ftN   = 0;    $stN   = 0;    $ttN   = 0;
                                    foreach ($yearData['subjects'] as $s) {
                                        $a = $termAvg($s['first_term']);
                                        if ($a !== null) { $ftSum = ($ftSum ?? 0) + $a; $ftN++; }
                                        $a = $termAvg($s['second_term']);
                                        if ($a !== null) { $stSum = ($stSum ?? 0) + $a; $stN++; }
                                        $a = $termAvg($s['third_term']);
                                        if ($a !== null) { $ttSum = ($ttSum ?? 0) + $a; $ttN++; }
                                    }
                                    $ftPct = $ftN > 0 ? round(($ftSum / (100 * $ftN)) * 100) : null;
                                    $stPct = $stN > 0 ? round(($stSum / (100 * $stN)) * 100) : null;
                                    $ttPct = $ttN > 0 ? round(($ttSum / (100 * $ttN)) * 100) : null;
                                    $termSums = array_filter([$ftSum, $stSum, $ttSum], fn($v) => $v !== null);
                                    $yearlySum = count($termSums) > 0 ? array_sum($termSums) / count($termSums) : null;
                                    $termPcts = array_filter([$ftPct, $stPct, $ttPct], fn($v) => $v !== null);
                                    $yearlyPct = count($termPcts) > 0 ? round(array_sum($termPcts) / count($termPcts)) : null;
                                    $conductInfo = function($pct) {
                                        if ($pct === null) return null;
                                        if ($pct <= 49) return ['text' => 'Poor',      'badge' => 'bg-danger text-white'];
                                        if ($pct <= 69) return ['text' => 'Good',      'badge' => 'bg-warning text-dark'];
                                        if ($pct <= 89) return ['text' => 'Very Good', 'badge' => 'bg-primary text-white'];
                                        return                  ['text' => 'Excellent', 'badge' => 'bg-success text-white'];
                                    };
                                @endphp

                                {{-- Total --}}
                                <tr class="table-light fw-bold border-top">
                                    <td>Total</td>
                                    <td colspan="4" class="text-center border-start">{{ $ftSum !== null ? round($ftSum) : '—' }}</td>
                                    <td colspan="4" class="text-center border-start">{{ $stSum !== null ? round($stSum) : '—' }}</td>
                                    <td colspan="4" class="text-center border-start">{{ $ttSum !== null ? round($ttSum) : '—' }}</td>
                                    <td class="text-center fw-bold border-start table-primary">{{ $yearlySum !== null ? round($yearlySum) : '—' }}</td>
                                </tr>

                                {{-- Percentage --}}
                                <tr class="table-light fw-bold">
                                    <td>Percentage</td>
                                    <td colspan="4" class="text-center border-start">{{ $ftPct !== null ? $ftPct . '%' : '—' }}</td>
                                    <td colspan="4" class="text-center border-start">{{ $stPct !== null ? $stPct . '%' : '—' }}</td>
                                    <td colspan="4" class="text-center border-start">{{ $ttPct !== null ? $ttPct . '%' : '—' }}</td>
                                    <td class="text-center fw-bold border-start table-primary">{{ $yearlyPct !== null ? $yearlyPct . '%' : '—' }}</td>
                                </tr>

                                {{-- Conduct --}}
                                @php $ftC = $conductInfo($ftPct); $stC = $conductInfo($stPct); $ttC = $conductInfo($ttPct); $yC = $conductInfo($yearlyPct); @endphp
                                <tr class="table-light fw-bold">
                                    <td>Conduct</td>
                                    <td colspan="4" class="text-center border-start">
                                        @if($ftC)<span class="badge {{ $ftC['badge'] }}">{{ $ftC['text'] }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                    <td colspan="4" class="text-center border-start">
                                        @if($stC)<span class="badge {{ $stC['badge'] }}">{{ $stC['text'] }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                    <td colspan="4" class="text-center border-start">
                                        @if($ttC)<span class="badge {{ $ttC['badge'] }}">{{ $ttC['text'] }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                    <td class="text-center fw-bold border-start table-primary">
                                        @if($yC)<span class="badge {{ $yC['badge'] }}">{{ $yC['text'] }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                </tr>

                                {{-- Performance Outcome --}}
                                @php
                                    $outcome = function($pct) {
                                        if ($pct === null) return null;
                                        return $pct > 49
                                            ? ['text' => 'Promoted',       'badge' => 'bg-success text-white']
                                            : ['text' => 'To be repeated', 'badge' => 'bg-danger text-white'];
                                    };
                                    $ftO = $outcome($ftPct); $stO = $outcome($stPct); $ttO = $outcome($ttPct); $yO = $outcome($yearlyPct);
                                @endphp
                                <tr class="table-light fw-bold">
                                    <td>Performance Outcome</td>
                                    <td colspan="12" class="border-start"></td>
                                    <td class="text-center fw-bold border-start table-primary">
                                        @if($yO)<span class="badge {{ $yO['badge'] }}">{{ $yO['text'] }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif

        {{-- ── CASS CARDS ── --}}
        @php
            $cassConfig = [
                'npse'   => ['title' => 'CASS for NPSE',   'subtitle' => 'Primary 4, 5 & 6'],
                'bece'   => ['title' => 'CASS for BECE',   'subtitle' => 'JSS 1, 2 & 3'],
                'wassce' => ['title' => 'CASS for WASSCE', 'subtitle' => 'SSS 1, 2 & 3'],
            ];
            $cassGrade = function($score) {
                if ($score === null) return null;
                if ($score >= 75) return ['label' => 'A', 'class' => 'bg-success text-white'];
                if ($score >= 65) return ['label' => 'B', 'class' => 'bg-info text-white'];
                if ($score >= 50) return ['label' => 'C', 'class' => 'bg-warning text-dark'];
                if ($score >= 40) return ['label' => 'D', 'class' => 'bg-secondary text-white'];
                return ['label' => 'F', 'class' => 'bg-danger text-white'];
            };
            $fmtCa = fn($v) => $v !== null ? number_format((float)$v, 1) : '—';
            // Weighted CASS: (L1×1 + L2×1 + L3×2) / 4 — denominator is always 4
            $cassWeights    = [0 => 1, 1 => 1, 2 => 2];
            $calcWeightedCa = function(array $levelKeys, array $scores) use ($cassWeights) {
                $wSum = 0; $hasAny = false;
                foreach ($levelKeys as $i => $lk) {
                    $v = $scores[$lk] ?? null;
                    if ($v !== null) { $wSum += $v * $cassWeights[$i]; $hasAny = true; }
                }
                return $hasAny ? round($wSum / array_sum($cassWeights), 1) : null;
            };
        @endphp
        @foreach($cassConfig as $type => $cfg)
        @php
            $typeData  = $cassData[$type];
            $levelKeys = array_values(array_intersect($cassLevelOrder[$type], array_keys($typeData['levels'])));
        @endphp
        @if(count($typeData['subjects']) > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-bold d-flex align-items-baseline gap-3">
                    <span>{{ $cfg['title'] }}</span>
                    <span class="text-muted fw-normal small">{{ $cfg['subtitle'] }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:160px">Subject</th>
                                    @foreach($levelKeys as $lk)
                                        <th class="text-center" style="width:110px">{{ $typeData['levels'][$lk] }} CA</th>
                                    @endforeach
                                    <th class="text-center table-primary fw-bold" style="width:110px">CASS</th>
                                    <th class="text-center" style="width:75px">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($typeData['subjects'] as $subj)
                                @php
                                    $cassAvg = $calcWeightedCa($levelKeys, $subj['scores']);
                                    $g       = $cassGrade($cassAvg);
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $subj['subject_name'] }}</td>
                                    @foreach($levelKeys as $lk)
                                        <td class="text-center">{{ $fmtCa($subj['scores'][$lk] ?? null) }}</td>
                                    @endforeach
                                    <td class="text-center table-primary fw-bold">{{ $fmtCa($cassAvg) }}</td>
                                    <td class="text-center">
                                        @if($g)<span class="badge {{ $g['class'] }}">{{ $g['label'] }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                </tr>
                                @endforeach
                                @php
                                    // Overall row: mean of per-subject weighted CASS averages
                                    $allCassScores = [];
                                    foreach ($typeData['subjects'] as $subj) {
                                        $ca = $calcWeightedCa($levelKeys, $subj['scores']);
                                        if ($ca !== null) $allCassScores[] = $ca;
                                    }
                                    $overallCass  = count($allCassScores) > 0
                                        ? round(array_sum($allCassScores) / count($allCassScores), 1)
                                        : null;
                                    $overallGrade = $cassGrade($overallCass);
                                    // Per-level totals for overall row
                                    $levelTotals = [];
                                    foreach ($levelKeys as $lk) {
                                        $vals = array_filter(array_column(
                                            array_map(fn($s) => ['v' => $s['scores'][$lk] ?? null], $typeData['subjects']),
                                            'v'
                                        ), fn($v) => $v !== null);
                                        $levelTotals[$lk] = count($vals) > 0
                                            ? round(array_sum($vals) / count($vals), 1)
                                            : null;
                                    }
                                @endphp
                                <tr class="table-light fw-bold border-top">
                                    <td>Overall</td>
                                    @foreach($levelKeys as $lk)
                                        <td class="text-center">{{ $fmtCa($levelTotals[$lk]) }}</td>
                                    @endforeach
                                    <td class="text-center table-primary">{{ $fmtCa($overallCass) }}</td>
                                    <td class="text-center">
                                        @if($overallGrade)<span class="badge {{ $overallGrade['class'] }}">{{ $overallGrade['label'] }}</span>@else<span class="text-muted">—</span>@endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach

        {{-- ── SCHOOL HISTORY ── --}}
        @if(count($schoolHistory) > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-bold">School History</div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>School</th>
                                <th>Year Group</th>
                                <th>Classroom</th>
                                <th>Academic Year</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schoolHistory as $row)
                            <tr>
                                <td>{{ $row->school_name ?? '—' }}</td>
                                <td>{{ $row->year_group ?? '—' }}</td>
                                <td>{{ $row->classroom ?? '—' }}</td>
                                <td>{{ $row->academic_year ?? '—' }}</td>
                                <td>{{ $row->start_date ?? '—' }}</td>
                                <td>{{ $row->end_date ?? '—' }}</td>
                                <td>{{ $row->end_reason ?? '—' }}</td>
                                <td>
                                    @if($row->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Ended</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ── ATTENDANCE RECORDS ── --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span>Attendance Records</span>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="date" id="att-start" class="form-control form-control-sm" style="width:150px"
                               value="{{ \Carbon\Carbon::now()->subDays(30)->toDateString() }}">
                        <span>to</span>
                        <input type="date" id="att-end" class="form-control form-control-sm" style="width:150px"
                               value="{{ \Carbon\Carbon::now()->toDateString() }}">
                        <button class="btn btn-sm btn-primary" id="btn-att-load">Load</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table id="dt-attendance" class="table table-sm table-bordered mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>AM</th>
                                <th>PM</th>
                                <th>Absent Reason</th>
                                <th>Attendance Status</th>
                                <th>School</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- end row --}}
    @endif

</div>

<script>
    const learnerUuid = '{{ $learnerUuid }}';
    let attTable;

    function loadAttendance() {
        const startDate = document.getElementById('att-start').value;
        const endDate   = document.getElementById('att-end').value;

        fetch('/api/learner-profile/attendance-records/' + learnerUuid, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ startDate, endDate })
        })
        .then(r => r.json())
        .then(resp => {
            const rows = resp.data || [];
            if (attTable) {
                attTable.clear().rows.add(rows).draw();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const yearFilter = document.getElementById('yearFilter');
        if (yearFilter) {
            function applyYearFilter() {
                const selected = yearFilter.value;
                document.querySelectorAll('.report-card-year').forEach(el => {
                    el.style.display = el.dataset.year === selected ? '' : 'none';
                });
            }
            yearFilter.addEventListener('change', applyYearFilter);
            applyYearFilter(); // show only the most recent year on load
        }

        function getAttendanceInfo(amOid, pmOid) {
            if (amOid === 'present' && pmOid === 'present') return { label: 'Present',         color: '#c7eed8' };
            if (amOid === 'absent'  && pmOid === 'absent')  return { label: 'Absent',          color: '#f7c6c5' };
            if (amOid === 'absent'  && pmOid === 'present') return { label: 'Late',            color: '#fffacc' };
            if (amOid === 'present' && pmOid === 'absent')  return { label: 'Early Departure', color: '#FFDDB0' };
            if (amOid || pmOid)                             return { label: '',                color: '#d6d8db' };
            return { label: '—', color: null };
        }

        attTable = $('#dt-attendance').DataTable({
            data: [],
            order: [[0, 'desc']],
            createdRow: function(row, data) {
                const info = getAttendanceInfo(data.attendance_am_status_oid, data.attendance_pm_status_oid);
                if (info.color) $(row).css('background-color', info.color);
            },
            columns: [
                { data: 'date_formatted', defaultContent: '—' },
                { data: 'attendance_am',  defaultContent: '—', className: 'text-center' },
                { data: 'attendance_pm',  defaultContent: '—', className: 'text-center' },
                {
                    data: 'absent_reason',
                    defaultContent: '—',
                    render: function(data, type, full) {
                        if (full.absent_reason_other && !data) return full.absent_reason_other;
                        return data || '—';
                    }
                },
                {
                    data: 'attendance_am_status_oid',
                    className: 'text-center fw-semibold',
                    defaultContent: '—',
                    render: function(data, type, full) {
                        return getAttendanceInfo(full.attendance_am_status_oid, full.attendance_pm_status_oid).label;
                    }
                },
                { data: 'school_name', defaultContent: '—' },
            ]
        });

        document.getElementById('btn-att-load').addEventListener('click', loadAttendance);
        loadAttendance();
    });
</script>
@endsection
