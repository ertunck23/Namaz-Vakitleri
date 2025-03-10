<?php
/*
Plugin Name: Namaz-Vakitleri
Plugin URI: https://github.com/ertunck23/Namaz-Vakitleri 
Description: Bu eklenti namaz vakitlerini bileşen olarak gösterir.
Version: 6.0.0
Author: Erdem ARSLAN - Ertunç KAYGUSUZ
Author URI: https://github.com/ertunck23/Namaz-Vakitleri 
*/


$plugin		= plugin_basename(__FILE__);
$plugindir	= dirname(__FILE__) . DIRECTORY_SEPARATOR;

// Tanımlamaları yap
# Eklenti tanımlamaları
define( 'NV_NAME', $plugin );
define( 'NV_VERSION', '6.0.0' );
define( 'NV_PLUGIN_DIR', $plugindir );

# Veritabanı tanımlamaları
define( 'NV_DB_WIDGET_COLORSET', 'namazvakti_widget_rengi' );
define( 'NV_DB_DEFAULT_COUNTRY_NAME', 'namazvakti_varsayilan_ulke' );
define( 'NV_DB_DEFAULT_CITY_NAME', 'namazvakti_varsayilan_sehir' );
define( 'NV_DB_DEFAULT_TOWN_NAME', 'namazvakti_varsayilan_ilce' );

// Gerekli Dosyaları Çek
require_once 'include/wp-less.php';
require_once 'include/class.namaz.php';
require_once 'include/widget.namazvakti.php';

$hicriaylar = array(
	1 => __('Muharrem', 'namazvakti'),
	2 => __('Safer', 'namazvakti'),
	3 => __("Rebiü'l-Evvel", 'namazvakti'),
	4 => __("Rebiü'l-Ahir", 'namazvakti'),
	5 => __("Cemaziye'l-Evvel", 'namazvakti'),
	6 => __("Cemaziye'l-Ahir", 'namazvakti'),
	7 => __('Recep', 'namazvakti'),
	8 => __('Şaban', 'namazvakti'),
	9 => __('Ramazan', 'namazvakti'),
	10 => __('Sevval', 'namazvakti'),
	11 => __("Zi'l-ka'de", 'namazvakti'),
	12 => __("Zi'l-Hicce", 'namazvakti')
);

