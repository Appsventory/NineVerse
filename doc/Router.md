
# 📄 Dokumentasi `Router.md` (web.php)

```markdown
# Router (web.php)

Router adalah sistem routing sederhana namun powerful, terinspirasi dari NineVerse, untuk mengatur URL dan request HTTP.

## Fitur

- Mendukung metode HTTP: GET, POST, PUT, DELETE, dan ANY.
- Parameter dinamis di URL, misal `/user/{id}`.
- Middleware support untuk filter request sebelum ke controller.
- Mendukung action controller dengan format:
  - `'Controller@method'` (string)
  - `[Controller, method]` (array)
- Override HTTP method via form field `_method`.
- Fallback 404 otomatis.

## Cara Definisi Route

```php
use App\Core\Router;

// Route GET tanpa middleware
Router::get('/', 'HomeController@index');

// Route POST dengan middleware
Router::post('/user', 'UserController@store')->middleware('AuthMiddleware');

// Route dengan parameter dan middleware
Router::get('/post/{id}', 'PostController@show')->middleware('AuthMiddleware');
````

## Middleware

Middleware dapat didefinisikan sebagai:

* Kelas dengan method `handle()`.
* Kelas dan method tertentu dengan parameter, contoh: `'Throttle@check:60&1'`.

## Dispatch Request

Pada `public/index.php` cukup panggil:

```php
Router::dispatch();
```

Fungsi ini akan mencari route yang cocok dan mengeksekusi controller + middleware secara otomatis.

## Contoh Middleware sederhana

```php
namespace App\Middleware;

class AuthMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}
```
Berikut adalah tambahan bagian dokumentasi untuk menjelaskan **format middleware lengkap**, termasuk dukungan `@`, `:`, dan `#`, yang akan menyatu rapi ke dalam `Router.md`:

---

### ✅ Tambahan: Format Middleware yang Didukung

````markdown
## Format Middleware yang Didukung

Router mendukung berbagai format middleware dengan fleksibilitas tinggi:

| Format                            | Deskripsi                                                | Contoh                                       | Yang Dipanggil                                 |
|-----------------------------------|----------------------------------------------------------|----------------------------------------------|------------------------------------------------|
| `AuthMiddleware`                  | Panggil `handle()` tanpa parameter                       | `'AuthMiddleware'`                           | `new AuthMiddleware()->handle()`               |
| `RoleMiddleware#admin`            | Panggil `handle()` dengan parameter                      | `'RoleMiddleware#admin'`                     | `new RoleMiddleware()->handle('admin')`        |
| `Throttle@check`                  | Panggil static method tanpa parameter                    | `'Throttle@check'`                           | `Throttle::check()`                            |
| `Throttle@check:60&1`             | Panggil static method dengan beberapa parameter          | `'Throttle@check:60&1'`                      | `Throttle::check('60', '1')`                   |
| `Verify@handle:token`             | Panggil static method `handle()` dengan parameter        | `'Verify@handle:token'`                      | `Verify::handle('token')`                      |

> Catatan:
> - Gunakan `@` untuk menyebutkan method dan (jika perlu) tambahkan `:` untuk parameter.
> - Gunakan `#` sebagai shorthand untuk memanggil method `handle()` dengan parameter.
> - Format `#` cocok untuk middleware instance, sedangkan `@` cocok untuk method static.

### Contoh Middleware dengan Parameter

```php
namespace App\Middleware;

class RoleMiddleware
{
    public function handle($role)
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
            http_response_code(403);
            echo 'Access Denied';
            exit;
        }
    }
}
````

Penggunaan:

```php
Router::get('/admin', 'AdminController@index')->middleware('RoleMiddleware#admin');
```

Atau menggunakan static:

```php
Router::get('/admin', 'AdminController@index')->middleware('RoleMiddleware@handle:admin');
```

```

---

Kalau kamu mau saya gabungkan seluruh `Router.md` final termasuk bagian ini, tinggal bilang saja!
```


## Error Handling

Jika route tidak ditemukan, otomatis memanggil:

```php
\App\Controllers\ErrorController@index();
```

Atau tampilkan pesan `404 Not Found` jika controller error tidak ada.


