class ArabicKeyboard {
    constructor() {
        this.currentInput = null;
        this.isVisible = false;
        this.init();
    }

    init() {
        // Add event listeners to all Arabic input fields
        this.attachToInputs();

        // Add keyboard event listeners
        this.setupKeyboardEvents();

        // Add click outside to close
        this.setupOutsideClick();
    }

    attachToInputs() {
        // Attach to existing inputs
        const arabicInputs = document.querySelectorAll(
            ".arabic-input, [data-arabic-keyboard]"
        );
        arabicInputs.forEach((input) => {
            this.setupInputEvents(input);
        });

        // Observer for dynamically added inputs
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        const newInputs =
                            node.querySelectorAll?.(
                                ".arabic-input, [data-arabic-keyboard]"
                            ) || [];
                        newInputs.forEach((input) => {
                            this.setupInputEvents(input);
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    setupInputEvents(input) {
        // Desktop: Show keyboard on focus
        input.addEventListener("focus", (e) => {
            if (window.innerWidth > 768) {
                // Only on desktop
                this.showKeyboard(e.target);
            }
        });

        // Mobile: Show on special button click
        input.addEventListener("click", (e) => {
            if (e.target.hasAttribute("data-mobile-arabic")) {
                this.showKeyboard(e.target);
            }
        });

        // Set RTL direction and Arabic font
        input.style.direction = "rtl";
        input.style.textAlign = "right";
        input.style.fontFamily = "'Amiri', 'Times New Roman', serif";
    }

    showKeyboard(input) {
        this.currentInput = input;
        const keyboard = document.getElementById("arabic-keyboard");

        if (keyboard) {
            keyboard.classList.remove("d-none");
            keyboard.classList.add("show");
            this.isVisible = true;

            // Position cursor at end of input
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }
    }

    hideKeyboard() {
        const keyboard = document.getElementById("arabic-keyboard");
        if (keyboard && this.isVisible) {
            keyboard.classList.add("hide");
            setTimeout(() => {
                keyboard.classList.add("d-none");
                keyboard.classList.remove("show", "hide");
            }, 300);
            this.isVisible = false;
            this.currentInput = null;
        }
    }

    setupKeyboardEvents() {
        document.addEventListener("click", (e) => {
            if (e.target.classList.contains("key")) {
                e.preventDefault();
                this.handleKeyPress(e.target);
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

        // Keep focus on input
        this.currentInput.focus();
    }

    insertText(text) {
        if (!this.currentInput) return;

        const start = this.currentInput.selectionStart;
        const end = this.currentInput.selectionEnd;
        const currentValue = this.currentInput.value;

        // Insert text at cursor position
        const newValue =
            currentValue.substring(0, start) +
            text +
            currentValue.substring(end);
        this.currentInput.value = newValue;

        // Move cursor after inserted text
        const newPosition = start + text.length;
        this.currentInput.setSelectionRange(newPosition, newPosition);

        // Trigger input event for frameworks
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
        }
    }

    handleBackspace() {
        const start = this.currentInput.selectionStart;
        const end = this.currentInput.selectionEnd;

        if (start !== end) {
            // Delete selected text
            const currentValue = this.currentInput.value;
            this.currentInput.value =
                currentValue.substring(0, start) + currentValue.substring(end);
            this.currentInput.setSelectionRange(start, start);
        } else if (start > 0) {
            // Delete character before cursor
            const currentValue = this.currentInput.value;
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
            const isClickInsideKeyboard = keyboard?.contains(e.target);
            const isClickOnArabicInput =
                e.target.classList.contains("arabic-input") ||
                e.target.hasAttribute("data-arabic-keyboard");

            if (
                !isClickInsideKeyboard &&
                !isClickOnArabicInput &&
                this.isVisible
            ) {
                this.hideKeyboard();
            }
        });

        // Hide on escape key
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && this.isVisible) {
                this.hideKeyboard();
            }
        });
    }
}

// Global functions for template usage
window.hideArabicKeyboard = function () {
    if (window.arabicKeyboard) {
        window.arabicKeyboard.hideKeyboard();
    }
};

window.showArabicKeyboard = function (input) {
    if (window.arabicKeyboard) {
        window.arabicKeyboard.showKeyboard(input);
    }
};

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    window.arabicKeyboard = new ArabicKeyboard();
});
