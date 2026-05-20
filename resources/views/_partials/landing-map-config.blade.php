<script>

    (function (H) {
        H.wrap(H.Chart.prototype, 'pan', function (proceed) {
            H.each(this.yAxis, function (axis) {
                axis.fixTo = null;
            });
            proceed.apply(this, Array.prototype.slice.call(arguments, 1));
        });
    })(Highcharts);


    {{--var pointData = {!! json_encode($gpsSchools,JSON_NUMERIC_CHECK) !!};--}}
    let pointData = [];
    let slDistrictsGeoJson = {!! file_get_contents('json/sl_districts.geojson') !!};

    // pointData.forEach(function(el, i) {
    //     el['marker'] = {
    //         lineColor: el['teachers_reported'] > 0 ? "#52BE80" : "grey",
    //         fillColor: el['teachers_reported'] > 0 ? "#6df1a4" : "#FFFFFF",
    //     }
    //
    // });

    let attendanceMap = new Highcharts.mapChart('school-map-chart-container', {
        chart: {
            animation: false,
            events: {
                load: function () {
                    {{--
                    https://stackoverflow.com/questions/28933713/how-to-zoom-to-specific-point-in-highmaps
                    https://api.highcharts.com/class-reference/Highcharts.Chart#mapZoom
                    https://www.highcharts.com/forum/viewtopic.php?t=40287
                    https://www.highcharts.com/forum/viewtopic.php?t=37127
                    --}}
                    // this.mapZoom(0.4, -13.5, -50, 0, 0);
                    // this.xAxis[0].setExtremes(-13.205, -13.2005);
                    // chart.yAxis[0].startingExtremes = chart.yAxis[1].getExtremes();
                }
            }
        },
        title: {
            text: 'Participating Schools: Daily Report Status'
        },
        legend: {
            itemHiddenStyle:{color : null},
        },
        mapNavigation: {
            enabled: true,
            //     enableButtons: true,
            //     buttonOptions: {
            //         verticalAlign: 'bottom'
            //     },
                enableDoubleClickZoomTo:true,
                enableMouseWheelZoom:true,
                enableTouchZoom:true
        },

        mapView: {
            maxZoom: 8
        },

        tooltip: {
            pointFormatter: function(){
                // split the geo information in brackets at the end of the school name so it doesn't make the hover box huge
                //     (otherwise long school name gets forced onto 1 line)
                let nameSplit = this.name.split('(');
                let first = (typeof nameSplit[0] === 'undefined') ? 'No Name Set' : nameSplit[0];
                let second = (typeof nameSplit[1].split(')')[0] === 'undefined') ? '' : '<br>' + nameSplit[1].split(')')[0];

                return '<strong>'+first+'</strong>'+second+'<br>' +
                    'EMIS Code: '+ ( (this.emis_id !== null) ? this.emis_id : 'None' ) +'<br>' +
                    'School Payroll ID: '+ ( (this.payroll_sid !== null) ? this.payroll_sid : 'None' ) +'<br>' +
                    '————'+'<br>' +
                    'Teachers Total: '+this.teachers_total +'<br>' +
                    'Teachers Present: '+this.teachers_present +'<br>' +
                    'Teachers Absent: '+this.teachers_absent +'<br>' +
                    // 'Unknown: '+this.unknown +'<br>' +
                    'Teachers Not Reported: '+this.teachers_not_reported +'<br>' +
                    '————'+'<br>' +
                    'Learners Total: '+this.learners_total +'<br>' +
                    'Learners Reported: '+this.learners_reported +'<br>' +
                    'Learners Not Reported: '+this.learners_not_reported +'<br>'
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
                name: 'School',
                type: 'mappoint',
                title: false,
                data: pointData,
                {{--data: {!! json_encode($gpsSchools,JSON_NUMERIC_CHECK) !!},--}}
                color: '#009ADE',
                marker: {
                    fillColor: '#FFFFFF',
                    lineColor: '#009ADE',
                    lineWidth: 1,
                    radius: 3
                },
                dataLabels: {enabled: false},
                // dataLabels: false,
                animation: false,
                tooltip: {
                    pointFormat: '{point.name}'
                },
                turboThreshold: 0,
                showInLegend: false,
                point: {
                    events: {
                        click: function () {
                            if (this.id) {
                                window.open('/school/' + this.id);
                            }
                        }
                    },
                },

            },
            { 
                name: "Submitted Attendance",
                color: '#6df1a4',
            },
            { 
                name: "Not Submitted Attendance",
                color:'#D3D3D3',
            },

        ],
        credits: {
            enabled: false
        },
    });
</script>
