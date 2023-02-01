<?php

namespace App\Faker;

use Faker\Provider\Base;
use Faker\Provider\File;

class FakerImageProvider extends Base
{
    public function productImage(string $sourceDir, string $targetDir): string
    {
        if (!file_exists($targetDir)) {
            mkdir($targetDir);
        }
        return 'storage/images/products/' . File::file($sourceDir, $targetDir, false);
    }

}