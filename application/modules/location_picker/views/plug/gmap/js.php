<script src="<?= base_url("views/skin/backend/plugins/fastclick/fastclick.js") ?>"></script>
<script src="<?= base_url("views/skin/backend/plugins/locationpicker/locationpicker.jquery.js") ?>"></script>
<script type="text/javascript" src='https://maps.googleapis.com/maps/api/js?key=<?= MAPS_API_KEY ?>&libraries=places'></script>
<script>


    $(document).on({
        'DOMNodeInserted': function() {
            $('.pac-item, .pac-item span', this).addClass('no-fastclick');
        }
    }, '.pac-container');


    $('#somecomponent_<?=$var?>').locationpicker({
        location: {latitude: <?=$lat?>, longitude:<?=$lng?>},
        radius: 300,
        inputBinding: {
            latitudeInput: $('#lat_<?=$var?>'),
            longitudeInput: $('#lng_<?=$var?>'),
            radiusInput: $('#radius_<?=$var?>'),
            locationNameInput: $('#places_<?=$var?>')
        },
        enableAutocomplete: true
    });



    $("#<?="lat_".$var?>").on('change',function (event) {
        setTimeout(function () {
           // get_place_detail();
        },1000);
    });


    function get_place_detail() {

        let latitude = $("#<?="lat_".$var?>").val();
        let longitude = $("#<?="lng_".$var?>").val();

        $.get( "<?=site_url("location_picker/ajax/getAddressDetail")?>?latitude="+latitude+"&longitude="+longitude, function( data ) {

            let response = JSON.parse(data);
            console.log(response);

        });
    }


</script>