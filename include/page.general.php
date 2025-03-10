<?php if( !defined('NV_NAME') ) die('You can not access this file directly!');

if (isset($_POST['submit']))
{
	$ulke = $_POST['ulkeler'];
	$sehir = $_POST['sehirler'];
	$ilce = $_POST['ilceler'];
	$locale = $_POST['locale'];
	$vakit_dili = $_POST['vakit_dili'];

	if( $ulke != "0" OR $sehir != "0" )
	{
		// Dil ayarlarını kaydet
		update_option('namazvakti_locale', $locale);
		update_option('namazvakti_vakit_dili', $vakit_dili);
		
		// kaydetmeye başlayabiliriz!
		if( $ulke == 2 || $ulke == 33 || $ulke == 52 || $ulke == 13 || $ulke == 42 || $ulke == 47 || $ulke == 64 ) {
			if($ilce == 0) {
				echo '<div id="message" class="updated fade"><p>';
				_e('Lütfen tüm alanları doldurunuz!', 'namazvakti');
				echo '</p></div>';
			} else {
				update_option( NV_DB_DEFAULT_COUNTRY_NAME, $ulke );
				update_option( NV_DB_DEFAULT_CITY_NAME, $sehir );
				update_option( NV_DB_DEFAULT_TOWN_NAME, $ilce );
				
				echo '<div id="message" class="updated fade"><p>';
				_e('Ayarlar başarıyla kaydedildi.', 'namazvakti');
				echo '</p></div>';
			}
		} else {
			update_option( NV_DB_DEFAULT_COUNTRY_NAME, $ulke );
			update_option( NV_DB_DEFAULT_CITY_NAME, $sehir );
			update_option( NV_DB_DEFAULT_TOWN_NAME, $sehir );
			
			echo '<div id="message" class="updated fade"><p>';
			_e('Ayarlar başarıyla kaydedildi.', 'namazvakti');
			echo '</p></div>';
		}
	} else {
		echo '<div id="message" class="updated fade"><p>';
		_e('Lütfen tüm alanları doldurunuz!', 'namazvakti');
		echo '</p></div>';
	}
}

$varsayilan_ulke	= get_option( NV_DB_DEFAULT_COUNTRY_NAME );
$varsayilan_sehir	= get_option( NV_DB_DEFAULT_CITY_NAME );
$varsayilan_ilce	= get_option( NV_DB_DEFAULT_TOWN_NAME );

$disabled = "";
if( $varsayilan_ulke == 2 || $varsayilan_ulke == 33 || $varsayilan_ulke == 52 || $varsayilan_ulke == 13 || $varsayilan_ulke == 42 || $varsayilan_ulke == 47 || $varsayilan_ulke == 64 ) {
	$disabled = '';
} else {
	$disabled = ' disabled = "disabled"';
}

?>

