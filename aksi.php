<?php
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
	//panggil koneksi database
	include "koneksi.php";

	//pengujian jika tombol upload diklik
	if(isset($_POST['bupload']))
	{
		//ekstensi file yang boleh di upload
		$ekstensi_diperbolehkan = array('txt');
		$nama = $_FILES['file']['name']; // untuk mendapatkan nama file yang diupload
		//nama_file.jpg
		$x = explode('.', $nama);
		$ekstensi = strtolower(end($x));
		$ukuran = $_FILES['file']['size']; //untuk mendapatkan ukuran file yang akan di upload
		$file_tmp = $_FILES['file']['tmp_name']; //untuk mendapatkan temporary file yang akan di upload (tmp)

		//uji jika ekstensi file yang diupload sesuai
		if(in_array($ekstensi, $ekstensi_diperbolehkan) == true){
			//boleh upload file
			//uji jika ukuran file dibawah 1mb
			if($ukuran < 1044070){
				//jika ukuran sesuai
				//PINDAHKAN FILE YANG DI UPLOAD KE FOLDER FILE aplikasi
				move_uploaded_file($file_tmp, 'file/'.$nama);
//baca file
				$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
				$stemer = $stemmerFactory->createStemmer();

				$testfile = "file/text.txt";
				$file = fopen($testfile, "r");
				$filedata = fread($file, filesize($testfile));
				fclose($file);
				//echo $filedata;
				require_once __DIR__ . '/vendor/autoload.php';

				// stemmer
				$stemmer  = $stemmerFactory->createStemmer();

				// stem
				$sentence = $filedata ;
				$output   = $stemmer->stem($sentence);


					// vektor
				require_once __DIR__ . '/vendor/autoload.php';
				$vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());

			$vectorizer->fit($sentence);
				$vectorizer->transform($sentence);
				
				$serialisasi = serialize($sentence); 

				//simpan data ke dalam database
				$simpan = mysqli_query($koneksi, "INSERT INTO 
												  tupload 
												  VALUES ('', '$nama', '$output', '$sentence')");
				if($simpan){
					echo "<script>alert('FILE BERHASIL DI UPLOAD'); document.location='index.php'</script>";
				}else{
					echo "<script>alert('GAGAL MENGUPLOAD FILE'); document.location='index.php'</script>";
				}

			}else{
				//ukuran tidak sesuai
				echo "<script>alert('UKURAN FILE TERLALU BESAR, MAX. 1MB'); document.location='index.php'</script>";
			}
		}else{
			//ektensi file yang di upload tidak sesuai
			echo "<script>alert('EKSTENSI FILE YANG DI UPLOAD TIDAK DIPERBOLEHKAN'); document.location='index.php'</script>";
		}


	}


?>