if (!window.__opLaskuOpts) {
  window.__opLaskuOpts = [];
}

// Calculate the amount based on the product price or cart total
const amount = op_lasku_data.product_price
  ? op_lasku_data.product_price * 100
  : op_lasku_data.cart_total
  ? op_lasku_data.cart_total * 100
  : 0;

window.__opLaskuOpts = {
  amount: amount,
  lang: "fi",
  type: "lasku",
};
