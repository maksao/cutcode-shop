<?php

namespace Domain\Catalog\ViewModels;

use Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Support\Traits\Makeable;

class CategoryViewModel
{
    use Makeable;

    public function homePage(): Collection|array
    {
        // Если будут изменения данных по категориям, то в обсервере скидываем кэш
        //return Cache::rememberForever('category_home_page', function () {
        return $categories = Category::query()
            ->homePage()
            ->get();
        //});
    }
}