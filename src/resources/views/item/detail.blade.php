@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css')}}">
@endsection

@section('content')
<div class="detail-content">
    <div class="product-card">
        <img src="{{ '/storage/' . $item['image'] }}" alt=" 商品画像" class="product-image">
    </div>
    <div class="product-info">
        <p class="product-name">{{ $item->name }}</p>
        <p class="product-brand">{{ $item->brand_name }}</p>
        <p class="product-price">￥{{ $item->price }}（税込）</p>

        <div class="product-icon">
            <div class="like-container">
                @if (Auth::check() && $item->likedUsers->contains(Auth::user()))
                <form method="POST" action="/item/{{ $item['id'] }}/unlike" novalidate>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="unlike-submit">★</button>
                </form>
                @else
                <form method="POST" action="/item/{{ $item['id'] }}/like" novalidate>
                    @csrf
                    <button type="submit" class="like-submit">☆</button>
                </form>

                @endif
                <div class="like-count">
                    {{ $item->liked_users_count }}
                </div>
            </div>


            <div class="comment-box">
                <span class="comment-icon">💬</span>
                <span class="comment-number">{{ $item->comments_count }}</span>
            </div>

        </div>

        <form class="purchase-form" action="/purchase/{{ $item['id'] }}" method="get" novalidate>
            @csrf
            <input class="purchase_btn " type="submit" value="購入手続きへ">
        </form>

        <div class="product-description">
            <h3>商品説明</h3>
            <p>{{ $item->description }}</p>
        </div>
        <div class="product-details">
            <h3>商品の情報</h3>
            <dl>
                <p><strong>カテゴリー</strong>
                    @foreach ($item->categories as $category)
                    <span class="category-label">{{ $category->content }}</span>
                    @endforeach
                </p>
                <p><strong>商品の状態</strong>{{ $item->condition->content }}</p>
            </dl>
        </div>
        <div class="comment-section">
            <h2 class="comment-heading">コメント({{ $item->comments_count }})</h2>
            @foreach($item->comments as $comment)
            @if ($comment->user && $comment->user->profile)
            <div class="comment-list">

                <div class="comment-item">
                    <div class="comment-user">
                        <img src=" {{ $comment->user->profile->image ? asset('storage/' . $comment->user->profile->image) : asset('default.png') }}" alt="アイコン画像"
                            class="profile-icon">
                        <p class="profile-name">{{ $comment->user->profile->name }}</p>
                    </div>
                    <div class="comment-view">
                        <p class="comment-content">{{ $comment->content }}</p>
                    </div>
                </div>

            </div>
            @endif
            @endforeach
        </div>

        <div class="comment-form-section">
            <h3>商品へのコメント</h3>
            @auth
            <form action="/item/{{ $item['id'] }}/comments" method="post" novalidate>
                @csrf
                <textarea class="form-control" name="content" rows="10" cols="70" required>
                    </textarea>
                <input class="purchase_btn" type="submit" value="コメントを送信する">
            </form>
            @endauth
            <div class="form__error">
                @error('content')
                <div class="text-danger" style="font-size: 0.9em; margin-top: 4px; color:red">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>
</div>
@endsection