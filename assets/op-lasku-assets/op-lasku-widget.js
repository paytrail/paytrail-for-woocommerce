(function(){
    const O = "https://eficode.pohjola-finance.fi/tililuotto-uusi/";
    const j = "d584a131-5594-4c2a-8e1a-a12424b982f2";
  
    function P(amount) {
      return fetch(`${O}api/bills/credit-costs?uuid=${j}&amount=${amount}`);
    }
  
    // Strings for translations
    const translations = {
      fi: {
        calc: {
          openButton: "OP Laskun erämaksulla",
          closeButton: "Sulje laskuri",
          modalHeading: "Maksa joustavasti OP Laskulla",
          teaserFirst:
            "Valitse OP Lasku maksutavaksi verkkokaupan kassalla - saat luottopäätöksen saman tien. OP Lasku toimii kaikkien suomalaisten pankkien verkkotunnuksilla.",
          teaserSecond:
            "Saat 45 päivää korotonta maksuaikaa. Maksa ensimmäinen lasku kerralla tai erissä omaan tahtiisi:",
          fields: {
            amountLabel: "Rahoituksen määrä",
            installmentLabel: "Ensimmäinen erämaksuerä",
            installmentInfo:
              "Kuukausittainen erämaksuerä sisältää koron ja pienenee kun lyhennät luottoa.",
            annualPercentageRateLabel: "Todellinen vuosikorko",
            creditPeriodLengthLabel: "Takaisinmaksuaika",
            totalCostLabel: "Arvioitu kokonaiskustannus",
          },
          footer: {
            companySignature: "Luoton myöntää OP Yrityspankki Oyj.",
          },
          exampleCalculationFirst:
            "Erissä maksaessasi kokonaiskorko on {totalInterest} % ({date}).",
          exampleCalculationSecond: `
          OP Lasku on jatkuvakäyttöinen luotto, jonka todellinen vuosikorko
          <span>1 000</span>
          euron luotolle on
          <span>{annualPercentageRate} %</span>,
          kun luoton korko on korkolain 12 § mukainen viitekorko +
          <span>{margin} %</span> ({totalInterest} % {date})
          ja tilinhoitomaksu
          <span>{installmentFee} euroa kuukaudessa</span>.
          Arvioitu luoton kokonaiskustannus on
          <span>{total} euroa</span>.
          Laskelma on tehty olettaen, että luotto on nostettu kokonaan, luoton korko sekä maksut ja palkkiot pysyvät samana
          koko luottoajan ja luotto maksetaan
          <span>{installment} euron</span>
          minimilyhennysmaksuerinä kuukauden välein, jolloin luottoaika on
          <span>{creditPeriodLengthInMonths} kk</span>.`,
          abbr: {
            eurosPerMonth: "€ / kk",
            months: "kk",
          },
        },
      },
      en: {
        calc: {
          openButton: "With OP Lasku",
          closeButton: "Close calculator",
          modalHeading: "Pay flexibly with OP Lasku",
          teaserFirst:
            "Select OP Lasku as the payment method at the online store's payment stage – you'll get a credit decision right away. OP Lasku is compatible for log in with any Finnish online banking codes.",
          teaserSecond:
            "You get a 45-day interest-free payment time. Pay the first invoice in one go or in instalments at your own pace:",
          fields: {
            amountLabel: "Financing amount",
            installmentLabel: "The first instalment",
            installmentInfo:
              "The monthly instalment includes interest and it decreases as you repay the credit.",
            annualPercentageRateLabel: "Effective interest rate",
            creditPeriodLengthLabel: "Repayment period",
            totalCostLabel: "Estimated total cost",
          },
          footer: {
            companySignature: "The credit is issued by OP Corporate Bank plc.",
          },
          exampleCalculationFirst:
            "When paying in instalments, the total interest rate is {totalInterest} % ({date}).",
          exampleCalculationSecond: `
          OP Lasku is a revolving credit facility. The effective interest rate for the credit of
          <span>1,000</span>
          euros is
          <span>{annualPercentageRate} %</span>
          when the interest rate, under §12 of the Interest Act, is a interest reference rate +
          <span>{margin} %</span>
          margin
          ({totalInterest} % in {date})
          and the account management fee is
          <span>{installmentFee} euros per month</span>.
          The estimated total credit cost would be
          <span>{total} euros</span>.
          The calculation is made on the assumption that the credit has been drawn down in full, the interest rate, charges and fees remain the same
          throughout the credit period, and the credit is repaid in the minimum monthly instalments of
          <span>{installment} euros</span>,
          resulting in a credit period of
          <span>{creditPeriodLengthInMonths} months</span>.`,
          abbr: {
            eurosPerMonth: "€ / month",
            months: "mo",
          },
        },
      },
      se: {
        calc: {
          openButton: "Med OP Lasku",
          closeButton: "Stäng räknaren",
          modalHeading: "Betala flexibelt med OP Lasku",
          teaserFirst:
            "Välj OP Lasku som betalsätt i nätbutikens kassa – du får kreditbeslutet genast. OP Lasku fungerar med alla finländska bankkoder.",
          teaserSecond:
            "Du får 45 dagar räntefri betalningstid. Betala den första fakturan på en gång eller i delar i egen takt:",
          fields: {
            amountLabel: "Finansieringsbelopp",
            installmentLabel: "Första månadsbetalningen",
            installmentInfo:
              "Den månatliga avbetalningen inkluderar ränta och den minskar när du betalar tillbaka krediten.",
            annualPercentageRateLabel: "Effektiv ränta",
            creditPeriodLengthLabel: "Återbetalningstid",
            totalCostLabel: "Uppskattad totalkostnad",
          },
          footer: {
            companySignature: "Krediten beviljas av OP Företagsbanken Abp.",
          },
          exampleCalculationFirst:
            "Vid delbetalning är den totala räntan {totalInterest} % ({date}).",
          exampleCalculationSecond: `
          OP Lasku är en fortlöpande kredit. Den effektiva räntan på en kredit på
          <span>1 000</span>
          euro är
          <span>{annualPercentageRate} %</span>
          då krediträntan är referensräntan i enlighet med 12 § i räntelagen +
          <span>{margin} %</span>
          ({totalInterest} % {date})
          och kontoavgiften är
          <span>{installmentFee} euro per månad</span>.
          Den uppskattade totalkostnaden för krediten är
          <span>{total} euro</span>.
          Beräkningen görs utifrån antagandet att krediten har utnyttjats i sin helhet, att räntan på krediten samt avgifterna förblir desamma
          under hela kredittiden och att krediten återbetalas i minst
          <span>{installment} €</span>
          varje månad, vilket resulterar i en kredittid på
          <span>{creditPeriodLengthInMonths} månader</span>.`,
          abbr: {
            eurosPerMonth: "€ / mån",
            months: "mån.",
          },
        },
      },
    };
  
    /**
     * A safer name for the translator function instead of `l()`.
     */
    const translateString = (lang, keyPath, replacements = {}) => {
      // Fallback to 'fi' if the given language does not exist
      if (!Object.keys(translations).includes(lang)) {
        lang = "fi";
      }
      let translation = keyPath
        .split(".")
        .reduce((acc, part) => acc[part], translations[lang]);
  
      Object.keys(replacements).forEach((k) => {
        translation = translation.replace(`{${k}}`, replacements[k]);
      });
  
      return translation;
    };
  
    const replaceDotWithComma = (val) => val.replace(".", ",");
  
    const formatNumber = (num, lang) => {
      return lang === "en"
        ? new Intl.NumberFormat("en-EN", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          }).format(num)
        : new Intl.NumberFormat("fi-FI", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          }).format(num);
    };
  
    const formatDisplayNumber = (num, lang) => {
      return lang === "en" ? num.toFixed(2) : replaceDotWithComma(num.toFixed(2));
    };
  
    const resultRows = (obj) => `
      <dl>
        <dt>${translateString(obj.lang, "calc.fields.amountLabel")}</dt>
        <dd class="op-lasku__result-amount"><span>${formatNumber(
          obj.details.amount / 100,
          obj.lang
        )}</span> €</dd>
  
        <dt>${translateString(obj.lang, "calc.fields.installmentLabel")}</dt>
        <dd class="op-lasku__result-monthlypayment"><span>${formatNumber(
          obj.details.installment / 100,
          obj.lang
        )}</span> €</dd>
        <p class="op-lasku__result-monthlypayment-info"><span>${translateString(
          obj.lang,
          "calc.fields.installmentInfo"
        )}</p>
  
        <dt>${translateString(obj.lang, "calc.fields.annualPercentageRateLabel")}</dt>
        <dd class="op-lasku__result-realinterest"><span>${formatDisplayNumber(
          obj.details.interest.annualPercentageRate * 100,
          obj.lang
        )}</span> %</dd>
  
        <dt>${translateString(obj.lang, "calc.fields.creditPeriodLengthLabel")}</dt>
        <dd class="op-lasku__result-time"><span>${
          obj.details.creditPeriodLengthInMonths
        }</span> ${translateString(obj.lang, "calc.abbr.months")}</dd>
  
        <dt>${translateString(obj.lang, "calc.fields.totalCostLabel")}</dt>
        <dd class="op-lasku__result-overall"><span>${formatNumber(
          obj.details.costs.total / 100,
          obj.lang
        )}</span> €</dd>
      </dl>
    `;
  
    const exampleCalculation = (obj) => `
      <p>
        ${translateString(obj.lang, "calc.exampleCalculationFirst", {
          totalInterest: formatDisplayNumber(
            obj.details.exampleCalculationCosts.interest.totalInterest * 100,
            obj.lang
          ),
          date: currentDate(),
        })}
      </p>
      <p>
        ${translateString(obj.lang, "calc.exampleCalculationSecond", {
          annualPercentageRate: formatDisplayNumber(
            obj.details.exampleCalculationCosts.interest.annualPercentageRate * 100,
            obj.lang
          ),
          margin: formatDisplayNumber(
            obj.details.exampleCalculationCosts.interest.margin * 100,
            obj.lang
          ),
          totalInterest: formatDisplayNumber(
            obj.details.exampleCalculationCosts.interest.totalInterest * 100,
            obj.lang
          ),
          date: obj.lang === "en" ? currentDate(true) : currentDate(),
          installmentFee: formatNumber(
            obj.details.exampleCalculationCosts.fees.installmentFee / 100,
            obj.lang
          ),
          total: formatNumber(
            obj.details.exampleCalculationCosts.costs.total / 100,
            obj.lang
          ),
          installment: formatNumber(
            obj.details.exampleCalculationCosts.installment / 100,
            obj.lang
          ),
          creditPeriodLengthInMonths:
            obj.details.exampleCalculationCosts.creditPeriodLengthInMonths,
        })}
      </p>
    `;
  
    const currentDate = (longForm = false) => {
      const date = new Date();
      if (longForm) {
        const monthName = new Intl.DateTimeFormat("en", { month: "long" });
        const yearFull = date.getFullYear().toString();
        return monthName.format(date) + " " + yearFull;
      }
      const shortYear = date.getFullYear().toString().slice(-2);
      return (date.getMonth() + 1).toString().padStart(2, "0") + "/" + shortYear;
    };
  
    const modalContent = (obj) => `
    <h2 id="op-lasku__heading">${translateString(obj.lang, "calc.modalHeading")}</h2>
    <span class="op-lasku__current_amount" hidden>${obj.details.amount}</span>
    <p class="op-lasku__teaser-text">${translateString(obj.lang, "calc.teaserFirst")}</p>
    <p class="op-lasku__teaser-text">${translateString(obj.lang, "calc.teaserSecond")}</p>
  
    <div class="op-lasku__result-rows">
      ${resultRows(obj)}
    </div>
  
    <footer>
      ${exampleCalculation(obj)}
      <div class="op-lasku__footercompany">
        <p>${translateString(obj.lang, "calc.footer.companySignature")}</p>
      </div>
    </footer>
    `;
  
    /**
     * Helper to create DOM elements
     */
    function el(tag, props) {
      if (!tag) {
        throw new Error("Cant create element without type.");
      }
      const element = document.createElement(tag);
      if (typeof props === "object") {
        Object.keys(props).forEach(function (k) {
          if (k.indexOf("data-") > -1) {
            element.setAttribute(k, props[k]);
          } else if (k === "children" && props[k].length) {
            if (typeof props[k] !== "object") {
              throw new Error("function el: children prop must be defined as array");
            }
            props[k].forEach(function (child) {
              element.appendChild(typeof child === "string" ? document.createTextNode(child) : child);
            });
          } else {
            element[k] = props[k];
          }
        });
      }
      return element;
    }
  
    const invalidClass = (target) => {
      target.classList.add("op-lasku--invalid");
    };
    const validClass = (target) => {
      target.classList.remove("op-lasku--invalid");
    };
  
    const MAX_AMOUNT = 500000; // 5000.00 in "cents"
  
    /**
     * Alias for document.querySelector
     */
    function $(ctx, selector, all = false) {
      return all ? ctx.querySelectorAll(selector) : ctx.querySelector(selector);
    }
  
    /**
     * Validate the amount
     */
    const validateAmount = (root, amount) => {
      if (amount > MAX_AMOUNT) {
        console.log("OP Laskun suurin luottoraja on 5000 euroa, laskuria ei näytetä." + amount);
        invalidClass(root);
        return false;
      }
      validClass(root);
      return true;
    };
  
    /**
     * Update the displayed data
     */
    const updateUI = (root, dataObj) => {
      $(".op-lasku__current_amount", root).textContent = dataObj.details.amount;
      $(".op-lasku__result-amount", root).firstChild.textContent = formatNumber(
        dataObj.details.amount / 100,
        dataObj.lang
      );
      $(".op-lasku__result-monthlypayment", root).firstChild.textContent = formatNumber(
        dataObj.details.installment / 100,
        dataObj.lang
      );
      $(".op-lasku__result-realinterest", root).firstChild.textContent = formatDisplayNumber(
        dataObj.details.interest.annualPercentageRate * 100,
        dataObj.lang
      );
      $(".op-lasku__result-time", root).firstChild.textContent =
        dataObj.details.creditPeriodLengthInMonths;
      $(".op-lasku__result-overall", root).firstChild.textContent = formatNumber(
        dataObj.details.costs.total / 100,
        dataObj.lang
      );
  
      $(".op-lasku__btn-open", root).firstChild.textContent = `${
        translateString(dataObj.lang, "calc.openButton")
      } ${formatNumber(dataObj.details.installment / 100, dataObj.lang)} ${translateString(
        dataObj.lang,
        "calc.abbr.eurosPerMonth"
      )}`;
    };
  
    /**
     * Constructor for the OP Lasku widget
     */
    function LaskuWidget(root, dataObj) {
      validateAmount(root, dataObj.details.amount);
  
      this.setAmount = (newAmount) => {
        const next = parseInt(newAmount, 10);
        if (!validateAmount(root, next)) {
          return false;
        }
        const currentVal = parseInt($(".op-lasku__current_amount", root).value, 10);
        if (currentVal === next) {
          return false;
        }
        P(next)
          .then((res) => res.json())
          .then((json) => {
            Object.assign(dataObj.details, json);
            updateUI(root, dataObj);
          });
        return true;
      };
    }
  
    let intervalId,
      attemptCount = 0;
  
    function onReady(fn) {
      if (document.readyState === "interactive" || document.readyState === "complete") {
        fn();
      } else {
        document.addEventListener("DOMContentLoaded", fn);
      }
    }
  
    function initLaskuWidget() {
      if (!window.fetch) {
        return false;
      }
      const opts = Object.assign(window.__opLaskuOpts || {}, {});
      if (attemptCount >= 5) {
        clearInterval(intervalId);
        return false;
      }
      if (!Object.keys(opts).length) {
        console.log("Financing calculator: Cannot find options!");
        console.log(`Financing calculator: Retrying ${attemptCount + 1} / 5 ...`);
        attemptCount += 1;
        if (!intervalId) {
          intervalId = setInterval(initLaskuWidget, 5000);
        }
        return false;
      }
  
      // We have options, proceed
      window.clearInterval(intervalId);
  
      P(opts.amount)
        .then((res) => {
          if (res.status === 200) return res.json();
          console.log("Virhe rajapinnassa, tarkista laskurin alustusarvot.");
          throw new Error(`Status ${res.status}`);
        })
        .then((json) => {
          let dataObj = opts;
          dataObj = Object.assign({ details: json }, dataObj);
  
          try {
            const doc = document;
            const openedClass = "op-lasku--opened";
  
            const widgetWrapper = el("div", {
              className: "op-lasku__widget op-lasku__type--lasku op-lasku__type--multi",
            });
            const backdrop = el("div", { className: "op-lasku-backdrop" });
            const container = el("div", { className: "op-lasku-container" });
  
            container.setAttribute("role", "dialog");
            container.setAttribute("aria-labelledby", "op-lasku__heading");
  
            const openButton = el("button", {
              textContent: `${translateString(dataObj.lang, "calc.openButton")} ${formatNumber(
                dataObj.details.installment / 100,
                dataObj.lang
              )} ${translateString(dataObj.lang, "calc.abbr.eurosPerMonth")}`,
              classList: "op-lasku__btn-open",
            });
  
            // Make the wrapper invalid by default (will be validated below)
            invalidClass(widgetWrapper);
  
            // Close button (SVG)
            const closeBtnSvg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
            closeBtnSvg.setAttribute("aria-hidden", "true");
            closeBtnSvg.setAttribute("viewbox", "0 0 32 32");
            closeBtnSvg.setAttribute("width", "32px");
            closeBtnSvg.setAttribute("height", "32px");
            closeBtnSvg.setAttribute("aria-label", translateString(dataObj.lang, "calc.closeButton"));
            closeBtnSvg.setAttribute("tabindex", "0");
            closeBtnSvg.classList.add("op-lasku__btn-close");
            path.setAttribute(
              "d",
              "M16.0001 14.7268L23.8637 6.86321C24.039 6.68796 24.2685 6.60009 24.4982 6.59961C24.7292 6.59913 24.9603 6.68699 25.1365 6.86321C25.3089 7.03558 25.3967 7.26047 25.4 7.48636C25.4034 7.72109 25.3156 7.9569 25.1365 8.13601L17.2729 15.9996L25.1365 23.8632C25.3219 24.0486 25.4095 24.2948 25.3993 24.5376C25.3902 24.7551 25.3026 24.9699 25.1365 25.136C24.9644 25.3081 24.7401 25.3959 24.5146 25.3995C24.2795 25.4033 24.0431 25.3154 23.8637 25.136L16.0001 17.2724L8.13649 25.136C7.96481 25.3077 7.74102 25.3955 7.51602 25.3995C7.28041 25.4036 7.04349 25.3158 6.8637 25.136C6.68594 24.9582 6.59808 24.7246 6.60013 24.4916C6.60214 24.264 6.69 24.0369 6.8637 23.8632L14.7273 15.9996L6.8637 8.13601C6.68765 7.95995 6.59978 7.7291 6.6001 7.49836C6.60042 7.26844 6.68828 7.03863 6.8637 6.86321C7.03928 6.68763 7.26936 6.59977 7.49949 6.59961C7.73002 6.59945 7.9606 6.68732 8.13649 6.86321L16.0001 14.7268Z"
            );
            path.setAttribute("fill", "#6C6C6C");
            path.setAttribute("fill-rule", "evenodd");
            path.setAttribute("clip-rule", "evenodd");
            closeBtnSvg.appendChild(path);
  
            container.appendChild(closeBtnSvg);
            container.insertAdjacentHTML("beforeend", modalContent(dataObj));
            widgetWrapper.appendChild(openButton);
            widgetWrapper.appendChild(backdrop);
            widgetWrapper.appendChild(container);
  
            const initElem = doc.querySelector(dataObj.selector ? dataObj.selector : "#op-lasku--init");
            if (!initElem) {
              console.warn(
                `Financing calculator: Selector ${
                  dataObj.selector ? dataObj.selector : "#op-lasku--init"
                } does not exist!`
              );
              return false;
            }
            initElem.insertAdjacentElement("afterend", widgetWrapper);
  
            // Lock scroll
            const lockScroll = () => {
              document.body.classList.add("op-lasku--prevent-scroll");
            };
            // Unlock scroll
            const unlockScroll = () => {
              document.body.classList.remove("op-lasku--prevent-scroll");
            };
            const openModal = () => {
              widgetWrapper.classList.add(openedClass);
              container.classList.add(openedClass);
              widgetWrapper.querySelector("#op-lasku__heading").focus();
              lockScroll();
            };
            const closeModal = () => {
              widgetWrapper.classList.remove(openedClass);
              container.classList.remove(openedClass);
              widgetWrapper.querySelector(".op-lasku__btn-open").focus();
              unlockScroll();
            };
  
            // Add event handlers
            openButton.addEventListener("click", () => {
              openModal();
            });
            closeBtnSvg.addEventListener("click", () => {
              closeModal();
            });
            closeBtnSvg.addEventListener("keypress", function (evt) {
              if (evt.code === "Space" || evt.code === "Enter") {
                closeModal();
                evt.preventDefault();
              }
            });
  
            // Expose constructor on window for later usage
            window.__opLaskuCalcWidget = window.__opLaskuCalcWidget
              ? window.__opLaskuCalcWidget
              : {};
            window.__opLaskuCalcWidget[dataObj.type] = new LaskuWidget(widgetWrapper, dataObj);
          } catch (err) {
            const initElem = document.querySelector("#op-lasku--init");
            if (initElem) initElem.remove();
            console.error(err);
          }
        })
        .catch((err) => {
          console.error(err);
        });
  
      return true;
    }
  
    onReady(() => {
      setTimeout(() => {
        initLaskuWidget();
      }, 10);
    });
  })();
  