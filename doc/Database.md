# 📄 Dokumentasi `Database.md`

```markdown
# Database (PDO Wrapper)

Class `Database` adalah singleton wrapper PDO untuk operasi database yang mudah dan efisien.

## Fitur

- Singleton instance.
- Auto koneksi ke MySQL dengan pengaturan dari `.env`.
- Query builder sederhana menggunakan `query()`, `bind()`, `execute()`.
- Mendukung method CRUD: `resultSet()`, `single()`, `fetchColumn()`.
- Mendapatkan jumlah baris hasil query `rowCount()`.
- Mendapatkan ID terakhir yang di-insert `lastInsertId()`.
- Support transaksi: `beginTransaction()`, `commit()`, `rollBack()`.
- Shortcut method `run()`, `fetch()`, `fetchAll()` untuk query cepat.

## Contoh Penggunaan

```php
$db = \App\Core\Database::getInstance();

// Select all
$users = $db->query('SELECT * FROM users')->resultSet();

// Select single
$user = $db->query('SELECT * FROM users WHERE id = :id')
           ->bind(':id', 1)
           ->single();

// Insert
$db->query('INSERT INTO users(name, email) VALUES(:name, :email)')
   ->bind(':name', 'John')
   ->bind(':email', 'john@example.com')
   ->execute();

// Transaksi
$db->beginTransaction();
try {
    $db->run('UPDATE accounts SET balance = balance - 100 WHERE id = 1');
    $db->run('UPDATE accounts SET balance = balance + 100 WHERE id = 2');
    $db->commit();
} catch (\Exception $e) {
    $db->rollBack();
}
````