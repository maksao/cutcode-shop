<?php

namespace App\Http\Controllers;

use Database\Factories\ProductFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_success_response(): void
    {
        $product = ProductFactory::new()->createOne();

        $this->get(action(ProductController::class, $product))
            ->assertOk();
    }


}
