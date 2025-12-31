<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Payment;
use App\Models\Item;
use App\Models\Condition;

class UserchangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_変更画面に初期値としてプロフィール情報がセットされている()
    {


        /** @var \App\Models\User $user */
        $user = User::factory()->create();


        $profileData = [
            'name' => 'テストユーザー',
            'postal_code' => '123-4567',
            'address' => '東京都新宿区西新宿2-8-1',
            'building' => '新宿ビル101',
            'image' => '',
        ];

        $user->profile()->updateOrCreate([], $profileData);

        $response = $this->actingAs($user)->get('/mypage/profile/{$item->id}');

        $response->assertStatus(200);

        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区西新宿2-8-1');
        $response->assertSee('新宿ビル101');
    }
}
