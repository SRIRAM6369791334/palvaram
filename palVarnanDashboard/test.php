<?php
$im = @imagecreatefrompng('public/assets/images/logoooo.png');
if(!$im) die('No image');
$w = imagesx($im);
$h = imagesy($im);
$colors = [];
for($x=0; $x<$w; $x+=5){
    for($y=0; $y<$h; $y+=5){
        $rgb = imagecolorat($im, $x, $y);
        $c = imagecolorsforindex($im, $rgb);
        if($c['alpha'] < 50 && !($c['red']>240 && $c['green']>240 && $c['blue']>240) && !($c['red']<20 && $c['green']<20 && $c['blue']<20)) {
            $hex = sprintf('#%02x%02x%02x', $c['red'], $c['green'], $c['blue']);
            $colors[$hex] = ($colors[$hex]??0)+1;
        }
    }
}
arsort($colors);
print_r(array_slice($colors, 0, 5));
