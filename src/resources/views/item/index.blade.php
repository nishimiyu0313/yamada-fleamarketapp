@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="toppage-list">
        <div class="recommend">
            <h3>おすすめ</h3>
        </div>
        <a class="mylist" href="/item/search?keyword={{request('keyword')}}&type=mylist">
            <h3>マイリスト</h3>
        </a>
    </div>
    <div class="products-form">
        <div class="products-row">
            @foreach ($items as $item)
            <div class="products-card">
                <a href="/item/{{ $item['id'] }}">
                    <img src="{{ '/storage/' . $item['image'] }}" alt=" 商品画像" class="products-image">
                </a>
                <div class="item-name">{{ $item->name }}</div>
                @if ($item->is_sold)
                <p class="sold-label" style="color: red; font-weight: bold;">Sold</p>
                @endif

            </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $items->links('vendor.pagination.semantic-ui') }}
        </div>
    </div>
</div>
@endsection