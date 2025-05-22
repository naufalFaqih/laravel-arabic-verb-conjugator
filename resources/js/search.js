document.addEventListener("DOMContentLoaded", function () {
    // Check for query parameter in URL
    const urlParams = new URLSearchParams(window.location.search);
    const queryParam = urlParams.get("query");

    if (queryParam) {
        const verbInput = document.getElementById("verb");
        if (verbInput) {
            verbInput.value = queryParam;
            // Optional: Auto submit the form
            setTimeout(() => {
                document
                    .getElementById("searchForm")
                    .dispatchEvent(new Event("submit"));
            }, 500);
        }
    }

    // ...your existing search.js code
});
document.addEventListener("DOMContentLoaded", function () {
    // Check if user is logged in (history element exists)
    const searchHistoryElement = document.getElementById("searchHistory");

    if (!searchHistoryElement) return;

    // Load search history from local storage
    const loadSearchHistory = () => {
        const history = JSON.parse(
            localStorage.getItem("searchHistory") || "[]"
        );

        if (history.length === 0) {
            searchHistoryElement.innerHTML =
                '<p class="text-gray-500 text-sm">Belum ada riwayat pencarian.</p>';
            return;
        }

        // Display history (limit to 5 recent searches)
        const historyHtml = history
            .slice(0, 5)
            .map((item) => {
                return `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-right text-gray-800 font-medium">${
                        item.query
                    }</span>
                    <span class="text-xs text-gray-500">${new Date(
                        item.timestamp
                    ).toLocaleString()}</span>
                </div>
            `;
            })
            .join("");

        searchHistoryElement.innerHTML = historyHtml;
    };

    // Save search to history when form is submitted
    const searchForm = document.getElementById("searchForm");
    if (searchForm) {
        searchForm.addEventListener("submit", function () {
            const query = document.getElementById("verb").value.trim();
            if (!query) return;

            // Get existing history
            const history = JSON.parse(
                localStorage.getItem("searchHistory") || "[]"
            );

            // Add new search to history
            history.unshift({
                query: query,
                timestamp: new Date().toISOString(),
            });

            // Limit history to 20 items
            const limitedHistory = history.slice(0, 20);

            // Save back to local storage
            localStorage.setItem(
                "searchHistory",
                JSON.stringify(limitedHistory)
            );

            // Update display
            loadSearchHistory();
        });
    }

    // Initial load
    loadSearchHistory();
});

document.getElementById("verb").addEventListener("input", function (event) {
    const arabicRegex = /^[\u0600-\u06FF\s]+$/; // Hanya karakter Arab dan spasi
    const inputField = event.target;

    if (!arabicRegex.test(inputField.value)) {
        inputField.setCustomValidity(
            "Hanya diperbolehkan karakter dalam bahasa Arab"
        );
    } else {
        inputField.setCustomValidity(""); // Reset pesan error jika valid
    }
});

document
    .getElementById("searchForm")
    .addEventListener("submit", function (event) {
        const inputField = document.getElementById("verb");
        const arabicRegex = /^[\u0600-\u06FF\s]+$/;

        if (!arabicRegex.test(inputField.value)) {
            event.preventDefault(); // Batalkan pengiriman form
            alert("Hanya diperbolehkan karakter dalam bahasa Arab");
        }
    });
