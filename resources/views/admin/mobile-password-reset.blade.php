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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center"><h3>Mobile Password Reset Requests</h3></div>
                    <table id="dt-mobile-password-reset" data-csrf="{{csrf_token()}}" class="table table-sm small-font table-responsive-lg" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-left">Time of request</th>
                            <th class="text-left">Username</th>
                            <th class="text-left">Name</th>
                            <th class="text-left">Permission Level</th>
                            <th class="text-left">Phone</th>
                            <th class="text-left">Status</th>
                            <th class="text-left">Internal Remarks</th>
                            <th class="text-left"></th>
                            <th class="text-left"></th>
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
        String.prototype.toProperCase = function () {
            return this.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substring(1).toLowerCase();});
        };

        // MOBILE PASSWORD RESET
        let tableDtMobilePasswordReset = $('#dt-mobile-password-reset').DataTable({
            // lengthChange: true,
            // lengthMenu: [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
            // pageLength: 5,
            // dom: "Bfrtip",
            language: {
                emptyTable: 'No requests for review'
            },
            ajax: {
                url: "/api/dte/dt-mobile-password-reset",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-mobile-password-reset').data("csrf")
                }
            },
            serverSide: true,
            order: [[0,'desc']],
            columns: [
                {data: "android_password_resets.created_at"},
                {data: "android_password_resets.username"},
                {data: "user.name"},
                {data: "user_type.type_name"},
                {data: "user.phone"},
                {
                    data: "android_password_resets.status",
                    render: function ( data, type, row, meta ) {
                        let fText = data.toProperCase()
                        switch(data){
                            case 'approved':
                                return "<span class='text-success'>"+fText+"</span>"
                            case 'cancelled':
                                return "<span class='text-warning'>"+fText+"</span>"
                            case 'rejected':
                                return "<span class='text-danger'>"+fText+"</span>"
                            case 'pending':
                                return "<span class='text-info'>"+fText+"</span>"
                            default:
                                return fText
                        }

                    },
                },
                {data: "android_password_resets.remarks"},
                {
                    data: null,
                    render: function ( data, type, row, meta ) {
                        return (row.android_password_resets.status !== 'pending') ? '' : '<button type="button" class="btn btn-outline-success btn-sm"><i class="fa fa-check"></i> Approve</button>';
                    },
                    className: 'row-edit dt-center button-approve',
                    orderable: false
                },
                {
                    data: null,
                    render: function ( data, type, row, meta ) {
                        return (row.android_password_resets.status !== 'pending') ? '' : '<button type="button" class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> Reject</button>';
                    },
                    className: 'row-edit dt-center button-reject',
                    orderable: false
                }
            ],
        });

        @php
        $approveDialogMessage = '
        <p>How did you confirm the identity for the Teacher making the request?</p>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="radio-approve" id="chk-1" value="confirmed_phone">
          <label class="form-check-label" for="chk-1">
            Teacher identity was confirmed over phone call
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="radio-approve" id="chk-2" value="confirmed_in_person">
          <label class="form-check-label" for="chk-2">
            Teacher identity was confirmed in person
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="radio-approve" id="chk-3" value="other">
          <label class="form-check-label" for="chk-3">
            Other (specify in remarks below)
          </label>
        </div>
        <p>
            <div class="form-group">
               <label for="dialog-text-area">Any other remarks</label>
               <textarea class="form-control" id="dialog-remarks" rows="3"></textarea>
            </div>
        </p>
        <p>
            <div id="dialog-approve-feedback" class="text-danger"></div>
        </p>
        ';
		$approveDialogMessage = str_replace(["\r", "\n"], '', $approveDialogMessage);
        $rejectDialogMessage = '
        <p>Why did you decide to reject the requested password change?</p>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="radio-reject" id="chk-1" value="unable_to_contact">
          <label class="form-check-label" for="chk-1">
            The teacher could not be contacted through their phone number
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="radio-reject" id="chk-2" value="teacher_requested">
          <label class="form-check-label" for="chk-2">
            The teacher requested for it to be cancelled
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="radio-reject" id="chk-3" value="other">
          <label class="form-check-label" for="chk-3">
            Other (specify in remarks below)
          </label>
        </div>
        <p>
            <div class="form-group">
               <label for="dialog-text-area">Any other remarks</label>
               <textarea class="form-control" id="dialog-remarks" rows="3"></textarea>
            </div>
        </p>
        <p>
            <div id="dialog-reject-feedback" class="text-danger"></div>
        </p>
        ';
		$rejectDialogMessage = str_replace(["\r", "\n"], '', $rejectDialogMessage);
        @endphp

        $('#dt-mobile-password-reset').on('click', 'td.button-approve', function (e) {
            e.preventDefault();

            let rowData = tableDtMobilePasswordReset.row( this ).data();
            let rowId = rowData.android_password_resets.id;
            let userFullName = rowData.user.name;

            bootbox.dialog({
                title: 'Approve password change request for: ' + userFullName,
                message: '{!! $approveDialogMessage !!}',
                centerVertical: true,
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Confirm',
                        callback: function (result) {

                            let radioVal = $('input[name="radio-approve"]:checked').val();
                            let remarks = $('#dialog-remarks').val();
                            // console.log(radioVal);
                            if(radioVal == undefined){
                                $('#dialog-approve-feedback').text("No selection made");
                                return false
                            }else if(radioVal === "other" && remarks.trim().length === 0){
                                $('#dialog-approve-feedback').text("A remark must be specified for 'Other' selection");
                                return false
                            }

                            let remarksCombined = (remarks.trim().length === 0) ? radioVal : radioVal + '|' + remarks;

                            $.ajax({
                                url:        '/admin/password-reset/approve-reset',
                                dataType:   'json',
                                data:       'id='+rowId+'&remarks='+remarksCombined,
                                type:       'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                success:        function(data){
                                    //refresh dt
                                    tableDtMobilePasswordReset.ajax.reload();
                                },
                                error:          function(xhr, ajaxOptions, thrownError) {
                                    console.log(xhr.responseText);
                                }
                            });
                        }
                    }
                },

            });
        } );

        $('#dt-mobile-password-reset').on('click', 'td.button-reject', function (e) {
            e.preventDefault();

            let rowData = tableDtMobilePasswordReset.row( this ).data();
            let rowId = rowData.android_password_resets.id;
            let userFullName = rowData.user.name;

            bootbox.dialog({
                title: 'Reject password change request for: ' + userFullName,
                message: '{!! $rejectDialogMessage !!}',
                centerVertical: true,
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Confirm',
                        callback: function (result) {

                            let radioVal = $('input[name="radio-reject"]:checked').val();
                            let remarks = $('#dialog-remarks').val();
                            // console.log(radioVal);
                            if(radioVal == undefined){
                                $('#dialog-reject-feedback').text("No selection made");
                                return false
                            }else if(radioVal === "other" && remarks.trim().length === 0){
                                $('#dialog-reject-feedback').text("A remark must be specified for 'Other' selection");
                                return false
                            }

                            let remarksCombined = (remarks.trim().length === 0) ? radioVal : radioVal + '|' + remarks;

                            $.ajax({
                                url:        '/admin/password-reset/reject-reset',
                                dataType:   'json',
                                data:       'id='+rowId+'&remarks='+remarksCombined,
                                type:       'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                success:        function(data){
                                    //refresh dt
                                    tableDtMobilePasswordReset.ajax.reload();
                                },
                                error:          function(xhr, ajaxOptions, thrownError) {
                                    console.log(xhr.responseText);
                                }
                            });
                        }
                    }
                },

            });
        } );

    </script>
@endsection
