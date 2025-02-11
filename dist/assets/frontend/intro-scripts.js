/******/ (() => { // webpackBootstrap
/*!*********************************************!*\
  !*** ./assets/js/paytrail-intro-scripts.js ***!
  \*********************************************/
jQuery(document).ready(function ($) {
  let isPaytrailOverlayOpen = $('#paytrail-overlay-container').length > 0;

  // Function to initialize the dialog
  const initializeDialog = () => {
    const dialogOptions = {
      autoOpen: false,
      modal: true,
      width: 500,
      open: function (event, ui) {
        $(this).parent().css('z-index', 9998);
        isPaytrailOverlayOpen = true; // Set to true when the overlay is opened
      },
      close: function (event, ui) {
        $(this).parent().css('z-index', '');
        isPaytrailOverlayOpen = false; // Set to false when the overlay is closed
      }
    };

    // Open the dialog when the button is clicked
    $(document).on('click', '#open-lightbox', function () {
      $('#user-data-form').dialog(dialogOptions).dialog('open');
    });

    // Event handling for credentials button
    $(document).on('click', '#credentials', function () {
      closePaytrailOverlayContainer();
    });

    // Event handling for test mode button
    $(document).on('click', '#test-mode-button', function () {
      // Toggle the "test mode" checkbox
      $('#woocommerce_paytrail_enable_test_mode').prop('checked', function () {
        return !$(this).prop('checked');
      });

      // Trigger a change event on the checkbox
      $('#woocommerce_paytrail_enable_test_mode').trigger('change');

      // Simulate a click on the "Save changes" button
      $('#mainform [name="save"]').click();
    });
  };

  // Function to handle form submission
  const handleFormSubmission = () => {
    $(document).on('submit', '#user-data-form', function (event) {
      event.preventDefault();
      const formData = $(this).serialize();
      const customUrl = 'https://www.paytrail.com/tilaa-palvelu/lisatiedot';
      const popup = window.open(customUrl + '?' + formData + '&tekninen_alusta=WooCommerce', '_blank');
      popup.focus();
    });
  };

  // Function to close the paytrail overlay container
  const closePaytrailOverlayContainer = () => {
    $('#paytrail-overlay-container').remove(); // Remove the overlay container from the DOM
    isPaytrailOverlayOpen = false; // Set to false when the overlay is closed
  };

  // Call the functions when the DOM is ready
  initializeDialog();
  handleFormSubmission();
});
/******/ })()
;
//# sourceMappingURL=intro-scripts.js.map