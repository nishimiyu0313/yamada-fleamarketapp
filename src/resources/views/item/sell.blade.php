@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-form__content">

    <div class="sell-form__heading">
        <h1>商品の出品</h1>
    </div>
    <form class="form" action="/sell" method="post" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">商品画像</span>
            </div>
            <label for="image" class="file-button">画像を選択する</label>
            <input type="file" id="image" name="image" class="file-input" required>
            <img src="" id="preview" class="sell-form__img">
            <script src="{{ asset('js/image-preview.js') }}"></script>
            <div class="register-form__error-message">
                @error('image')
                {{ $message }}
                @enderror
            </div>

        </div>
        <div class="product-detail">
            <h2>商品の詳細</h2>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">カテゴリー</span>
                </div>
                <div class="category-list">
                    @foreach ($categories as $category)
                    <input type="checkbox" name="category_ids[]" value=" {{ $category->id }}" id="category_{{ $category->id }}" required {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                    <label class="category-option" for="category_{{ $category->id }}">
                        {{ $category->content }}{{ old('category') }}
                    </label>
                    @endforeach
                </div>
                <div class="register-form__error-message">
                    @error('category_ids')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">商品の状態</span>
                </div>
                <div class="contact-form__select-inner">
                    <select class="contact-form__select" name="condition_id" id="condition" required>
                        <option disabled selected>選択してください</option>
                        @foreach($conditions as $condition)
                        <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}>
                            {{ $condition->content }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="register-form__error-message">
                    @error('condition_id')
                    {{ $message }}
                    @enderror
                </div>
            </div>

            <h2>商品名と説明</h2>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">商品名</span>
                </div>
                <div class="sell-form__name-inputs">
                    <input class="sell-form__input contact-form__name-input" type="text" name="name" id="name"
                        value="{{ old('name') }}">
                </div>
                <div class="register-form__error-message">
                    @error('name')
                    {{ $message }}
                    @enderror

                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">ブランド名</span>
                </div>
                <div class="sell-form__name-inputs">
                    <input class="sell-form__input contact-form__name-input" type="text" name="brand_name" id="brand_name"
                        value="{{ old('brand_name') }}">
                </div>

            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">商品の説明</span>
                </div>
                <div class="sell-form__name-inputs">
                    <textarea class="sell-form__input contact-form__name-input" type="textarea" name="description" id="description"
                        value="{{ old('description') }}">{{ old('description') }}</textarea>
                </div>
                <div class="register-form__error-message">
                    @error('description')
                    {{ $message }}
                    @enderror


                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">販売価格</span>
                </div>
                <div class="sell-form__name-inputs">
                    <input class="sell-form__input contact-form__name-input" type="text" name="price" id="price"
                        value="{{ old('price') }}">
                </div>
                <div class="register-form__error-message">
                    @error('price')
                    {{ $message }}
                    @enderror

                </div>
            </div>
        </div>
        <input class="sell-form__btn" type="submit" value="出品する">
    </form>
</div>
@endsection