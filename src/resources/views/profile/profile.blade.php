@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css')}}">
@endsection

@section('content')
<div class="profile-form__content">
    <div class="form-group__content">
        <div class="profile-form__heading">
            <h2>プロフィール設定</h2>
        </div>
        <form class="form" action="/mypage/profile" method="post" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="profile__image">
                <img src=" {{ isset($profile['image']) ? 
                asset('storage/' . $profile['image']) : asset('img/default.jpg') }}"
                    alt="アイコン画像" class="profile-icon" id="preview">
                <label for="image" class="file-button">画像を選択する</label>
                <input type="file" id="image" name="image" class="file-input">
                <script src="{{ asset('js/image-preview.js') }}"></script>
                <div class="form__error">
                    @error('image')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">ユーザー名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name') }}" />
                    </div>
                    <div class="form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">郵便番号</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" />
                    </div>
                    <div class="form__error">
                        @error('postal_code')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">住所</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="address" value="{{ old('address') }}" />
                    </div>
                    <div class="form__error">
                        @error('address')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label--item">建物名</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="building" value="{{ old('building') }}" />
                    </div>
                </div>
            </div>
            <input class="profile-form__btn" type="submit" value="更新する">
        </form>
    </div>
</div>
@endsection