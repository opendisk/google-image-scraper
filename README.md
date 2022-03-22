# Google Image Scraper

Scraper image from Google search engine

## Installation:

```bash
composer require opendisk/google-image-scraper
```

### Usage:

```php
<?php

include 'vendor/autoload.php';

use Opendisk\WebScraper\GoogleImage;

$keyword = 'resep ayam goreng';
$results = GoogleImage::get($keyword,'',['as_sitesearch' => 'pinterest.com','tbs' => 'isz:l']);

echo '<pre>';
print_r($results);
```

**Result:** 
```
Array
(
    [0] => Array
        (
            [keyword] => resep ayam goreng
            [slug] => resep-ayam-goreng
            [title] => Resep Ayam Goreng Sederhana Yang Nikmat Resep Ayam Ayam Goreng Resep
            [alt] => resep ayam goreng sederhana yang nikmat resep ayam ayam goreng resep
            [url] => https://i.pinimg.com/originals/4e/38/ec/4e38eca0356cbaa68fce5046c92d2916.jpg
            [thumb] => https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS-zFW-k6I9WNftuqr3R-utwDIu_8DMpwucnibYjUWRIF9UdlWHzGrEcaZgB9HSUhHY6QI
            [filetype] => jpg
            [width] => 860
            [height] => 1188
            [source] => https://id.pinterest.com/pin/563090759649205132/
            [domain] => id.pinterest.com
        )

    .........
```

**Available Option:** 
```php
'tbs' => [
    'all' => '',
    'medium' => 'isz:m',
    'large' => 'isz:l',
    'icon' => 'isz:i'
],
'as_filetype' => [
    'all' => '',
    'jpg' => 'jpg',
    'png' => 'png',
    'gif' => 'gif',
    'bmp' => 'bmp',
    'webp' => 'webp'
],
'as_sitesearch' => 'example.com',
'as_qdr' => [
    'last 24h' => 'd',
    'last week' => 'w',
    'last month' => 'm',
    'last 2month' => 'm2',
    'last year' => 'y'
]
```