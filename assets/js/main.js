// Require main style file here for concatenation.
require(__dirname + '/../scss/payment_fields.scss');
require(__dirname + '/../scss/introStyles.scss');
const handleSize = function (elem, size) {
    if (size < 600) {
        elem.classList.remove('col-wide');
        elem.classList.add('col-narrow');
    } else {
        elem.classList.remove('col-narrow');
        elem.classList.add('col-wide');
    }
};
// handleSize for resize event
const delta = 300;
let startTime;
let timeout = false;

const handleResize = function () {
    const container = document.getElementById('payment');
    const paytrailContainer = document.getElementsByClassName('payment_method_paytrail');
    if (new Date() - startTime < delta) {
        setTimeout(handleResize, delta)
    } else {
        timeout = false;
        handleSize(paytrailContainer[0], Math.round(container.offsetWidth));
    }
};
//Handle Apple Pay button and mobile group visibility
const handleApplePay = function () {
    if (typeof paytrail !== 'undefined') {
        const applePay = paytrail.applePayButton;
        const button = document.getElementsByClassName('paytrail-woocommerce-payment-fields--list-item apple-pay');
        // Return if there is no Apple Pay button (for blocks order-pay page)
        if (button.length === 0) {
            return;
        }
        const groupDiv = button[0].parentNode.previousElementSibling;
        // Select the provider list within the mobile group
        const providerList = groupDiv.getElementsByClassName('provider-list')[0];
        const providerText = providerList.textContent.trim();
        // Check if there are other providers besides Apple Pay
        const providers = providerList.textContent.split(',').map(provider => provider.trim());
        const hasOtherProviders = providers.some(provider => provider !== 'Apple Pay');

        // Hide manually created mobile group if there are no other mobile providers and apple pay is not available
        if (!hasOtherProviders && !applePay?.canMakePayment()) {
            groupDiv.style.display = 'none';
        } else if (hasOtherProviders && !applePay?.canMakePayment() && providerText.includes('Apple Pay')) {
            // If there are other mobile providers and Apple Pay is not available, remove Apple Pay title from the list
            providerList.textContent = providerText.replace(/,\s*Apple Pay|Apple Pay,\s*|Apple Pay/g, '').trim();
        }
        //Display Apple Pay button if it is available
        if (button.length > 0 && applePay?.canMakePayment()) {
            button[0].classList.remove('apple-pay');
        }
    }
}
window.initPaytrail = () => {
    const paytrailProviderGroups = document.getElementsByClassName('paytrail-provider-group');
    if (paytrailProviderGroups.length === 1) {
        Array.from(paytrailProviderGroups).map(providerGroup => {
            providerGroup.style = 'display: none;'
            const fields = document.getElementsByClassName('paytrail-woocommerce-payment-fields');
            Array.from(fields).map(field => field.classList.remove('hidden'))
        })
    } else if (paytrailProviderGroups.length > 1) {
        Array.from(paytrailProviderGroups).map(providerGroup => {
            if (providerGroup && !providerGroup.hasAttribute('listenerOnClick')) {
                providerGroup.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (this.classList.contains('selected')) {
                        this.classList.remove('selected');
                        this.nextSibling.classList.add('hidden');
                        return;
                    }
                    // Clear active state
                    const active = document.getElementsByClassName('paytrail-provider-group selected');
                    if (active.length !== 0) {
                        active[0].classList.remove('selected');
                    }
                    // Hide payment fields
                    const fields = document.getElementsByClassName('paytrail-woocommerce-payment-fields');
                    Array.from(fields).map(field => field.classList.add('hidden'))
                    // Show current group
                    this.classList.add('selected');
                    this.nextSibling.classList.remove('hidden');
                    // Use scrolIntoView(alignTo) method
                    const closestUl = this.nextSibling.closest('ul');
                    closestUl.scrollIntoView(false); // align to the bottom of the scrollable element
                });
                providerGroup.setAttribute('listenerOnClick', 'true');
            }
        })
    }
    const methods = document.getElementsByClassName('paytrail-woocommerce-payment-fields--list-item paytrail-for-woocommerce-tokenized-payment-method');
    if (methods.length > 0) {
        Array.from(methods).map(method => {
            method.addEventListener('click', function (e) {
                e.preventDefault();
                Array.from(methods).map(method => method.classList.remove('selected'))
                let radio = this.childNodes[0].childNodes[0];
                if (typeof radio !== 'undefined') {
                    radio.checked = true;
                    this.classList.add('selected');
                }
            });
        })
    }
    handleResize()
    handleApplePay()
}
document.addEventListener('DOMContentLoaded', function (event) {
    initPaytrail();
});
window.addEventListener('resize', function () {
    startTime = new Date();
    if (timeout === false) {
        timeout = true;
        setTimeout(handleResize, delta);
    }
});
