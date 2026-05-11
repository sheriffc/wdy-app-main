<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyBOWcZ_90o5xYRVyfU7cnLIoH-fF72re4E"></script>
<script type="text/javascript" language="javascript" class="init">
(function ($, DataTable) {
    if ( ! DataTable.ext.editorFields ) {
        DataTable.ext.editorFields = {};
        }

    var _fieldTypes = DataTable.ext.editorFields;

    _fieldTypes.gmap = {
        create: function ( conf ) {

            conf._input = $(
                    '<div id="mapitems" style="width: 300px; height: 250px"></div>');

            return conf._input;
        },
    };
    })(jQuery, jQuery.fn.dataTable);
</script>