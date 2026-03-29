<?php

$url = 'https://res.cloudinary.com/demo/image/upload/v1570979139/avatars/sample.jpg';
preg_match('/upload\/(?:v\d+\/)?([^\.]+)/', $url, $matches);
$publicId = $matches[1] ?? null;

echo "URL: $url\n";
echo "Public ID: $publicId\n";

$url2 = 'http://res.cloudinary.com/demo/image/upload/sample.jpg';
preg_match('/upload\/(?:v\d+\/)?([^\.]+)/', $url2, $matches);
$publicId2 = $matches[1] ?? null;
echo "URL2: $url2\n";
echo "Public ID2: $publicId2\n";

$url3 = 'https://res.cloudinary.com/demo/image/upload/v17112345/trainings/documento.pdf';
preg_match('/upload\/(?:v\d+\/)?([^\.]+)/', $url3, $matches);
$publicId3 = $matches[1] ?? null;
echo "URL3: $url3\n";
echo "Public ID3: $publicId3\n";
