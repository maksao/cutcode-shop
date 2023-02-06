<?php

namespace App\Faker;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;

class FakerImageProvider extends Base
{
    public function fixturesImage(string $fixturesDir, string $storageDir): string
    {
        if (!Storage::exists($storageDir)) {
            Storage::makeDirectory($storageDir);
        }

        $filename = $this->generator->file(
            base_path('tests/Fixtures/images/' . $fixturesDir),
            Storage::path($storageDir),
            false
        );

        return '/storage/' . trim($storageDir, '/') . '/' . $filename;
    }

}