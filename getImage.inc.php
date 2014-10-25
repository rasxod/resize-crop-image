<?
/*
    CMS 		: SmartEscortGallery
    Created on 	: 24-10-2014
    Author 		: Goncharov S.
    Document 	: getimage.inc.php
    Company 	: Ssmart Lab.
    Site 		: ssmart.ru
*/

include $_SERVER['DOCUMENT_ROOT']."/settings/general.php";

function myImage($resImg, $outImg, $w, $h) {
	$sub = array();
	list($w_i, $h_i, $type) = getimagesize($resImg);
	if (!$w_i || !$h_i) {
		echo 'Невозможно получить длину и ширину изображения';
		return$err = '=1=';
	}
	$types = array('','gif','jpeg','png');
	$ext = $types[$type];
	if ($ext) {
		$func = 'imagecreatefrom'.$ext;
		$img = $func($resImg);
	} else {
		echo 'Некорректный формат файла';
		return $err = '=2=';
	}
	//определяем ориентацию
	if ($w_i < $h_i) {
		$w_o = $w;
		$h_o = $h;
	} else {
		$w_o = $h;
		$h_o = $w;
	}
	if ($w_i < $w_o || $h_i < $h_o) {
		echo 'некорректный размер файла';
		return $err = '=3=';
	}

	//вычисляем сторону по которой будем уменьшать
	function bigStorona($w_o, $h_o, $w_i, $h_i){
		// 700*900
		// 200/700=0,28571429
		// 300*0,28571429=85,714287
		$new_out_size = array();
		$Pr = $w_o/$w_i;
		$new_out_size[w] = $w_o;
		$new_out_size[h] = $h_i*$Pr;
		$new_out_size[x] = 0;
		$new_out_size[y] = ($new_out_size[h]-$h_o)/2;
		if ($new_out_size[h] < $h_o) {
			$Pr = $h_o/$h_i;
			$new_out_size[w] = $w_i*$Pr;
			$new_out_size[h] = $h_o;
			$new_out_size[x] = ($new_out_size[w]-$w_o)/2;
			$new_out_size[y] = 0;
		}
		print_r($new_out_size);
		return $new_out_size;
	}
	$outSize = bigStorona($w_o, $h_o, $w_i, $h_i);
	// ресэмплирование
	// print_r($outSize);
	$image_o = imagecreatetruecolor($w_o, $h_o);
	$image = imagecreatefromjpeg($resImg);
	imagecopyresampled($image_o, $image, 0, 0, $outSize['x'], $outSize['y'], $outSize['w'], $outSize['h'], $w_i, $h_i);
	imagejpeg($image_o,$outImg,100);
	
	return $err = $outImg.' =0=';
}



// пример вызова
// myImage('crop', '200', 200, $SiteRow);
if ($act == 'updMainImg') {
	//выбираем фотографии для данной галереи которую добавили для сайта
	//"SELECT * FROM sitePhotos WHERE id=".$_GET['id']." AND lastUpDate > '". $SiteRow."'"
	$allMainPhotos = $DB->select("SELECT sG.gName, sP.* 
									FROM siteGallery AS sG 
									LEFT JOIN sitePhotos AS sP 
										ON sP.sGalleryId = sG.id 
										WHERE sG.sitesID = ".$_GET['id']." 
										AND sP.lastUpDate > '".$SiteRow['lastUpDate']."' 
										AND spMain = '1'");
	// echo "SELECT sG.gName, sP.* 
	// 								FROM siteGallery AS sG 
	// 								LEFT JOIN sitePhotos AS sP 
	// 									ON sP.sGalleryId = sG.id 
	// 									WHERE sG.sitesID = ".$_GET['id']." 
	// 									AND sP.lastUpDate > '".$SiteRow['lastUpDate']."' 
	// 									AND spMain = '1'";
	foreach ($allMainPhotos as $key => $aMP) {
		echo $_SERVER['DOCUMENT_ROOT'].$aMP['pAdress'].", ".$_SERVER['DOCUMENT_ROOT'].$aMP['mainCash'].", '200', '300' <br />";
		print_r(myImage($_SERVER['DOCUMENT_ROOT'].$aMP['pAdress'], $_SERVER['DOCUMENT_ROOT'].$aMP['mainCash'], '200', '300'));
	}
}
?>
