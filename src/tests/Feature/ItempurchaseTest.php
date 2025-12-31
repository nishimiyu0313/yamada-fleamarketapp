<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Condition;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class ItempurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_「購入する」ボタンを押下すると購入が完了する()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $condition = Condition::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'サンプルビル101',
        ]);
        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'is_sold' => false,
        ]);


        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'content' => 'カード払い',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'サンプルビル101',

        ]);

        $response->assertStatus(302);
        $response->assertRedirect("/");


        $this->assertDatabaseHas('payments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => 1,
        ]);
    }

    public function test_購入済み商品は商品一覧で_sold_と表示される()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $condition = Condition::factory()->create();


        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'is_sold' => false,
        ]);


        $this->actingAs($user)->post("/purchase/{$item->id}", [
            'content' => 'カード払い',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'サンプルビル101',
        ]);


        $response = $this->get('/');


        $response->assertStatus(200);
        $response->assertSeeText('Sold');
    }

    public function test_購入した商品がプロフィール購入履歴に表示される()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $condition = Condition::factory()->create();

        $item = Item::factory()->create([
            'condition_id' => $condition->id,
            'is_sold' => false,
            'name' => 'テスト商品A',
        ]);


        $this->actingAs($user)->post("/purchase/{$item->id}", [
            'content' => 'カード払い',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区',
            'building' => 'サンプルビル101',
        ]);


        $response = $this->actingAs($user)->get('/mypage/buy');

        $response->assertStatus(200);
        $response->assertSeeText('テスト商品A');
    }
}
