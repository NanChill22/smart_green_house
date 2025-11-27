// JavaScript Kustom untuk Smart Green House

console.log("Script loaded!");

// Contoh interaktivitas (misalnya, konfirmasi sebelum menghapus)
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('a[title="Hapus"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                event.preventDefault();
            }
        });
    });
});
