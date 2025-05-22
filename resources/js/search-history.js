/**
 * File ini hanya bertugas menyimpan riwayat pencarian kata
 * Tampilan hasil pencarian ditangani oleh search.js
 */
document.addEventListener("DOMContentLoaded", function () {
    // Ambil referensi ke form pencarian
    const searchForm = document.getElementById("searchForm");

    // Periksa apakah user terautentikasi
    const isAuthenticated =
        document.querySelector('meta[name="auth-check"]')?.content === "true";

    // Ambil CSRF token untuk request POST
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;

    // Jika tidak ada form atau user tidak terautentikasi, keluar
    if (!searchForm || !isAuthenticated) return;

    // Fungsi untuk menyimpan kata yang dicari ke riwayat pencarian
    // Update fungsi saveSearchHistory untuk mengirim field result yang kosong

    async function saveSearchHistory(query) {
        try {
            // Pastikan query tidak kosong
            if (!query || query.trim() === "") {
                console.warn("Query kosong, tidak menyimpan ke riwayat");
                return false;
            }

            // Hindari duplikasi penyimpanan dengan throttling
            const lastQuery = sessionStorage.getItem("last_search_query");
            const lastTimestamp = sessionStorage.getItem(
                "last_search_timestamp"
            );
            const now = Date.now();

            // Jika query sama dan belum lewat 1 menit, hindari duplikasi
            if (
                lastQuery === query &&
                lastTimestamp &&
                now - parseInt(lastTimestamp) < 60000
            ) {
                console.log("Menghindari duplikasi penyimpanan kata", query);
                return false;
            }

            // Update sessionStorage
            sessionStorage.setItem("last_search_query", query);
            sessionStorage.setItem("last_search_timestamp", now.toString());

            console.log("Menyimpan kata ke riwayat:", query);

            // PERUBAHAN: Tambahkan field result yang kosong untuk memenuhi ekspektasi backend
            const response = await fetch("/history", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    query: query, // Kata yang dimasukkan pengguna
                    result: "{}", // Kirim objek JSON kosong sebagai string
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                console.error("Gagal menyimpan kata ke riwayat:", data.message);
                return false;
            }

            console.log(
                "%cBerhasil menyimpan kata '" + query + "' ke riwayat",
                "color: green; font-weight: bold"
            );
            return true;
        } catch (error) {
            console.error("Error saat menyimpan kata ke riwayat:", error);
            return false;
        }
    }
    const originalSubmitHandler = searchForm.onsubmit;

    // Tambahkan event listener untuk form submit
    searchForm.addEventListener("submit", function (e) {
        // Tidak perlu preventDefault() karena kita hanya "mendengarkan" event
        // dan ingin form tetap submit secara normal (ditangani oleh search.js)

        const verbInput = document.getElementById("verb");
        const query = verbInput?.value?.trim();

        if (!query) return; // Jika input kosong, tidak perlu menyimpan

        // Simpan dengan delay kecil untuk memastikan pencarian selesai dulu
        setTimeout(() => {
            saveSearchHistory(query).catch((err) => {
                console.error("Gagal menyimpan riwayat:", err);
            });
        }, 500);
    });

    // Cek parameter query di URL untuk auto-submit
    const urlParams = new URLSearchParams(window.location.search);
    const queryParam = urlParams.get("query");

    // Jika ada parameter query, simpan ke riwayat
    if (queryParam && queryParam.trim() !== "") {
        console.log("Parameter query ditemukan di URL:", queryParam);
        setTimeout(() => {
            saveSearchHistory(queryParam).catch((err) => {
                console.error(
                    "Gagal menyimpan riwayat dari URL parameter:",
                    err
                );
            });
        }, 1000); // Delay lebih lama untuk memastikan search.js selesai memproses
    }
});
