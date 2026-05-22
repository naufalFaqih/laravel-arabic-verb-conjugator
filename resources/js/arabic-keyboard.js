/**
 * Arabic on-screen keyboard.
 *
 * Behavior:
 *   - Auto-shows whenever an `.arabic-input` (or `[data-arabic-keyboard]`)
 *     element receives focus, on every viewport size.
 *   - Native mobile virtual keyboard is suppressed via `inputmode="none"`
 *     (added automatically if not already present on the input).
 *   - Tapping/clicking a keyboard key uses `mousedown`/`touchstart` +
 *     `preventDefault()` so the underlying input never loses focus, which
 *     keeps things responsive and prevents flicker.
 *   - Hides only when the input loses focus to something that is not the
 *     keyboard, when Escape is pressed, or when the close (✕) button is
 *     activated.
 *   - When shown, the page gains a bottom padding (`body.ak-active`) and
 *     the focused input is scrolled into view, so the keyboard never
 *     covers what the user is typing.
 *
 * Bundled via Vite from `resources/js/app.js`.
 */
class ArabicKeyboard {
    constructor() {
        this.currentInput = null;
        this.isVisible = false;
        this.boundInputs = new WeakSet();
        this.init();
    }

    init() {
        this.attachToInputs();
        this.setupKeyEvents();
        this.setupGlobalShortcuts();
    }

    // ---------------------------------------------------------------- inputs

    attachToInputs() {
        const setup = (el) => this.setupInputEvents(el);

        document
            .querySelectorAll(".arabic-input, [data-arabic-keyboard]")
            .forEach(setup);

        new MutationObserver((mutations) => {
            mutations.forEach((mutation) =>
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType !== 1) return;
                    if (node.matches?.(".arabic-input, [data-arabic-keyboard]")) {
                        setup(node);
                    }
                    node.querySelectorAll?.(
                        ".arabic-input, [data-arabic-keyboard]"
                    ).forEach(setup);
                })
            );
        }).observe(document.body, { childList: true, subtree: true });
    }

    setupInputEvents(input) {
        if (this.boundInputs.has(input)) return;
        this.boundInputs.add(input);

        // Suppress native mobile virtual keyboard so only ours appears.
        if (!input.hasAttribute("inputmode")) {
            input.setAttribute("inputmode", "none");
        }

        // RTL Arabic display tweaks.
        input.style.direction = "rtl";
        input.style.textAlign = "right";
        input.style.fontFamily = "'Amiri', 'Times New Roman', serif";

        input.addEventListener("focus", (event) =>
            this.showKeyboard(event.target)
        );
        input.addEventListener("click", (event) =>
            this.showKeyboard(event.target)
        );

        // Hide when the input loses focus, *unless* focus moved to another
        // arabic input. Keyboard buttons themselves never grab focus because
        // we preventDefault on their `mousedown`.
        input.addEventListener("blur", () => {
            setTimeout(() => {
                if (!this.isVisible) return;
                const active = document.activeElement;
                if (
                    !active ||
                    !active.matches?.(".arabic-input, [data-arabic-keyboard]")
                ) {
                    this.hideKeyboard();
                }
            }, 150);
        });
    }

    // ------------------------------------------------------------ visibility

    showKeyboard(input) {
        this.currentInput = input;
        const keyboard = document.getElementById("arabic-keyboard");
        if (!keyboard) return;

        if (!this.isVisible) {
            keyboard.classList.add("ak-visible");
            keyboard.setAttribute("aria-hidden", "false");
            document.body.classList.add("ak-active");
            this.isVisible = true;
        }

        // Defer so the layout reflows with the new bottom padding before we
        // scroll, otherwise the input may briefly appear behind the keyboard.
        requestAnimationFrame(() => {
            try {
                input.scrollIntoView({ behavior: "smooth", block: "center" });
            } catch (_) {
                /* older browsers */
            }
            try {
                const length = input.value.length;
                input.setSelectionRange(length, length);
            } catch (_) {
                /* readonly fields, etc. */
            }
        });
    }

    hideKeyboard() {
        const keyboard = document.getElementById("arabic-keyboard");
        if (!keyboard || !this.isVisible) return;

        keyboard.classList.remove("ak-visible");
        keyboard.setAttribute("aria-hidden", "true");
        document.body.classList.remove("ak-active");

        this.isVisible = false;
        this.currentInput = null;
    }

    // ----------------------------------------------------------------- keys

    setupKeyEvents() {
        const handleKey = (event) => {
            const key = event.target.closest?.(".ak-key");
            if (!key) return;
            // Prevent the underlying input from losing focus and stop the
            // default touch behaviour that would re-focus the body.
            event.preventDefault();
            this.handleKeyPress(key);
        };

        document.addEventListener("mousedown", handleKey);
        document.addEventListener("touchstart", handleKey, { passive: false });
    }

    handleKeyPress(keyElement) {
        const action = keyElement.getAttribute("data-action");
        if (action === "close") {
            this.hideKeyboard();
            return;
        }

        if (!this.currentInput) return;

        const char = keyElement.getAttribute("data-char");
        if (char) {
            this.insertText(char);
            return;
        }

        if (action === "backspace") this.handleBackspace();
        if (action === "space") this.insertText(" ");
    }

    insertText(text) {
        if (!this.currentInput) return;

        const input = this.currentInput;
        const start = input.selectionStart ?? input.value.length;
        const end = input.selectionEnd ?? input.value.length;
        const value = input.value;

        input.value = value.substring(0, start) + text + value.substring(end);

        const newPosition = start + text.length;
        try {
            input.setSelectionRange(newPosition, newPosition);
        } catch (_) {
            /* ignore */
        }

        input.dispatchEvent(new Event("input", { bubbles: true }));
    }

    handleBackspace() {
        if (!this.currentInput) return;

        const input = this.currentInput;
        const start = input.selectionStart ?? input.value.length;
        const end = input.selectionEnd ?? input.value.length;
        const value = input.value;

        if (start !== end) {
            input.value = value.substring(0, start) + value.substring(end);
            try {
                input.setSelectionRange(start, start);
            } catch (_) {
                /* ignore */
            }
        } else if (start > 0) {
            input.value =
                value.substring(0, start - 1) + value.substring(start);
            try {
                input.setSelectionRange(start - 1, start - 1);
            } catch (_) {
                /* ignore */
            }
        }

        input.dispatchEvent(new Event("input", { bubbles: true }));
    }

    // -------------------------------------------------------------- shortcuts

    setupGlobalShortcuts() {
        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && this.isVisible) {
                this.hideKeyboard();
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    window.arabicKeyboard = new ArabicKeyboard();
});

export default ArabicKeyboard;
