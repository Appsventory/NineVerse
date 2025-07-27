function formatRupiah(angka, last = '') {
    if (isNaN(angka)) return '0' + last;
    let reverse = angka.toString().split('').reverse().join('');
    let ribuan = reverse.match(/\d{1,3}/g);
    let hasil = ribuan.join('.').split('').reverse().join('');
    return hasil + last;
}

function toRupiah(angka, prefix = 'Rp. ') {
    if (typeof angka !== 'string') angka = angka.toString();

    let number_string = angka.replace(/[^,\d]/g, '');
    let split = number_string.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah ? prefix + rupiah : '';
}

function toRupiah(angka) {
angka = parseInt(angka); // buang desimal jika ada
return 'Rp. ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function slugify(text) {
    return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9-]+/g, '-')  // Ganti karakter selain huruf, angka, dan - dengan -
        .replace(/^-+|-+$/g, '');      // Hapus strip di awal/akhir
}