@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Academic Year Management</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreateAcademicYear">
                            + New Academic Year
                        </button>
                    </div>
                    <table id="dt-academic-years" class="table table-bordered table-sm small-font align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>Year Name</th>
                                <th class="text-center">Year</th>
                                <th class="text-center">Date From</th>
                                <th class="text-center">Date To</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="academic-year-rows"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create / Edit Modal -->
<div class="modal fade" id="modalCreateAcademicYear" tabindex="-1" aria-labelledby="modalAcademicYearLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formAcademicYear">
                @csrf
                <input type="hidden" id="ayUuid" name="uuid">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAcademicYearLabel">New Academic Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="ayFormAlert" class="alert d-none"></div>
                    <div class="mb-3">
                        <label for="ayYear" class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="ayYear" name="academic_year" min="2000" max="2100" placeholder="e.g. 2026" required>
                        <div class="form-text">Enter the starting year. The year name will be set automatically (e.g. 2026 → 2026/2027).</div>
                    </div>
                    <div class="mb-3">
                        <label for="ayDateFrom" class="form-label fw-semibold">Date From <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="ayDateFrom" name="date_from" required>
                    </div>
                    <div class="mb-3">
                        <label for="ayDateTo" class="form-label fw-semibold">Date To <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="ayDateTo" name="date_to" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="aySetActive" name="set_active">
                        <label class="form-check-label" for="aySetActive">
                            Set as active academic year
                            <small class="text-muted d-block">This will deactivate the currently active year.</small>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveAcademicYear">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Activate Confirmation Modal -->
<div class="modal fade" id="modalActivateAcademicYear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activate Academic Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Set <strong id="activateYearName"></strong> as the active academic year?</p>
                <p class="text-warning small mb-0">The currently active year will be deactivated.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" id="btnConfirmActivate">Activate</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let activateTargetUuid = null;
    let editMode = false;

    // ── Load table ──────────────────────────────────────────────────────────────
    function loadAcademicYears() {
        fetch('/api/admin/academic-years', {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('academic-year-rows');
            tbody.innerHTML = '';
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No academic years found.</td></tr>';
                return;
            }
            const today = new Date(); today.setHours(0,0,0,0);
            data.forEach(row => {
                const isActive = parseInt(row.active) === 1;
                const isPast   = row.date_to && new Date(row.date_to) < today;
                let statusBadge;
                if (isActive)      statusBadge = '<span class="badge bg-success">Active</span>';
                else if (isPast)   statusBadge = '<span class="badge bg-danger">Past</span>';
                else               statusBadge = '<span class="badge bg-secondary">Inactive</span>';

                tbody.innerHTML += `
                    <tr>
                        <td>${row.academic_year_name ?? '—'}</td>
                        <td class="text-center">${row.academic_year ?? '—'}</td>
                        <td class="text-center">${row.date_from ?? '—'}</td>
                        <td class="text-center">${row.date_to ?? '—'}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center">
                            ${!isPast ? `
                            <button class="btn btn-outline-secondary btn-sm me-1"
                                onclick="openEditModal('${row.uuid}','${row.academic_year}','${row.date_from}','${row.date_to}','${row.academic_year_name}')">
                                Edit
                            </button>` : ''}
                            ${!isActive && !isPast ? `
                            <button class="btn btn-outline-success btn-sm"
                                onclick="openActivateModal('${row.uuid}','${row.academic_year_name}')">
                                Activate
                            </button>` : ''}
                        </td>
                    </tr>`;
            });
        });
    }

    // ── Create / Edit modal ──────────────────────────────────────────────────────
    function openEditModal(uuid, year, dateFrom, dateTo, yearName) {
        editMode = true;
        document.getElementById('modalAcademicYearLabel').textContent = 'Edit Academic Year';
        document.getElementById('ayUuid').value = uuid;
        document.getElementById('ayYear').value = year;
        document.getElementById('ayDateFrom').value = dateFrom;
        document.getElementById('ayDateTo').value = dateTo;
        document.getElementById('aySetActive').checked = false;
        document.getElementById('ayFormAlert').classList.add('d-none');
        new bootstrap.Modal(document.getElementById('modalCreateAcademicYear')).show();
    }

    document.getElementById('modalCreateAcademicYear').addEventListener('show.bs.modal', function (e) {
        if (!editMode) {
            document.getElementById('modalAcademicYearLabel').textContent = 'New Academic Year';
            document.getElementById('formAcademicYear').reset();
            document.getElementById('ayUuid').value = '';
            document.getElementById('ayFormAlert').classList.add('d-none');
        }
    });
    document.getElementById('modalCreateAcademicYear').addEventListener('hidden.bs.modal', function () {
        editMode = false;
    });

    // ── Save (create or update) ──────────────────────────────────────────────────
    document.getElementById('formAcademicYear').addEventListener('submit', function (e) {
        e.preventDefault();
        const alertEl = document.getElementById('ayFormAlert');
        alertEl.classList.add('d-none');

        const payload = {
            uuid:          document.getElementById('ayUuid').value || null,
            academic_year: document.getElementById('ayYear').value,
            date_from:     document.getElementById('ayDateFrom').value,
            date_to:       document.getElementById('ayDateTo').value,
            set_active:    document.getElementById('aySetActive').checked ? 1 : 0,
        };

        document.getElementById('btnSaveAcademicYear').disabled = true;

        fetch('/api/admin/academic-years/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('btnSaveAcademicYear').disabled = false;
            if (data.status) {
                bootstrap.Modal.getInstance(document.getElementById('modalCreateAcademicYear')).hide();
                loadAcademicYears();
            } else {
                alertEl.className = 'alert alert-danger';
                alertEl.textContent = data.message || 'An error occurred.';
            }
        })
        .catch(() => {
            document.getElementById('btnSaveAcademicYear').disabled = false;
            alertEl.className = 'alert alert-danger';
            alertEl.textContent = 'A network error occurred. Please try again.';
        });
    });

    // ── Activate modal ───────────────────────────────────────────────────────────
    function openActivateModal(uuid, yearName) {
        activateTargetUuid = uuid;
        document.getElementById('activateYearName').textContent = yearName;
        new bootstrap.Modal(document.getElementById('modalActivateAcademicYear')).show();
    }

    document.getElementById('btnConfirmActivate').addEventListener('click', function () {
        if (!activateTargetUuid) return;
        this.disabled = true;

        fetch('/api/admin/academic-years/activate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ uuid: activateTargetUuid })
        })
        .then(r => r.json())
        .then(data => {
            this.disabled = false;
            bootstrap.Modal.getInstance(document.getElementById('modalActivateAcademicYear')).hide();
            if (data.status) {
                loadAcademicYears();
            } else {
                alert(data.message || 'Could not activate academic year.');
            }
        })
        .catch(() => {
            this.disabled = false;
            alert('A network error occurred. Please try again.');
        });
    });

    loadAcademicYears();
</script>
@endsection
