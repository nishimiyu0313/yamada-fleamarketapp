<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Condition;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねした商品だけが表示される()
    {
        /** @var \App\Models\User $user */


        $user = User::factory()->create();

        $condition = Condition::factory()->create();

        $likedProduct = Item::factory()->create([
            'name' => 'キウイ',
            'condition_id' => $condition->id,
        ]);

        $notLikedProduct = Item::factory()->create([
            'name' => 'バナナ',
            'condition_id' => $condition->id,
        ]);


        $user->likedItems()->attach($likedProduct->id);

        $response = $this->actingAs($user)->get('/mylist');

        $response->assertDontSee('バナナ');
    }

    public function test_購入済み商品は「Sold」と表示される()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();

        $condition = Condition::factory()->create();

        $soldItem = Item::factory()->create([
            'name' => 'りんご',
            'condition_id' => $condition->id,
            'is_sold' => true,
        ]);

        $availableItem = Item::factory()->create([
            'name' => 'みかん',
            'condition_id' => $condition->id,
            'is_sold' => false,
        ]);


        $user->likedItems()->attach([$soldItem->id, $availableItem->id]);

        $response = $this->actingAs($user)->get('/mylist');

        $response->assertSee('りんご');
        $response->assertSee('みかん');

        $response->assertSee('Sold');
    }

    public function test_自分が出品した商品は表示されない()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $condition = Condition::factory()->create();

        $ownItem = Item::factory()->create([
            'name' => '自分の商品',
            'condition_id' => $condition->id,
            'user_id' => $user->id,
        ]);

        $otherUser = User::factory()->create();
        $likedItem = Item::factory()->create([
            'name' => '他人の商品',
            'condition_id' => $condition->id,
            'user_id' => $otherUser->id,
        ]);

        $user->likedItems()->attach([$ownItem->id, $likedItem->id]);

        $response = $this->actingAs($user)->get('/mylist');

        $response->assertDontSee('自分の商品');

        $response->assertSee('他人の商品');
    }

    public function test_未認証の場合は何も表示されない()
    {
        $response = $this->get('/mylist');

        $response->assertRedirect('/login');
    }
}
