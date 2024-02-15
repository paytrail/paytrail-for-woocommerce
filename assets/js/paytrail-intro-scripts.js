// Function to initialize the dialog
jQuery(document).ready(function ($) {
    const initializeDialog = () => {
        const dialogOptions = {
            autoOpen: false,
            modal: true,
            width: 500,
            open: function (event, ui) {
                $(this).parent().css('z-index', 9998);
            },
            close: function (event, ui) {
                $(this).parent().css('z-index', '');
            },
        };

        // Initialize the dialog when the DOM is ready
        const userDataForm = document.getElementById('user-data-form');
        const openLightboxButton = document.getElementById('open-lightbox');

        if (userDataForm && openLightboxButton) {
            openLightboxButton.addEventListener('click', function () {
                // Open the dialog
                $(userDataForm).dialog(dialogOptions).dialog('open');
            });
        }
    };

    const closeOverlay = () => {
        console.log('Closing overlay');
        const overlayContainer = document.getElementById('paytrail-overlay-container');
        if (overlayContainer) {
            const currentDisplay = window.getComputedStyle(overlayContainer).getPropertyValue('display');
            overlayContainer.style.display = currentDisplay === 'none' ? 'block' : 'none';
        }
    };

    const handleFormSubmission = () => {
        const userDataForm = document.getElementById('user-data-form');
        if (userDataForm) {
            userDataForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const formData = new FormData(this);
                const customUrl = 'https://www.paytrail.com/tilaa-palvelu/lisatiedot';
                const queryString = new URLSearchParams(formData).toString();
                const popup = window.open(customUrl + '?' + queryString + '&tekninen_alusta=WooCommerce', '_blank');
                popup.focus();
            });
        }
    };

    // Call the functions when the DOM is ready
    initializeDialog();
    handleFormSubmission();

    const credentialButton = document.getElementById('credentials');
    const testModeButton = document.getElementById('test-mode-button');
    const testModeCheckbox = document.getElementById('woocommerce_paytrail_enable_test_mode');
    const mainForm = document.getElementById('mainform');

    if (credentialButton || testModeButton) {
        credentialButton.addEventListener('click', function () {
            closeOverlay();
        });

        testModeButton.addEventListener('click', function (event) {
            // Toggle the "test mode" checkbox
            testModeCheckbox.checked = !testModeCheckbox.checked;

            // Trigger a change event on the checkbox
            const changeEvent = new Event('change', { bubbles: true });
            testModeCheckbox.dispatchEvent(changeEvent);

            // Simulate a click on the "Save changes" button
            const saveChangesButton = document.getElementById('mainform').querySelector('[name="save"]');
            if (saveChangesButton) {
                // Submit the form
                saveChangesButton.click();

                // Close the overlay after submitting the form
                closeOverlay();
            }

            // Prevent the default button click behavior
            event.preventDefault();
        });
    }
});