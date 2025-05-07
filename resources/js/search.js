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

document
    .getElementById("searchForm")
    .addEventListener("submit", async function (event) {
        event.preventDefault(); // Mencegah pengiriman form secara default

        const verb = document.getElementById("verb").value; // Ambil nilai dari input
        const apiUrl = `/api/search-verb?verb=${encodeURIComponent(verb)}`; // Route Laravel

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
            const response = await fetch(apiUrl); // Kirim request ke Laravel
            if (!response.ok) {
                throw new Error("Gagal mengambil data dari server");
            }

            const data = await response.json(); // Parse hasil response

            // Tampilkan hasil JSON lengkap
            //document.getElementById("resultContent").textContent =
            //JSON.stringify(data, null, 2);
            //resultElement.classList.remove("hidden"); // Tampilkan hasil

            // Tampilkan verb_info di section terpisah

            // Tampilkan hasil dalam bentuk kolom
            if (data.result && Array.isArray(data.result[0])) {
                const reversedResult = [...data.result[0]].reverse(); // Balikkan urutan array
                reversedResult.forEach((item) => {
                    const column = document.createElement("div");
                    column.textContent = item;
                    column.classList.add(
                        "p-4",
                        "bg-gray-100",
                        "rounded-lg",
                        "shadow-md"
                    ); // Tambahkan styling
                    resultColumnsElement.appendChild(column);
                });
            }

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
            }

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
            document.getElementById(
                "resultContent"
            ).textContent = `Error: ${error.message}`;
            resultElement.classList.remove("hidden");
        } finally {
            // Sembunyikan loading screen setelah selesai
            loadingElement.classList.add("hidden");
        }
    });
