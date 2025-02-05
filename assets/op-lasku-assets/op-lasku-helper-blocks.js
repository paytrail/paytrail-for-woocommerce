import { useEffect } from "react";
import { select, subscribe } from "@wordpress/data";
import { CART_STORE_KEY } from "@woocommerce/block-data";
import { registerPlugin } from "@wordpress/plugins";
import { ExperimentalOrderMeta } from "@woocommerce/blocks-checkout";

const render = () => {
  if (typeof ExperimentalOrderMeta === "undefined" || typeof window.op_lasku_data === "undefined") {
    return null;
  }

  // Initialize calculator options if not available
  if (!window.__opLaskuOpts) {
    window.__opLaskuOpts = [];
  }

  // Subscribe to cart changes
  const onCartChange = () => {
    const cart = select(CART_STORE_KEY).getCartData();
    if (!cart) {
      return;
    }

    const cartTotal = cart.totals.total_price || 0;

    if (window.__opLaskuCalcWidget?.lasku) {
      window.__opLaskuCalcWidget.lasku.setAmount(cartTotal);
    }

    window.__opLaskuOpts = {
      amount: cartTotal,
      lang: window.op_lasku_data.language || "fi",
      type: "lasku",
    };
  };

  useEffect(() => {
    const unsubscribe = subscribe(onCartChange);

    return () => {
      unsubscribe();
    };
  }, []);

  return (
    <ExperimentalOrderMeta>
      <div className="wc-block-components-totals-wrapper">
        <div id="op-lasku--init"></div>
      </div>
    </ExperimentalOrderMeta>
  );
};

registerPlugin("paytrail-oplasku-cart-blocks", {
  render,
  scope: "woocommerce-checkout",
});
