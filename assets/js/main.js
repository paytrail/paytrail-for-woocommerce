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

// Function to handle both click and keyboard events
const handleSelection = function(e) {
    // Only handle Enter or Space for keyboard events
    if (e.type === 'keydown') {
        if (e.key !== 'Enter' && e.key !== ' ') {
            return;
        }
        e.preventDefault();
    }
    
    if (this.classList.contains('selected')) {
        this.setAttribute('aria-expanded', 'false');
        this.classList.remove('selected');
        this.nextSibling.classList.add('hidden');
        return;
    }

    // Clear active state
    const active = document.getElementsByClassName('paytrail-provider-group selected');
    if (active.length !== 0) {
        active[0].setAttribute('aria-expanded', 'false');
        active[0].classList.remove('selected');
    }

    // Hide payment fields
    const fields = document.getElementsByClassName('paytrail-woocommerce-payment-fields');
    Array.from(fields).map(field => field.classList.add('hidden'))

    // Show current group
    this.classList.add('selected');
    this.nextSibling.classList.remove('hidden');
    this.setAttribute('aria-expanded', 'true');

    //For keyboard events, focus the first radio button in the group
    const firstRadio = this.nextSibling.querySelector('input[type="radio"]');
    if (e.type === 'keydown' && firstRadio) {
        firstRadio.focus();
        firstRadio.checked = true;
    }
    // Use scrolIntoView(alignTo) method
    const closestUl = this.nextSibling.closest('ul');
    closestUl.scrollIntoView(false); // align to the bottom of the scrollable element
};

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
                // Add both click and keyboard event listeners
                providerGroup.addEventListener('click', handleSelection);
                providerGroup.addEventListener('keydown', handleSelection);
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
