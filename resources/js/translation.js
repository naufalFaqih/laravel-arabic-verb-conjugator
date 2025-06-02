// Translation Enhanced untuk DeepSeek API dengan debugging
window.TranslationEnhanced = {
    cache: new Map(),
    debugMode: true,

    log: function (message, data = null) {
        if (this.debugMode) {
            console.log(`[Translation] ${message}`, data || "");
        }
    },

    error: function (message, error = null) {
        console.error(`[Translation Error] ${message}`, error || "");
    },

    translateAll: function () {
        this.log("🔄 Starting DeepSeek translation for all elements...");

        const elements = document.querySelectorAll("[data-translate-arabic]");
        this.log(`📝 Found ${elements.length} elements to translate`);

        if (elements.length === 0) {
            this.log("⚠️ No elements with data-translate-arabic found");
            return;
        }

        elements.forEach((element, index) => {
            const arabicText = element.getAttribute("data-translate-arabic");

            this.log(`Element ${index + 1}:`, {
                id: element.id,
                text: arabicText,
                element: element,
            });

            if (!arabicText || arabicText === "-" || arabicText.trim() === "") {
                this.log(`⏭️ Skipping element ${index + 1}: empty text`);
                return;
            }

            let translationElement =
                this.findOrCreateTranslationElement(element);
            this.translateElement(
                arabicText,
                translationElement,
                element.id || `element-${index}`
            );
        });
    },

    findOrCreateTranslationElement: function (element) {
        let translationElement = element.nextElementSibling;

        if (
            translationElement &&
            translationElement.classList.contains("translation-text")
        ) {
            this.log("Found existing translation element");
            return translationElement;
        }

        let parentTranslation =
            element.parentElement.querySelector(".translation-text");
        if (parentTranslation) {
            this.log("Found parent translation element");
            return parentTranslation;
        }

        this.log("Creating new translation element");
        translationElement = document.createElement("div");
        translationElement.className =
            "translation-text text-xs mt-2 text-gray-600";

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

    translateElement: function (arabicText, targetElement, sourceId = "") {
        this.log(
            `🔤 Translating "${arabicText}" for element ${sourceId} using DeepSeek`
        );

        if (this.cache.has(arabicText)) {
            const cachedTranslation = this.cache.get(arabicText);
            this.log(`💾 Using memory cache: ${cachedTranslation}`);
            targetElement.textContent = cachedTranslation;
            return Promise.resolve(cachedTranslation);
        }

        const cacheKey = `deepseek_translate_${btoa(
            encodeURIComponent(arabicText)
        )}`;
        const cachedTranslation = localStorage.getItem(cacheKey);

        if (cachedTranslation && !/[\u0600-\u06FF]/.test(cachedTranslation)) {
            this.log(`💽 Using localStorage cache: ${cachedTranslation}`);
            targetElement.textContent = cachedTranslation;
            this.cache.set(arabicText, cachedTranslation);
            return Promise.resolve(cachedTranslation);
        }

        targetElement.innerHTML =
            '<div class="translation-loading inline-flex gap-1"><span>•</span><span>•</span><span>•</span></div>';

        return this.callTranslationAPI(arabicText)
            .then((translation) => {
                if (translation) {
                    targetElement.textContent = translation;
                    this.cache.set(arabicText, translation);
                    localStorage.setItem(cacheKey, translation);
                    this.log(
                        `✅ DeepSeek translation success for "${arabicText}": ${translation}`
                    );
                    return translation;
                } else {
                    throw new Error("No translation returned");
                }
            })
            .catch((error) => {
                this.error(`Translation failed for "${arabicText}":`, error);
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

    callTranslationAPI: function (text) {
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        )?.content;

        if (!csrfToken) {
            this.error("CSRF token not found");
            return Promise.reject(new Error("CSRF token not found"));
        }

        this.log(`🌐 Calling API for: "${text}"`);

        const requestData = {
            text: text,
            source: "ar",
            target: "id",
            force: false,
        };

        this.log("Request data:", requestData);

        return fetch("/api/translate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify(requestData),
        })
            .then((response) => {
                this.log(`API Response status: ${response.status}`);

                if (!response.ok) {
                    throw new Error(
                        `API error: ${response.status} ${response.statusText}`
                    );
                }
                return response.json();
            })
            .then((data) => {
                this.log("API Response data:", data);

                if (data.success && data.translation) {
                    return data.translation;
                }
                throw new Error(data.message || "Translation failed");
            })
            .catch((error) => {
                this.error("API call failed:", error);
                throw error;
            });
    },

    getLocalTranslation: function (text) {
        const localDict = {
            الماضي: "masa lampau",
            المضارع: "masa sekarang",
            الأمر: "perintah",
            كَتَبَ: "menulis (dia lk)",
            يَكْتُبُ: "sedang menulis (dia lk)",
            اُكْتُبْ: "tulislah!",
            ":Informasi Kata Kerja": "Informasi Kata Kerja",
            ":Ditemukan Juga Pada Bab": "Ditemukan Juga Pada Bab",
        };

        if (localDict[text]) {
            return localDict[text];
        }

        for (const [key, value] of Object.entries(localDict)) {
            if (text.includes(key)) {
                return value + " (sebagian)";
            }
        }

        return null;
    },

    clearCache: function () {
        this.cache.clear();
        const keys = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith("deepseek_translate_")) {
                keys.push(key);
            }
        }
        keys.forEach((key) => localStorage.removeItem(key));
        this.log(
            `🗑️ Cleared ${keys.length} DeepSeek translation cache entries`
        );
    },

    forceRetranslate: function () {
        this.log("🔄 Force retranslating all elements with DeepSeek...");
        this.clearCache();
        document.querySelectorAll(".translation-text").forEach((el) => {
            el.innerHTML = "";
        });
        setTimeout(() => {
            this.translateAll();
        }, 500);
    },

    testAPI: function () {
        this.log("🧪 Testing DeepSeek API...");

        return fetch("/api/translate/check", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        })
            .then((r) => r.json())
            .then((data) => {
                this.log("API Test Result:", data);
                return data;
            })
            .catch((error) => {
                this.error("API Test Failed:", error);
                throw error;
            });
    },
};

// Auto-initialize
document.addEventListener("DOMContentLoaded", function () {
    console.log("🌐 DeepSeek Translation Enhanced loading...");

    // Test API first
    if (window.TranslationEnhanced) {
        window.TranslationEnhanced.testAPI()
            .then(() => {
                setTimeout(() => {
                    window.TranslationEnhanced.translateAll();
                }, 1000);
            })
            .catch(() => {
                console.log(
                    "API test failed, but continuing with translation attempt..."
                );
                setTimeout(() => {
                    window.TranslationEnhanced.translateAll();
                }, 1000);
            });
    }
});

// Export untuk penggunaan global
window.TranslationAPI = window.TranslationEnhanced;
