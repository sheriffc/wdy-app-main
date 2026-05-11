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
                                    @if(!$row->deleted_at)
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
        attTable = $('#dt-attendance').DataTable({
            data: [],
            order: [[0, 'desc']],
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
                { data: 'school_name', defaultContent: '—' },
            ]
        });

        document.getElementById('btn-att-load').addEventListener('click', loadAttendance);
        loadAttendance();
    });
</script>
@endsection
