@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
@endsection

@section('content')
@if ($errors->has('msg'))
<div class="alert alert-danger">
    {{ $errors->first('msg') }}
</div>
@endif
<form class="purchase-form" action="{{ '/purchase/' . $item->id }}" method="post" novalidate>
    @csrf
    <div class="purchase">
        <div class="product-content">
            <div class="product-info">
                <div class="product-card">
                    <img src="{{ '/storage/' . ($item['image'] ?? 'noimage.png') }}" alt="商品画像" class="product-image">
                </div>
                <div class="productc-detail">
                    <p class="product-name">{{ $item->name }}</p>
                    <p class="product-price">￥{{ $item->price }}</p>
                </div>
            </div>

            <div class="product-pay">
                <h3>支払い方法</h3>
                <div class="purchase-form__select-inner">
                    <select class="purchase-form__select" name="content" id="content" required>
                        <option value="" disabled selected>選択してください</option>
                        <option value="カード払い">カード払い</option>
                        <option value="コンビニ払い">コンビニ払い</option>
                    </select>
                </div>
                @error('content')
                <p class="error-message" style="color:red">{{ $message }}</p>
                @enderror
            </div>

            @php
            $payment = \App\Models\Payment::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->first();

            $postal_code = $payment->postal_code ?? $profile->postal_code;
            $address = $payment->address ?? $profile->address;
            $building = $payment->building ?? $profile->building;
            @endphp

            <div class="address">
                <h3>配送先</h3>
                <div class="address-display">
                    @if ($postal_code && $address)
                    <p>郵便番号：{{ $postal_code }}</p>
                    <p>住所：{{ $address }}</p>
                    <p>建物名：{{ $building }}</p>
                    <input type="hidden" name="postal_code" value="{{ $postal_code }}">
                    <input type="hidden" name="address" value="{{ $address }}">
                    <input type="hidden" name="building" value="{{ $building }}">
                    @else
                    <p>プロフィール情報がありません。</p>
                    @endif
                    @error('address')
                    <p class="error-message" style="color:red">{{ $message }}</p>
                    @enderror
                </div>
                <a href="/purchase/address/{{ $item['id'] }}" class="btn-address">変更する</a>
            </div>
        </div>
        <div class="product-confirm">
            <div class="product-edit">
                <p class="charge"><strong>商品代金</strong>¥{{ number_format($item->price) }}</p>
                <p class="pay"><strong>支払い方法</strong></p>
            </div>
            <input class="purchase-form__btn" type="submit" value="購入する">
        </div>
        <script>
            const selectElem = document.getElementById('content');
            const payElem = document.querySelector('.pay');

            selectElem.addEventListener('change', function() {
                const selectedValue = this.value;
                payElem.innerHTML = '<strong>支払い方法:</strong> ' + selectedValue;
            });
        </script>
    </div>
</form>

@endsection