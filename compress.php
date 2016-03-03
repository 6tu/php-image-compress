<?
$srcfolder='iPhone/';
$desfolder='compressed/';
$ext=array('jpg');//Ö§³Öpng,jpg,gif

require 'PHP_JPEG_Metadata_Toolkit_1.12/EXIF.php';
require 'FileUploader.php';
$files = scandir($srcfolder);
$total = count($files);
$i = 1;
if(!file_exists($desfolder)){
	mkdir($desfolder, 0777, TRUE);
}
foreach ($files as $imagefile) {
	if ($imagefile == '.' || $imagefile == '..') {
		continue;
	}
	$process = ($i++) . '/' . $total . ' ';
	$file = $srcfolder . $imagefile;
	$newfile = $desfolder . $imagefile;
	$fileExt=strtolower(pathinfo($imagefile, PATHINFO_EXTENSION));
	if (in_array($fileExt, $ext)) {
		compress($file, $newfile);
		echo $process . $file . " compressed\n";
	} else {
		file_put_contents($newfile, file_get_contents($file));
		echo $process . $file . " copied\n";
	}
	$mtime = filemtime($file);
	$ctime = strftime('%d-%m-%Y %H:%M:%S', filectime($file));
	$cmd = ".\\nircmdc.exe setfilefoldertime \"$newfile\" \"$ctime\"";
	touch($newfile, $mtime);
	system($cmd);
}

function compress($file, $newfile) {
	$client = new Image_FileUploader();
	$client->setZoom(true)
		->setMaxZoomWidth(1024)
		->setZoomPercent(array(1024))
		->setCompress(true)
		->setMinCompressSize('300K')
		->setCompressPercent(0.8)
		->zoomImage($file, $newfile);
	$jpeg_exif_data = get_EXIF_JPEG($file);
	if ($jpeg_exif_data) {
		$jpeg_header_data = get_jpeg_header_data($newfile);
		$jpeg_header_data = put_EXIF_JPEG($jpeg_exif_data, $jpeg_header_data);
		return put_jpeg_header_data($newfile, $newfile, $jpeg_header_data);
	}
	return false;
}
