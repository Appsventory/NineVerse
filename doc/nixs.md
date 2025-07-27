# 📄 Dokumentasi `nixs.md` (Template Engine)

````markdown
# Nixs Template Engine

Nixs adalah template engine ringan yang dirancang untuk menggantikan Blade pada NineVerse, dengan sintaks sederhana dan performa cepat.

## Fitur Utama

- Sintaks `@yield('section')` untuk menandai tempat konten dinamis.
- Sintaks `@section('section') ... @endsection` untuk mendefinisikan blok konten.
- Sintaks `@include('view.name')` untuk menyisipkan partial view.
- Sintaks `@extends('layouts.master')` untuk inheritance layout.
- Mendukung variable PHP di template, misal `{{ \$variable }}` (otomatis escape).
- Mendukung penggunaan PHP murni di dalam template.
- Cache template agar lebih cepat.

## Cara Pakai

### 1. Layout dasar `layouts/master.nixs.php`

```html
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
</head>
<body>
    @include('partials.nav')
    <main>
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>
````

### 2. Membuat view

`home.nixs.php`

```php
@extends('layouts.master')

@section('title', 'Home Page')

@section('content')
<h1>Welcome to Nixs Template Engine!</h1>
<p>This is a sample page.</p>
@endsection
```

### 3. Render di controller

```php
use App\Console\Nixs;

Nixs::render('home', ['name' => 'User']);
```

## Catatan

* File `.nixs.php` disimpan di folder `app/Views`.
* Nixs otomatis melakukan escaping variabel kecuali menggunakan sintaks `{!! !!}`.