window.lastSearchData = null;
document
    .getElementById("searchForm")
    .addEventListener("submit", async function (event) {
        event.preventDefault(); // Mencegah pengiriman form secara default

        const verb = document.getElementById("verb").value; // Ambil nilai dari input
        const apiUrl = `/api/search-verb?verb=${encodeURIComponent(verb)}`; // Route Laravel

        console.log("Memulai pencarian untuk kata:", verb);
        // Tampilkan loading screen
        const loadingElement = document.getElementById("loading");
        const resultElement = document.getElementById("result");
        const verbInfoElement = document.getElementById("verbInfo");
        const suggestListElement = document.getElementById("suggestList");
        const resultColumnsElement = document.getElementById("resultColumns");
        const domirDataElement = document.getElementById("domirData");
        const madhiMalumDataElement = document.getElementById("madhiMalumData");
        const mudhoriMalumDataElement =
            document.getElementById("mudhoriMalumData");
        const mudhoriMajzumDataElement =
            document.getElementById("mudhoriMajzumData");
        const mudhoriMansubDataElement =
            document.getElementById("mudhoriMansubData");
        const amarDataElement = document.getElementById("amarData");
        const mudhoriMuakkadDataElement =
            document.getElementById("mudhoriMuakkadData");
        const amarMuakkadDataElement =
            document.getElementById("amarMuakkadData");

        loadingElement.classList.remove("hidden"); // Tampilkan loading
        resultElement.classList.add("hidden"); // Sembunyikan hasil sebelumnya
        verbInfoElement.classList.add("hidden"); // Sembunyikan informasi sebelumnya
        suggestListElement.innerHTML = ""; // Kosongkan daftar suggest sebelumnya
        resultColumnsElement.innerHTML = ""; // Kosongkan kolom sebelumnya
        domirDataElement.innerHTML = ""; // Kosongkan data tambahan sebelumnya
        madhiMalumDataElement.innerHTML = ""; // Kosongkan data tambahan sebelumnya
        mudhoriMalumDataElement.innerHTML = ""; // Kosongkan data tambahan sebelumnya
        mudhoriMajzumDataElement.innerHTML = ""; // Kosongkan data tambahan sebelumnya
        mudhoriMansubDataElement.innerHTML = ""; // Kosongkan data tambahan sebelumnya
        amarDataElement.innerHTML = ""; // Kosongkan data tambahan sebelumnya
        mudhoriMuakkadDataElement.innerHTML = ""; // Kosongkan data tambahan sebelumnya
        amarMuakkadDataElement.innerHTML = ""; // Kosongkan data tambahan sebelumnya

        try {
            console.log("Mengirim request ke:", apiUrl);
            const response = await fetch(apiUrl); // Kirim request ke Laravel
            console.log("Status response:", response.status);
            if (!response.ok) {
                throw new Error(
                    `Error ${response.status}: ${response.statusText}`
                );
            }

            const data = await response.json(); // Parse hasil response

            // Tampilkan hasil JSON lengkap
            //document.getElementById("resultContent").textContent =
            //JSON.stringify(data, null, 2);
            //resultElement.classList.remove("hidden"); // Tampilkan hasil

            // Tampilkan verb_info di section terpisah

            // Tampilkan hasil dalam bentuk kolom
            window.lastSearchData = data;
            console.log("Data diterima dari API:", data);

            // Cek apakah data valid
            if (!data) {
                throw new Error("Tidak ada data yang diterima dari server");
            }

            // Cek struktur data untuk menampilkan
            if (data.result) {
                console.log("Data.result tersedia:", typeof data.result);
            } else {
                console.warn("Data.result tidak tersedia");
            }

            // Tampilkan informasi kata kerja
            if (verbInfoElement) {
                const verbInfoContent =
                    document.getElementById("verbInfoContent");

                // Logic untuk menampilkan verbInfo
                if (data.verb_info) {
                    verbInfoContent.textContent = data.verb_info;
                } else if (data.result && data.result[0] && data.result[0][0]) {
                    verbInfoContent.textContent = data.result[0][0];
                } else {
                    verbInfoContent.textContent = `الفعل ${verb}`;
                }

                verbInfoElement.classList.remove("hidden");
            }
            /*
            if (data.result && Array.isArray(data.result[0])) {
                const reversedResult = [...data.result[0]].reverse(); // Balikkan urutan array
                reversedResult.forEach((item) => {
                    if (item) {
                        const column = document.createElement("div");
                        column.textContent = item;
                        column.classList.add(
                            "p-4",
                            "bg-gray-100",
                            "rounded-lg",
                            "shadow-md",
                            "break-words"
                        ); // Tambahkan styling
                        resultColumnsElement.appendChild(column);
                    }
                });
            } else {
                console.warn("data.result[0] bukan array atau tidak ada");
            }*/
            if (data.result && data.result[0]) {
                console.log("Menampilkan hasil dari data.result[0]");

                // Konversi objek ke array jika perlu
                let resultArray;
                if (Array.isArray(data.result[0])) {
                    resultArray = [...data.result[0]];
                } else if (typeof data.result[0] === "object") {
                    // Jika objek dengan properti numerik, konversi ke array
                    resultArray = Object.keys(data.result[0])
                        .sort((a, b) => parseInt(a) - parseInt(b))
                        .map((key) => data.result[0][key]);
                }

                if (resultArray && resultArray.length) {
                    console.log(
                        "Ditemukan " +
                            resultArray.length +
                            " item untuk ditampilkan"
                    );

                    // Tampilkan judul kolom dari data ke 8 sampai ke 1
                    const headings = resultArray.slice(0, 8).reverse(); // Ambil 8 pertama untuk header
                    headings.forEach((item, index) => {
                        if (item) {
                            const column = document.createElement("div");
                            column.textContent = item;
                            column.classList.add(
                                "p-2",
                                "bg-blue-50",
                                "rounded-lg",
                                "shadow-sm",
                                "break-words",
                                "font-bold",
                                "text-center",
                                "text-md",
                                "text-blue-800"
                            );
                            resultColumnsElement.appendChild(column);
                        }
                    });
                } else {
                    console.warn("Tidak dapat mengekstrak data dari result[0]");
                }
            } else {
                console.warn("data.result[0] tidak ditemukan");
            }

            for (let i = 1; i <= 14; i++) {
                if (data.result && data.result[i]) {
                    let domirData,
                        madhiMalumData,
                        mudhoriMalumData,
                        mudhoriMajzumData,
                        mudhoriMansubData,
                        amarData,
                        mudhoriMuakkadData,
                        amarMuakkadData;
                    if (data.result[i] && Array.isArray(data.result[i])) {
                        domirData = data.result[i][0]; // Ambil data dengan indeks 0
                        madhiMalumData = data.result[i][1]; // Ambil data dengan indeks 1
                        mudhoriMalumData = data.result[i][2]; // Ambil data dengan indeks 2
                        mudhoriMajzumData = data.result[i][3]; // Ambil data dengan indeks 3
                        mudhoriMansubData = data.result[i][4]; // Ambil data dengan indeks 4
                        amarData = data.result[i][6]; // Ambil data dengan indeks 5
                        mudhoriMuakkadData = data.result[i][5]; // Ambil data dengan indeks 6
                        amarMuakkadData = data.result[i][7]; // Ambil data dengan indeks 7
                    } else if (typeof data.result[i] === "object") {
                        // Jika objek dengan properti numerik, konversi ke array
                        domirData = data.result[i]["0"]; // Ambil data dengan indeks 0
                        madhiMalumData = data.result[i]["1"]; // Ambil data dengan indeks 1
                        mudhoriMalumData = data.result[i]["2"]; // Ambil data dengan indeks 2
                        mudhoriMajzumData = data.result[i]["3"]; // Ambil data dengan indeks 3
                        mudhoriMansubData = data.result[i]["4"]; // Ambil data dengan indeks 4
                        amarData = data.result[i]["6"]; // Ambil data dengan indeks 5
                        mudhoriMuakkadData = data.result[i]["5"]; // Ambil data dengan indeks 6
                        amarMuakkadData = data.result[i]["7"]; // Ambil data dengan indeks 7
                    }

                    if (domirDataElement && domirData) {
                        const word = document.createElement("div");
                        word.textContent = domirData || "-"; // Tampilkan "-" jika kosong
                        word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                        domirDataElement.appendChild(word);
                    }

                    if (madhiMalumDataElement && madhiMalumData) {
                        const word = document.createElement("div");
                        word.textContent = madhiMalumData || "-"; // Tampilkan "-" jika kosong
                        word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                        madhiMalumDataElement.appendChild(word);
                    }

                    if (mudhoriMalumDataElement && mudhoriMalumData) {
                        const word = document.createElement("div");
                        word.textContent = mudhoriMalumData || "-"; // Tampilkan "-" jika kosong
                        word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                        mudhoriMalumDataElement.appendChild(word);
                    }

                    if (mudhoriMajzumDataElement && mudhoriMajzumData) {
                        const word = document.createElement("div");
                        word.textContent = mudhoriMajzumData || "-"; // Tampilkan "-" jika kosong
                        word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                        mudhoriMajzumDataElement.appendChild(word);
                    }

                    if (mudhoriMansubDataElement && mudhoriMansubData) {
                        const word = document.createElement("div");
                        word.textContent = mudhoriMansubData || "-"; // Tampilkan "-" jika kosong
                        word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                        mudhoriMansubDataElement.appendChild(word);
                    }

                    if (mudhoriMuakkadDataElement && mudhoriMuakkadData) {
                        const word = document.createElement("div");
                        word.textContent = mudhoriMuakkadData || "-"; // Tampilkan "-" jika kosong
                        word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                        mudhoriMuakkadDataElement.appendChild(word);
                    }

                    if (amarDataElement && amarData) {
                        const word = document.createElement("div");
                        word.textContent = amarData || "-"; // Tampilkan "-" jika kosong
                        word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                        amarDataElement.appendChild(word);
                    }

                    if (amarMuakkadDataElement && amarMuakkadData) {
                        const word = document.createElement("div");
                        word.textContent = amarMuakkadData || "-"; // Tampilkan "-" jika kosong
                        word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                        amarMuakkadDataElement.appendChild(word);
                    }
                }
            }
            console.log("Semua elemen telah ditampilkan");
            resultElement.classList.remove("hidden");
            /*
            for (let i = 1; i <= 14; i++) {
                if (data.result[i] && Array.isArray(data.result[i])) {
                    const pronoun = data.result[i][0]; // Ambil data dengan indeks 0
                    const word = document.createElement("div");
                    word.textContent = pronoun || "-"; // Tampilkan "-" jika kosong
                    word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                    domirDataElement.appendChild(word);
                }
            }

            // Tampilkan data "الماضي المعلوم" di madhiMalumData
            for (let i = 1; i <= 14; i++) {
                if (data.result[i] && Array.isArray(data.result[i])) {
                    const madhi = data.result[i][1]; // Ambil data dengan indeks 1
                    const word = document.createElement("div");
                    word.textContent = madhi || "-"; // Tampilkan "-" jika kosong
                    word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                    madhiMalumDataElement.appendChild(word);
                }
            }

            // Tampilkan data "المضارع المعلوم" di mudhoriMalumData
            for (let i = 1; i <= 14; i++) {
                if (data.result[i] && Array.isArray(data.result[i])) {
                    const mudhori = data.result[i][2]; // Ambil data dengan indeks 2
                    const word = document.createElement("div");
                    word.textContent = mudhori || "-"; // Tampilkan "-" jika kosong
                    word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                    mudhoriMalumDataElement.appendChild(word);
                }
            }

            // Tampilkan data "المضارع المجزوم" di mudhoriMajzumData
            for (let i = 1; i <= 14; i++) {
                if (data.result[i] && Array.isArray(data.result[i])) {
                    const mudhoriMajzum = data.result[i][3]; // Ambil data dengan indeks 3
                    const word = document.createElement("div");
                    word.textContent = mudhoriMajzum || "-"; // Tampilkan "-" jika kosong
                    word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                    mudhoriMajzumDataElement.appendChild(word);
                }
            }
            // Tampilkan data "المضارع المنصوب" di mudhoriMansubData
            for (let i = 1; i <= 14; i++) {
                if (data.result[i] && Array.isArray(data.result[i])) {
                    const mudhoriMansub = data.result[i][4]; // Ambil data dengan indeks 4
                    const word = document.createElement("div");
                    word.textContent = mudhoriMansub || "-"; // Tampilkan "-" jika kosong
                    word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                    mudhoriMansubDataElement.appendChild(word);
                }
            }
            // Tampilkan data "الأمر" di amarData
            for (let i = 1; i <= 14; i++) {
                if (data.result[i] && Array.isArray(data.result[i])) {
                    const amar = data.result[i][6]; // Ambil data dengan indeks 5
                    const word = document.createElement("div");
                    word.textContent = amar || "-"; // Tampilkan "-" jika kosong
                    word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                    amarDataElement.appendChild(word);
                }
            }
            // Tampilkan data "المضارع المؤكد" di mudhoriMuakkadData
            for (let i = 1; i <= 14; i++) {
                if (data.result[i] && Array.isArray(data.result[i])) {
                    const mudhoriMuakkad = data.result[i][5]; // Ambil data dengan indeks 6
                    const word = document.createElement("div");
                    word.textContent = mudhoriMuakkad || "-"; // Tampilkan "-" jika kosong
                    word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                    mudhoriMuakkadDataElement.appendChild(word);
                }
            }
            // Tampilkan data "الأمر المؤكد" di amarMuakkadData
            for (let i = 1; i <= 14; i++) {
                if (data.result[i] && Array.isArray(data.result[i])) {
                    const amarMuakkad = data.result[i][7]; // Ambil data dengan indeks 7
                    const word = document.createElement("div");
                    word.textContent = amarMuakkad || "-"; // Tampilkan "-" jika kosong
                    word.classList.add("mb-2", "text-right"); // Tambahkan margin bawah dan rata kanan
                    amarMuakkadDataElement.appendChild(word);
                }
            } */

            if (data.verb_info) {
                document.getElementById("verbInfoContent").textContent =
                    data.verb_info;
                verbInfoElement.classList.remove("hidden"); // Tampilkan informasi kata kerja
            } else {
                document.getElementById("verbInfoContent").textContent =
                    "Informasi kata kerja tidak tersedia.";
                verbInfoElement.classList.remove("hidden");
            }

            if (data.suggest && Array.isArray(data.suggest)) {
                data.suggest.forEach((item) => {
                    const listItem = document.createElement("li");

                    const verbSpan = document.createElement("span");
                    verbSpan.textContent = `${item.verb}`;
                    verbSpan.style.marginRight = "20px"; // Tambahkan jarak antar elemen

                    const futureSpan = document.createElement("span");
                    futureSpan.textContent = `- ${item.future}`;

                    listItem.appendChild(verbSpan);
                    listItem.appendChild(futureSpan);
                    suggestListElement.appendChild(listItem);
                });
            }
            resultElement.classList.remove("hidden"); // Tampilkan hasil
        } catch (error) {
            console.error("Error saat mencari kata kerja:", error);
            document.getElementById(
                "resultContent"
            ).textContent = `Error: ${error.message}`;
            resultElement.classList.remove("hidden");
        } finally {
            // Sembunyikan loading screen setelah selesai
            loadingElement.classList.add("hidden");
        }
    });
