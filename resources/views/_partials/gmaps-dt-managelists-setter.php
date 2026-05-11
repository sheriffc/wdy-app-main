<script>
    var gmapdata;
    var gmapmarker;
    var infoWindow;

    var def_zoomval = 15;

    function if_gmap_init(def_latval, def_longval)
    {
        var curpoint = new google.maps.LatLng(def_latval,def_longval);

        gmapdata = new google.maps.Map(document.getElementById("mapitems"), {
            center: curpoint,
            zoom: def_zoomval,
            mapTypeId: 'roadmap',
        });

        gmapmarker = new google.maps.Marker({
            map: gmapdata,
            position: curpoint
        });

        infoWindow = new google.maps.InfoWindow;
        google.maps.event.addListener(gmapdata, 'click', function(event) {
            gmapmarker.setPosition(event.latLng);
            editor.field('district_office.lng').set(event.latLng.lng().toFixed(6));
            editor.field('district_office.lat').set(event.latLng.lat().toFixed(6));
        });

        google.maps.event.addListener(gmapmarker, 'click', function() {
            infoWindow.open(gmapdata, gmapmarker);
        });

        return false;
    }
</script>
