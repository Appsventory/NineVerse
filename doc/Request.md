# 📄 Dokumentasi `Request.md`

```markdown
# Request

Class `Request` menyediakan akses mudah, aman, dan terstruktur ke data request HTTP.

## Fitur

- Mendapatkan method HTTP (`GET`, `POST`, dll).
- Mendapatkan URI yang di-request.
- Mendapatkan data input dari GET, POST, JSON body.
- Sanitasi input otomatis untuk mencegah XSS.
- Mendukung akses data file upload.
- Memeriksa keberadaan key di request.
- Mendapatkan semua data sekaligus.
- Mendukung pengecekan method request.
- Mendapatkan raw input request (untuk API).

## Contoh Penggunaan

```php
use App\Core\Request;

$name = Request::input('name', 'Guest');
$email = Request::post('email');
$id = Request::get('id');

if (Request::is('POST')) {
    // Proses form submit
}

$allData = Request::all();
````

## Mendapatkan data JSON dari API

```php
if (Request::contentType() === 'application/json') {
    $jsonData = Request::json();
}
```

## Mendapatkan file upload

```php
$file = Request::file('avatar');
if ($file) {
    // proses upload
}
```