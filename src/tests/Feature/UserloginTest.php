<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserloginTest extends TestCase
{
    use RefreshDatabase;

    public function test_メールアドレスが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',

        ]);

        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    public function test_パスワードが入力されていない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',

        ]);

        $response->assertSessionHasErrors('password');

        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    public function test_入力情報が間違っている場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',

        ]);

        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertEquals('ログイン情報が一致しません', $errors->first('email'));
    }

    public function test_正しい情報が入力された場合、ログイン処理が実行される()
    {

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);


        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);


        $this->assertAuthenticatedAs($user);


        $response->assertRedirect('/');
    }
}
