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
                    <div class="text-center"><h3>Manage Scope Groups</h3></div>
                    <table id="dt-scope-groups" data-csrf="{{csrf_token()}}" class="table table-sm small-font table-responsive-lg" style="width:100%">
                        <thead>
                        <tr>
                            <th class="text-left">Group Name</th>
                            <th class="text-left">Group Description</th>
                            <th class="text-left">Districts</th>
                            <th class="text-left">Custom Schools (count)</th>
                            <th class="text-left">Active</th>
                            <th class="text-left">Created At</th>
                            <th class="text-left">Updated At</th>
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

        // MANAGE USERS
        let tableDtVisGroups = $('#dt-scope-groups').DataTable({
            // lengthChange: true,
            // lengthMenu: [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
            // pageLength: 5,
            // dom: "Bfrtip",
            ajax: {
                url: "/api/dte/dt-scope-groups",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-scope-groups').data("csrf")
                }
            },
            serverSide: true,
            // order: [[1,'asc'],[0,'asc']],
            columns: [
                {data: "user_scope_group.group_name"},
                {data: "user_scope_group.group_description"},
                {
                    data: "user_scope_group.district_selection",
                    render: function ( data, type, row ) {
                        if(!data){
                            return 0
                        }else{
                            return data.split(",").length;
                        }
                    }
                },
                {
                    data: "user_scope_group.school_selection",
                    render: function ( data, type, row ) {
                        if(!data){
                            return 0
                        }else{
                            return data.split(",").length;
                        }
                    }
                },
                {
                    data: "user_scope_group.active",
                    render: function ( data, type, row ) {
                        if ( data ) {
                            return '<input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked disabled>';
                        }else{
                            return '<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" disabled>';
                        }
                    },
                },
                {data: "user_scope_group.created_at"},
                {data: "user_scope_group.updated_at"},
            ],
            select: true,
        });

        let editorDtVisGroups = new $.fn.dataTable.Editor({
            ajax: {
                url: "/api/dte/dt-scope-groups",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('#dt-scope-groups').data("csrf")
                }
            },
            table: "#dt-scope-groups",
            fields: [
                {
                    label: "ID:",
                    name: "user_scope_group.id",
                    type: 'hidden'
                }, {
                    label: "Active:",
                    name: "user_scope_group.active",
                    type:      "checkbox",
                    separator: "|",
                    options:   [
                        { label: '', value: 1 }
                    ]
                }, {
                    label: "Name:",
                    name: "user_scope_group.group_name"
                }, {
                    label: "Description:",
                    name: "user_scope_group.description",
                    type: 'textarea',
                },{
                    label: "District Offices:",
                    name: "user_scope_group.district_selection",
                    type: "datatable",
                    multiple: true,
                    separator: ','
                },{
                    label: "Schools:",
                    name: "user_scope_group.school_selection",
                    type: "datatable",
                    multiple: true,
                    separator: ','
                },{
                    label: "Last Updated:",
                    name: "user_scope_group.updated_at",
                    type: 'readonly'
                }, {
                    label: "Date Registered:",
                    name: "user_scope_group.created_at",
                    type: 'readonly'
                }
            ]
        });

        new $.fn.dataTable.Buttons( tableDtVisGroups, [
            {
                extend: "create",
                editor: editorDtVisGroups,
                formTitle: 'Create new group',
                className: 'btn-sm'
            },
            {
                extend: "edit",
                editor: editorDtVisGroups,
                formTitle: 'Edit group',
                className: 'btn-sm'
            },
            {
                extend: "remove",
                editor: editorDtVisGroups,
                formTitle: 'Delete a group',
                className: 'btn-sm'
            },
        ] );

        tableDtVisGroups.buttons().container()
            .appendTo( $('.col-md-6:eq(0)', tableDtVisGroups.table().container() ) );

        editorDtVisGroups.on('open', function () {
            $(".dataTable").addClass('table-sm small-font');
        });

    </script>
@endsection
