<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\User;
use App\Models\Comment;
use App\Models\Profile;



class DetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品詳細に必要な情報が表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $category = Category::factory()->create();


        $item = Item::factory()->create([
            'name' => '青いりんご',
            'brand_name' => 'Apple Inc.',
            'price' => 1000,
            'description' => '新鮮な青いりんごです。',
            'condition_id' => $condition->id,

            'image' => 'sample.jpg',
        ]);

        $item->categories()->attach($category->id);


        $likeUsers = User::factory()->count(3)->create();
        foreach ($likeUsers as $likeUser) {
            $likeUser->likedItems()->attach($item->id);
        }


        $commentUsers = User::factory()->count(2)->create();

        foreach ($commentUsers as $commentUser) {

            Profile::factory()->create([
                'user_id' => $commentUser->id,
                'name' => 'テストユーザー',
            ]);

            Comment::factory()->create([
                'item_id' => $item->id,
                'user_id' => $commentUser->id,
                'content' => 'コメントテスト',
            ]);
        }


        $response = $this->actingAs($user)->get('/item/' . $item->id);


        $response->assertSee('sample.jpg');


        $response->assertSee('青いりんご');
        $response->assertSee('Apple Inc.');
        $response->assertSee('1000');
        $response->assertSee('新鮮な青いりんごです。');


        $response->assertSee($category->name);
        $response->assertSee($condition->name);


        $response->assertSee('3');


        $response->assertSee('2');


        foreach ($commentUsers as $commentUser) {
            $response->assertSee($commentUser->profile->name);
            $response->assertSee('コメントテスト');
        }
    }
    public function test_複数選択されたカテゴリが表示されているか()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $condition = Condition::factory()->create();

        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'user_id' => $user->id,
            'image' => 'sample.jpg',
            'condition_id' => $condition->id,
        ]);

        $categories = Category::factory()->count(3)->create([]);


        $item->categories()->attach($categories->pluck('id'));


        $response = $this->actingAs($user)->get('/item/' . $item->id);


        foreach ($categories as $category) {

            $response->assertSee($category->content);
        }
    }
}