// Namazvakti sınıfı için ülkelerin dil dosyası için ayarlanmış hali!
$ulke_dil_isimleri = array('ABD' => __('ABD', 'namazvakti'), 'AFGANISTAN' => __('AFGANISTAN', 'namazvakti'), 'ALMANYA' => __('ALMANYA', 'namazvakti'), 'ANDORRA' => __('ANDORRA', 'namazvakti'), 'ANGOLA' => __('ANGOLA', 'namazvakti'), 'ANGUILLA' => __('ANGUILLA', 'namazvakti'), 'ANTIGUA VE BARBUDA' => __('ANTIGUA VE BARBUDA', 'namazvakti'), 'ARJANTIN' => __('ARJANTIN', 'namazvakti'), 'ARNAVUTLUK' => __('ARNAVUTLUK', 'namazvakti'), 'ARUBA' => __('ARUBA', 'namazvakti'), 'AVUSTRALYA' => __('AVUSTRALYA', 'namazvakti'), 'AVUSTURYA' => __('AVUSTURYA', 'namazvakti'), 'AZERBAYCAN' => __('AZERBAYCAN', 'namazvakti'), 'BAHAMALAR' => __('BAHAMALAR', 'namazvakti'), 'BAHREYN' => __('BAHREYN', 'namazvakti'), 'BANGLADES' => __('BANGLADES', 'namazvakti'), 'BARBADOS' => __('BARBADOS', 'namazvakti'), 'BELARUS' => __('BELARUS', 'namazvakti'), 'BELCIKA' => __('BELCIKA', 'namazvakti'), 'BELIZE' => __('BELIZE', 'namazvakti'), 'BENIN' => __('BENIN', 'namazvakti'), 'BERMUDA' => __('BERMUDA', 'namazvakti'), 'BIRLESIK ARAP EMIRLIGI' => __('BIRLESIK ARAP EMIRLIGI', 'namazvakti'), 'BOLIVYA' => __('BOLIVYA', 'namazvakti'), 'BOSNA HERSEK' => __('BOSNA HERSEK', 'namazvakti'), 'BOTSVANA' => __('BOTSVANA', 'namazvakti'), 'BREZILYA' => __('BREZILYA', 'namazvakti'), 'BRUNEI' => __('BRUNEI', 'namazvakti'), 'BULGARISTAN' => __('BULGARISTAN', 'namazvakti'), 'BURKINA FASO' => __('BURKINA FASO', 'namazvakti'), 'BURMA (MYANMAR)' => __('BURMA (MYANMAR)', 'namazvakti'), 'BURUNDI' => __('BURUNDI', 'namazvakti'), 'BUTAN' => __('BUTAN', 'namazvakti'), 'CAD' => __('CAD', 'namazvakti'), 'CECENISTAN' => __('CECENISTAN', 'namazvakti'), 'CEK CUMHURIYETI' => __('CEK CUMHURIYETI', 'namazvakti'), 'CEZAYIR' => __('CEZAYIR', 'namazvakti'), 'CIBUTI' => __('CIBUTI', 'namazvakti'), 'CIN' => __('CIN', 'namazvakti'), 'DANIMARKA' => __('DANIMARKA', 'namazvakti'), 'DEMOKRATIK KONGO CUMHURIYETI' => __('DEMOKRATIK KONGO CUMHURIYETI', 'namazvakti'), 'DOGU TIMOR' => __('DOGU TIMOR', 'namazvakti'), 'DOMINIK' => __('DOMINIK', 'namazvakti'), 'DOMINIK CUMHURIYETI' => __('DOMINIK CUMHURIYETI', 'namazvakti'), 'EKVATOR' => __('EKVATOR', 'namazvakti'), 'EKVATOR GINESI' => __('EKVATOR GINESI', 'namazvakti'), 'EL SALVADOR' => __('EL SALVADOR', 'namazvakti'), 'ENDONEZYA' => __('ENDONEZYA', 'namazvakti'), 'ERITRE' => __('ERITRE', 'namazvakti'), 'ERMENISTAN' => __('ERMENISTAN', 'namazvakti'), 'ESTONYA' => __('ESTONYA', 'namazvakti'), 'ETYOPYA' => __('ETYOPYA', 'namazvakti'), 'FAS' => __('FAS', 'namazvakti'), 'FIJI' => __('FIJI', 'namazvakti'), 'FILDISI SAHILI' => __('FILDISI SAHILI', 'namazvakti'), 'FILIPINLER' => __('FILIPINLER', 'namazvakti'), 'FILISTIN' => __('FILISTIN', 'namazvakti'), 'FINLANDIYA' => __('FINLANDIYA', 'namazvakti'), 'FRANSA' => __('FRANSA', 'namazvakti'), 'GABON' => __('GABON', 'namazvakti'), 'GAMBIYA' => __('GAMBIYA', 'namazvakti'), 'GANA' => __('GANA', 'namazvakti'), 'GINE' => __('GINE', 'namazvakti'), 'GRANADA' => __('GRANADA', 'namazvakti'), 'GRONLAND' => __('GRONLAND', 'namazvakti'), 'GUADELOPE' => __('GUADELOPE', 'namazvakti'), 'GUAM ADASI' => __('GUAM ADASI', 'namazvakti'), 'GUATEMALA' => __('GUATEMALA', 'namazvakti'), 'GUNEY AFRIKA' => __('GUNEY AFRIKA', 'namazvakti'), 'GUNEY KORE' => __('GUNEY KORE', 'namazvakti'), 'GURCISTAN' => __('GURCISTAN', 'namazvakti'), 'GUYANA' => __('GUYANA', 'namazvakti'), 'HAITI' => __('HAITI', 'namazvakti'), 'HINDISTAN' => __('HINDISTAN', 'namazvakti'), 'HIRVATISTAN' => __('HIRVATISTAN', 'namazvakti'), 'HOLLANDA' => __('HOLLANDA', 'namazvakti'), 'HOLLANDA ANTILLERI' => __('HOLLANDA ANTILLERI', 'namazvakti'), 'HONDURAS' => __('HONDURAS', 'namazvakti'), 'HONG KONG' => __('HONG KONG', 'namazvakti'), 'INGILTERE' => __('INGILTERE', 'namazvakti'), 'IRAK' => __('IRAK', 'namazvakti'), 'IRAN' => __('IRAN', 'namazvakti'), 'IRLANDA' => __('IRLANDA', 'namazvakti'), 'ISPANYA' => __('ISPANYA', 'namazvakti'), 'ISRAIL' => __('ISRAIL', 'namazvakti'), 'ISVEC' => __('ISVEC', 'namazvakti'), 'ISVICRE' => __('ISVICRE', 'namazvakti'), 'ITALYA' => __('ITALYA', 'namazvakti'), 'IZLANDA' => __('IZLANDA', 'namazvakti'), 'JAMAIKA' => __('JAMAIKA', 'namazvakti'), 'JAPONYA' => __('JAPONYA', 'namazvakti'), 'KAMBOCYA' => __('KAMBOCYA', 'namazvakti'), 'KAMERUN' => __('KAMERUN', 'namazvakti'), 'KANADA' => __('KANADA', 'namazvakti'), 'KARADAG' => __('KARADAG', 'namazvakti'), 'KATAR' => __('KATAR', 'namazvakti'), 'KAZAKISTAN' => __('KAZAKISTAN', 'namazvakti'), 'KENYA' => __('KENYA', 'namazvakti'), 'KIRGIZISTAN' => __('KIRGIZISTAN', 'namazvakti'), 'KIRGIZISTAN' => __('KIRGIZISTAN', 'namazvakti'), 'KOLOMBIYA' => __('KOLOMBIYA', 'namazvakti'), 'KOMORLAR' => __('KOMORLAR', 'namazvakti'), 'KOSOVA' => __('KOSOVA', 'namazvakti'), 'KOSTARIKA' => __('KOSTARIKA', 'namazvakti'), 'KUBA' => __('KUBA', 'namazvakti'), 'KUDUS' => __('KUDUS', 'namazvakti'), 'KUVEYT' => __('KUVEYT', 'namazvakti'), 'KUZEY KIBRIS' => __('KUZEY KIBRIS', 'namazvakti'), 'KUZEY KORE' => __('KUZEY KORE', 'namazvakti'), 'LAOS' => __('LAOS', 'namazvakti'), 'LESOTO' => __('LESOTO', 'namazvakti'), 'LETONYA' => __('LETONYA', 'namazvakti'), 'LIBERYA' => __('LIBERYA', 'namazvakti'), 'LIBYA' => __('LIBYA', 'namazvakti'), 'LIECHTENSTEIN' => __('LIECHTENSTEIN', 'namazvakti'), 'LITVANYA' => __('LITVANYA', 'namazvakti'), 'LUBNAN' => __('LUBNAN', 'namazvakti'), 'LUKSEMBURG' => __('LUKSEMBURG', 'namazvakti'), 'MACARISTAN' => __('MACARISTAN', 'namazvakti'), 'MADAGASKAR' => __('MADAGASKAR', 'namazvakti'), 'MAKAO' => __('MAKAO', 'namazvakti'), 'MAKEDONYA' => __('MAKEDONYA', 'namazvakti'), 'MALAVI' => __('MALAVI', 'namazvakti'), 'MALDIVLER' => __('MALDIVLER', 'namazvakti'), 'MALEZYA' => __('MALEZYA', 'namazvakti'), 'MALI' => __('MALI', 'namazvakti'), 'MALTA' => __('MALTA', 'namazvakti'), 'MARTINIK' => __('MARTINIK', 'namazvakti'), 'MAURITIUS ADASI' => __('MAURITIUS ADASI', 'namazvakti'), 'MAYOTTE' => __('MAYOTTE', 'namazvakti'), 'MEKSIKA' => __('MEKSIKA', 'namazvakti'), 'MIKRONEZYA' => __('MIKRONEZYA', 'namazvakti'), 'MISIR' => __('MISIR', 'namazvakti'), 'MOGOLISTAN' => __('MOGOLISTAN', 'namazvakti'), 'MOLDAVYA' => __('MOLDAVYA', 'namazvakti'), 'MONAKO' => __('MONAKO', 'namazvakti'), 'MONTSERRAT (U.K.)' => __('MONTSERRAT (U.K.)', 'namazvakti'), 'MORITANYA' => __('MORITANYA', 'namazvakti'), 'MOZAMBIK' => __('MOZAMBIK', 'namazvakti'), 'NAMBIYA' => __('NAMBIYA', 'namazvakti'), 'NEPAL' => __('NEPAL', 'namazvakti'), 'NIJER' => __('NIJER', 'namazvakti'), 'NIJERYA' => __('NIJERYA', 'namazvakti'), 'NIKARAGUA' => __('NIKARAGUA', 'namazvakti'), 'NIUE' => __('NIUE', 'namazvakti'), 'NORVEC' => __('NORVEC', 'namazvakti'), 'ORTA AFRIKA CUMHURIYETI' => __('ORTA AFRIKA CUMHURIYETI', 'namazvakti'), 'OZBEKISTAN' => __('OZBEKISTAN', 'namazvakti'), 'PAKISTAN' => __('PAKISTAN', 'namazvakti'), 'PALAU' => __('PALAU', 'namazvakti'), 'PANAMA' => __('PANAMA', 'namazvakti'), 'PAPUA YENI GINE' => __('PAPUA YENI GINE', 'namazvakti'), 'PARAGUAY' => __('PARAGUAY', 'namazvakti'), 'PERU' => __('PERU', 'namazvakti'), 'PITCAIRN ADASI' => __('PITCAIRN ADASI', 'namazvakti'), 'POLONYA' => __('POLONYA', 'namazvakti'), 'PORTEKIZ' => __('PORTEKIZ', 'namazvakti'), 'PORTO RIKO' => __('PORTO RIKO', 'namazvakti'), 'REUNION' => __('REUNION', 'namazvakti'), 'ROMANYA' => __('ROMANYA', 'namazvakti'), 'RUANDA' => __('RUANDA', 'namazvakti'), 'RUSYA' => __('RUSYA', 'namazvakti'), 'SAMOA' => __('SAMOA', 'namazvakti'), 'SENEGAL' => __('SENEGAL', 'namazvakti'), 'SEYSEL ADALARI' => __('SEYSEL ADALARI', 'namazvakti'), 'SILI' => __('SILI', 'namazvakti'), 'SINGAPUR' => __('SINGAPUR', 'namazvakti'), 'SIRBISTAN' => __('SIRBISTAN', 'namazvakti'), 'SLOVAKYA' => __('SLOVAKYA', 'namazvakti'), 'SLOVENYA' => __('SLOVENYA', 'namazvakti'), 'SOMALI' => __('SOMALI', 'namazvakti'), 'SRI LANKA' => __('SRI LANKA', 'namazvakti'), 'SUDAN' => __('SUDAN', 'namazvakti'), 'SURINAM' => __('SURINAM', 'namazvakti'), 'SURIYE' => __('SURIYE', 'namazvakti'), 'SUUDI ARABISTAN' => __('SUUDI ARABISTAN', 'namazvakti'), 'SVALBARD' => __('SVALBARD', 'namazvakti'), 'SVAZILAND' => __('SVAZILAND', 'namazvakti'), 'TACIKISTAN' => __('TACIKISTAN', 'namazvakti'), 'TANZANYA' => __('TANZANYA', 'namazvakti'), 'TAYLAND' => __('TAYLAND', 'namazvakti'), 'TAYVAN' => __('TAYVAN', 'namazvakti'), 'TOGO' => __('TOGO', 'namazvakti'), 'TONGA' => __('TONGA', 'namazvakti'), 'TRINIDAT VE TOBAGO' => __('TRINIDAT VE TOBAGO', 'namazvakti'), 'TUNUS' => __('TUNUS', 'namazvakti'), 'TURKIYE' => __('TURKIYE', 'namazvakti'), 'TURKMENISTAN' => __('TURKMENISTAN', 'namazvakti'), 'UGANDA' => __('UGANDA', 'namazvakti'), 'UKRAYNA' => __('UKRAYNA', 'namazvakti'), 'UKRAYNA-KIRIM' => __('UKRAYNA-KIRIM', 'namazvakti'), 'UMMAN' => __('UMMAN', 'namazvakti'), 'URDUN' => __('URDUN', 'namazvakti'), 'URUGUAY' => __('URUGUAY', 'namazvakti'), 'VANUATU' => __('VANUATU', 'namazvakti'), 'VATIKAN' => __('VATIKAN', 'namazvakti'), 'VENEZUELA' => __('VENEZUELA', 'namazvakti'), 'VIETNAM' => __('VIETNAM', 'namazvakti'), 'YEMEN' => __('YEMEN', 'namazvakti'), 'YENI KALEDONYA' => __('YENI KALEDONYA', 'namazvakti'), 'YENI ZELLANDA' => __('YENI ZELLANDA', 'namazvakti'), 'YESIL BURUN' => __('YESIL BURUN', 'namazvakti'), 'YUNANISTAN' => __('YUNANISTAN', 'namazvakti'), 'ZAMBIYA' => __('ZAMBIYA', 'namazvakti'), 'ZIMBABVE' => __('ZIMBABVE', 'namazvakti'));

