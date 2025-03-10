var default_sehirler_select_value	= '<option value="0">Lütfen bir şehir/eyalet seçiniz</option>';
var default_ilceler_select_value	= '<option value="0">Lütfen bir ilçe/şehir seçiniz</option>';

function selectCity()
{
	var ulke = jQuery('#ulkeler').val();
	
	// Varsayılanları yükle
	jQuery('#sehirler').html(default_sehirler_select_value);
	jQuery('#ilceler').html(default_ilceler_select_value);


	
	if (ulke != 0)
	{	
		jQuery('#sehirler').attr('disabled', true);
		jQuery('#ilceler').attr('disabled', true);
		jQuery.ajax({
			type:		'POST',
			url:		ajaxurl,
			data:		"action=ajax_action&do=getCities&country=" + ulke,
			//dataType:	"json",
			success: 	function( data ) {
				
				var response = jQuery.parseJSON( data );
				
				if( response.durum == 'basarili' )
				{
					jQuery.each( response.veri, function(key, val) {
						var newOption = jQuery('<option />');
						jQuery('#sehirler').append(newOption);
						
						newOption.val(key);
						newOption.html(val);
					});

					jQuery('#sehirler').attr('disabled', false);

					if(response.ilce == true) {
						jQuery('#ilceler').attr('disabled', true);
					} else {
						jQuery('#ilceler').attr('disabled', false);
					}
				} else {
					alert('ERROR');
				}
			}
		});
	}
}

function selectLocation()
{
	var ulke = jQuery('#ulkeler').val();
	var sehir = jQuery('#sehirler').val();
	var ilce = jQuery('#ilceler').val();

	
	jQuery('#ilceler').html(default_ilceler_select_value);


	
	if (ulke == 2 || ulke == 33 || ulke == 52 || ulke == 13 || ulke == 42 || ulke == 47 || ulke == 64)
	{
		jQuery.ajax({
			type:		'POST',
			url:		ajaxurl,
			data:		"action=ajax_action&do=getLocations&country=" + ulke + "&city=" + sehir,
			//dataType:	"json",
			success: 	function( data ) {
				
				var response = jQuery.parseJSON( data );
				
				if( response.durum == 'basarili' )
				{
					jQuery.each( response.veri, function(key, val) {
						var newOption = jQuery('<option />');
						jQuery('#ilceler').append(newOption);
						
						newOption.val(key);
						newOption.html(val);
					});
				} else {
					alert('ERROR');
				}
			}
		});
	}
}