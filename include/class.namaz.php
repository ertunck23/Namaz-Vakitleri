<?php
/**
 * Namaz - Diyanet İşleri Başkanlığından veri çekme sınıfı
 *
 * @author		Erdem ARSLAN <http://www.erdemarslan.com> <erdemsaid@gmail.com>
 * @copyright	Copyright (c) 2006-2017 erdemarslan.com
 * @link		http://www.eralabs.net/
 * @version     8.0
 * @license		GPL v2.0
 */

require_once "class.simple_html_dom.php";
require_once "class.hicri.php";


Class Namaz {

	// Sınıf içerisinde işlenecek veriler
	protected $veritabani, $adresler, $hicriSinif;

	// Ülke İsimlerinin dışarıdan alınabilmesini sağlayan değişken. Wordpress eklentisi için gerekli olacak!
	public $ulkeIsimleri = array();
	public $sehirIsimleri = array();
   public $ilceIsimleri = array();

	// API URL'sini tutacak değişken
	public $api_url;

	// Cache ile ilgili veriler
	protected $cache;
	protected $cacheKlasorYolu = "cache";

	// Miladi Ay isimleri. Dışarıdan değiştirilebilir. O yüzden diğer dillere tercüme de edilebilir!
	public $miladiAylar = array(
		1  =>  'Ocak',
		2  =>  'Şubat',
		3  =>  'Mart',
		4  =>  'Nisan',
		5  =>  'Mayıs',
		6  =>  'Haziran',
		7  =>  'Temmuz',
		8  =>  'Ağustos',
		9  =>  'Eylül',
		10  =>  'Ekim',
		11  =>  'Kasım',
		12  =>  'Aralık'
		);

	// Haftanın gün isimler. Aynı miladi aylar gibi tercüme edilebilir.
	public $haftaninGunleri = array(
		1  => 'Pazartesi',
		2  => 'Salı',
		3  => 'Çarşamba',
		4  => 'Perşembe',
		5  => 'Cuma',
		6  => 'Cumartesi',
		7  => 'Pazar'
		);

   // Hicri aylar. Aynı miladi aylar ve günler gibi bunlar da dışarıdan erişilip tercüme edilebilir!
	public $hicriAylar = array(
		1  =>  "Muharrem",
		2  =>  "Safer",
		3  =>  "Rebiü'l-Evvel",
		4  =>  "Rebiü'l-Ahir",
		5  =>  "Cemaziye'l-Evvel",
		6  =>  "Cemaziye'l-Ahir",
		7  =>  "Recep",
		8  =>  "Şaban",
		9  =>  "Ramazan",
		10  =>  "Şevval",
		11  =>  "Zi'l-ka'de",
		12  =>  "Zi'l-Hicce"
		);

	############################################################################
   #                          GENEL FONKSIYONLAR                              #
   ############################################################################


	// Yapılandırıcı Fonksiyon
	public function __construct($cacheklasoru = NULL) {
		// Bu dosyanın konumu
		$dosyaYolu = (__DIR__);

		// Cache klasörünü tanımlayalım tam olarak neresi diye! Daha sonradan değiştirilebilir
		$this->cache = is_null($cacheklasoru) ? $dosyaYolu . DIRECTORY_SEPARATOR . $this->cacheKlasorYolu . DIRECTORY_SEPARATOR : $cacheklasoru;
		
		// Cache dizininin varlığını kontrol et, yoksa oluştur
		if (!file_exists($this->cache)) {
			@mkdir($this->cache, 0755, true);
		}

		// Veritabanından listeleri alalım!
		$yerler_file = $dosyaYolu . DIRECTORY_SEPARATOR . "db" . DIRECTORY_SEPARATOR . "yerler.ndb";
		$this->veritabani = [];
		
		if (file_exists($yerler_file)) {
			$yerler_content = @file_get_contents($yerler_file);
			if ($yerler_content) {
				$this->veritabani = json_decode($yerler_content, true) ?: [];
			}
		}

		// Hicri takvim sınıfını ekleyelim!
		$this->hicriSinif = new HijriDateTime();
	}

   // Yıkıcı Fonksiyon
   public function __destruct() {
      $this->cache = null;
      $this->veritabani = null;
      $this->hicriSinif = null;
   }

	// Cache klasörünü değiştirir!
	public function cacheKlasoru($cacheklasoru) {
		$this->cache = $cacheklasoru;
		return $this;
	}

	// Ülke listesini verir!
	public function ulkeler($cikti = "array") {
      // sonuç değişkenini ayarla
		$sonuc = array(
			'durum' => 'hata',
			'veri' => array()
		);

		// Veritabanından sadece ülke adlarını ve isimlerini döndür!
		foreach ($this->veritabani as $ulke_id => $bilgi) {
			$sonuc['durum'] = 'basarili';
			$sonuc['veri'][$ulke_id] = isset($this->ulkeIsimleri[$bilgi['ulke_adi']]) ? $this->ulkeIsimleri[$bilgi['ulke_adi']] : $bilgi['ulke_adi'];
		}

		// fonksiyon dışına aktar!
		return ($cikti == 'array' ? $sonuc : json_encode($sonuc));
	}

	// Şehirlerin listesini verir! Ülke id verilmesi gerekir!
	public function sehirler($ulke, $cikti = "array") {

		$sonuc = array(
			'durum' => 'hata',
         'ilce' => 0,
			'veri' => array()
		);

		if(isset($this->veritabani[$ulke])) {
			$sonuc['durum'] = 'basarili';

         $ulke = $this->veritabani[$ulke];
         if($ulke['ilce_listesi_varmi']) {
            foreach ($ulke['sehirler'] as $sehir_id => $bilgi) {
               $sonuc['veri'][$sehir_id] = isset($this->sehirIsimleri[$bilgi['sehir_adi']]) ? $this->sehirIsimleri[$bilgi['sehir_adi']] : $bilgi['sehir_adi'];
            }
         } else {
            $sonuc['ilce'] = true;

            foreach ($ulke['sehirler'] as $sehir) {
               foreach($sehir['ilceler'] as $ilce_id => $ilce_adi) {
                  $sonuc['veri'][$ilce_id] = isset($this->ilceIsimleri[$ilce_adi]) ? $this->ilceIsimleri[$ilce_adi] : $ilce_adi;
               }
            }
         }
		}

		return ($cikti == 'array' ? $sonuc : json_encode($sonuc));
	}

	// İlçelerin listesini verir! Ülke ve Şehir id verilmesi gerekir.
	public function ilceler($ulke, $sehir, $cikti = 'array') {
		
		$sonuc = array(
			'durum' => 'hata',
			'veri' => array()
		);

      if (!isset($this->veritabani[$ulke])) {
         return ($cikti == 'array' ? $sonuc : json_encode($sonuc));
      }

      $ulke = $this->veritabani[$ulke];

      if($ulke['ilce_listesi_varmi']) {
         if(isset($ulke['sehirler'][$sehir])) {
            $sonuc['durum'] = 'basarili';
            foreach ($ulke['sehirler'][$sehir]['ilceler'] as $ilce_id => $isim) {
               $sonuc['veri'][$ilce_id] = isset($this->ilceIsimleri[$isim]) ? $this->ilceIsimleri[$isim] : $isim;
            }
         }
      }

		return ($cikti == 'array' ? $sonuc : json_encode($sonuc));
	}

	// cache belleği temizler
	public function cacheTemizle() {
		$files = glob($this->cache . "*.ndb");
		if (is_array($files)) {
			array_map('unlink', $files);
		}
	}

	// Tek vakti al
	public function vakit($sehir, $cikti = 'array') {

      $sonuc = array(
         'durum' => 'hata',
         'veri' => array(),
         'debug' => array(
            'sehir_id' => $sehir,
            'function_call' => 'vakit',
            'time' => date('Y-m-d H:i:s')
         )
      );

      // Yer bilgisini biz buluyoruz. Database gerekliliğini ortadan kaldıralım şimdilik!
      $yer = $this->_yerBilgisi($sehir);
      $sonuc['debug']['yer_bilgisi'] = $yer;
      
      if (empty($yer)) {
         $sonuc['debug']['yer_bilgisi_empty'] = true;
         $sonuc['debug']['sehir_id'] = $sehir;
         return ($cikti == 'array' ? $sonuc : json_encode($sonuc));
      }

      $cacheDosyasi = "cache_" . $yer['sehir_id'] . ".ndb";
      $sonuc['debug']['cache_dosyasi'] = $cacheDosyasi;

      // bugünü alalım. Lazım olacak!
      $bugun = date("d.m.Y", time());
      $sonuc['debug']['bugun'] = $bugun;

      if($this->_cacheSor($cacheDosyasi)) {
         // cache bellekte var! Hadi şimdi irdeleyelim!
         $sonuc['debug']['cache_var'] = true;
         $veri = json_decode($this->_cacheOku($cacheDosyasi), true);
         $sonuc['debug']['cache_veri'] = is_array($veri);

         if(isset($veri['veri']['vakitler'][$bugun])) {
            $sonuc['debug']['bugun_cache_var'] = true;
            $sonuc = $veri;
         } else {
            // bugün yok la içinde! hadi tekrar çekelim!
            $sonuc['debug']['bugun_cache_yok'] = true;
            $sunucu = $this->_sunucudanVeriCek($yer['url']);
            $sonuc['debug']['sunucu_yaniti'] = $sunucu;

            // vakitler dizisinin içeriğini kontrol et
            $vakitler_var = isset($sunucu['veri']['vakitler']) && is_array($sunucu['veri']['vakitler']) && !empty($sunucu['veri']['vakitler']);
            $sonuc['debug']['vakitler_var'] = $vakitler_var;
            
            if($sunucu['durum'] == 'basarili' && $vakitler_var) {
               // herşey yolunda gitmiş çok şükür!
               $icerik = array(
                  'ulke' => $yer['ulke'],
                  'sehir' => $yer['sehir'],
                  'ilce' => $yer['ilce'],
                  'yer_adi' => $yer['uzun_adi'],
                  'vakitler' => $sunucu['veri']['vakitler']
               );

               $sonuc['durum'] = 'basarili';
               $sonuc['veri'] = $icerik;
               $sonuc['debug']['basarili'] = true;

               // cache belleğe de yazalım ayıp olmasın!
               $this->_cacheYaz($cacheDosyasi, json_encode($sonuc));
            } else {
               // Hata bilgilerini ekle
               $sonuc['debug']['api_hatasi'] = true;
               $sonuc['debug']['sunucu_durum'] = $sunucu['durum'];
               if (isset($sunucu['aciklama'])) {
                  $sonuc['aciklama'] = $sunucu['aciklama'];
               }
               if (isset($sunucu['debug'])) {
                  $sonuc['debug']['api_debug'] = $sunucu['debug'];
               }
            }
         }
      } else {
         // cache bellekte veri yok!
         // ozaman sunucudan çek! Yaz Yerine koy!
         $sonuc['debug']['cache_yok'] = true;
         $sunucu = $this->_sunucudanVeriCek($yer['url']);
         $sonuc['debug']['sunucu_yaniti'] = $sunucu;
         
         // vakitler dizisinin içeriğini kontrol et
         $vakitler_var = isset($sunucu['veri']['vakitler']) && is_array($sunucu['veri']['vakitler']) && !empty($sunucu['veri']['vakitler']);
         $sonuc['debug']['vakitler_var'] = $vakitler_var;
         
         if($sunucu['durum'] == 'basarili' && $vakitler_var) {
            // herşey yolunda gitmiş çok şükür!
            $icerik = array(
               'ulke' => $yer['ulke'],
               'sehir' => $yer['sehir'],
               'ilce' => $yer['ilce'],
               'yer_adi' => $yer['uzun_adi'],
               'vakitler' => $sunucu['veri']['vakitler']
            );

            $sonuc['durum'] = 'basarili';
            $sonuc['veri'] = $icerik;
            $sonuc['debug']['basarili'] = true;

            // cache belleğe de yazalım ayıp olmasın!
            $this->_cacheYaz($cacheDosyasi, json_encode($sonuc));
         } else {
            // Hata bilgilerini ekle
            $sonuc['debug']['api_hatasi'] = true;
            $sonuc['debug']['sunucu_durum'] = $sunucu['durum'];
            if (isset($sunucu['aciklama'])) {
               $sonuc['aciklama'] = $sunucu['aciklama'];
            }
            if (isset($sunucu['debug'])) {
               $sonuc['debug']['api_debug'] = $sunucu['debug'];
            }
         }
      } // cache dosyası yoktu biz de oluşturduk sonu!

      if($sonuc['durum'] == 'basarili') {
         $sonuc['veri']['vakit'] = $sonuc['veri']['vakitler'][$bugun] ?? [];
         unset($sonuc['veri']['vakitler']);
      }
      return ($cikti == 'array' ? $sonuc : json_encode($sonuc));
	}


	// Bütün vakitleri alır. Cacheden okumaz! Sadece cacheye yazar!
	public function vakitler($sehir, $cikti = 'array') {

      $sonuc = array(
         'durum' => 'hata',
         'veri' => array()
      );

      // Yer bilgisini biz buluyoruz. Database gerekliliğini ortadan kaldıralım şimdilik!
      $yer = $this->_yerBilgisi($sehir);
      
      if (empty($yer)) {
         return ($cikti == 'array' ? $sonuc : json_encode($sonuc));
      }

      $cacheDosyasi = "cache_" . $yer['sehir_id'] . ".ndb";

      // Aladhan.com API'sini kullanarak aylık verileri çekelim
      $method = 13; // Diyanet İşleri Başkanlığı, Turkey
      
      // Aladhan.com API endpoint'i - aylık veriler için
      $apiUrl = "https://api.aladhan.com/v1/calendarByCity/" . date('Y') . "/" . date('m');
      
      // API parametreleri
      $params = [
         'city' => $yer['sehir'],
         'country' => $yer['ulke'],
         'method' => $method
      ];
      
      // URL'yi oluştur
      $apiUrl .= '?' . http_build_query($params);
      
      // API'ye istek gönder
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $apiUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
      
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      
      if ($httpCode == 200) {
         $data = json_decode($response, true);
         if (isset($data['code']) && $data['code'] == 200 && isset($data['data'])) {
            // Verileri dönüştür
            $icerik = [
               'ulke' => $yer['ulke'],
               'sehir' => $yer['sehir'],
               'ilce' => $yer['ilce'] ?? '',
               'yer_adi' => $yer['uzun_adi'],
               'vakitler' => []
            ];
            
            foreach ($data['data'] as $gun) {
               $tarih = date('d.m.Y', strtotime($gun['date']['gregorian']['date']));
               
               $icerik['vakitler'][$tarih] = [
                  'tarih' => $tarih,
                  'tarih_uzun' => $gun['date']['gregorian']['day'] . ' ' . $gun['date']['gregorian']['month']['en'] . ' ' . $gun['date']['gregorian']['year'],
                  'hicri' => $gun['date']['hijri']['date'],
                  'hicri_uzun' => $gun['date']['hijri']['day'] . ' ' . $gun['date']['hijri']['month']['en'] . ' ' . $gun['date']['hijri']['year'],
                  'imsak' => $gun['timings']['Imsak'],
                  'gunes' => $gun['timings']['Sunrise'],
                  'ogle' => $gun['timings']['Dhuhr'],
                  'ikindi' => $gun['timings']['Asr'],
                  'aksam' => $gun['timings']['Maghrib'],
                  'yatsi' => $gun['timings']['Isha']
               ];
            }
            
            $sonuc['durum'] = 'basarili';
            $sonuc['veri'] = $icerik;
            
            // cache belleğe de yazalım
            $this->_cacheYaz($cacheDosyasi, json_encode($sonuc));
         }
      }

      return ($cikti == 'array' ? $sonuc : json_encode($sonuc));
	}


	############################################################################
	#                          ÖZEL  FONKSIYONLAR                              #
	############################################################################

   // Yer bilgisini verir!
   private function _yerBilgisi($sehir) {
      $adresler_file = (__DIR__) . DIRECTORY_SEPARATOR . "db" . DIRECTORY_SEPARATOR . "adresler.ndb";
      $adresler = [];
      
      if (file_exists($adresler_file)) {
         $adresler_content = @file_get_contents($adresler_file);
         if ($adresler_content) {
            $adresler = json_decode($adresler_content, true) ?: [];
         }
      }
      
      $veri = array();
      if(isset($adresler[$sehir])) {
         $veri = $adresler[$sehir];
      }
      $adresler = null;
      unset($adresler);
      return $veri;
   }
	// cache bellekte dosya var mı diye sorar!
	private function _cacheSor($dosyaAdi, $gunluk = FALSE) {
		// cache bellekte veri var mı diye sorar!
      $dosya = $this->cache . $dosyaAdi;
      if(file_exists($dosya) && is_readable($dosya)) {
         if($gunluk) {
            $bugun = date("dmY", time());
            $dosyaZamani = date("dmY", filemtime($dosya));
            if($bugun == $dosyaZamani) {
               return true;
            } else {
               return false;
            }
         } else {
            return true;
         }
      } else {
         return false;
      }
	}

	// cache belleğe yaz!
	private function _cacheYaz($dosyaAdi, $jsonVeri) {
		// cache belleğe veri yazar!
      $dosya = $this->cache . $dosyaAdi;
      $fp = @fopen($dosya, "w");
      if ($fp) {
         fwrite($fp, $jsonVeri);
         fclose($fp);
      }
      return;
	}

	// cache belleği okur!
	private function _cacheOku($dosyaAdi) {
		// cache bellekteki dosyayı okur
      $dosya = $this->cache . $dosyaAdi;
      if (file_exists($dosya)) {
         return @file_get_contents($dosya);
      }
      return '';
	}

   // Sunucudan veri çeker!
   private function _sunucudanVeriCek($url) {
      // URL'yi parçalara ayırıp şehir ID'sini alalım
      $urlParts = explode('/', $url);
      
      // Doğru şehir ID'sini alalım - URL formatı: /tr-TR/9541/istanbul-icin-namaz-vakti
      // Şehir ID'si sondan bir önceki parça olmalı
      $cityId = '';
      if (count($urlParts) >= 3) {
          $cityId = $urlParts[count($urlParts) - 2]; // Sondan ikinci parça
      } else {
          $cityId = end($urlParts); // Eski yöntem yedek olarak kalsın
      }
      
      // Debug bilgilerini ekleyelim
      $debug = [
          'url' => $url,
          'url_parts' => $urlParts,
          'extracted_city_id' => $cityId
      ];
      
      // _yerBilgisi fonksiyonundan şehir bilgilerini alalım
      $yerBilgisi = $this->_yerBilgisi($cityId);
      
      // Şehir bilgisi yoksa hata döndür
      if (empty($yerBilgisi)) {
         return [
            'durum' => 'hata',
            'veri' => ['mesaj' => 'Şehir bilgisi bulunamadı'],
            'debug' => array_merge($debug, ['yer_bilgisi_empty' => true, 'city_id' => $cityId])
         ];
      }
      
      // Debug bilgilerine yer bilgisini ekleyelim
      $debug['yer_bilgisi'] = $yerBilgisi;
      
      // Bugünün tarihini alalım
      $bugun = date('d-m-Y');
      
      // Hesaplama metodunu belirleyelim (Diyanet İşleri Başkanlığı metodu - 13)
      $method = 13; // Diyanet İşleri Başkanlığı, Turkey
      
      // Aladhan.com API endpoint'i
      $apiUrl = "https://api.aladhan.com/v1/timingsByCity/{$bugun}";
      
      // API parametreleri - Ülke ve şehir adlarını API'nin anlayacağı şekilde düzenle
      $ulke = $this->formatCountryName($yerBilgisi['ulke']);
      $sehir = $this->formatCityName($yerBilgisi['sehir']);
      
      $params = [
         'city' => $sehir,
         'country' => $ulke,
         'method' => $method
      ];
      
      // URL'yi oluştur
      $apiUrl .= '?' . http_build_query($params);
      
      // Debug için API URL'sini saklayalım
      $this->api_url = $apiUrl;
      
      // API'ye istek gönder
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $apiUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
      
      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curl_error = curl_error($ch);
      curl_close($ch);
      
      $sonuc = [
         'durum' => 'hata',
         'veri' => ['vakitler' => []]
      ];
      
      // cURL hatası varsa
      if ($curl_error) {
         $sonuc['aciklama'] = 'cURL hatası: ' . $curl_error;
         $sonuc['debug'] = ['curl_error' => $curl_error];
         return $sonuc;
      }
      
      if ($httpCode == 200) {
         $data = json_decode($response, true);
         if (isset($data['code']) && $data['code'] == 200 && isset($data['data'])) {
            // Veriyi dönüştürmeye çalış
            $donusturulen_veri = $this->_aladhanVerisiniDonustur($data, $yerBilgisi);
            
            // Dönüştürülmüş veri kontrol
            if (!empty($donusturulen_veri['vakitler'])) {
                $sonuc['durum'] = 'basarili';
                $sonuc['veri'] = $donusturulen_veri;
            } else {
                $sonuc['aciklama'] = 'API yanıtı dönüştürülürken hata oluştu: boş vakitler dizisi';
                $sonuc['debug'] = [
                    'donusturulen_veri' => $donusturulen_veri,
                    'api_data' => $data
                ];
            }
         } else {
            // API yanıtı başarılı değilse hata mesajı ekle
            $sonuc['aciklama'] = 'API yanıtı başarılı değil. HTTP kodu: ' . $httpCode;
            if (isset($data['data']['meta']['timezone'])) {
               $sonuc['aciklama'] .= ' Zaman dilimi: ' . $data['data']['meta']['timezone'];
            }
            if (isset($data['code'])) {
               $sonuc['aciklama'] .= ' API kodu: ' . $data['code'];
            }
            if (isset($data['status'])) {
               $sonuc['aciklama'] .= ' Durum: ' . $data['status'];
            }
            // Hata ayıklama için API yanıtını ekle
            $sonuc['api_response'] = $response;
            $sonuc['api_url'] = $apiUrl;
            $sonuc['debug'] = ['response_data' => $data];
         }
      } else {
         // HTTP kodu 200 değilse hata mesajı ekle
         $sonuc['aciklama'] = 'API yanıtı başarısız. HTTP kodu: ' . $httpCode;
         // Hata ayıklama için API yanıtını ekle
         $sonuc['api_response'] = $response;
         $sonuc['api_url'] = $apiUrl;
         $sonuc['debug'] = ['http_code' => $httpCode];
      }
      
      return $sonuc;
   }
   
   // Ülke adını API için uygun formata dönüştürür
   public function formatCountryName($countryName) {
      // Ülke adını küçük harfe çevir
      $formatted = mb_strtolower($countryName, 'UTF-8');
      
      // Türkçe karakterleri değiştir
      $turkishChars = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
      $englishChars = ['i', 'g', 'u', 's', 'o', 'c', 'I', 'G', 'U', 'S', 'O', 'C'];
      $formatted = str_replace($turkishChars, $englishChars, $formatted);
      
      // Ülke adlarını standartlaştır
      $countryMap = [
         'turkiye' => 'Turkey',
         'turkey' => 'Turkey',
         'tr' => 'Turkey'
      ];
      
      return isset($countryMap[$formatted]) ? $countryMap[$formatted] : ucfirst($formatted);
   }
   
   // Şehir adını API için uygun formata dönüştürür
   public function formatCityName($cityName) {
      // Şehir adını API'nin anlayacağı şekilde düzenle
      // Örneğin: İSTANBUL -> Istanbul
      
      // Küçük harfe çevir
      $formatted = mb_strtolower($cityName, 'UTF-8');
      
      // Türkçe karakterleri değiştir
      $turkishChars = ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'];
      $englishChars = ['i', 'g', 'u', 's', 'o', 'c', 'I', 'G', 'U', 'S', 'O', 'C'];
      $formatted = str_replace($turkishChars, $englishChars, $formatted);
      
      // İlk harfi büyük yap
      $formatted = ucfirst($formatted);
      
      // Şehir adlarını standartlaştır
      $cityMap = [
         'istanbul' => 'Istanbul',
         'ankara' => 'Ankara',
         'izmir' => 'Izmir'
      ];
      
      return isset($cityMap[$formatted]) ? $cityMap[$formatted] : $formatted;
   }
   
   // Aladhan.com API'sinden gelen veriyi eski formata dönüştürmek için yeni fonksiyon
   private function _aladhanVerisiniDonustur($apiData, $yerBilgisi) {
      $sonuc = [
         'vakitler' => []
      ];
      
      $debug = [
         'yer_bilgisi' => $yerBilgisi,
         'api_data_structure' => $this->_arrayStructure($apiData)
      ];
      
      try {
         // API yanıtını kontrol et
         $debug['api_data_keys'] = array_keys($apiData);
         $debug['has_data'] = isset($apiData['data']);
         
         if (isset($apiData['data'])) {
            $debug['data_keys'] = array_keys($apiData['data']);
            $debug['has_timings'] = isset($apiData['data']['timings']);
            
            if (isset($apiData['data']['timings'])) {
               $debug['timings_keys'] = array_keys($apiData['data']['timings']);
               
               $tarih = date('d.m.Y');
               $tarihUzun = date('d F Y l');
               $debug['current_date'] = $tarih;
               
               $hicri_date = isset($apiData['data']['date']['hijri']['date']) ? $apiData['data']['date']['hijri']['date'] : '';
               $hicri_day = isset($apiData['data']['date']['hijri']['day']) ? $apiData['data']['date']['hijri']['day'] : '';
               $hicri_month = isset($apiData['data']['date']['hijri']['month']['en']) ? $apiData['data']['date']['hijri']['month']['en'] : '';
               $hicri_year = isset($apiData['data']['date']['hijri']['year']) ? $apiData['data']['date']['hijri']['year'] : '';
               
               $debug['has_hijri_data'] = !empty($hicri_date);
               
               $hicri_uzun = '';
               if (!empty($hicri_day) && !empty($hicri_month) && !empty($hicri_year)) {
                  $hicri_uzun = $hicri_day . ' ' . $hicri_month . ' ' . $hicri_year;
               }
               
               // Vakit bilgilerini ekle
               $sonuc['vakitler'][$tarih] = [
                  'tarih' => $tarih,
                  'tarih_uzun' => $tarihUzun,
                  'hicri' => $hicri_date,
                  'hicri_uzun' => $hicri_uzun,
                  'imsak' => $apiData['data']['timings']['Imsak'] ?? '',
                  'gunes' => $apiData['data']['timings']['Sunrise'] ?? '',
                  'ogle' => $apiData['data']['timings']['Dhuhr'] ?? '',
                  'ikindi' => $apiData['data']['timings']['Asr'] ?? '',
                  'aksam' => $apiData['data']['timings']['Maghrib'] ?? '',
                  'yatsi' => $apiData['data']['timings']['Isha'] ?? ''
               ];
               
               $debug['added_vakitler'] = true;
               $debug['vakitler_count'] = count($sonuc['vakitler']);
               $debug['tarih_key'] = $tarih;
               $debug['vakitler_keys'] = array_keys($sonuc['vakitler']);
            }
         }
         
         // Yer bilgilerini ekle
         $sonuc['ulke'] = $yerBilgisi['ulke'] ?? '';
         $sonuc['sehir'] = $yerBilgisi['sehir'] ?? '';
         $sonuc['ilce'] = $yerBilgisi['ilce'] ?? '';
         $sonuc['yer_adi'] = $yerBilgisi['uzun_adi'] ?? '';
         
         // Debug bilgilerini ekle
         $sonuc['debug'] = $debug;
      } catch (Exception $e) {
         error_log('Namaz Vakti API Veri Dönüştürme Hatası: ' . $e->getMessage());
         $sonuc['debug'] = array_merge($debug, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
         ]);
      }
      
      return $sonuc;
   }
   
   // Array yapısını recursive olarak düzleştirerek debug için gösterir
   private function _arrayStructure($array, $prefix = '') {
      $result = [];
      foreach ($array as $key => $value) {
         if (is_array($value)) {
            $result[$prefix . $key] = gettype($value);
            $result = array_merge($result, $this->_arrayStructure($value, $prefix . $key . '.'));
         } else {
            $result[$prefix . $key] = gettype($value);
         }
      }
      return $result;
   }

   // 23 Nisan 1923 Pazartesi şeklindeki tarihi 23.04.1923 şeklinde döndürür
   private function _kisaTarih($uzuntarih=null) {
      if(is_null($uzuntarih)) {
         return date("d.m.Y", time());
      }

      $parca = explode(" ", $uzuntarih);
      $aylar = array_flip($this->miladiAylar);

      $ay = "00";
      $gun = $parca[0];
      $yil = $parca[2];

      if($aylar[$parca[1]] > 10) {
         $ay = $aylar[$parca[1]];
      } else {
         $ay = "0" . $aylar[$parca[1]];
      }

      return $gun . "." . $ay . "." . $yil;
   }

   // Miladi tarihi  hicri tarihe çevirir.
   private function _hicriTarih($tarih, $uzun = false) {
      if ($tarih === null) $tarih = date('d.m.Y',time());
      $t = explode('.',$tarih);
      $bugun = $this->hicriSinif->GeToHijr($t[0], $t[1], $t[2]);
      $sonuc = "";
      if($uzun) {
         $sonuc = $bugun['day'] . ' ' . $this->hicriAylar[$bugun['month']] . ' ' . $bugun['year'];
      } else {
         $sonuc = $bugun['day'] . '.' . $bugun['month'] . '.' . $bugun['year'];
      }
      return $sonuc;
   }
}