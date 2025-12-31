<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Condition;
use App\Models\User;
use App\Models\Item;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン済みのユーザーはコメントを送信できる()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);

        $commentData = [
            'content' => 'テストコメントです。',
        ];

        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", $commentData);

        $response->assertStatus(302);


        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメントです。',
        ]);
    }
    public function test_ログイン前のユーザーはコメントを送信できない()
    {


        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);

        $commentData = [
            'content' => 'ログインしていないコメント',
        ];

        $response = $this->post("/item/{$item->id}/comments", $commentData);

        $response->assertStatus(302);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'content' => 'ログインしていないコメント',
        ]);
    }

    public function test_コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);


        $commentData = [
            'content' => '',
        ];


        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", $commentData);


        $response->assertStatus(302);


        $response->assertSessionHasErrors(['content']);


        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => '',
        ]);
    }

    public function test_コメントが255字以上の場合_バリデーションエラーになる()
    {

        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $item = Item::factory()->create(['condition_id' => $condition->id]);


        $longComment = str_repeat('あ', 256);

        $commentData = [
            'content' => $longComment,
        ];

        $response = $this->actingAs($user)->post("/item/{$item->id}/comments", $commentData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['content']);

        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => $longComment,
        ]);
    }
}