// Namaz Sınıfını Başlat!
$nv = new Namaz( plugin_dir_path( __FILE__ ) . 'cache/' );

// Hicri ayları sınıfa entegre edelim!
$nv->hicriAylar = $hicriaylar;
// Ülke Dil isimlerini entegre edelim!
$nv->ulkeIsimleri = $ulke_dil_isimleri;


Class WP_Namazvakti
{
	// Sınıf Değişkenleri
	private $nv;

	/*
		Yapılandırıcı Fonksiyon
	*/
	public function __construct()
	{
		// Eklenti aktifleştirilirken yapılması gerekenler!
		add_action( 'plugins_loaded', array( $this, 'update_namazvakti_plugin' ) );

		global $nv;
		// Diyanetten verileri çekecek ana sınıfımızı yüklüyoruz. Cache klasörünü de ayrıca belirtiyoruz.
		$this->nv = $nv;
		
		// Cache dizinini kontrol et ve oluştur
		$this->check_cache_directory();

		// Eklentiyi Başlat
		add_action( 'init', array( $this, '__namazvakti_init' ) );

		// Widgetler için Shortcode desteği ekleyelim
		add_filter( 'widget_text', 'do_shortcode' );

		// Widgetleri Aktifleştir
		add_action( 'widgets_init', array( $this, '__widget_init' ) );

		// Admin menüsü ekle - Ayarların Altına
		add_action( 'admin_menu', array( $this, '_namazvakti_admin_menu_init' ) );

		// Eklentiye extra linkler ekle
		add_filter('plugin_action_links', array( $this, '__namazvakti_plugin_linkleri' ), 10, 2);

		// Ajax işlemlerini ekle
		add_action( 'wp_ajax_ajax_action', array( &$this, 'namazvakti_ajax' ) ); // ajax for logged in users
		add_action( 'wp_ajax_nopriv_ajax_action', array( &$this, 'namazvakti_ajax' ) ); // ajax for not logged in users
		
		// API test işlemi için AJAX ekle
		add_action( 'wp_ajax_test_api_connection', array( $this, 'test_api_connection' ) );
		
		// Cache temizleme için AJAX ekle
		add_action( 'wp_ajax_clear_cache', array( $this, 'clear_cache' ) );

		// Dil desteği için
		add_action('plugins_loaded', array($this, 'translation_support'));
	}

	/*
		Cache dizinini kontrol et ve oluştur
	*/
	private function check_cache_directory() {
		$cache_dir = plugin_dir_path(__FILE__) . 'cache';
		
		// Cache dizini yoksa oluştur
		if (!is_dir($cache_dir)) {
			@mkdir($cache_dir, 0755, true);
		}
		
		// Cache dizini yazılabilir değilse izinleri değiştir
		if (!is_writable($cache_dir)) {
			@chmod($cache_dir, 0755);
		}
		
		// index.php dosyası oluştur (dizin listelemesini engeller)
		$index_file = $cache_dir . '/index.php';
		if (!file_exists($index_file)) {
			@file_put_contents($index_file, '<?php // Silence is golden');
		}
		
		// .htaccess dosyası oluştur (dizin erişimini engeller)
		$htaccess_file = $cache_dir . '/.htaccess';
		if (!file_exists($htaccess_file)) {
			@file_put_contents($htaccess_file, 'Deny from all');
		}
	}

	/*
		Eklentiyi Başlat
	*/
	public function __namazvakti_init()
	{
		// Eklenti dil dosyasını yükle
		load_plugin_textdomain( 'namazvakti', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Cache dizinini kontrol et ve oluştur
		$this->check_cache_directory();
		
		// Eklenti CSS ve JS dosyalarını yükle
		add_action( 'admin_enqueue_scripts', array( $this, '_namazvakti_admin_scripts_styles' ) );

		// Shortcode'ları ekle
		add_shortcode( 'namazvakti', array( $this, '_namazvakti_shortcode' ) );

		// Sessionları aç!
		if (PHP_SESSION_NONE === session_status()) {
			session_start();
		}

		// Veritabanından çek ve renkleri ayarla! PHP Version >= PHP 5.3 olmalı
		add_filter( 'less_vars', function( $vars, $handle ){
			$color = explode( '|', get_option( NV_DB_WIDGET_COLORSET ) );
			$vars[ 'bg_location' ]	= $color[0] ?? '#C4364C';
			$vars[ 'bg_time' ]		= $color[1] ?? '#F04862';
			$vars[ 'cl_active' ]	= $color[2] ?? '#F04862';
			$vars[ 'bg_times' ]		= $color[3] ?? '#364363';
			$vars[ 'bg_active' ]	= $color[4] ?? '#50587C';
			return $vars;
		}, 10, 2 );

		// Still ve JS dosyalarını ayarla
		wp_enqueue_script( 'jquery'); // javascript kütüphanesi

		if( !is_admin() )
		{
			wp_enqueue_style( 'era_nv_user_style', plugins_url( "/assets/main-style.less", __FILE__ ), array(), NV_VERSION ); // ana still dosyası
			wp_enqueue_script( 'era-countdown-plugin', plugins_url( "/assets/js/jquery.countdown.js", __FILE__), array( 'jquery' ), NV_VERSION ); // sayaç dosyası
			wp_enqueue_script( 'era-ajax-request', plugins_url( "/assets/js/namazvakti.js", __FILE__), array( 'jquery' ), NV_VERSION ); // eklenti ile ilgili js dosyası
			wp_localize_script( 'era-ajax-request', 'eranvjs', array(
				'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
				// bundan sonrası javascript dosyalarında dil ayarları için
				'imsak'	=> __("İmsak'a kalan zaman", 'namazvakti'),
				'gunes'	=> __("Güneş'e kalan zaman", 'namazvakti'),
				'ogle'	=> __("Öğle'ne kalan zaman",'namazvakti'),
				'ikindi' => __("İkindi'ye kalan zaman", 'namazvakti'),
				'aksam'	=> __("Akşam'a kalan zaman", 'namazvakti'),
				'yatsi'	=> __("Yatsı'ya kalan zaman", 'namazvakti'),
				'sehir_sec'	=> __('Lütfen bir şehir seçiniz', 'namazvakti'),
				'ilce_sec'	=> __('Lütfen bir ilçe seçiniz', 'namazvakti'),
				'ulke_secilmemis' => __('Vakitleri çekebilmek için öncelikle ülke seçmelisiniz!', 'namazvakti'),
				'sehir_secilmemis' => __('Vakitleri çekebilmek için öncelikle şehir seçmelisiniz!', 'namazvakti'),
				'ilce_secilmemis'	=> __('Vakitleri çekebilmek için öncelikle ilçe seçmelisiniz!', 'namazvakti'),
				'hata_veri_cekilemedi'	=> __('Sunucudan veri çekilemedi!', 'namazvakti')
			)); // ajax işlemleri için bilgi ve js dosyalarının yerelleştirilmesi için gerekli

		} else {
			wp_localize_script( 'era-ajax-request', 'EraAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php') ) ); // admin için
			wp_enqueue_script( 'era-ajax-request', plugins_url( "/assets/js/namazvakti-admin.js", __FILE__), array( 'jquery' ), NV_VERSION ); // admin namazvakti js eklentisi
		}
	}

	/**
	 * Admin sayfaları için stil ve script dosyalarını yükler
	 */
	public function _namazvakti_admin_scripts_styles($hook) {
		// Sadece eklenti sayfalarında yükle
		if (strpos($hook, 'settings_page_namazvakti') !== false) {
			wp_enqueue_style('namazvakti-admin-style', plugins_url('/assets/css/namazvakti-admin.css', __FILE__), array(), NV_VERSION);
			wp_enqueue_script('namazvakti-admin-script', plugins_url('/assets/js/namazvakti-admin.js', __FILE__), array('jquery'), NV_VERSION, true);
		}
	}
	
	/**
	 * Ön yüz için stil ve script dosyalarını yükler
	 */
	public function _namazvakti_scripts_styles() {
		// Değişkenleri al
		$varsayilan_ilce = get_option( NV_DB_DEFAULT_TOWN_NAME );
		$vakit_dili = get_option('namazvakti_vakit_dili', 'tr');
		
		// Css dosyasını ekleyelim
		wp_enqueue_style( 'namazvakti-reset', plugins_url( 'assets/css/reset.css', __FILE__ ) );
		wp_enqueue_style( 'namazvakti-style', plugins_url( 'assets/css/namazvakti-style.css', __FILE__ ) );
		
		// Javascript dosyaları
		wp_enqueue_script('jquery');
		wp_enqueue_script('namazvakti-js', plugins_url( 'assets/js/namazvakti.js', __FILE__ ), array('jquery'), null, true);
		
		// Javascript dosyasının içerisinde kullanılacak değişkenleri tanımlıyoruz.
		wp_localize_script('namazvakti-js', 'nvAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('nv-ajax-nonce'),
			'vakit_dili' => $vakit_dili
		));
	}

	/*
		Eski güncellemelere ait veritabanı bilgilerini uçurur! Yenilerini yerine koyar!
	*/
	public function update_namazvakti_plugin()
	{
		// renk seçeneği yoksa ekle! daha öncekilerde problem olmuştu!
		if ( get_option( NV_DB_WIDGET_COLORSET ) === FALSE )
		{
			add_option( NV_DB_WIDGET_COLORSET, '#C4364C|#F04862|#F04862|#364363|#50587C');
		}

		// daha önceden ekli değilse ekler!
		// ülke ekle!
		if ( get_option( NV_DB_DEFAULT_COUNTRY_NAME ) === FALSE )
		{
			add_option( NV_DB_DEFAULT_COUNTRY_NAME, 2);
		}

		// şehir ekle
		if ( get_option( NV_DB_DEFAULT_CITY_NAME ) === FALSE )
		{
			add_option( NV_DB_DEFAULT_CITY_NAME, 521);
		}

		// ilçe ekle!
		if ( get_option( NV_DB_DEFAULT_TOWN_NAME ) === FALSE )
		{
			add_option( NV_DB_DEFAULT_TOWN_NAME, 9352);
		}

		// güncelleme sonrasında veri sayı değilse günceller!
		// ülke ekle!
		if ( is_numeric( get_option( NV_DB_DEFAULT_COUNTRY_NAME ) ) === FALSE )
		{
			update_option( NV_DB_DEFAULT_COUNTRY_NAME, 2);
		}

		// şehir ekle
		if ( is_numeric( get_option( NV_DB_DEFAULT_CITY_NAME ) ) === FALSE )
		{
			update_option( NV_DB_DEFAULT_CITY_NAME, 521);
		}

		// ilçe ekle!
		if ( is_numeric( get_option( NV_DB_DEFAULT_TOWN_NAME ) ) === FALSE )
		{
			update_option( NV_DB_DEFAULT_TOWN_NAME, 9352);
		}

		if ( ! file_exists( NV_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'v.' . NV_VERSION . '.version' ) )
		{
			// Cache dizininin varlığını kontrol et, yoksa oluştur
			$cache_dir = NV_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'cache';
			if (!file_exists($cache_dir)) {
				mkdir($cache_dir, 0755, true);
			}
			
			// Mevcut cache dosyalarını temizle
			$dir = glob( $cache_dir . DIRECTORY_SEPARATOR . '*.*' );
			if (is_array($dir)) {
				foreach( $dir as $cfile )
				{
					@unlink($cfile);
				}
			}

			// Versiyon dosyasını oluştur
			$version_file = $cache_dir . DIRECTORY_SEPARATOR . 'v.' . NV_VERSION . '.version';
			$fp = @fopen($version_file, "w");
			if ($fp) {
				fclose($fp);
			}
		}
	}

	/*
		Ajax ile gelen sorguları yap ve geri döndür!
	*/
	public function namazvakti_ajax()
	{
		switch($_POST['do'])
		{
			case 'getCities':
				$sehirler = $this->nv->sehirler($_POST['country'], 'json');
				echo $sehirler;
				die();
			break;

			case 'getLocations':
				$ilceler = $this->nv->ilceler($_POST['country'], $_POST['city'], 'json');
				echo $ilceler;
				die();
			break;

			case 'getTimes':

				// sessionları buraya ekle!
				$_SESSION[NV_DB_DEFAULT_TOWN_NAME] = $_POST['town'];

				$vakit = $this->nv->vakit($_POST['town'], 'json');
				echo $vakit;
				die();
			break;
		}
	}


	/*
		namazvakti admin menü init - admin menüsünü ayarlar
	*/
	public function _namazvakti_admin_menu_init()
	{
		add_options_page( __( 'Namaz Vakti Ayarları', 'namazvakti' ), __( 'Namaz Vakti Ayarları', 'namazvakti' ), 'manage_options', 'namazvakti', array( $this, '__namazvakti_ayar_sayfalari' ) );
	}

	/*
		Plugin sayfasındaki eklentimize yeni linkler ekleyelim
	*/
	public function __namazvakti_plugin_linkleri( $linkler, $dosya )
	{
		if( NV_NAME == $dosya )
		{
			$settings	= sprintf( '<a href="%s"> %s </a>', admin_url( 'options-general.php?page=namazvakti' ), __( 'Ayarlar', 'namazvakti' ) );
			$colors		= sprintf( '<a href="%s"> %s </a>', admin_url( 'options-general.php?page=namazvakti&tab=style' ), __( 'Renk Ayarları', 'namazvakti' ) );
			array_unshift( $linkler, $colors );
			array_unshift( $linkler, $settings);
		}
		return $linkler;
	}

	/*
		namazvakti option sayfaları!
	*/

	public function __namazvakti_ayar_sayfalari()
	{
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __('Bu sayfaya erişim izniniz bulunmuyor.', 'namazvakti') );
		}
		?>
        <h2><?php _e('Wordpress için Namaz Vakitleri', 'namazvakti'); ?></h2>
        <?php

		// Tablar
		$default_tabs = array(
			'general'	=> __( 'Genel Ayarlar', 'namazvakti' ),
			'style'		=> __( 'Renk Ayarları', 'namazvakti' ),
			'about'		=> __( 'Hakkında', 'namazvakti' ),
		);

		// Tab isimlerini filitrele
		$tabs = apply_filters( 'namazvakti_settings_tabs', $default_tabs);

		?><h2 class="nav-tab-wrapper"><?php

		$ctab = isset( $_GET['tab'] ) === false ? 'general' : $_GET['tab'];
		foreach( $tabs as $tab => $name )
		{
			$class = ( $tab == $ctab ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='?page=namazvakti&tab=$tab'>$name</a>";
		}

		?>
        </h2>
        <div class="wrap">
        <?php
            // Tab içeriklerini göstermek için 'namazvakti_admin_page_' başlangıcı kullanarak diğer sayfaları ekler ve fonksiyonlarını çağırır.
            $this->__ayar_sayfasini_getir( $ctab );
        ?>
        </div>
        <?php

	}

	/*
		Ayar sayfası varsa getirir - yoksa yok der!
	*/
	private function __ayar_sayfasini_getir( $tab='genel' )
	{
		if( file_exists( NV_PLUGIN_DIR . 'include' . DIRECTORY_SEPARATOR . 'page.' . $tab .  '.php' ) )
		{
			include_once NV_PLUGIN_DIR . 'include' . DIRECTORY_SEPARATOR . 'page.' . $tab .  '.php';
		} else {
			_e( 'Ayarlar sayfası bulunamadı!', 'namazvakti' );
		}
	}


	/*
		Widgetleri ekle
	*/
	public function __widget_init()
	{
		register_widget( 'NV_Widget' );
	}

	/*
		API bağlantısını test et
	*/
	public function test_api_connection()
	{
		// Nonce kontrolü
		check_ajax_referer('test_api_connection_nonce', 'nonce');
		
		// Cache dizinini kontrol et ve oluştur
		$this->check_cache_directory();
		
		// Parametreleri al
		$country = isset($_POST['country']) ? intval($_POST['country']) : 0;
		$city = isset($_POST['city']) ? intval($_POST['city']) : 0;
		$town = isset($_POST['town']) ? intval($_POST['town']) : 0;
		
		// Parametreler boşsa varsayılan değerleri kullan
		if ($country == 0) {
			$country = get_option(NV_DB_DEFAULT_COUNTRY_NAME);
		}
		
		if ($city == 0) {
			$city = get_option(NV_DB_DEFAULT_CITY_NAME);
		}
		
		if ($town == 0) {
			$town = get_option(NV_DB_DEFAULT_TOWN_NAME);
		}
		
		// Hata detaylarını tutacak dizi
		$debug_info = [
			'country' => $country,
			'city' => $city,
			'town' => $town,
			'api_responses' => [],
			'curl_errors' => []
		];
		
		// API'ye istek gönder
		try {
			// Önce API'nin çalışıp çalışmadığını kontrol et
			$api_test_url = "https://api.aladhan.com/v1/methods";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $api_test_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
			
			$response = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$curl_error = curl_error($ch);
			curl_close($ch);
			
			// Debug bilgilerini ekle
			$debug_info['api_responses']['methods'] = [
				'url' => $api_test_url,
				'http_code' => $httpCode,
				'response' => $response,
				'curl_error' => $curl_error
			];
			
			if ($curl_error) {
				$debug_info['curl_errors'][] = $curl_error;
			}
			
			if ($httpCode != 200) {
				echo json_encode([
					'success' => false,
					'message' => sprintf(__('API bağlantısı başarısız! HTTP kodu: %d', 'namazvakti'), $httpCode),
					'debug_info' => $debug_info
				]);
				wp_die();
			}
			
			// _yerBilgisi fonksiyonu private olduğu için doğrudan erişemiyoruz
			// Bunun yerine vakit fonksiyonunu kullanarak yer bilgisini dolaylı olarak alacağız
			
			// Adresler dosyasını manuel olarak okuyalım
			$adresler_file = plugin_dir_path(__FILE__) . "include/db/adresler.ndb";
			$yerBilgisi = [];
			
			if (file_exists($adresler_file)) {
				$adresler_content = @file_get_contents($adresler_file);
				if ($adresler_content) {
					$adresler = json_decode($adresler_content, true) ?: [];
					if (isset($adresler[$town])) {
						$yerBilgisi = $adresler[$town];
					}
				}
			}
			
			$debug_info['yer_bilgisi'] = $yerBilgisi;
			
			// Yer bilgisi boş ise hata mesajı göster
			if (empty($yerBilgisi)) {
				echo json_encode([
					'success' => false,
					'message' => __('Seçilen konum bilgileri bulunamadı! Lütfen farklı bir konum seçin.', 'namazvakti'),
					'debug_info' => $debug_info
				]);
				wp_die();
			}
			
			// API URL'sini oluştur
			$bugun = date('d-m-Y');
			$method = 13; // Diyanet İşleri Başkanlığı, Turkey
			
			$apiUrl = "https://api.aladhan.com/v1/timingsByCity/{$bugun}";
			$params = [
				'city' => $yerBilgisi['sehir'],
				'country' => $yerBilgisi['ulke'],
				'method' => $method
			];
			
			$apiUrl .= '?' . http_build_query($params);
			$debug_info['api_url'] = $apiUrl;
			
			// API'ye doğrudan istek gönder
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
			
			$direct_response = curl_exec($ch);
			$direct_httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$direct_curl_error = curl_error($ch);
			curl_close($ch);
			
			// Debug bilgilerini ekle
			$debug_info['api_responses']['timings'] = [
				'url' => $apiUrl,
				'http_code' => $direct_httpCode,
				'response' => $direct_response,
				'curl_error' => $direct_curl_error
			];
			
			if ($direct_curl_error) {
				$debug_info['curl_errors'][] = $direct_curl_error;
			}
			
			// Şimdi namaz vakitlerini çek
			$vakit = $this->nv->vakit($town);
			$debug_info['vakit_response'] = $vakit;
			
			// API URL'sini almaya çalış
			if (isset($this->nv->api_url)) {
				$debug_info['nv_api_url'] = $this->nv->api_url;
			}
			
			// Doğrudan API'yi tekrar çağırarak veri çekelim
			$direct_api_url = "https://api.aladhan.com/v1/timingsByCity/" . date('d-m-Y');
			$direct_params = [
				'city' => $this->_formatCityName($yerBilgisi['sehir']),
				'country' => $this->_formatCountryName($yerBilgisi['ulke']),
				'method' => 13
			];
			
			$direct_api_url .= '?' . http_build_query($direct_params);
			$debug_info['direct_api_url'] = $direct_api_url;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $direct_api_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
			
			$direct_response = curl_exec($ch);
			$direct_httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$direct_curl_error = curl_error($ch);
			curl_close($ch);
			
			// Debug bilgilerini ekle
			$debug_info['direct_api'] = [
				'url' => $direct_api_url,
				'http_code' => $direct_httpCode,
				'curl_error' => $direct_curl_error
			];
			
			if ($direct_httpCode == 200) {
				$direct_data = json_decode($direct_response, true);
				if (isset($direct_data['code']) && $direct_data['code'] == 200) {
					$debug_info['direct_api']['success'] = true;
					
					// Manuel olarak bir vakitler dizisi oluşturalım
					$tarih = date('d.m.Y');
					$tarihUzun = date('d F Y l');
					$vakitler = [];
					
					if (isset($direct_data['data']['timings'])) {
						$vakitler[$tarih] = [
							'tarih' => $tarih,
							'tarih_uzun' => $tarihUzun,
							'imsak' => $direct_data['data']['timings']['Imsak'] ?? '',
							'gunes' => $direct_data['data']['timings']['Sunrise'] ?? '',
							'ogle' => $direct_data['data']['timings']['Dhuhr'] ?? '',
							'ikindi' => $direct_data['data']['timings']['Asr'] ?? '',
							'aksam' => $direct_data['data']['timings']['Maghrib'] ?? '',
							'yatsi' => $direct_data['data']['timings']['Isha'] ?? ''
						];
						
						$debug_info['manuel_vakitler'] = $vakitler;
					}
				}
			}
			
			if ($vakit['durum'] == 'basarili') {
				echo json_encode([
					'success' => true,
					'message' => __('API bağlantısı başarılı!', 'namazvakti'),
					'data' => $vakit,
					'debug_info' => $debug_info
				]);
			} else {
				// Hata detaylarını ekle
				$error_details = '';
				if (isset($vakit['aciklama'])) {
					$error_details = $vakit['aciklama'];
				}
				
				// Doğrudan API yanıtını kontrol et
				$direct_data = json_decode($direct_response, true);
				if ($direct_httpCode == 200 && isset($direct_data['code']) && $direct_data['code'] == 200) {
					$error_details .= ' ' . __('API doğrudan çağrıldığında başarılı yanıt veriyor, ancak eklenti içinde bir sorun oluşuyor olabilir.', 'namazvakti');
					
					// En son yaptığımız değişiklikleri test edelim - manuel olarak işlem yapalım
					try {
						// Şehir bilgisini doğrudan alalım
						$adresler_file = plugin_dir_path(__FILE__) . "include/db/adresler.ndb";
						$yerBilgisi_manuel = [];
						
						if (file_exists($adresler_file)) {
							$adresler_content = @file_get_contents($adresler_file);
							if ($adresler_content) {
								$adresler = json_decode($adresler_content, true) ?: [];
								if (isset($adresler[$town])) {
									$yerBilgisi_manuel = $adresler[$town];
								}
							}
						}
						
						$debug_info['yerBilgisi_manuel'] = $yerBilgisi_manuel;
						
						if (!empty($yerBilgisi_manuel)) {
							// Bugünün tarihini alalım
							$bugun = date('d-m-Y');
							
							// API URL'sini oluştur
							$apiUrl_manuel = "https://api.aladhan.com/v1/timingsByCity/{$bugun}";
							$params_manuel = [
								'city' => $this->_formatCityName($yerBilgisi_manuel['sehir']),
								'country' => $this->_formatCountryName($yerBilgisi_manuel['ulke']),
								'method' => 13
							];
							
							$apiUrl_manuel .= '?' . http_build_query($params_manuel);
							$debug_info['apiUrl_manuel'] = $apiUrl_manuel;
							
							// API'ye istek gönder
							$ch_manuel = curl_init();
							curl_setopt($ch_manuel, CURLOPT_URL, $apiUrl_manuel);
							curl_setopt($ch_manuel, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch_manuel, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($ch_manuel, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt($ch_manuel, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
							
							$response_manuel = curl_exec($ch_manuel);
							$httpCode_manuel = curl_getinfo($ch_manuel, CURLINFO_HTTP_CODE);
							curl_close($ch_manuel);
							
							$debug_info['api_manuel'] = [
								'http_code' => $httpCode_manuel
							];
							
							if ($httpCode_manuel == 200) {
								$data_manuel = json_decode($response_manuel, true);
								
								if (isset($data_manuel['code']) && $data_manuel['code'] == 200 && isset($data_manuel['data'])) {
									// Namaz vakitlerini manuel olarak oluşturalım
									$tarih = date('d.m.Y');
									$tarihUzun = date('d F Y l');
									
									$hicri_date = isset($data_manuel['data']['date']['hijri']['date']) ? $data_manuel['data']['date']['hijri']['date'] : '';
									$hicri_day = isset($data_manuel['data']['date']['hijri']['day']) ? $data_manuel['data']['date']['hijri']['day'] : '';
									$hicri_month = isset($data_manuel['data']['date']['hijri']['month']['en']) ? $data_manuel['data']['date']['hijri']['month']['en'] : '';
									$hicri_year = isset($data_manuel['data']['date']['hijri']['year']) ? $data_manuel['data']['date']['hijri']['year'] : '';
									
									$hicri_uzun = '';
									if (!empty($hicri_day) && !empty($hicri_month) && !empty($hicri_year)) {
										$hicri_uzun = $hicri_day . ' ' . $hicri_month . ' ' . $hicri_year;
									}
									
									$vakitler_manuel = [];
									$vakitler_manuel[$tarih] = [
										'tarih' => $tarih,
										'tarih_uzun' => $tarihUzun,
										'hicri' => $hicri_date,
										'hicri_uzun' => $hicri_uzun,
										'imsak' => $data_manuel['data']['timings']['Imsak'] ?? '',
										'gunes' => $data_manuel['data']['timings']['Sunrise'] ?? '',
										'ogle' => $data_manuel['data']['timings']['Dhuhr'] ?? '',
										'ikindi' => $data_manuel['data']['timings']['Asr'] ?? '',
										'aksam' => $data_manuel['data']['timings']['Maghrib'] ?? '',
										'yatsi' => $data_manuel['data']['timings']['Isha'] ?? ''
									];
									
									// Manuel veri oluşturalım
									$icerik_manuel = [
										'ulke' => $yerBilgisi_manuel['ulke'],
										'sehir' => $yerBilgisi_manuel['sehir'],
										'ilce' => $yerBilgisi_manuel['ilce'] ?? '',
										'yer_adi' => $yerBilgisi_manuel['uzun_adi'],
										'vakitler' => $vakitler_manuel
									];
									
									$vakit_manuel = [
										'durum' => 'basarili',
										'veri' => $icerik_manuel
									];
									
									$debug_info['vakit_manuel'] = $vakit_manuel;
									
									// Eğer manuel oluşturma başarılıysa, bunu sonuç olarak kullanalım
									echo json_encode([
										'success' => true,
										'message' => __('Manuel olarak oluşturulan API yanıtı başarılı!', 'namazvakti'),
										'data' => $vakit_manuel,
										'debug_info' => $debug_info
									]);
									wp_die();
								}
							}
						}
					} catch (Exception $e) {
						$debug_info['manuel_error'] = $e->getMessage();
					}
					
					// Yer bilgisi sorununu kontrol et
					if (empty($yerBilgisi['sehir']) || empty($yerBilgisi['ulke'])) {
						$error_details .= ' ' . __('Yer bilgisi eksik veya hatalı olabilir.', 'namazvakti');
					}
					
					// Cache sorununu kontrol et
					$cache_dir = plugin_dir_path(__FILE__) . 'cache/';
					if (!is_dir($cache_dir) || !is_writable($cache_dir)) {
						$error_details .= ' ' . __('Cache dizini bulunamadı veya yazılabilir değil.', 'namazvakti');
					}
					
					// Namaz sınıfını geçici olarak oluşturup test edelim
					$test_namaz = new Namaz(plugin_dir_path(__FILE__) . 'cache/');
					$test_sonuc = $test_namaz->vakit($town);
					$debug_info['test_namaz_sonuc'] = $test_sonuc;
					
					// API yanıtını ve yer bilgisini karşılaştır
					$error_details .= ' ' . __('API yanıtı alındı ancak işlenemedi. Ülke ve şehir adlarının formatı düzeltildi, lütfen tekrar deneyin.', 'namazvakti');
					
					// Hata ayıklama bilgilerini ekle
					$debug_info['cache_dir'] = [
						'exists' => is_dir($cache_dir),
						'writable' => is_writable($cache_dir),
						'path' => $cache_dir
					];
					
					// Formatlanan ülke ve şehir adlarını göster
					$debug_info['formatted_names'] = [
						'original_country' => $yerBilgisi['ulke'],
						'formatted_country' => $this->_formatCountryName($yerBilgisi['ulke']),
						'original_city' => $yerBilgisi['sehir'],
						'formatted_city' => $this->_formatCityName($yerBilgisi['sehir'])
					];
				} else {
					$error_details .= ' ' . __('API doğrudan çağrıldığında da hata veriyor. Seçilen konum bilgileri hatalı olabilir.', 'namazvakti');
					
					// API yanıtını kontrol et
					if (isset($direct_data['data']) && isset($direct_data['data']['meta']) && isset($direct_data['data']['meta']['timezone'])) {
						$error_details .= ' ' . sprintf(__('API zaman dilimi: %s', 'namazvakti'), $direct_data['data']['meta']['timezone']);
					}
					
					if (isset($direct_data['data']) && isset($direct_data['data']['meta']) && isset($direct_data['data']['meta']['method'])) {
						$error_details .= ' ' . sprintf(__('API metodu: %s', 'namazvakti'), $direct_data['data']['meta']['method']['name']);
					}
				}
				
				echo json_encode([
					'success' => false,
					'message' => __('Namaz vakitleri çekilemedi!', 'namazvakti') . ' ' . $error_details,
					'data' => $vakit,
					'debug_info' => $debug_info
				]);
			}
		} catch (Exception $e) {
			$debug_info['exception'] = [
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'trace' => $e->getTraceAsString()
			];
			
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage(),
				'debug_info' => $debug_info
			]);
		}
		
		wp_die();
	}

	/**
	 * Ülke adını API için uygun formata dönüştürür
	 */
	private function _formatCountryName($countryName) {
		// Namaz sınıfındaki formatCountryName fonksiyonunu çağır
		return $this->nv->formatCountryName($countryName);
	}
	
	/**
	 * Şehir adını API için uygun formata dönüştürür
	 */
	private function _formatCityName($cityName) {
		// Namaz sınıfındaki formatCityName fonksiyonunu çağır
		return $this->nv->formatCityName($cityName);
	}

	/**
	 * Cache temizleme fonksiyonu - AJAX ile çağrılır
	 */
	public function clear_cache() {
		// Nonce kontrolü
		check_ajax_referer('clear_cache_nonce', 'nonce');
		
		// Cache dizinini kontrol et
		$cache_dir = plugin_dir_path(__FILE__) . 'cache/';
		
		$files_removed = 0;
		$total_files = 0;
		
		if (is_dir($cache_dir)) {
			$files = glob($cache_dir . 'cache_*.ndb');
			$total_files = count($files);
			
			foreach ($files as $file) {
				if (is_file($file)) {
					if (unlink($file)) {
						$files_removed++;
					}
				}
			}
		}
		
		echo json_encode([
			'success' => true,
			'message' => sprintf(__('Cache temizlendi. %d dosya silindi.', 'namazvakti'), $files_removed),
			'total_files' => $total_files,
			'removed_files' => $files_removed
		]);
		
		wp_die();
	}

	/**
	 * Çoklu dil desteği için tüm dil dosyalarını yükle
	 */
	public function translation_support() {
		load_plugin_textdomain('namazvakti', false, dirname(plugin_basename(__FILE__)) . '/languages');
		
		// Arayüz dilini yükle
		$locale = get_option('namazvakti_locale', 'tr_TR');
		if ($locale != 'tr_TR') {
			$mo_file = dirname(plugin_basename(__FILE__)) . '/languages/namazvakti-' . $locale . '.mo';
			load_textdomain('namazvakti', WP_PLUGIN_DIR . '/' . $mo_file);
		}
	}

}

// Eklentiyi Çalıştır!
$namazvakti = new WP_Namazvakti();
