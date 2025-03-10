<?php if( !defined('NV_NAME') ) die('You can not access this file directly!');

Class NV_Widget extends WP_Widget
{
	// Değişkenler
	private $nv;
	private $plugin;

	/*
		Yapılandırıcı Fonksiyon
	*/
	public function __construct()
	{
		global $nv, $namazvakti;
		$this->nv = $nv;
		$this->plugin = $namazvakti;

		$params = array(
			'name'			=> __('Namaz Vakitleri', 'namazvakti'),
			'description'	=> __('Bu widget namaz vakitlerini gösterir.', 'namazvakti')
		);
		parent::__construct('NV_Widget','',$params);
	}

	/*
		form - admin paneli bileşenlerde yer alır
	*/
	public function form( $instance )
	{
		$baslik = isset($instance['baslik']) ? esc_attr( $instance['baslik'] ) : '';
		?>
        <p>
        	<label for ="<?php echo $this->get_field_id( 'baslik' ); ?>"><strong><?php echo __( 'Widget Başlığı' , 'namazvakti' ); ?></strong>
            <input class="widefat" id="<?php echo $this->get_field_id( 'baslik' ); ?>" name="<?php echo $this->get_field_name( 'baslik' ); ?>" type="text" value="<?php echo $baslik; ?>" />
            </label>
        </p>
        <?php
	}

	/*
		update
	*/
	public function update( $new_instance, $old_instance )
	{
		$instance = array();
		$instance['baslik'] = ( ! empty( $new_instance['baslik'] ) ) ? sanitize_text_field( $new_instance['baslik'] ) : '';
		return $instance;
	}

	/*
		Widget - kullanıcının göreceği şey!
	*/
	public function widget( $args, $instance )
	{
		// extract yerine doğrudan değişkenlere atama yapıyoruz
		$before_widget = $args['before_widget'] ?? '';
		$after_widget = $args['after_widget'] ?? '';
		$before_title = $args['before_title'] ?? '';
		$after_title = $args['after_title'] ?? '';
		$title = $instance['baslik'] ?? '';
		
		// Widget ayarları yerine genel ayarlardan dil seçeneklerini al
		$dil = get_option('namazvakti_vakit_dili', 'tr');
		
		echo $before_widget;

		if ( $title )
		{
			echo $before_title . $title . $after_title;
		}

		// Tanımlamaları yap!
		$aylar = array( '01' => __('Ocak', 'namazvakti'), '02' => __('Şubat', 'namazvakti'), '03' => __('Mart', 'namazvakti'), '04' => __('Nisan', 'namazvakti'), '05' => __('Mayıs', 'namazvakti'), '06' => __('Haziran', 'namazvakti'), '07' => __('Temmuz', 'namazvakti'), '08' => __('Ağustos', 'namazvakti'), '09' => __('Eylül', 'namazvakti'), '10' => __('Ekim', 'namazvakti'), '11' => __('Kasım', 'namazvakti'), '12' => __('Aralık', 'namazvakti') );

		// Vakit bilgisini buradan çekeceğiz!
		$varsayilan_ilce = $_SESSION[NV_DB_DEFAULT_TOWN_NAME] ?? get_option( NV_DB_DEFAULT_TOWN_NAME );

		// Ülkeler
		$db_ulkeler = $this->nv->ulkeler();


		// vakitleri al!
		$veriAl = $this->nv->vakit( $varsayilan_ilce );


		if( $veriAl['durum'] == 'basarili' )
		{
			$vakit = $veriAl['veri']['vakit'];

			$hangi_vakit = $this->hangi_vakitteyiz($vakit);

			$bilgi = $veriAl['veri'];

		// JavaScript için dil çevirilerini ekleyelim
		?>
		<script type="text/javascript">
		// Temel değişkenler
		var namazvakti_dil = '<?php echo esc_js(get_option("namazvakti_vakit_dili", "tr")); ?>';
		
		// Namaz vakitlerinin isimlerini tanımla
		var namazvakti_vakitler_tr = {
			'imsak': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Fajr', 'İmsak', 'tr')); ?>',
			'gunes': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Tulu', 'Güneş', 'tr')); ?>',
			'ogle': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Zuhr', 'Öğle', 'tr')); ?>',
			'ikindi': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Asr', 'İkindi', 'tr')); ?>',
			'aksam': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Maghrib', 'Akşam', 'tr')); ?>',
			'yatsi': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Isha', 'Yatsı', 'tr')); ?>'
		};
		var namazvakti_vakitler_en = {
			'imsak': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Fajr', 'İmsak', 'en')); ?>',
			'gunes': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Tulu', 'Güneş', 'en')); ?>',
			'ogle': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Zuhr', 'Öğle', 'en')); ?>',
			'ikindi': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Asr', 'İkindi', 'en')); ?>',
			'aksam': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Maghrib', 'Akşam', 'en')); ?>',
			'yatsi': '<?php echo esc_js($this->namaz_vakti_isimleri_cevirisi('Isha', 'Yatsı', 'en')); ?>'
		};
		
		// Namaz vakitlerine kalan zaman metinlerini tanımla
		var vakit_metinleri_tr = {
			'imsak': '<?php echo esc_js($this->hangi_vakit_text('imsak', 'tr')); ?>',
			'gunes': '<?php echo esc_js($this->hangi_vakit_text('gunes', 'tr')); ?>',
			'ogle': '<?php echo esc_js($this->hangi_vakit_text('ogle', 'tr')); ?>',
			'ikindi': '<?php echo esc_js($this->hangi_vakit_text('ikindi', 'tr')); ?>',
			'aksam': '<?php echo esc_js($this->hangi_vakit_text('aksam', 'tr')); ?>',
			'yatsi': '<?php echo esc_js($this->hangi_vakit_text('yatsi', 'tr')); ?>'
		};
		var vakit_metinleri_en = {
			'imsak': '<?php echo esc_js($this->hangi_vakit_text('imsak', 'en')); ?>',
			'gunes': '<?php echo esc_js($this->hangi_vakit_text('gunes', 'en')); ?>',
			'ogle': '<?php echo esc_js($this->hangi_vakit_text('ogle', 'en')); ?>',
			'ikindi': '<?php echo esc_js($this->hangi_vakit_text('ikindi', 'en')); ?>',
			'aksam': '<?php echo esc_js($this->hangi_vakit_text('aksam', 'en')); ?>',
			'yatsi': '<?php echo esc_js($this->hangi_vakit_text('yatsi', 'en')); ?>'
		};
		
		// JavaScript içinde kullanılan değişkenlerin tüm versiyonlarını tanımla
		var vakit_isimleri_tr = namazvakti_vakitler_tr;
		var vakit_isimleri_en = namazvakti_vakitler_en;
		
		// Arayüz metin çevirileri
		var namazvakti_messages = {
			'select_country': '<?php echo esc_js(__('Lütfen bir ülke seçiniz', 'namazvakti')); ?>',
			'select_city': '<?php echo esc_js(__('Lütfen bir şehir seçiniz', 'namazvakti')); ?>',
			'select_town': '<?php echo esc_js(__('Lütfen bir ilçe seçiniz', 'namazvakti')); ?>',
			'get_times': '<?php echo esc_js(__('Vakti Al', 'namazvakti')); ?>',
			'cities_error': '<?php echo esc_js(__('Şehirler alınamadı, teknik bir sorun oluştu!', 'namazvakti')); ?>',
			'towns_error': '<?php echo esc_js(__('İlçeler alınamadı, teknik bir sorun oluştu!', 'namazvakti')); ?>'
		};
		
		// AJAX URL'ini tanımla
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		
		// Vakitleri JavaScript'e aktar
		var js_vakitler = {
			imsak: "<?php echo $vakit['imsak']; ?>",
			gunes: "<?php echo $vakit['gunes']; ?>",
			ogle: "<?php echo $vakit['ogle']; ?>",
			ikindi: "<?php echo $vakit['ikindi']; ?>",
			aksam: "<?php echo $vakit['aksam']; ?>",
			yatsi: "<?php echo $vakit['yatsi']; ?>"
		};
		</script>
		<?php

		// Buraya widget ile ilgili şeyler gelecek!
		?>


        <div class="namazvakti">

        	<div class="yer" data-durum="kapali" onClick="flipAyarlar();">
            	<?php echo htmlspecialchars_decode(str_replace("-", "<br />", $bilgi['yer_adi'])); ?>
            </div>

            <div class="namazvakti_ayarlar">
        		<label id="label_ulkeler" for="ulkeler"><?php _e('Ülke Seçiniz', 'namazvakti'); ?></label>
                <select name="ulkeler" id="ulkeler" onChange="selectCity();">
                	<option value="0"><?php _e('Lütfen bir ülke seçiniz', 'namazvakti'); ?></option>
                    <?php
						foreach ($db_ulkeler['veri'] as $id => $ulke)
						{
							echo '<option value="' . $id . '">' . __($ulke, 'namazvakti') . '</option>';
						}
					?>
                </select>


                <label id="label_sehirler" for="sehirler"><?php _e('Şehir Seçiniz', 'namazvakti'); ?></label>
                <select name="sehirler" id="sehirler" onChange="selectLocation();">
                	<option value="0" selected="selected"><?php _e('Lütfen bir şehir seçiniz', 'namazvakti'); ?></option>
                </select>



                <label id="label_ilceler" for="ilceler"><?php _e('İlçe Seçiniz', 'namazvakti'); ?></label>
                <select name="ilceler" id="ilceler">
                	<option value="0" selected="selected"><?php _e('Lütfen bir ilçe seçiniz', 'namazvakti'); ?></option>
                </select>

                <div class="buton_alani">
                	<div class="btn_kendisi"><button type="button" id="get_button" class="nvButton" onClick="getTimes();"><?php _e('Vakti Al', 'namazvakti'); ?></button></div>

                    <div class="loader"><img src="<?php echo plugins_url( 'assets/img/loader.gif', NV_NAME ); ?>"/></div>
                </div>
        	</div>


            <div class="namazvakti_saatler">
            <div class="zaman">
            	<div class="bugun"><?php echo date("d", time()). ' ' . $aylar[date('m')]; ?></div>
                <div class="hicri"><?php echo $this->hicri_dil_duzeltmesi($vakit['hicri_uzun']); ?></div>
                <div class="kalanvakit_text"><?php echo $this->hangi_vakit_text($hangi_vakit, get_option('namazvakti_vakit_dili', 'tr')); ?></div>
                <div class="kalanvakit_zaman">
                    <!-- Countdown dashboard start -->
                    <div id="countdown_dashboard">
                        <div class="dash weeks_dash">
                            <div class="digit">0</div>
                            <div class="digit">0</div>
                            <div class="dash_title">:</div>
                        </div>

                        <div class="dash days_dash">
                            <div class="digit">0</div>
                            <div class="digit">0</div>
                            <div class="dash_title">:</div>
                        </div>

                        <div class="dash hours_dash">
                            <div class="digit">0</div>
                            <div class="digit">0</div>
                            <div class="dash_title">:</div>
                        </div>

                        <div class="dash minutes_dash">
                            <div class="digit">0</div>
                            <div class="digit">0</div>
                            <div class="dash_title">:</div>
                        </div>

                        <div class="dash seconds_dash">
                            <div class="digit">0</div>
                            <div class="digit">0</div>
                        </div>

                    </div>
                    <!-- Countdown dashboard end -->
                </div>
            </div>

            <div class="vakitler">

            	<div id="imsak" class="vakit">
                	<div class="vakit_adi"><?php echo $this->namaz_vakti_isimleri_cevirisi('Fajr', 'İmsak', get_option('namazvakti_vakit_dili', 'tr')); ?></div>
                    <div class="vakit_saati"><?php echo $vakit['imsak']; ?></div>
                </div>

                <div id="gunes" class="vakit">
                	<div class="vakit_adi"><?php echo $this->namaz_vakti_isimleri_cevirisi('Tulu', 'Güneş', get_option('namazvakti_vakit_dili', 'tr')); ?></div>
                    <div class="vakit_saati"><?php echo $vakit['gunes']; ?></div>
                </div>

                <div id="ogle" class="vakit">
                	<div class="vakit_adi"><?php echo $this->namaz_vakti_isimleri_cevirisi('Zuhr', 'Öğle', get_option('namazvakti_vakit_dili', 'tr')); ?></div>
                    <div class="vakit_saati"><?php echo $vakit['ogle']; ?></div>
                </div>

                <div id="ikindi" class="vakit">
                	<div class="vakit_adi"><?php echo $this->namaz_vakti_isimleri_cevirisi('Asr', 'İkindi', get_option('namazvakti_vakit_dili', 'tr')); ?></div>
                    <div class="vakit_saati"><?php echo $vakit['ikindi']; ?></div>
                </div>

                <div id="aksam" class="vakit">
                	<div class="vakit_adi"><?php echo $this->namaz_vakti_isimleri_cevirisi('Maghrib', 'Akşam', get_option('namazvakti_vakit_dili', 'tr')); ?></div>
                    <div class="vakit_saati"><?php echo $vakit['aksam']; ?></div>
                </div>

                <div id="yatsi" class="vakit">
                	<div class="vakit_adi"><?php echo $this->namaz_vakti_isimleri_cevirisi('Isha', 'Yatsı', get_option('namazvakti_vakit_dili', 'tr')); ?></div>
                    <div class="vakit_saati"><?php echo $vakit['yatsi']; ?></div>
                </div>

            </div>
            
            <script type="text/javascript">
            // Sayfa yüklendiğinde çalışacak kodlar
            document.addEventListener("DOMContentLoaded", function() {
                // Sayacı başlat
                sayaci_baslat();
            });
            
            // Ülke/Şehir/İlçe seçim fonksiyonları
            // Ülke seçildiğinde şehir/ilçeler için AJAX çağrısı
            function selectCity() {
                var ulke = document.getElementById('ulkeler').value;
                var sehirlerSelect = document.getElementById('sehirler');
                var ilcelerSelect = document.getElementById('ilceler');
                
                // Varsayılanları yükle
                sehirlerSelect.innerHTML = '<option value="0"><?php _e("Lütfen bir şehir seçiniz", "namazvakti"); ?></option>';
                ilcelerSelect.innerHTML = '<option value="0"><?php _e("Lütfen bir ilçe seçiniz", "namazvakti"); ?></option>';
                
                if (ulke != 0) {
                    // Loader göster
                    document.querySelector('.loader').style.display = 'block';
                    
                    // AJAX isteği
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', ajaxurl, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            document.querySelector('.loader').style.display = 'none';
                            
                            if (xhr.status === 200) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    
                                    if (response && response.durum === 'basarili') {
                                        if (response.ilce === true) {
                                            // Şehir yok doğrudan ilçe
                                            var options = '';
                                            for (var key in response.veri) {
                                                options += '<option value="' + key + '">' + response.veri[key] + '</option>';
                                            }
                                            ilcelerSelect.innerHTML = '<option value="0"><?php _e("Lütfen bir ilçe seçiniz", "namazvakti"); ?></option>' + options;
                                            
                                            document.getElementById('label_sehirler').style.display = 'none';
                                            sehirlerSelect.style.display = 'none';
                                            document.getElementById('label_ilceler').style.display = 'block';
                                            ilcelerSelect.style.display = 'block';
                                        } else {
                                            // Şehir var - ilçeler bir sonrakinde!
                                            var options = '';
                                            for (var key in response.veri) {
                                                options += '<option value="' + key + '">' + response.veri[key] + '</option>';
                                            }
                                            sehirlerSelect.innerHTML = '<option value="0"><?php _e("Lütfen bir şehir seçiniz", "namazvakti"); ?></option>' + options;
                                            
                                            document.getElementById('label_sehirler').style.display = 'block';
                                            sehirlerSelect.style.display = 'block';
                                            document.getElementById('label_ilceler').style.display = 'block';
                                            ilcelerSelect.style.display = 'block';
                                        }
                                    } else {
                                        alert("<?php _e('Şehirler alınamadı, teknik bir sorun oluştu!', 'namazvakti'); ?>");
                                    }
                                } catch (e) {
                                    console.error("JSON hatası:", e);
                                    alert("<?php _e('Sunucudan gelen veri işlenemedi!', 'namazvakti'); ?>");
                                }
                            } else {
                                alert("<?php _e('Sunucu hatası:', 'namazvakti'); ?> " + xhr.status);
                            }
                        }
                    };
                    xhr.send('action=ajax_action&do=getCities&country=' + ulke + '&lang=' + namazvakti_dil);
                }
            }
            
            // Şehir seçildiğinde ilçeler için AJAX çağrısı
            function selectLocation() {
                var ulke = document.getElementById('ulkeler').value;
                var sehir = document.getElementById('sehirler').value;
                var ilcelerSelect = document.getElementById('ilceler');
                
                ilcelerSelect.innerHTML = '<option value="0"><?php _e("Lütfen bir ilçe seçiniz", "namazvakti"); ?></option>';
                
                if (sehir != 0) {
                    // Loader göster
                    document.querySelector('.loader').style.display = 'block';
                    
                    // AJAX isteği
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', ajaxurl, true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            document.querySelector('.loader').style.display = 'none';
                            
                            if (xhr.status === 200) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    
                                    if (response && response.durum === 'basarili') {
                                        var options = '';
                                        for (var key in response.veri) {
                                            options += '<option value="' + key + '">' + response.veri[key] + '</option>';
                                        }
                                        ilcelerSelect.innerHTML = '<option value="0"><?php _e("Lütfen bir ilçe seçiniz", "namazvakti"); ?></option>' + options;
                                    } else {
                                        alert("<?php _e('İlçeler alınamadı, teknik bir sorun oluştu!', 'namazvakti'); ?>");
                                    }
                                } catch (e) {
                                    console.error("JSON hatası:", e);
                                    alert("<?php _e('Sunucudan gelen veri işlenemedi!', 'namazvakti'); ?>");
                                }
                            } else {
                                alert("<?php _e('Sunucu hatası:', 'namazvakti'); ?> " + xhr.status);
                            }
                        }
                    };
                    xhr.send('action=ajax_action&do=getLocations&country=' + ulke + '&city=' + sehir + '&lang=' + namazvakti_dil);
                }
            }
            
            // Vakit bilgilerini almak için AJAX çağrısı
            function getTimes() {
                var ulke = document.getElementById('ulkeler').value;
                var sehir = document.getElementById('sehirler').value;
                var ilce = document.getElementById('ilceler').value;
                var getButton = document.getElementById('get_button');
                
                if (getButton) getButton.disabled = true;
                
                if (ulke == 0) {
                    alert("<?php _e('Vakitleri çekebilmek için öncelikle ülke seçmelisiniz!', 'namazvakti'); ?>");
                    if (getButton) getButton.disabled = false;
                    return;
                }
                
                if (sehir == 0 && ilce == 0) {
                    alert("<?php _e('Vakitleri çekebilmek için öncelikle şehir seçmelisiniz!', 'namazvakti'); ?>");
                    if (getButton) getButton.disabled = false;
                    return;
                }
                
                if (sehir != 0 && ilce == 0) {
                    alert("<?php _e('Vakitleri çekebilmek için öncelikle ilçe seçmelisiniz!', 'namazvakti'); ?>");
                    if (getButton) getButton.disabled = false;
                    return;
                }
                
                // Loader göster
                document.querySelector('.loader').style.display = 'block';
                
                // AJAX isteği
                var xhr = new XMLHttpRequest();
                xhr.open('POST', ajaxurl, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        document.querySelector('.loader').style.display = 'none';
                        
                        if (xhr.status === 200) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                
                                if (response && response.durum === 'basarili') {
                                    // Verileri DOM'a aktar
                                    document.querySelector('.namazvakti .yer').innerHTML = response.veri.yer_adi.replace("-", "<br />");
                                    document.querySelector('.zaman .hicri').innerHTML = response.veri.vakit.hicri_uzun;
                                    document.querySelector('#imsak .vakit_saati').innerHTML = response.veri.vakit.imsak;
                                    document.querySelector('#gunes .vakit_saati').innerHTML = response.veri.vakit.gunes;
                                    document.querySelector('#ogle .vakit_saati').innerHTML = response.veri.vakit.ogle;
                                    document.querySelector('#ikindi .vakit_saati').innerHTML = response.veri.vakit.ikindi;
                                    document.querySelector('#aksam .vakit_saati').innerHTML = response.veri.vakit.aksam;
                                    document.querySelector('#yatsi .vakit_saati').innerHTML = response.veri.vakit.yatsi;
                                    
                                    // Global vakitleri güncelle
                                    js_vakitler = {
                                        imsak: response.veri.vakit.imsak,
                                        gunes: response.veri.vakit.gunes,
                                        ogle: response.veri.vakit.ogle,
                                        ikindi: response.veri.vakit.ikindi,
                                        aksam: response.veri.vakit.aksam,
                                        yatsi: response.veri.vakit.yatsi
                                    };
                                    
                                    // Ayarlar kısmını kapat
                                    document.querySelector(".namazvakti_ayarlar").style.display = "none";
                                    
                                    // Sayacı yeniden başlat
                                    sayaci_baslat();
                                } else {
                                    alert("<?php _e('Vakitler alınamadı, teknik bir sorun oluştu!', 'namazvakti'); ?>");
                                }
                            } catch (e) {
                                console.error("JSON hatası:", e);
                                alert("<?php _e('Sunucudan gelen veri işlenemedi!', 'namazvakti'); ?>");
                            }
                        } else {
                            alert("<?php _e('Sunucu hatası:', 'namazvakti'); ?> " + xhr.status);
                        }
                        
                        if (getButton) getButton.disabled = false;
                    }
                };
                xhr.send('action=ajax_action&do=getTimes&town=' + ilce + '&lang=' + namazvakti_dil);
            }
            
            // Ayarlar menüsünü aç/kapat
            function flipAyarlar() {
                var settingsArea = document.querySelector(".namazvakti_ayarlar");
                if (settingsArea.style.display === "block") {
                    settingsArea.style.display = "none";
                } else {
                    settingsArea.style.display = "block";
                }
            }
            
            // Sayacı başlat (jQuery plugin veya kendi implementasyonu)
            function sayaci_baslat() {
                // jQuery varsa countdown plugin'i kullan, yoksa kendi implementasyonumuzu çalıştır
                if (typeof jQuery !== 'undefined' && typeof jQuery('#countdown_dashboard').countDown === 'function') {
                    // jQuery countdown plugin varsa kullan
                    startJQueryCountdown();
                } else {
                    // jQuery yoksa kendi sayaç fonksiyonumuzu çalıştır
                    startVanillaCountdown();
                }
            }
            
            // jQuery countdown plugin ile sayaç
            function startJQueryCountdown() {
                // Sonraki vakti bul
                var sonraki = hangi_vakitteyiz();
                if (!sonraki || !sonraki.vakit) return;
                
                // Vakit metinlerini güncelle
                var vakit_metni = vakit_metinleri_tr[sonraki.vakit] || "<?php _e('Sonraki vakte kalan süre', 'namazvakti'); ?>";
                jQuery('.kalanvakit_text').text(vakit_metni);
                
                // Aktif vakti işaretle
                jQuery('.vakit').removeClass('aktif');
                jQuery('#' + sonraki.vakit).addClass('aktif');
                
                // Sayaca hedef süreyi ayarla
                var simdi = new Date();
                var hedef = new Date(simdi.getTime() + sonraki.kalan_ms);
                
                jQuery('#countdown_dashboard').countDown({
                    targetDate: {
                        'day': hedef.getDate(),
                        'month': hedef.getMonth() + 1,
                        'year': hedef.getFullYear(),
                        'hour': hedef.getHours(),
                        'min': hedef.getMinutes(),
                        'sec': hedef.getSeconds()
                    },
                    onComplete: function() {
                        // Tamamlandığında yeniden başlat
                        sayaci_baslat();
                    }
                });
            }
            
            // Vanilla JavaScript ile sayaç
            function startVanillaCountdown() {
                // Sonraki vakti hesapla
                var sonraki = hangi_vakitteyiz();
                if (!sonraki || !sonraki.vakit) {
                    return;
                }
                
                // Vakit adını metin olarak göster
                var vakit_metni = vakit_metinleri_tr[sonraki.vakit] || "<?php _e('Sonraki vakte kalan süre', 'namazvakti'); ?>";
                var metin_alani = document.querySelector('.kalanvakit_text');
                if (metin_alani) {
                    metin_alani.textContent = vakit_metni;
                }
                
                // Aktif vakti vurgula
                var vakit_elementleri = document.querySelectorAll('.vakit');
                for (var i = 0; i < vakit_elementleri.length; i++) {
                    vakit_elementleri[i].classList.remove('aktif');
                }
                
                var aktif_vakit = document.getElementById(sonraki.vakit);
                if (aktif_vakit) {
                    aktif_vakit.classList.add('aktif');
                }
                
                // Kalan süreyi hesapla
                var kalan_ms = sonraki.kalan_ms;
                var kalan_saniye = Math.floor(kalan_ms / 1000);
                
                // Hafta, gün, saat, dakika, saniye hesapla
                var saniye = kalan_saniye % 60;
                var dakika = Math.floor((kalan_saniye % 3600) / 60);
                var saat = Math.floor((kalan_saniye % 86400) / 3600);
                var gun = Math.floor((kalan_saniye % (86400 * 7)) / 86400);
                var hafta = Math.floor(kalan_saniye / (86400 * 7));
                
                // Sayaç rakamlarını güncelle
                updateDigits('weeks_dash', hafta);
                updateDigits('days_dash', gun);
                updateDigits('hours_dash', saat);
                updateDigits('minutes_dash', dakika);
                updateDigits('seconds_dash', saniye);
                
                // Eğer süre bittiyse
                if (kalan_saniye <= 0) {
                    // Sayacı yeniden başlat
                    setTimeout(sayaci_baslat, 1000);
                    return;
                }
                
                // Her saniye güncelle
                setTimeout(startVanillaCountdown, 1000);
            }
            
            // Sayaç rakamlarını güncelle
            function updateDigits(dashClass, value) {
                try {
                    var dash = document.querySelector('#countdown_dashboard .' + dashClass);
                    if (!dash) return;
                    
                    var digits = dash.querySelectorAll('.digit');
                    if (!digits || digits.length < 2) return;
                    
                    // Değeri iki basamaklı olacak şekilde ayarla
                    value = parseInt(value, 10) || 0;
                    digits[0].textContent = Math.floor(value / 10);
                    digits[1].textContent = value % 10;
                } catch (e) {
                    console.error("UpdateDigits hatası:", e);
                }
            }
            
            // Hangi vakitteyiz hesaplama
            function hangi_vakitteyiz() {
                try {
                    // Şu anki zamanı alalım
                    var simdi = new Date();
                    
                    // Tüm vakitleri bugün için zaman nesnelerine dönüştürelim
                    var vakit_saatleri = [];
                    var vakit_tipleri = ['imsak', 'gunes', 'ogle', 'ikindi', 'aksam', 'yatsi'];
                    
                    for (var i = 0; i < vakit_tipleri.length; i++) {
                        var vakit_tipi = vakit_tipleri[i];
                        var vakit_saati = js_vakitler[vakit_tipi];
                        
                        if (!vakit_saati || vakit_saati.indexOf(':') === -1) {
                            continue;
                        }
                        
                        var saat_dakika = vakit_saati.split(':');
                        var saat = parseInt(saat_dakika[0], 10);
                        var dakika = parseInt(saat_dakika[1], 10);
                        
                        if (isNaN(saat) || isNaN(dakika)) {
                            continue;
                        }
                        
                        var vakit_tarih = new Date();
                        vakit_tarih.setHours(saat, dakika, 0, 0);
                        
                        vakit_saatleri.push({
                            tip: vakit_tipi,
                            zaman: vakit_tarih
                        });
                    }
                    
                    // Bugünün vakitleri bittiyse yarının imsak vaktini ekleyelim
                    var yarin_imsak = new Date();
                    var imsak_saati = js_vakitler.imsak.split(':');
                    var imsak_saat = parseInt(imsak_saati[0], 10);
                    var imsak_dakika = parseInt(imsak_saati[1], 10);
                    
                    if (!isNaN(imsak_saat) && !isNaN(imsak_dakika)) {
                        yarin_imsak.setDate(yarin_imsak.getDate() + 1);
                        yarin_imsak.setHours(imsak_saat, imsak_dakika, 0, 0);
                        
                        vakit_saatleri.push({
                            tip: 'yarin_imsak',
                            zaman: yarin_imsak
                        });
                    }
                    
                    // Şu an ile gelecek vakitleri karşılaştıralım ve en yakın vakti bulalım
                    var sonraki_vakit = null;
                    var en_kucuk_fark = Number.MAX_SAFE_INTEGER;
                    
                    for (var j = 0; j < vakit_saatleri.length; j++) {
                        var vakit = vakit_saatleri[j];
                        var fark_ms = vakit.zaman.getTime() - simdi.getTime();
                        
                        // Sadece gelecekteki vakitleri göz önüne alalım
                        if (fark_ms > 0 && fark_ms < en_kucuk_fark) {
                            en_kucuk_fark = fark_ms;
                            sonraki_vakit = vakit.tip === 'yarin_imsak' ? 'imsak' : vakit.tip;
                        }
                    }
                    
                    return {
                        vakit: sonraki_vakit,
                        kalan_ms: en_kucuk_fark
                    };
                } catch (e) {
                    console.error("Hangi vakitteyiz hesaplama hatası:", e);
                    // Hata durumunda varsayılan değer döndür
                    return {
                        vakit: 'imsak',
                        kalan_ms: 24 * 60 * 60 * 1000 // 24 saat
                    };
                }
            }
            </script>
        </div>
		<?php
		} else {
			echo 'İstenilen şehre ait veriler alınamadı!';
		}


		echo $after_widget;
	}


	private function hangi_vakitteyiz($vakitler)
	{
		$imsak	= strtotime( date('d.m.Y') . ' ' . $vakitler['imsak'] . ':00');
		$gunes	= strtotime( date('d.m.Y') . ' ' . $vakitler['gunes'] . ':00');
		$ogle	= strtotime( date('d.m.Y') . ' ' . $vakitler['ogle'] . ':00');
		$ikindi	= strtotime( date('d.m.Y') . ' ' . $vakitler['ikindi'] . ':00');
		$aksam	= strtotime( date('d.m.Y') . ' ' . $vakitler['aksam'] . ':00');
		$yatsi	= strtotime( date('d.m.Y') . ' ' . $vakitler['yatsi'] . ':00');

		$simdi = strtotime(date('d.m.Y H:i:s'));

		if ($simdi > $yatsi)
		{
			return 'yatsi';
		}
		elseif ($simdi <= $yatsi AND $simdi > $aksam)
		{
			return 'aksam';
		}
		elseif ($simdi <= $aksam AND $simdi > $ikindi)
		{
			return 'ikindi';
		}
		elseif ($simdi <= $ikindi AND $simdi > $ogle)
		{
			return 'ogle';
		}
		elseif ($simdi <= $ogle AND $simdi > $gunes)
		{
			return 'gunes';
		} else {
			return 'imsak';
		}
	}

	private function hangi_vakit_text($vakit_ismi, $dil = 'tr')
	{
		$ingilizce_vakitler = array(
			'imsak' => 'Time until Fajr',
			'gunes' => 'Time until Tulu',
			'ogle' => 'Time until Zuhr',
			'ikindi' => 'Time until Asr',
			'aksam' => 'Time until Maghrib',
			'yatsi' => 'Time until Isha'
		);
		
		$turkce_vakitler = array(
			'imsak' => 'İmsak\'a kalan zaman',
			'gunes' => 'Güneş\'e kalan zaman',
			'ogle' => 'Öğle\'ye kalan zaman',
			'ikindi' => 'İkindi\'ye kalan zaman',
			'aksam' => 'Akşam\'a kalan zaman',
			'yatsi' => 'Yatsı\'ya kalan zaman'
		);
		
		if ($dil == 'tr') {
			return $turkce_vakitler[$vakit_ismi];
		} elseif ($dil == 'en') {
			return $ingilizce_vakitler[$vakit_ismi];
		} else { // 'both'
			return $ingilizce_vakitler[$vakit_ismi] . ' / ' . $turkce_vakitler[$vakit_ismi];
		}
	}

	private function hicri_dil_duzeltmesi($tarih)
	{
		$exp = explode( ' ', $tarih );

		$hicri_ay = trim($exp[1]);
		
		// Hicri ayların çevirileri farklı dillerde
		$hicri_aylar = array(
			'Muharrem' => __('Muharrem', 'namazvakti'),
			'Safer' => __('Safer', 'namazvakti'),
			"Rebiü'l-Evvel" => __("Rebiü'l-Evvel", 'namazvakti'),
			"Rebiü'l-Ahir" => __("Rebiü'l-Ahir", 'namazvakti'),
			"Cemaziye'l-Evvel" => __("Cemaziye'l-Evvel", 'namazvakti'),
			"Cemaziye'l-Ahir" => __("Cemaziye'l-Ahir", 'namazvakti'),
			'Recep' => __('Recep', 'namazvakti'),
			'Şaban' => __('Şaban', 'namazvakti'),
			'Ramazan' => __('Ramazan', 'namazvakti'),
			'Sevval' => __('Sevval', 'namazvakti'),
			"Zi'l-ka'de" => __("Zi'l-ka'de", 'namazvakti'),
			"Zi'l-Hicce" => __("Zi'l-Hicce", 'namazvakti'),
			
			// İngilizce versiyonlar için
			'Muharram' => __('Muharram', 'namazvakti'),
			'Safar' => __('Safar', 'namazvakti'),
			"Rabi' al-awwal" => __("Rabi' al-awwal", 'namazvakti'),
			"Rabi' al-thani" => __("Rabi' al-thani", 'namazvakti'),
			"Jumada al-awwal" => __("Jumada al-awwal", 'namazvakti'),
			"Jumada al-thani" => __("Jumada al-thani", 'namazvakti'),
			'Rajab' => __('Rajab', 'namazvakti'),
			"Sha'ban" => __("Sha'ban", 'namazvakti'),
			'Ramadan' => __('Ramadan', 'namazvakti'),
			'Shawwal' => __('Shawwal', 'namazvakti'),
			"Dhu al-Qi'dah" => __("Dhu al-Qi'dah", 'namazvakti'),
			"Dhu al-Hijjah" => __("Dhu al-Hijjah", 'namazvakti')
		);
		
		// Eğer hicri ay bizim tanımladığımız listede varsa çeviri yapılır
		if (isset($hicri_aylar[$hicri_ay])) {
			return $exp[0] . ' ' . $hicri_aylar[$hicri_ay] . ' ' . $exp[2];
		}

		return $exp[0] . ' ' . __($hicri_ay, 'namazvakti') . ' ' . $exp[2];
	}

	private function namaz_vakti_isimleri_cevirisi($ingilizce_vakit, $turkce_vakit, $dil = 'tr')
	{
		$ingilizce_vakitler = array(
			'Fajr' => 'Fajr',
			'Tulu' => 'Tulu',
			'Zuhr' => 'Zuhr',
			'Asr' => 'Asr',
			'Maghrib' => 'Maghrib',
			'Isha' => 'Isha'
		);
		
		$turkce_vakitler = array(
			'Fajr' => 'İmsak',
			'Tulu' => 'Güneş',
			'Zuhr' => 'Öğle',
			'Asr' => 'İkindi',
			'Maghrib' => 'Akşam',
			'Isha' => 'Yatsı'
		);
		
		if ($dil == 'tr') {
			return $turkce_vakitler[$ingilizce_vakit];
		} elseif ($dil == 'en') {
			return $ingilizce_vakitler[$ingilizce_vakit];
		} else { // 'both'
			return $ingilizce_vakitler[$ingilizce_vakit] . ' / ' . $turkce_vakitler[$ingilizce_vakit];
		}
	}
}
