// Extend search.js with translation functionality

document.addEventListener("DOMContentLoaded", function () {
    console.log("Translation extensions loaded");

    // Hook into the search form submission
    const searchForm = document.getElementById("searchForm");
    if (searchForm) {
        searchForm.addEventListener("submit", function (event) {
            // Wait for results to be displayed before translating
            setTimeout(function () {
                // Update data-translate-arabic attributes for summary cells
                const madhiCell = document.getElementById("summary-madhi");
                const mudhoriCell = document.getElementById("summary-mudhori");
                const amarCell = document.getElementById("summary-amar");

                if (madhiCell && madhiCell.textContent) {
                    madhiCell.setAttribute(
                        "data-translate-arabic",
                        madhiCell.textContent
                    );
                }

                if (mudhoriCell && mudhoriCell.textContent) {
                    mudhoriCell.setAttribute(
                        "data-translate-arabic",
                        mudhoriCell.textContent
                    );
                }

                if (amarCell && amarCell.textContent) {
                    amarCell.setAttribute(
                        "data-translate-arabic",
                        amarCell.textContent
                    );
                }

                // Update verb info content
                const verbInfoContent =
                    document.getElementById("verbInfoContent");
                if (verbInfoContent && verbInfoContent.textContent) {
                    verbInfoContent.setAttribute(
                        "data-translate-arabic",
                        verbInfoContent.textContent
                    );
                }

                // Trigger translation
                if (window.translateAllMarkedElements) {
                    console.log(
                        "Translating all marked elements after search..."
                    );
                    window.translateAllMarkedElements();
                }
            }, 1500); // Delay to ensure results are loaded
        });
    }

    // Initial translation of any existing elements
    setTimeout(function () {
        if (window.translateAllMarkedElements) {
            window.translateAllMarkedElements();
        }
    }, 500);
});
