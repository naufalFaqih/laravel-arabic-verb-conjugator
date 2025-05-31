// Translation Enhanced untuk Google Translate
window.TranslationEnhanced = {
    /**
     * Cache untuk menyimpan hasil terjemahan
     */
    cache: new Map(),

    /**
     * Terjemahkan semua elemen dengan data-translate-arabic
     */
    translateAll: function () {
        console.log("🔄 Starting enhanced translation for all elements...");

        const elements = document.querySelectorAll("[data-translate-arabic]");
        console.log(`📝 Found ${elements.length} elements to translate`);

        if (elements.length === 0) {
            console.log("⚠️ No elements with data-translate-arabic found");
            return;
        }

        elements.forEach((element, index) => {
            const arabicText = element.getAttribute("data-translate-arabic");

            if (!arabicText || arabicText === "-" || arabicText.trim() === "") {
                console.log(`⏭️ Skipping element ${index + 1}: empty text`);
                return;
            }

            // Cari atau buat elemen terjemahan
            let translationElement =
                this.findOrCreateTranslationElement(element);

            // Terjemahkan
            this.translateElement(
                arabicText,
                translationElement,
                element.id || `element-${index}`
            );
        });
    },

    /**
     * Cari atau buat elemen terjemahan
     */
    findOrCreateTranslationElement: function (element) {
        let translationElement = element.nextElementSibling;

        // Jika sudah ada elemen translation-text, gunakan itu
        if (
            translationElement &&
            translationElement.classList.contains("translation-text")
        ) {
            return translationElement;
        }

        // Cari di dalam parent element
        let parentTranslation =
            element.parentElement.querySelector(".translation-text");
        if (parentTranslation) {
            return parentTranslation;
        }

        // Buat elemen baru
        translationElement = document.createElement("div");
        translationElement.className =
            "translation-text text-xs mt-2 text-gray-600";

        // Insert setelah element Arab
        if (element.nextSibling) {
            element.parentNode.insertBefore(
                translationElement,
                element.nextSibling
            );
        } else {
            element.parentNode.appendChild(translationElement);
        }

        return translationElement;
    },

    /**
     * Terjemahkan elemen individual
     */
    translateElement: function (arabicText, targetElement, sourceId = "") {
        console.log(`🔤 Translating "${arabicText}" for element ${sourceId}`);

        // Cek cache terlebih dahulu
        if (this.cache.has(arabicText)) {
            const cachedTranslation = this.cache.get(arabicText);
            console.log(`💾 Using memory cache: ${cachedTranslation}`);
            targetElement.textContent = cachedTranslation;
            return Promise.resolve(cachedTranslation);
        }

        // Cek localStorage
        const cacheKey = `google_translate_${btoa(
            encodeURIComponent(arabicText)
        )}`;
        const cachedTranslation = localStorage.getItem(cacheKey);

        if (cachedTranslation && !/[\u0600-\u06FF]/.test(cachedTranslation)) {
            console.log(`💽 Using localStorage cache: ${cachedTranslation}`);
            targetElement.textContent = cachedTranslation;
            this.cache.set(arabicText, cachedTranslation);
            return Promise.resolve(cachedTranslation);
        }

        // Tampilkan loading
        targetElement.innerHTML =
            '<div class="translation-loading inline-flex gap-1"><span>•</span><span>•</span><span>•</span></div>';

        // Panggil API
        return this.callTranslationAPI(arabicText)
            .then((translation) => {
                if (translation) {
                    targetElement.textContent = translation;

                    // Simpan ke cache
                    this.cache.set(arabicText, translation);
                    localStorage.setItem(cacheKey, translation);

                    console.log(
                        `✅ Translation success for "${arabicText}": ${translation}`
                    );
                    return translation;
                } else {
                    throw new Error("No translation returned");
                }
            })
            .catch((error) => {
                console.error(
                    `❌ Translation failed for "${arabicText}":`,
                    error
                );

                // Coba terjemahan lokal sebagai fallback
                const localTranslation = this.getLocalTranslation(arabicText);
                if (localTranslation) {
                    targetElement.textContent = localTranslation + " (lokal)";
                    return localTranslation;
                } else {
                    targetElement.innerHTML =
                        '<span class="translation-error text-red-500 text-xs">Gagal menerjemahkan</span>';
                    return null;
                }
            });
    },

    /**
     * Panggil API terjemahan
     */
    callTranslationAPI: function (text) {
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;

        if (!csrfToken) {
            return Promise.reject(new Error("CSRF token not found"));
        }

        return fetch("/api/translate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify({
                text: text,
                source: "ar",
                target: "id",
                force: false,
            }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        `API error: ${response.status} ${response.statusText}`
                    );
                }
                return response.json();
            })
            .then((data) => {
                if (data.success && data.translation) {
                    return data.translation;
                }
                throw new Error(data.message || "Translation failed");
            });
    },

    /**
     * Ambil terjemahan lokal sebagai fallback
     */
    getLocalTranslation: function (text) {
        const localDict = {
            الماضي: "masa lampau",
            المضارع: "masa sekarang",
            الأمر: "perintah",
            كَتَبَ: "menulis (dia lk)",
            يَكْتُبُ: "sedang menulis (dia lk)",
            اُكْتُبْ: "tulislah!",
            كَتَبَتْ: "menulis (dia pr)",
            تَكْتُبُ: "sedang menulis (dia pr)",
            كَتَبْتُ: "saya menulis",
            أَكْتُبُ: "saya sedang menulis",
            كَتَبْنَا: "kami menulis",
            نَكْتُبُ: "kami sedang menulis",
            الفعل: "kata kerja",
            قَرَأَ: "membaca",
            يَقْرَأُ: "sedang membaca",
            اِقْرَأْ: "bacalah!",
            سَلَّمَ: "menyerahkan/mengucapkan salam",
            يُسَلِّمُ: "sedang menyerahkan/mengucapkan salam",
            سَلِّمْ: "serahkanlah!/ucapkan salam!",
            ضَرَبَ: "memukul",
            يَضْرِبُ: "sedang memukul",
            اِضْرِبْ: "pukullah!",
            اِشْتَغَلَ: "bekerja/sibuk dengan",
            يَشْتَغِلُ: "sedang bekerja/sibuk dengan",
            اِشْتَغِلْ: "bekerjalah!/sibukkan diri dengan!",
            فعل: "kata kerja",
            "فعل ثلاثي": "kata kerja berwazan ثلاثي",
            "فعل خماسي": "kata kerja berwazan خماسي",
            متعدي: "transitif (membutuhkan objek)",
            لازم: "intransitif (tidak membutuhkan objek)",
            صحيح: "kata kerja tanpa huruf illat",
            سالم: "kata kerja dengan huruf yang selamat",
        };

        // Cari exact match
        if (localDict[text]) {
            return localDict[text];
        }

        // Cari partial match
        for (const [key, value] of Object.entries(localDict)) {
            if (text.includes(key)) {
                return value + " (sebagian)";
            }
        }

        return null;
    },

    /**
     * Clear cache
     */
    clearCache: function () {
        // Clear memory cache
        this.cache.clear();

        // Clear localStorage
        const keys = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith("google_translate_")) {
                keys.push(key);
            }
        }

        keys.forEach((key) => localStorage.removeItem(key));
        console.log(`🗑️ Cleared ${keys.length} translation cache entries`);
    },

    /**
     * Force retranslate all elements
     */
    forceRetranslate: function () {
        console.log("🔄 Force retranslating all elements...");
        this.clearCache();

        // Clear existing translations
        document.querySelectorAll(".translation-text").forEach((el) => {
            el.innerHTML = "";
        });

        // Start translating again
        setTimeout(() => {
            this.translateAll();
        }, 500);
    },

    /**
     * Test translation dengan teks contoh
     */
    testTranslation: function () {
        console.log("🧪 Testing translation...");

        // Buat element test
        const testDiv = document.createElement("div");
        testDiv.innerHTML = `
            <div class="test-element arabic-text" data-translate-arabic="السلام عليكم">السلام عليكم</div>
            <div class="translation-text"></div>
        `;

        document.body.appendChild(testDiv);

        // Translate
        this.translateAll();

        // Remove after 5 seconds
        setTimeout(() => {
            document.body.removeChild(testDiv);
        }, 5000);
    },
};

// Auto-initialize saat DOM ready
document.addEventListener("DOMContentLoaded", function () {
    console.log("🌐 Google Translate Enhanced loading...");

    // Tunggu sebentar untuk memastikan semua elemen dimuat
    setTimeout(() => {
        // Hapus cache terjemahan yang rusak
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith("google_translate_")) {
                const value = localStorage.getItem(key);
                // Jika nilai cache adalah teks Arab (mengandung karakter Arab)
                if (/[\u0600-\u06FF]/.test(value)) {
                    console.log(
                        `🗑️ Removing invalid Google Translate cache: ${key}`
                    );
                    localStorage.removeItem(key);
                }
            }
        }

        // Tunggu sedikit kemudian terjemahkan semua
        setTimeout(() => {
            if (window.TranslationEnhanced) {
                window.TranslationEnhanced.translateAll();
            }
        }, 500);
    }, 1000);
});

// Export untuk penggunaan global
window.TranslationAPI = window.TranslationEnhanced;
