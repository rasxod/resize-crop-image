<?
/*
    CMS 	: ForAll
    Created on 	: 24-10-2014
    Author	: Goncharov S.
    Document 	: getimage.inc.php
    Company 	: Ssmart Lab.
    Site 	: ssmart.ru
*/

function myImage($resImg, $outImg, $w, $h) {
	//$sub = array();
	list($w_i, $h_i, $type) = getimagesize($resImg);
	if (!$w_i || !$h_i) {
		//echo 'Невозможно получить длину и ширину изображения';
		return $err = '1';
	}
	$types = array('','gif','jpeg','png');
	$ext = $types[$type];
	if ($ext) {
		$func = 'imagecreatefrom'.$ext;
		$img = $func($resImg);
	} else {
		//echo 'Некорректный формат файла';
		return $err = '2';
	}
	//определяем ориентацию
	if ($w_i < $h_i) {
		$w_o = $w;
		$h_o = $h;
	} else {
		$w_o = $h;
		$h_o = $w;
	}
	//проверям, вдруг изображение изначально меньше нам нужного
	if ($w_i < $w_o || $h_i < $h_o) {
		echo 'некорректный размер файла';
		return $err = '3';
	}

	//вычисляем стороны и сдвиги
	$outSize = array();
	$Pr = round($w_o/$w_i, 4);
	$outSize[w] = $w_o;
	$outSize[h] = $h_i*$Pr; //высота получится больше чем надо
	$outSize[x] = 0;
	$outSize[y] = ($outSize[h]-$h_o)/2; //сдвиг по y
	if ($outSize[h] < $h_o) {
		$Pr = round($h_o/$h_i, 4);
		$outSize[w] = $w_i*$Pr; //ширина получится больше чем надо
		$outSize[h] = $h_o;
		$outSize[x] = ($outSize[w]-$w_o)/2; //сдвиг по x
		$outSize[y] = 0;
	}

	// ресэмплирование
	$image = $img;
	$image_o = imagecreatetruecolor($w_o, $h_o);
	imagecopyresampled($image_o, $image, 0, 0, $outSize['x'], $outSize['y'], $outSize['w'], $outSize['h'], $w_i, $h_i);
	imagejpeg($image_o,$outImg,100);
	unset($image_o, $image);
	
	return $err = 0;
}



// пример вызова
/*
$resImg - адрес исходного изображения
$outImg - адрес результирующего изображения
$w - ширина которая вам нужна
$h - высота которая вам нужна
*/
myImage($resImg, $outImg, $w, $h);
?>
