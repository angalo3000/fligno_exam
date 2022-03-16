<script>
    $(function () {
        var table = $("#user_table");
        if (table) {
            table = table.DataTable({
                responsive: true, lengthChange: false, autoWidth: false, serverSide: true, processing: true,
                "language": {
                    "lengthMenu": "Display _MENU_ records per page",
                    "zeroRecords": "Nothing found - sorry",
                    "info": "Showing page _PAGE_ of _PAGES_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(filtered from _MAX_ total records)"
                },
                dom: 'Blfrtip',
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'All']
                ],
                ajax: {
                    url: "{{ route('user.show') }}"
                },
                columns: [
                    { data: "action", name: "action", orderable: false },
                    { data: "name", name: "name" },
                    { data: "email", name: "email" },
                    { data: "created_at", 
                        render: function(data, type, row){
                            if(type === "sort" || type === "type"){
                                return data;
                            }
                            return moment(data).format("MMM D, YYYY");
                        }
                    },
                ],
            });
        }

        $("#user_form").on("submit", function(event) {
            event.preventDefault();

            $.ajax({
                url: "{{ route('user.store') }}",
                method: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                dataType: "json",
                headers: {
                    "X-Socket-Id": Echo.socketId(), // THIS IS NEEDED TO BUKOD THE CURRENT USER FROM RECEIVING TOASTR NOTIF
                },
                error: function(data){
                    console.error(data);
                },
                success: function(data) {
                    if (data.errors) return alert(data.errors[0]);
                    alert(data.success);
                    // window.location.reload();
                }
            });
            
        });
        $(document).on("click", ".delete", function() {
            atten_id = $(this).attr("id");
            name = $(this).attr("data-name");
            console.log(name);

            Swal.fire({
                title: 'Do you want to remove record of ' + name + '?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/user/" + atten_id,
                        dataType: "json",
                        error: function(data){
                            console.log(data);
                            console.log('err');
                        },
                        success: function(data) {
                            // console.log(data);
                            $("#user_table").DataTable().ajax.reload();
                            Swal.fire(
                                'Deleted!',
                                'Record has been deleted.',
                                'success'
                            )
                        }
                    });
                }
            })
            
        });


        const getLocation = () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, handleError);
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }

        const handleError = (error) => {
            let errorStr;
            switch (error.code) {
                case error.PERMISSION_DENIED:
                errorStr = 'User denied the request for Geolocation.';
                break;
                case error.POSITION_UNAVAILABLE:
                errorStr = 'Location information is unavailable.';
                break;
                case error.TIMEOUT:
                errorStr = 'The request to get user location timed out.';
                break;
                case error.UNKNOWN_ERROR:
                errorStr = 'An unknown error occurred.';
                break;
                default:
                errorStr = 'An unknown error occurred.';
            }
            console.error('Error occurred: ' + errorStr);
        }

        // SAVING OF LOCATION
        var lat, long;
        const showPosition = (position) => {
            lat = position.coords.latitude;
            long = position.coords.longitude;
            console.log(lat);
            console.log(long);
            var token = 'pk.dc13b169fa3c660eef4411138d34a5bd'; // GIVEN FROM https://my.locationiq.com/dashboard#accesstoken
            
            $.ajax({ // CHECK IF LOCATION IS SAVED FOR TODAY THEN STORE
                type: 'POST',
                url: '/location',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'lat'   : lat,
                    'long'  : long,
                    'token' : token,
                },
                success: function(response) {
                    let loc = response.address;
                    var html = '';
                    document.getElementById('location').value = JSON.stringify(loc);
                    // document.getElementById('location').value = loc.town + ", " + loc.state + ", " + loc.country;
                    console.log(loc);
                    console.log('map processing...');
                    var key = 'pk.dc13b169fa3c660eef4411138d34a5bd';   //Insert your LocationIQ access token here

                    olms('map','https://tiles.locationiq.com/v3/streets/vector.json?key='+key).then(function(map) {

                        //Set the view for this map
                        map.setView(new ol.View({
                            center: ol.proj.fromLonLat([long, lat]),
                            zoom: 12
                        }));  

                        //to create a marker
                        var marker1 = new ol.Feature({
                                geometry: new ol.geom.Point(
                                ol.proj.fromLonLat([long, lat])
                                ), 
                            });

                        //to enhance style and add icon to the map
                        marker1.setStyle(new ol.style.Style({
                            image: new ol.style.Icon({ 
                            scale: 0.2,               //scale to adjust the proportion of the icon   
                            src: '{{ asset('storage/pics/marker.jpg') }}',           //link of the icon
                            })
                        }));

                        //Let’s include the markers and create a vector source with it
                        var vectorSource = new ol.source.Vector({
                            features: [marker1]          
                        });

                        //Let’s create a vector layer, with a source created above
                        var vectorLayer = new ol.layer.Vector({
                            source: vectorSource,
                        });

                        map.addLayer(vectorLayer);

                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
        getLocation();


    })
</script>