<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Condition;

class ItemgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品が表示される()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();

        $condition = Condition::factory()->create();

        $items = Item::factory()->count(3)->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト用の商品説明です。',
            'price' => 5000,
            'image' => 'test-image.jpg',
            'is_sold' => false,
        ]);

        $response = $this->get('/');


        $response->assertStatus(200);


        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }
    public function test_購入済み商品はSoldと表示される()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'is_sold' => true,
            'name' => '購入済み商品',
            'brand_name' => 'テストブランド',
            'description' => '購入済みの商品説明です。',
            'price' => 10000,
            'image' => 'sold-image.jpg',
        ]);

        $response = $this->get('/');


        $response->assertStatus(200);


        $response->assertSee($item->name);


        $response->assertSee('Sold');
    }
}
