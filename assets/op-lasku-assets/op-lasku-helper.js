if (!window.__opLaskuOpts) {
  window.__opLaskuOpts = [];
}
//Get the language
const lang = op_lasku_data.language;

// Initialize options based on page type
if (op_lasku_data.product_price) {
    // Product page
    window.__opLaskuOpts = {
        amount: op_lasku_data.product_price * 100,
        lang: lang,
        type: "lasku",
    };
} else if (op_lasku_data.cart_total) {
    // Cart page
    window.__opLaskuOpts = {
        amount: op_lasku_data.cart_total * 100,
        lang: lang,
        type: "lasku",
    };

    // Attach cart update listener on cart page
    jQuery(document.body).on('updated_cart_totals', function() {
        const totalElement = document.querySelector('.order-total .amount');
        if (!totalElement) return;
        
        const cartTotal = totalElement.textContent
            .replace(/[^0-9.,]/g, '')
            .replace(',', '.');

        window.__opLaskuOpts = {
            amount: parseFloat(cartTotal) * 100,
            lang: lang,
            type: 'lasku'
        };
        
        if (typeof window.initOPLaskuWidget === 'function') {
            window.initOPLaskuWidget();
        }
    });
}