<form method="post" action="">
<table class="form-table">
    
    <tr valign="top" id="dil_alani">
        <th scope="row"><?php _e('Arayüz Dili', 'namazvakti'); ?></th>
        
        <td>
            <select name="locale" id="locale">
                <option value="tr_TR" <?php selected(get_option('namazvakti_locale', 'tr_TR'), 'tr_TR'); ?>><?php _e('Türkçe', 'namazvakti'); ?></option>
                <option value="en_US" <?php selected(get_option('namazvakti_locale', 'tr_TR'), 'en_US'); ?>><?php _e('İngilizce', 'namazvakti'); ?></option>
                <option value="bs_BS" <?php selected(get_option('namazvakti_locale', 'tr_TR'), 'bs_BS'); ?>><?php _e('Boşnakça', 'namazvakti'); ?></option>
                <option value="da_DK" <?php selected(get_option('namazvakti_locale', 'tr_TR'), 'da_DK'); ?>><?php _e('Danca', 'namazvakti'); ?></option>
                <option value="de_DE" <?php selected(get_option('namazvakti_locale', 'tr_TR'), 'de_DE'); ?>><?php _e('Almanca', 'namazvakti'); ?></option>
                <option value="es_ES" <?php selected(get_option('namazvakti_locale', 'tr_TR'), 'es_ES'); ?>><?php _e('İspanyolca', 'namazvakti'); ?></option>
                <option value="fr_FR" <?php selected(get_option('namazvakti_locale', 'tr_TR'), 'fr_FR'); ?>><?php _e('Fransızca', 'namazvakti'); ?></option>
            </select>
            <p class="description"><?php _e('Widget arayüzünde kullanılacak dil.', 'namazvakti'); ?></p>
        </td>
    </tr>
    
    <tr valign="top" id="vakit_dili_alani">
        <th scope="row"><?php _e('Vakit İsimleri Dili', 'namazvakti'); ?></th>
        
        <td>
            <select name="vakit_dili" id="vakit_dili">
                <option value="tr" <?php selected(get_option('namazvakti_vakit_dili', 'tr'), 'tr'); ?>><?php _e('Türkçe', 'namazvakti'); ?></option>
                <option value="en" <?php selected(get_option('namazvakti_vakit_dili', 'tr'), 'en'); ?>><?php _e('İngilizce/Arapça', 'namazvakti'); ?></option>
                <option value="both" <?php selected(get_option('namazvakti_vakit_dili', 'tr'), 'both'); ?>><?php _e('Her İkisi', 'namazvakti'); ?></option>
            </select>
            <p class="description"><?php _e('Namaz vakitlerinin gösterileceği dil.', 'namazvakti'); ?></p>
        </td>
    </tr>
    
    <tr valign="top" id="ulkeler_alani">
    	<th scope="row"><?php _e('Varsayılan Ülke', 'namazvakti'); ?></th>
        
        <td>
        	<select name="ulkeler" id="ulkeler" onChange="selectCity();">
            	<option value="0"><?php _e('Lütfen bir ülke seçiniz', 'namazvakti'); ?></option>
                <?php
					$page_ulkeler = $this->nv->ulkeler();
					foreach ($page_ulkeler['veri'] as $id => $ulke)
					{
						$selected = $varsayilan_ulke == $id ? ' selected' : '';
						echo '<option value="' . $id . '" ' . $selected . '>' . __($ulke, 'namazvakti') . '</option>';
					}
				?>
            </select>
		</td>
    </tr>
    
    
    <tr valign="top" id="sehirler_alani">
    	<th scope="row"><?php _e('Varsayılan Şehir', 'namazvakti'); ?></th>
        
        <td>
        	<select name="sehirler" id="sehirler" onChange="selectLocation();">
            	<option value="0"><?php _e('Lütfen bir şehir seçiniz', 'namazvakti'); ?></option>
                <?php
					$page_sehirler = $this->nv->sehirler( $varsayilan_ulke );
					foreach( $page_sehirler['veri'] as $key => $value )
					{
						$selectedsehir = $varsayilan_sehir == $key ? ' selected' : '';
						echo '<option value="' . $key . '" ' . $selectedsehir . '>' . $value . '</option>';
					}
				?>
            </select>            
		</td>
    </tr>
    
    
    
    <tr valign="top" id="ilceler_alani">
    	<th scope="row"><?php _e('Varsayılan İlçe', 'namazvakti'); ?></th>
        
        <td>
        	<select name="ilceler" id="ilceler"<?php echo $disabled; ?>>
            	<option value="0"><?php _e('Lütfen bir ilçe seçiniz', 'namazvakti'); ?></option>
                <?php
					if( $varsayilan_ulke == 2 || $varsayilan_ulke == 33 || $varsayilan_ulke == 52 || $varsayilan_ulke == 13 || $varsayilan_ulke == 42 || $varsayilan_ulke == 47 || $varsayilan_ulke == 64 )
					{
						$page_ilceler = $this->nv->ilceler( $varsayilan_ulke, $varsayilan_sehir );
						foreach ( $page_ilceler['veri'] as $key => $value )
						{
							$selectedilce = $varsayilan_ilce == $key ? ' selected' : '';
							echo '<option value="' . $key . '"' . $selectedilce . '>' . $value . '</option>';
						}
					}
				?>
            </select><br>
            <?php _e('Sadece; Ülke, TÜRKİYE seçilmiş ise İlçeler listesi aktif olur. Onun dışında bu liste boş olacaktır.', 'namazvakti'); ?>
            
		</td>
    </tr>
    
</table>

<input type="submit" name="submit" class="button-primary" value="<?php _e('Kaydet', 'namazvakti'); ?>" />

</form>

<hr>

<h3><?php _e('API Bağlantı Testi', 'namazvakti'); ?></h3>
<p><?php _e('Aşağıdaki butona tıklayarak API bağlantısını test edebilirsiniz. Bu test, seçtiğiniz şehir için namaz vakitlerini çekmeye çalışacak ve sonucu gösterecektir.', 'namazvakti'); ?></p>

<button type="button" id="test-api" class="button-secondary"><?php _e('API Bağlantısını Test Et', 'namazvakti'); ?></button>
<button type="button" id="clear-cache" class="button-secondary"><?php _e('Cache Temizle', 'namazvakti'); ?></button>

