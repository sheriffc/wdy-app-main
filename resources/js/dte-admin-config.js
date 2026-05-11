// let editorDtUsers = new $.fn.dataTable.Editor({
//     ajax: {
//         url: "/api/dte/dt-users",
//         type: "POST",
//         headers: {
//             'X-CSRF-TOKEN': $('#dt-users').data("csrf")
//         }
//     },
//     table: "#dt-users",
//     fields: [
//         {
//             label: "ID:",
//             name: "users.id",
//             type: 'hidden'
//         }, {
//             label: "Name:",
//             name: "users.name"
//         }, {
//             label: "Email:",
//             name: "users.email",
//             type: 'readonly',
//         }, {
//             label: "Last Updated:",
//             name: "users.updated_at",
//             type: 'readonly'
//         }, {
//             label: "Date Registered:",
//             name: "users.created_at",
//             type: 'readonly'
//         }
//     ]
// });
//
// let tableDtUsers = $('#dt-users').DataTable({
//     // lengthChange: true,
//     // lengthMenu: [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
//     // pageLength: 5,
//     // dom: "Bfrtip",
//     ajax: {
//         url: "/api/dte/dt-users",
//         type: "POST",
//         headers: {
//             'X-CSRF-TOKEN': $('#dt-users').data("csrf")
//         }
//     },
//     serverSide: true,
//     // order: [[1,'asc'],[0,'asc']],
//     columns: [
//         {data: "users.id"},
//         {data: "users.name"},
//         {data: "users.email"},
//         {data: "users.created_at"},
//         {data: "users.updated_at"},
//     ],
//     select: true,
// });
//
// new $.fn.dataTable.Buttons( tableDtUsers, [
//     { extend: "create", text: "test", editor: editorDtUsers },
//     { extend: "edit",   text: "text", editor: editorDtUsers },
//     { extend: "remove", text: "test", editor: editorDtUsers }
// ] );
//
// tableDtUsers.buttons().container()
//     .appendTo( $('.col-md-6:eq(0)', tableDtUsers.table().container() ) );

let editorDtDistrictOffices = new $.fn.dataTable.Editor({
    ajax: {
        url: "/api/dte/dt-district-offices",
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('#dt-district-offices').data("csrf")
        }
    },
    table: "#dt-district-offices",
    fields: [
        {
            label: "ID:",
            name: "users.id",
            type: 'hidden'
        }, {
            label: "Name:",
            name: "users.name"
        }, {
            label: "Email:",
            name: "users.email",
            type: 'readonly',
        }, {
            label: "Last Updated:",
            name: "users.updated_at",
            type: 'readonly'
        }, {
            label: "Date Registered:",
            name: "users.created_at",
            type: 'readonly'
        }
    ]
});

let tableDtDistrictOffices = $('#dt-district-offices').DataTable({
    // lengthChange: true,
    // lengthMenu: [[5,10, 25, 50, -1], [5,10, 25, 50, "All"]],
    // pageLength: 5,
    // dom: "Bfrtip",
    ajax: {
        url: "/api/dte/dt-district-offices",
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('#dt-district-offices').data("csrf")
        }
    },
    serverSide: true,
    // order: [[1,'asc'],[0,'asc']],
    columns: [
        {data: "district_office.district_name"},
        {data: "district_office.lat"},
        {data: "district_office.lng"},
        {data: "district_office.active"},
        {data: "district_office.created_at"},
        {data: "district_office.updated_at"},
    ],
    select: true,
});

new $.fn.dataTable.Buttons( tableDtDistrictOffices, [
    { extend: "create", editor: editorDtDistrictOffices },
    { extend: "edit",   editor: editorDtDistrictOffices },
    { extend: "remove", editor: editorDtDistrictOffices }
] );
//
tableDtDistrictOffices.buttons().container()
    .appendTo( $('.col-md-6:eq(0)', tableDtDistrictOffices.table().container() ) );
