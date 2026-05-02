<?php
session_start();

/* prevent cache */
header("Content-Type: image/png");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");

/* generate code */
$code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha'] = $code;

/* image */
$image = imagecreate(120, 40);

$bg = imagecolorallocate($image, 20, 25, 40);
$text = imagecolorallocate($image, 56, 189, 248);

/* noise (optional but better security) */
for($i=0; $i<50; $i++){
    imagesetpixel($image, rand(0,120), rand(0,40), $text);
}

/* text */
imagestring($image, 5, 30, 10, $code, $text);

/* output */
imagepng($image);
imagedestroy($image);
exit();
?>