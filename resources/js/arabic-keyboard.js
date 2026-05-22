/**
 * Arabic on-screen keyboard.
 *
 * Watches inputs marked with class `arabic-input` (or `data-arabic-keyboard`)
 * and pops up the keyboard rendered at #arabic-keyboard. On desktop the
 * keyboard appears on focus; on mobile it appears when the user taps a
 * trigger button marked with `data-mobile-arabic`.
 *
 * Bundled via Vite from `resources/js/app.js`.
 */
class ArabicKeyboard {
    constructor() {
        this.currentInput = null;
        this.isVisible = false;
        this.init();
    }

    init() {
        this.attachToInputs();
        this.setupKeyboardEvents();
        this.setupOutsideClick();
    }

    attachToInputs() {
        document
            .querySelectorAll(".arabic-input, [data-arabic-keyboard]")
            .forEach((input) => this.setupInputEvents(input));

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType !== 1) return;
                    const newInputs =
                        node.querySelectorAll?.(
                            ".arabic-input, [data-arabic-keyboard]"
                        ) || [];
                    newInputs.forEach((input) => this.setupInputEvents(input));
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    setupInputEvents(input) {
        if (input.dataset.arabicKeyboardBound === "1") return;
        input.dataset.arabicKeyboardBound = "1";

        input.addEventListener("focus", (e) => {
            if (window.innerWidth > 768) {
                this.showKeyboard(e.target);
            }
        });

        input.addEventListener("click", (e) => {
            if (e.target.hasAttribute("data-mobile-arabic")) {
                this.showKeyboard(e.target);
            }
        });

        input.style.direction = "rtl";
        input.style.textAlign = "right";
        input.style.fontFamily = "'Amiri', 'Times New Roman', serif";
    }

    showKeyboard(input) {
        this.currentInput = input;
        const keyboard = document.getElementById("arabic-keyboard");
        if (!keyboard) return;

        keyboard.classList.remove("ak-hidden");
        keyboard.classList.add("ak-visible");
        this.isVisible = true;

        input.focus();
        input.setSelectionRange(input.value.length, input.value.length);
    }

    hideKeyboard() {
        const keyboard = document.getElementById("arabic-keyboard");
        if (!keyboard || !this.isVisible) return;

        keyboard.classList.add("ak-hide");
        setTimeout(() => {
            keyboard.classList.remove("ak-visible", "ak-hide");
            keyboard.classList.add("ak-hidden");
        }, 200);

        this.isVisible = false;
        this.currentInput = null;
    }

    setupKeyboardEvents() {
        document.addEventListener("click", (e) => {
            const key = e.target.closest(".ak-key");
            if (key) {
                e.preventDefault();
                this.handleKeyPress(key);
            }
        });
    }

    handleKeyPress(keyElement) {
        if (!this.currentInput) return;

        const char = keyElement.getAttribute("data-char");
        const action = keyElement.getAttribute("data-action");

        if (char) {
            this.insertText(char);
        } else if (action) {
            this.handleAction(action);
        }

        this.currentInput.focus();
    }

    insertText(text) {
        if (!this.currentInput) return;

        const start = this.currentInput.selectionStart;
        const end = this.currentInput.selectionEnd;
        const currentValue = this.currentInput.value;

        const newValue =
            currentValue.substring(0, start) +
            text +
            currentValue.substring(end);
        this.currentInput.value = newValue;

        const newPosition = start + text.length;
        this.currentInput.setSelectionRange(newPosition, newPosition);

        this.currentInput.dispatchEvent(new Event("input", { bubbles: true }));
    }

    handleAction(action) {
        if (!this.currentInput) return;

        switch (action) {
            case "backspace":
                this.handleBackspace();
                break;
            case "space":
                this.insertText(" ");
                break;
            case "close":
                this.hideKeyboard();
                break;
        }
    }

    handleBackspace() {
        const start = this.currentInput.selectionStart;
        const end = this.currentInput.selectionEnd;
        const currentValue = this.currentInput.value;

        if (start !== end) {
            this.currentInput.value =
                currentValue.substring(0, start) + currentValue.substring(end);
            this.currentInput.setSelectionRange(start, start);
        } else if (start > 0) {
            this.currentInput.value =
                currentValue.substring(0, start - 1) +
                currentValue.substring(start);
            this.currentInput.setSelectionRange(start - 1, start - 1);
        }

        this.currentInput.dispatchEvent(new Event("input", { bubbles: true }));
    }

    setupOutsideClick() {
        document.addEventListener("click", (e) => {
            const keyboard = document.getElementById("arabic-keyboard");
            if (!keyboard) return;

            const isClickInside = keyboard.contains(e.target);
            const isClickOnArabicInput =
                e.target.classList?.contains("arabic-input") ||
                e.target.hasAttribute?.("data-arabic-keyboard");

            if (!isClickInside && !isClickOnArabicInput && this.isVisible) {
                this.hideKeyboard();
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && this.isVisible) {
                this.hideKeyboard();
            }
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
    window.arabicKeyboard = new ArabicKeyboard();
});

export default ArabicKeyboard;
