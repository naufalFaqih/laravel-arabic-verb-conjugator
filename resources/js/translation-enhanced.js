/**
 * TranslationEnhanced — client-side proxy to /api/translate.
 *
 * Extracted from the inline <script> block in home.blade.php as part of
 * Task 7 (Livewire migration). Behaviour preserved: dual-layer caching
 * (in-memory Map + localStorage), DeepSeek-driven translations, local
 * dictionary fallback, plus a small set of debug helpers exposed under
 * `window.debugDeepSeek`.
 */

const TranslationEnhanced = {
    cache: new Map(),
    debugMode: true,

    log(message, data = null) {
        if (this.debugMode) {
            console.log(`[Translation] ${message}`, data || "");
        }
    },

    error(message, error = null) {
        console.error(`[Translation Error] ${message}`, error || "");
    },

    translateAll() {
        this.log("🔄 Starting DeepSeek translation for all elements...");
        const elements = document.querySelectorAll("[data-translate-arabic]");
        this.log(`📝 Found ${elements.length} elements to translate`);

        if (elements.length === 0) {
            this.log("⚠️ No elements with data-translate-arabic found");
            return;
        }

        elements.forEach((element, index) => {
            const arabicText = element.getAttribute("data-translate-arabic");

            if (!arabicText || arabicText === "-" || arabicText.trim() === "") {
                this.log(`⏭️ Skipping element ${index + 1}: empty text`);
                return;
            }

            const translationElement = this.findOrCreateTranslationElement(element);
            this.translateElement(arabicText, translationElement, element.id || `element-${index}`);
        });
    },

    findOrCreateTranslationElement(element) {
        let translationElement = element.nextElementSibling;
        if (translationElement && translationElement.classList.contains("translation-text")) {
            return translationElement;
        }

        const parentTranslation = element.parentElement?.querySelector(".translation-text");
        if (parentTranslation) {
            return parentTranslation;
        }

        translationElement = document.createElement("div");
        translationElement.className = "translation-text text-xs mt-2 text-gray-600";
        if (element.nextSibling) {
            element.parentNode.insertBefore(translationElement, element.nextSibling);
        } else {
            element.parentNode.appendChild(translationElement);
        }
        return translationElement;
    },

    translateElement(arabicText, targetElement, sourceId = "") {
        this.log(`🔤 Translating "${arabicText}" for element ${sourceId}`);

        if (this.cache.has(arabicText)) {
            const cached = this.cache.get(arabicText);
            targetElement.textContent = cached;
            return Promise.resolve(cached);
        }

        const cacheKey = `deepseek_translate_${btoa(encodeURIComponent(arabicText))}`;
        const cached = localStorage.getItem(cacheKey);
        if (cached && !/[\u0600-\u06FF]/.test(cached)) {
            this.log(`💽 Using localStorage cache: ${cached}`);
            targetElement.textContent = cached;
            this.cache.set(arabicText, cached);
            return Promise.resolve(cached);
        }

        targetElement.innerHTML =
            '<div class="translation-loading inline-flex gap-1"><span>•</span><span>•</span><span>•</span></div>';

        return this.callTranslationAPI(arabicText)
            .then((translation) => {
                if (translation) {
                    targetElement.textContent = translation;
                    this.cache.set(arabicText, translation);
                    localStorage.setItem(cacheKey, translation);
                    return translation;
                }
                throw new Error("No translation returned");
            })
            .catch((err) => {
                this.error(`Translation failed for "${arabicText}":`, err);
                const local = this.getLocalTranslation(arabicText);
                if (local) {
                    targetElement.textContent = local + " (lokal)";
                    return local;
                }
                targetElement.innerHTML =
                    '<span class="translation-error text-red-500 text-xs">Gagal menerjemahkan</span>';
                return null;
            });
    },

    callTranslationAPI(text) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            this.error("CSRF token not found");
            return Promise.reject(new Error("CSRF token not found"));
        }

        return fetch("/api/translate", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify({ text, source: "ar", target: "id", force: false }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`API error: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.success && data.translation) return data.translation;
                throw new Error(data.message || "Translation failed");
            });
    },

    getLocalTranslation(text) {
        const localDict = {
            الماضي: "masa lampau",
            المضارع: "masa sekarang",
            الأمر: "perintah",
            كَتَبَ: "menulis (dia lk)",
            يَكْتُبُ: "sedang menulis (dia lk)",
            اُكْتُبْ: "tulislah!",
            "Informasi Kata Kerja": "Informasi Kata Kerja",
            "Ditemukan Juga Pada Bab": "Ditemukan Juga Pada Bab",
        };
        if (localDict[text]) return localDict[text];

        for (const [key, value] of Object.entries(localDict)) {
            if (text.includes(key)) return value + " (sebagian)";
        }
        return null;
    },

    clearCache() {
        this.cache.clear();
        const keys = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key && key.startsWith("deepseek_translate_")) keys.push(key);
        }
        keys.forEach((key) => localStorage.removeItem(key));
        this.log(`🗑️ Cleared ${keys.length} cached translation entries`);
    },

    forceRetranslate() {
        this.clearCache();
        document.querySelectorAll(".translation-text").forEach((el) => {
            el.innerHTML = "";
        });
        setTimeout(() => this.translateAll(), 500);
    },

    testAPI() {
        return fetch("/api/translate/check", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        })
            .then((r) => r.json())
            .then((data) => {
                this.log("API Test Result:", data);
                return data;
            });
    },
};

document.addEventListener("DOMContentLoaded", () => {
    // Purge invalid Arabic-text cache entries
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith("deepseek_translate_")) {
            const value = localStorage.getItem(key);
            if (/[\u0600-\u06FF]/.test(value)) {
                localStorage.removeItem(key);
            }
        }
    }

    setTimeout(() => TranslationEnhanced.translateAll(), 1000);

    // Re-translate when Livewire injects new Arabic content
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type !== "childList") return;
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType !== 1) return;
                const targets = node.querySelectorAll
                    ? node.querySelectorAll("[data-translate-arabic]")
                    : [];
                if (targets.length > 0) {
                    setTimeout(() => TranslationEnhanced.translateAll(), 100);
                }
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
});

window.TranslationEnhanced = TranslationEnhanced;
window.TranslationAPI = TranslationEnhanced;

window.debugDeepSeek = {
    testAPI: () => TranslationEnhanced.testAPI(),
    testTranslation: (text = "السلام عليكم") => TranslationEnhanced.callTranslationAPI(text),
    checkElements: () => {
        const elements = document.querySelectorAll("[data-translate-arabic]");
        console.log(`Found ${elements.length} elements with data-translate-arabic`);
        return elements;
    },
    forceTranslate: () => TranslationEnhanced.forceRetranslate(),
    clearCache: () => TranslationEnhanced.clearCache(),
    checkStatus: () => ({
        TranslationEnhanced: !!window.TranslationEnhanced,
        TranslationAPI: !!window.TranslationAPI,
        methods: Object.keys(TranslationEnhanced),
    }),
};

console.log("🔧 Translation enhanced loaded. Use window.debugDeepSeek for testing.");
