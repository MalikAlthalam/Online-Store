<?php
session_start();

// CAPTCHA configuration
$captcha_width = 200;
$captcha_height = 80;
$font_size = 5; // Use built-in font
$characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Remove confusing characters
$captcha_length = 5;

// Generate random CAPTCHA text
$captcha_text = '';
for ($i = 0; $i < $captcha_length; $i++) {
    $captcha_text .= $characters[rand(0, strlen($characters) - 1)];
}

// Store CAPTCHA in session
$_SESSION['captcha_text'] = $captcha_text;

// Create image
$image = imagecreate($captcha_width, $captcha_height);

// Define colors
$bg_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$line_color = imagecolorallocate($image, 150, 150, 150);
$noise_color = imagecolorallocate($image, 200, 200, 200);

// Fill background
imagefill($image, 0, 0, $bg_color);

// Add noise lines
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $captcha_width), rand(0, $captcha_height), 
              rand(0, $captcha_width), rand(0, $captcha_height), $line_color);
}

// Add noise dots
for ($i = 0; $i < 100; $i++) {
    imagesetpixel($image, rand(0, $captcha_width), rand(0, $captcha_height), $noise_color);
}

// Calculate text position (center the text)
$text_width = $captcha_length * 15; // Approximate width
$text_x = ($captcha_width - $text_width) / 2;
$text_y = ($captcha_height - 20) / 2;

// Add text to image
imagestring($image, $font_size, $text_x, $text_y, $captcha_text, $text_color);

// Set content type
header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Output image
imagepng($image);
imagedestroy($image);
?>
