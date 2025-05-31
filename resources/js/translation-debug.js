function forceRenderTranslations() {
    console.log("Force rendering translations");

    // Hapus cache dulu
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith("ar_id_trans:")) {
            localStorage.removeItem(key);
        }
    }

    // Dapatkan semua elemen dengan data-translate-arabic
    const elements = document.querySelectorAll("[data-translate-arabic]");
    console.log(`Found ${elements.length} elements to force translate`);

    elements.forEach((el, index) => {
        const arabicText = el.getAttribute("data-translate-arabic");
        if (!arabicText || arabicText === "-") return;

        // Dapatkan elemen terjemahan
        let translationElement = el.nextElementSibling;

        if (
            !translationElement ||
            !translationElement.classList.contains("translation-text")
        ) {
            console.log(`Creating translation element for #${index + 1}`);

            // Buat elemen terjemahan baru
            translationElement = document.createElement("div");
            translationElement.className =
                "translation-text text-xs mt-2 text-gray-600";

            // Sisipkan setelah elemen sumber
            if (el.nextSibling) {
                el.parentNode.insertBefore(translationElement, el.nextSibling);
            } else {
                el.parentNode.appendChild(translationElement);
            }
        }

        // Tampilkan loading
        translationElement.innerHTML =
            '<div class="translation-loading"><span>•</span><span>•</span><span>•</span></div>';

        // SELALU gunakan API untuk memaksa terjemahan baru
        console.log(`Forcing new translation for "${arabicText}"`);

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;
        if (!csrfToken) {
            console.error("CSRF token not found");
            return;
        }

        fetch("/api/translate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify({
                text: arabicText,
                source: "ar",
                target: "id",
                force: true, // Tambahkan flag force untuk bypass cache di server
            }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        `Translation API error: ${response.status}`
                    );
                }
                return response.json();
            })
            .then((data) => {
                console.log(`Translation result for "${arabicText}":`, data);

                if (data.success && data.translation) {
                    // Update teks dengan hasil terjemahan
                    translationElement.textContent = data.translation;

                    // Simpan ke cache hanya jika berbeda dari teks asli
                    if (data.translation !== arabicText) {
                        localStorage.setItem(
                            `ar_id_trans:${arabicText}`,
                            data.translation
                        );
                    }
                } else {
                    throw new Error("No translation data returned");
                }
            })
            .catch((error) => {
                console.error(`Error translating "${arabicText}":`, error);
                translationElement.innerHTML = `<span class="translation-error">Error: ${error.message}</span>`;
            });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    console.log("Translation debug script loaded");

    // Tambahkan tombol debug di sudut kanan bawah
    const debugButton = document.createElement("button");
    debugButton.textContent = "Debug Terjemahan";
    debugButton.style.position = "fixed";
    debugButton.style.bottom = "20px";
    debugButton.style.right = "20px";
    debugButton.style.zIndex = "9999";
    debugButton.style.padding = "8px 12px";
    debugButton.style.backgroundColor = "#f56565";
    debugButton.style.color = "white";
    debugButton.style.borderRadius = "4px";
    debugButton.style.fontSize = "12px";
    debugButton.style.cursor = "pointer";
    debugButton.style.display = "none"; // Sembunyikan tombol secara default

    debugButton.addEventListener("click", () => {
        console.log("=== TRANSLATION DEBUG ===");

        // Cek API tersedia
        if (window.TranslationAPI) {
            console.log("✅ TranslationAPI is available");
        } else {
            console.error("❌ TranslationAPI is NOT available");
        }

        // Cek elemen untuk diterjemahkan
        const translatableElements = document.querySelectorAll(
            "[data-translate-arabic]"
        );
        console.log(
            `Found ${translatableElements.length} elements with data-translate-arabic attribute`
        );

        translatableElements.forEach((el, i) => {
            const arabicText = el.getAttribute("data-translate-arabic");
            const translationTarget = el.nextElementSibling;
            const hasTranslationTarget =
                translationTarget &&
                translationTarget.classList.contains("translation-text");

            console.log(`Element #${i + 1}:`, {
                element: el,
                arabicText: arabicText,
                translationTarget: translationTarget,
                hasProperTarget: hasTranslationTarget,
            });

            // Jika tidak memiliki elemen terjemahan, tambahkan
            if (!hasTranslationTarget) {
                const newTarget = document.createElement("div");
                newTarget.className = "translation-text";

                if (el.nextSibling) {
                    el.parentNode.insertBefore(newTarget, el.nextSibling);
                } else {
                    el.parentNode.appendChild(newTarget);
                }

                console.log(
                    `Created new translation target for element #${i + 1}`
                );
            }
        });

        // Coba terjemahkan semua
        if (window.TranslationAPI) {
            console.log("Attempting to translate all elements...");
            window.TranslationAPI.translateAll();
        }

        // Coba terjemahkan kata tertentu untuk tes
        testTranslation("سَلَّمَ");
        forceRenderTranslations();
    });

    document.body.appendChild(debugButton);

    // Fungsi untuk menguji terjemahan secara manual
    function testTranslation(text) {
        console.log(`Testing translation for: "${text}"`);

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;
        if (!csrfToken) {
            console.error("❌ CSRF token not found");
            return;
        }

        fetch("/api/translate", {
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
            }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`API error: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log("✅ Test translation result:", data);
            })
            .catch((error) => {
                console.error("❌ Test translation error:", error);
            });
    }
});

// Ekspos fungsi debugging ke global scope
window.debugTranslation = {
    // Tambahkan fungsi ini ke window.debugTranslation
    // Tambahkan fungsi ini ke window.debugTranslation
    checkGoogleTranslateAPI: function () {
        console.log("Checking Google Translate API status...");

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;
        if (!csrfToken) {
            console.error("❌ CSRF token not found");
            return;
        }

        fetch("/api/translate/check", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`API check failed: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log("✅ Google Translate API status:", data);
                if (data.success) {
                    console.log(`Service: ${data.service}`);
                    console.log(`Sample translation: ${data.sample_result}`);
                } else {
                    console.error(`API error: ${data.message}`);
                }
            })
            .catch((error) => {
                console.error("❌ Google Translate API check error:", error);
            });
    },

    testBatchTranslation: function () {
        const testTexts = ["السلام عليكم", "كَتَبَ", "يَكْتُبُ", "اُكْتُبْ"];

        console.log("Testing batch translation...");

        window.TranslationEnhanced.batchTranslate(testTexts)
            .then((data) => {
                console.log("✅ Batch translation results:", data);
            })
            .catch((error) => {
                console.error("❌ Batch translation failed:", error);
            });
    },

    forceGoogleTranslation: function () {
        console.log("Forcing Google Translation for all elements...");
        window.TranslationEnhanced.forceRetranslate();
    },
};
