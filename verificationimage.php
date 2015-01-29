<?php
// ----------------------------------------- 
//  The Web Help .com
// ----------------------------------------- 

header('Content-type: image/jpeg');

$width = 64;
$height = 24;

$my_image = imagecreatetruecolor($width, $height);

imagefill($my_image, 0, 0, 0xFFFFFF);

// add noise
for ($c = 0; $c < 100; $c++){
	$x = rand(0,$width-1);
	$y = rand(0,$height-1);
	imagesetpixel($my_image, $x, $y, 0xFF0000);
}

$x = rand(1,10);
$y = rand(1,10);

$rand_string = rand(10000,99999);
imagestring($my_image, 5, $x, $y, $rand_string, 0x000000);

setcookie('validation',(md5($rand_string .'figVam')));

imagejpeg($my_image);
imagedestroy($my_image);
?>