<div id="api-test-results" style="margin-top: 20px; display: none;">
    <h4><?php _e('Test Sonuçları', 'namazvakti'); ?></h4>
    <div id="api-test-loading" style="display: none;">
        <p><img src="<?php echo admin_url('images/spinner.gif'); ?>" alt="Loading..." /> <?php _e('Test yapılıyor, lütfen bekleyin...', 'namazvakti'); ?></p>
    </div>
    <div id="api-test-success" style="display: none; background-color: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d; padding: 15px; border-radius: 4px;">
        <h4><?php _e('API Bağlantısı Başarılı!', 'namazvakti'); ?></h4>
        <p><?php _e('API bağlantısı başarıyla kuruldu ve namaz vakitleri çekildi.', 'namazvakti'); ?></p>
        <div id="api-test-data"></div>
    </div>
    <div id="api-test-error" style="display: none; background-color: #f2dede; border: 1px solid #ebccd1; color: #a94442; padding: 15px; border-radius: 4px;">
        <h4><?php _e('API Bağlantı Hatası!', 'namazvakti'); ?></h4>
        <p><?php _e('API bağlantısı sırasında bir hata oluştu.', 'namazvakti'); ?></p>
        <div id="api-test-error-message"></div>
        <div id="api-test-debug" style="margin-top: 15px;">
            <h5><?php _e('Hata Detayları', 'namazvakti'); ?></h5>
            <p><?php _e('Aşağıdaki teknik detayları destek ekibine iletebilirsiniz:', 'namazvakti'); ?></p>
            <div id="api-test-debug-info" style="background-color: #f8f8f8; border: 1px solid #ddd; padding: 10px; border-radius: 4px; max-height: 300px; overflow: auto; font-family: monospace; font-size: 12px;"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#test-api').on('click', function() {
        // Test sonuçları div'ini göster
        $('#api-test-results').show();
        
        // Yükleniyor mesajını göster
        $('#api-test-loading').show();
        
        // Başarı ve hata mesajlarını gizle
        $('#api-test-success').hide();
        $('#api-test-error').hide();
        
        // AJAX isteği gönder
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'test_api_connection',
                nonce: '<?php echo wp_create_nonce('test_api_connection_nonce'); ?>',
                country: $('#ulkeler').val(),
                city: $('#sehirler').val(),
                town: $('#ilceler').val()
            },
            success: function(response) {
                // Yükleniyor mesajını gizle
                $('#api-test-loading').hide();
                
                try {
                    var data = JSON.parse(response);
                    
                    if (data.success) {
                        // Başarı mesajını göster
                        $('#api-test-success').show();
                        
                        // API verilerini göster
                        var html = '<pre style="max-height: 300px; overflow: auto;">' + JSON.stringify(data.data, null, 2) + '</pre>';
                        $('#api-test-data').html(html);
                    } else {
                        // Hata mesajını göster
                        $('#api-test-error').show();
                        $('#api-test-error-message').html('<p>' + data.message + '</p>');
                        
                        // Debug bilgilerini göster
                        if (data.debug_info) {
                            $('#api-test-debug').show();
                            $('#api-test-debug-info').html(JSON.stringify(data.debug_info, null, 2));
                        } else {
                            $('#api-test-debug').hide();
                        }
                    }
                } catch (e) {
                    // JSON parse hatası
                    $('#api-test-error').show();
                    $('#api-test-error-message').html('<p><?php _e('API yanıtı işlenirken bir hata oluştu.', 'namazvakti'); ?></p><p>Hata: ' + e.message + '</p>');
                    $('#api-test-debug').show();
                    $('#api-test-debug-info').html(response);
                }
            },
            error: function(xhr, status, error) {
                // Yükleniyor mesajını gizle
                $('#api-test-loading').hide();
                
                // Hata mesajını göster
                $('#api-test-error').show();
                $('#api-test-error-message').html('<p>' + error + '</p>');
                
                // Debug bilgilerini göster
                $('#api-test-debug').show();
                $('#api-test-debug-info').html('Status: ' + status + '<br>Error: ' + error + '<br>Response: ' + xhr.responseText);
            }
        });
    });
    
    $('#clear-cache').on('click', function() {
        // Yükleniyor mesajını göster
        var $button = $(this);
        $button.prop('disabled', true);
        $button.text('<?php _e('Cache Temizleniyor...', 'namazvakti'); ?>');
        
        // AJAX isteği gönder
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'clear_cache',
                nonce: '<?php echo wp_create_nonce('clear_cache_nonce'); ?>'
            },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert('<?php _e('Cache temizlenirken bir hata oluştu!', 'namazvakti'); ?>');
                    }
                } catch (e) {
                    alert('<?php _e('Cache temizlenirken bir hata oluştu!', 'namazvakti'); ?>');
                }
                
                $button.prop('disabled', false);
                $button.text('<?php _e('Cache Temizle', 'namazvakti'); ?>');
            },
            error: function(xhr, status, error) {
                alert('<?php _e('Cache temizlenirken bir hata oluştu!', 'namazvakti'); ?>');
                $button.prop('disabled', false);
                $button.text('<?php _e('Cache Temizle', 'namazvakti'); ?>');
            }
        });
    });
});
</script>