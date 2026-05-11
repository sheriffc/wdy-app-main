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
                    <div class="text-center"><h3>Manage Users</h3></div>
                    <table id="dt-users" data-csrf="{{csrf_token()}}" class="table table-sm small-font table-responsive-lg" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-left">Username</th>
                            <th class="text-left">Full Name</th>
                            <th class="text-left">Email</th>
                            <th class="text-left">Granted Role</th>
                            <th class="text-left">Permission Level</th>
                            <th class="text-left">Mobile Access</th>
                            <th class="text-left">Active</th>
                            <th class="text-left">Created At</th>
                            <th class="text-left">Email Verified At</th>
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

        // CUSTOM ASSIGNMENT
        let editorDtUsersCustom = new $.fn.dataTable.Editor({
            ajax: {
                url: "/api/dte/dt-users-scope-custom",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function ( d ) {
                    var selected = tableDtUsers.row({selected: true});
                    console.log(d);
                    if (selected.any()) {
                        d.id = selected.data().user.id;
                    }
                }
            },
            // table: "#dt-users-custom",
            fields: [
                {
                    label: "School:",
                    name: "user_scope_custom_assignment.school_uuid",
                    type: 'select',
                    // placeholderDisabled: false,
                    // placeholder: ""
                }
            ],
            i18n: {
                create: {
                    submit: "Add"
                }
            }
        });

        // GROUP ASSIGNMENT
        let editorDtUsersGroup = new $.fn.dataTable.Editor({
            ajax: {
                url: "/api/dte/dt-users-scope-group",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function ( d ) {
                    var selected = tableDtUsers.row({selected: true});
                    if (selected.any()) {
                        d.id = selected.data().user.id;
                    }
                }
            },
            fields: [
                {
                    label: "Group:",
                    name: "user_scope_group_user_link.group_id",
                    type: 'select',
                    // placeholderDisabled: false,
                    // placeholder: ""
                }
            ],
            i18n: {
                create: {
                    submit: "Add"
                }
            }
        });

        // MANAGE USERS
        let tableDtUsers = $('#dt-users').DataTable({
            // lengthChange: true,
            // lengthMenu: [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
            // pageLength: 5,
            // dom: "Bfrtip",
            ajax: {
                url: "/api/dte/dt-users",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-users').data("csrf")
                }
            },
            serverSide: true,
            // order: [[1,'asc'],[0,'asc']],
            columns: [
                {data: "user.username"},
                {data: "user.name"},
                {data: "user.email"},
                {data: "user.granted_role"},
                {data: "user_type.type_name"},
                {
                    data: "user.mobile_access",
                    render: function ( data, type, row ) {
                        if ( data ) {
                            return '<input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked disabled>';
                        }else{
                            return '<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" disabled>';
                        }
                    },
                },
                {
                    data: "user.active",
                    render: function ( data, type, row ) {
                        if ( data ) {
                            return '<input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked disabled>';
                        }else{
                            return '<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" disabled>';
                        }
                    },
                },
                {data: "user.created_at"},
                {data: "user.email_verified_at"},
            ],
            select: true,
        });

        let editorDtUsers = new $.fn.dataTable.Editor({
            ajax: {
                url: "/api/dte/dt-users",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-users').data("csrf")
                }
            },
            table: "#dt-users",
            fields: [
                {
                    label: "ID:",
                    name: "user.id",
                    type: 'hidden',
                }, {
                    label: "Active:",
                    name: "user.active",
                    type:      "checkbox",
                    separator: "|",
                    options:   [
                        { label: '', value: 1 }
                    ],
                }, {
                    label: "Full Name:",
                    name: "user.name",
                }, {
                    label: "Username:",
                    name: "user.username",
                    type: 'readonly',
                }, {
                    label: "Email:",
                    name: "user.email",
                    type: 'readonly',
                }, {
                    label: "Requested Role:",
                    name: "user.requested_role",
                    type: 'select',
                    placeholderDisabled: false,
                    placeholder: "",
                    options: [
                            { label: 'School Leader', value: 'school_leader' },
                            { label: 'District Staff', value: 'district_staff' },
                            { label: 'TSC Central', value: 'tsc_central' },
                            { label: 'External/Other', value: 'external_other' }
                        ],
                    attr: {
                        disabled:true
                    },
                }, {
                    label: "Granted Role:",
                    name: "user.granted_role",
                    type: "select",
                    placeholderDisabled: false,
                    placeholder: "",
                    options: [
                            @if(Auth::user()->user_type_id >= 40)
                            { label: 'School Leader', value: 'school_leader' },
                            @endif
                            @if(Auth::user()->user_type_id >= 60)
                            { label: 'District Staff', value: 'district_staff' },
                            { label: 'TSC Central', value: 'tsc_central' },
                            { label: 'External/Other', value: 'external_other' }
                            @endif
                        ]
                }, {
                    label: "Permission Level:",
                    name: "user.user_type_id",
                    type: "select",
                }, {
                    label: "Mobile Access:",
                    name: "user.mobile_access",
                    type:      "checkbox",
                    separator: "|",
                    options:   [
                        { label: '', value: 1 }
                    ]
                }, {
                    label: 'Custom Scope:',
                    name: 'user.custom',
                    type: 'datatable',
                    editor: editorDtUsersCustom,
                    config: {
                        language: {
                            emptyTable: 'No Schools in Custom Scope'
                        },
                        ajax: {
                            url: "/api/dte/dt-users-scope-custom",
                            type: 'post',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            data: function (d) {
                                if (tableDtUsers) {
                                    let selected = tableDtUsers.row({ selected: true });
                                    if (selected.any()) {
                                        d.id = selected.data().user.id;
                                    }
                                }
                            },
                        },
                        buttons: [
                            { extend: 'create', text: 'Add', editor: editorDtUsersCustom, className: 'btn-sm btn-success', formTitle: 'Add School to User Permissions' },
                            { extend: 'remove', text: 'Remove', editor: editorDtUsersCustom, className: 'btn-sm btn-danger' },
                        ],
                        columns: [
                            {
                                data: 'district_office.name',
                                title: 'District Name',
                            },
                            {
                                data: 'school.name',
                                title: 'School Name',
                            },
                        ],
                    },
                },{
                    label: 'Group Scope:',
                    name: 'user.group',
                    type: 'datatable',
                    editor: editorDtUsersGroup,
                    config: {
                        language: {
                            emptyTable: 'No Groups in Scope'
                        },
                        ajax: {
                            url: "/api/dte/dt-users-scope-group",
                            type: 'post',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            data: function (d) {
                                if (tableDtUsers) {
                                    let selected = tableDtUsers.row({ selected: true });
                                    if (selected.any()) {
                                        d.id = selected.data().user.id;
                                    }
                                }
                            },
                        },
                        buttons: [
                            { extend: 'create', text: 'Add', editor: editorDtUsersGroup, className: 'btn-sm btn-success', formTitle: 'Add Group of Schools to User Permissions' },
                            { extend: 'remove', text: 'Remove', editor: editorDtUsersGroup, className: 'btn-sm btn-danger' },
                        ],
                        columns: [
                            {
                                data: 'user_scope_group.group_name',
                                title: 'Group Name',
                            },
                            {
                                data: 'user_scope_group.group_description',
                                title: 'Description',
                            },
                        ],
                    },
                },{
                //     label: "Scope Groups:",
                //     name: "user_scope_group[].id",
                //     type: "datatable",
                //     multiple: true,
                // }, {
                    label: "Registered At:",
                    name: "user.created_at",
                    type: 'readonly'
                }, {
                    label: "Email Verified At:",
                    name: "user.email_verified_at",
                    type: 'readonly'
                }, {
                    label: "Last Updated At:",
                    name: "user.updated_at",
                    type: 'readonly'
                }
            ]
        });

        new $.fn.dataTable.Buttons( tableDtUsers, [
            {
                extend: "edit",
                editor: editorDtUsers,
                formTitle: 'Edit User Details',
                className: 'btn-sm'
            },
            {
                text: "Filter Users Public View",
                action: function ( e, dt, node, config ) {
                    // editor
                    if(!dt.column(4).search()){
                        dt.column(4).search( 'Public View' ).draw(); //user type is public view only
                    }else{
                        dt.column(4).search( '' ).draw();
                    }
                },
                className: 'btn-sm btn-outline-light'
            },
            {
                text: "Unverified",
                action: function ( e, dt, node, config ) {
                    dt.column(4).search( '' ).draw(); //verified at is empty
                    dt.order([8, 'asc'],[7, 'desc']).draw(); //order by verified asc desc and created at
                },
                className: 'btn-sm btn-outline-light'
            },
            {
                text: "Recent Registrations",
                action: function ( e, dt, node, config ) {
                    dt.column(4).search( '' ).draw(); //user type is public view only
                    dt.order([7, 'desc'],[8, 'asc']).draw(); //order by created at desc and verified asc
                },
                className: 'btn-sm btn-outline-light'
            }
        ] );

        tableDtUsers.buttons().container()
            .appendTo( $('.col-md-6:eq(0)', tableDtUsers.table().container() ) );

        editorDtUsers.on('initEdit', function () {
            editorDtUsers.field('user.custom').dt().ajax.reload(function (json) {
                editorDtUsersCustom.field('user_scope_custom_assignment.school_uuid').update(json.options['user_scope_custom_assignment.school_uuid']);
                }
            );
            editorDtUsers.field('user.group').dt().ajax.reload(function (json) {
                // console.log(json);
                editorDtUsersGroup.field('user_scope_group_user_link.group_id').update(json.options['user_scope_group_user_link.group_id']);
                }
            );

        });

        editorDtUsers.on('open', function () {
            $(".dataTable").addClass('table-sm small-font');
        });

    </script>
@endsection
