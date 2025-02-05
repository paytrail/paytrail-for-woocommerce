if (!window.__opLaskuOpts) {
  window.__opLaskuOpts = [];
}

if (typeof window.op_lasku_data !== 'undefined') {
  //Get the language
  const lang = op_lasku_data.language || "fi";

  // Initialize options based on page type
  if (op_lasku_data?.product_price) {
    // Product page
    window.__opLaskuOpts = {
      amount: Math.round(op_lasku_data.product_price * 100),
      lang: lang,
      type: "lasku",
    };
  } else if (op_lasku_data?.cart_total) {
    // Cart page
    window.__opLaskuOpts = {
      amount: Math.round(op_lasku_data.cart_total * 100),
      lang: lang,
      type: "lasku",
    };

    // Attach cart update listener on shortcode cart page
    jQuery(document.body).on("updated_cart_totals", function () {
      const totalElement = document.querySelector(".order-total .amount");
      if (!totalElement) return;

      const cartTotal = totalElement.textContent
        .replace(/[^0-9.,]/g, "")
        .replace(",", ".");

      window.__opLaskuOpts = {
        amount: Math.round(parseFloat(cartTotal) * 100),
        lang: lang,
        type: "lasku",
      };

      // Call the OP Lasku initialization function
      if (typeof window.opLaskuInit === "function") {
        window.opLaskuInit();
      }
    });
  }
}
