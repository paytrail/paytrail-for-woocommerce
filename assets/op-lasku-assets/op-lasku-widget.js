(function () {
  // Version: 1.0.0
  const O = "https://eficode.pohjola-finance.fi/tililuotto-uusi/",
    j = "d584a131-5594-4c2a-8e1a-a12424b982f2";

  function P(t) {
    return fetch(`${O}api/bills/credit-costs?uuid=${j}&amount=${t}`);
  }

  const _ = {
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

  const l = (t, e, a = {}) => {
    Object.keys(_).includes(t) || (t = "fi");
    let n = e.split(".").reduce((s, o) => s[o], _[t]);
    Object.keys(a).forEach((s) => {
      n = n.replace(`{${s}}`, a[s]);
    });
    return n;
  };

  const A = (t) => t.replace(".", ",");

  const u = (t, e) =>
    e === "en"
      ? new Intl.NumberFormat("en-EN", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        }).format(t)
      : new Intl.NumberFormat("fi-FI", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        }).format(t);

  const m = (t, e) => (e === "en" ? t.toFixed(2) : A(t.toFixed(2)));

  const E = (t) => `
    <dl>
      <dt>${l(t.lang, "calc.fields.amountLabel")}</dt>
      <dd class="op-lasku__result-amount"><span>${u(
        t.details.amount / 100,
        t.lang
      )}</span> €</dd>

      <dt>${l(t.lang, "calc.fields.installmentLabel")}</dt>
      <dd class="op-lasku__result-monthlypayment"><span>${u(
        t.details.installment / 100,
        t.lang
      )}</span> €</dd>
      <p class="op-lasku__result-monthlypayment-info"><span>${l(
        t.lang,
        "calc.fields.installmentInfo"
      )}</span></p>

      <dt>${l(t.lang, "calc.fields.annualPercentageRateLabel")}</dt>
      <dd class="op-lasku__result-realinterest"><span>${m(
        t.details.interest.annualPercentageRate * 100,
        t.lang
      )}</span> %</dd>

      <dt>${l(t.lang, "calc.fields.creditPeriodLengthLabel")}</dt>
      <dd class="op-lasku__result-time"><span>${
        t.details.creditPeriodLengthInMonths
      }</span> ${l(t.lang, "calc.abbr.months")}</dd>

      <dt>${l(t.lang, "calc.fields.totalCostLabel")}</dt>
      <dd class="op-lasku__result-overall"><span>${u(
        t.details.costs.total / 100,
        t.lang
      )}</span> €</dd>
    </dl>
  `;

  const M = (t) => `
    <p>
      ${l(t.lang, "calc.exampleCalculationFirst", {
        totalInterest: m(
          t.details.exampleCalculationCosts.interest.totalInterest * 100,
          t.lang
        ),
        date: f(),
      })}
    </p>
    <p>
      ${l(t.lang, "calc.exampleCalculationSecond", {
        annualPercentageRate: m(
          t.details.exampleCalculationCosts.interest.annualPercentageRate * 100,
          t.lang
        ),
        margin: m(
          t.details.exampleCalculationCosts.interest.margin * 100,
          t.lang
        ),
        totalInterest: m(
          t.details.exampleCalculationCosts.interest.totalInterest * 100,
          t.lang
        ),
        date: t.lang === "en" ? f(true) : f(),
        installmentFee: u(
          t.details.exampleCalculationCosts.fees.installmentFee / 100,
          t.lang
        ),
        total: u(t.details.exampleCalculationCosts.costs.total / 100, t.lang),
        installment: u(
          t.details.exampleCalculationCosts.installment / 100,
          t.lang
        ),
        creditPeriodLengthInMonths:
          t.details.exampleCalculationCosts.creditPeriodLengthInMonths,
      })}
    </p>
  `;

  const f = (t = false) => {
    const e = new Date();
    if (t) {
      const s = new Intl.DateTimeFormat("en", { month: "long" }),
        o = e.getFullYear().toString();
      return s.format(e) + " " + o;
    }
    const a = e.getFullYear().toString().slice(-2);
    return (e.getMonth() + 1).toString().padStart(2, "0") + "/" + a;
  };

  const B = (t) => `
  <h2 id="op-lasku__heading">${l(t.lang, "calc.modalHeading")}</h2>
  <span class="op-lasku__current_amount" hidden>${t.details.amount}</span>
  <p class="op-lasku__teaser-text">${l(t.lang, "calc.teaserFirst")}</p>
  <p class="op-lasku__teaser-text">${l(t.lang, "calc.teaserSecond")}</p>

  <div class="op-lasku__result-rows">
    ${E(t)}
  </div>

  <footer>
    ${M(t)}
    <div class="op-lasku__footercompany">
      <p>${l(t.lang, "calc.footer.companySignature")}</p>
    </div>
  </footer>
  `;

  const p = (t, e) => {
    if (t) {
      var a = document.createElement(t);
      if (typeof e == "object")
        Object.keys(e).forEach(function (n) {
          if (n.indexOf("data-") > -1) {
            a.setAttribute(n, e[n]);
          } else if (n === "children" && e[n].length) {
            if (typeof e[n] != "object")
              throw new Error(
                "function el: children prop must be defined as array"
              );
            e[n].forEach(function (s) {
              a.appendChild(
                typeof s == "string" ? document.createTextNode(s) : s
              );
            });
          } else a[n] = e[n];
        });
      return a;
    }
    throw new Error("Cant create element without type.");
  };

  const x = (t) => {
    t.classList.add("op-lasku--invalid");
  };

  const R = (t) => {
    t.classList.remove("op-lasku--invalid");
  };

  const D = 5e5;

  function c(t, e, a = false) {
    return a ? t.querySelectorAll(e) : t.querySelector(e);
  }

  const y = (t, e) => {
    if (e > D) {
      console.log(
        "OP Laskun suurin luottoraja on 5000 euroa, laskuria ei näytetä."
      );
      x(t);
      return false;
    } else {
      R(t);
      return true;
    }
  };

  const T = (t, e) => {
    c(t, ".op-lasku__current_amount").textContent = e.details.amount;
    c(t, ".op-lasku__result-amount").firstChild.textContent = u(
      e.details.amount / 100,
      e.lang
    );
    c(t, ".op-lasku__result-monthlypayment").firstChild.textContent = u(
      e.details.installment / 100,
      e.lang
    );
    c(t, ".op-lasku__result-realinterest").firstChild.textContent = m(
      e.details.interest.annualPercentageRate * 100,
      e.lang
    );
    c(t, ".op-lasku__result-time").firstChild.textContent =
      e.details.creditPeriodLengthInMonths;
    c(t, ".op-lasku__result-overall").firstChild.textContent = u(
      e.details.costs.total / 100,
      e.lang
    );
    c(t, ".op-lasku__btn-open").firstChild.textContent = `${l(
      e.lang,
      "calc.openButton"
    )} ${u(e.details.installment / 100, e.lang)} ${l(
      e.lang,
      "calc.abbr.eurosPerMonth"
    )}`;
  };

  function W(t, e) {
    y(t, e.details.amount);
    this.setAmount = (a) => {
      const n = parseInt(a, 10);
      if (
        !y(t, n) ||
        parseInt(c(t, ".op-lasku__current_amount").value, 10) === n
      ) {
        return false;
      } else {
        P(n)
          .then((o) => o.json())
          .then((o) => {
            Object.assign(e.details, o);
            T(t, e);
          });
        return true;
      }
    };
  }

  let g,
    b = 0;

  function N(t) {
    if (
      document.readyState === "interactive" ||
      document.readyState === "complete"
    )
      t("params");
    else document.addEventListener("DOMContentLoaded", t.bind(null, "params"));
  }

  function w() {
    if (!window.fetch) return false;
    const t = Object.assign(window.__opLaskuOpts || {}, {});
    if (b >= 5) {
      clearInterval(g);
      return false;
    } else if (Object.keys(t).length) {
      window.clearInterval(g);
      P(t.amount)
        .then((e) => {
          if (e.status === 200) return e.json();
          throw (
            (console.log("Virhe rajapinnassa, tarkista laskurin alustusarvot."),
            new Error(`Status ${e.status}`))
          );
        })
        .then((e) => {
          let a = t;
          a = Object.assign({ details: e }, a);
          try {
            const s = document,
              o = "op-lasku--opened";
            var n;
            const i = p("div", {
                className:
                  "op-lasku__widget op-lasku__type--lasku op-lasku__type--multi",
              }),
              S = p("div", { className: "op-lasku-backdrop" }),
              d = p("div", { className: "op-lasku-container" });
            d.setAttribute("role", "dialog");
            d.setAttribute("aria-labelledby", "op-lasku__heading");
            const C = p("button", {
              textContent: `${l(a.lang, "calc.openButton")} ${u(
                a.details.installment / 100,
                a.lang
              )} ${l(a.lang, "calc.abbr.eurosPerMonth")}`,
              classList: "op-lasku__btn-open",
            });
            x(i);
            const r = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "svg"
              ),
              k = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "path"
              );
            r.setAttribute("viewbox", "0 0 32 32");
            r.setAttribute("width", "32px");
            r.setAttribute("height", "32px");
            r.setAttribute("aria-label", l(a.lang, "calc.closeButton"));
            r.setAttribute("tabindex", "0");
            r.classList.add("op-lasku__btn-close");
            k.setAttribute(
              "d",
              "M16.0001 14.7268L23.8637 6.86321C24.039 6.68796 24.2685 6.60009 24.4982 6.59961C24.7292 6.59913 24.9603 6.68699 25.1365 6.86321C25.3089 7.03558 25.3967 7.26047 25.4 7.48636C25.4034 7.72109 25.3156 7.9569 25.1365 8.13601L17.2729 15.9996L25.1365 23.8632C25.3219 24.0486 25.4095 24.2948 25.3993 24.5376C25.3902 24.7551 25.3026 24.9699 25.1365 25.136C24.9644 25.3081 24.7401 25.3959 24.5146 25.3995C24.2795 25.4033 24.0431 25.3154 23.8637 25.136L16.0001 17.2724L8.13649 25.136C7.96481 25.3077 7.74102 25.3955 7.51602 25.3995C7.28041 25.4036 7.04349 25.3158 6.8637 25.136C6.68594 24.9582 6.59808 24.7246 6.60013 24.4916C6.60214 24.264 6.69 24.0369 6.8637 23.8632L14.7273 15.9996L6.8637 8.13601C6.68765 7.95995 6.59978 7.7291 6.6001 7.49836C6.60042 7.26844 6.68828 7.03863 6.8637 6.86321C7.03928 6.68763 7.26936 6.59977 7.49949 6.59961C7.73002 6.59945 7.9606 6.68732 8.13649 6.86321L16.0001 14.7268Z"
            );
            k.setAttribute("fill", "#6C6C6C");
            k.setAttribute("fill-rule", "evenodd");
            k.setAttribute("clip-rule", "evenodd");
            r.appendChild(k);
            d.appendChild(r);
            d.insertAdjacentHTML("beforeend", B(a));
            i.appendChild(C);
            i.appendChild(S);
            i.appendChild(d);
            const L = s.querySelector(
              a.selector ? a.selector : "#op-lasku--init"
            );
            if (!L) {
              console.warn(
                `Financing calculator: Selector ${
                  a.selector ? a.selector : "#op-lasku--init"
                } does not exist!`
              );
              return false;
            }
            L.insertAdjacentElement("afterend", i);
            const F = () => {
              document.body.classList.add("op-lasku--prevent-scroll");
            };
            const $ = () => {
              document.body.classList.remove("op-lasku--prevent-scroll");
            };
            const I = () => {
              i.classList.add(o);
              d.classList.add(o);
              i.querySelector("#op-lasku__heading").focus();
              F();
            };
            const v = () => {
              i.classList.remove(o);
              d.classList.remove(o);
              i.querySelector(".op-lasku__btn-open").focus();
              $();
            };
            C.addEventListener("click", () => {
              I();
            });
            r.addEventListener("click", () => {
              v();
            });
            r.addEventListener("keypress", function (h) {
              if (h.code == "Space" || h.code == "Enter") {
                v();
                h.preventDefault();
              }
            });
            window.__opLaskuCalcWidget = window.__opLaskuCalcWidget
              ? window.__opLaskuCalcWidget
              : {};
            window.__opLaskuCalcWidget[a.type] = new W(i, a);
          } catch (s) {
            const o = document.querySelector("#op-lasku--init");
            if (o) o.remove();
            console.error(s);
          }
        })
        .catch((e) => {
          console.error(e);
        });
      return true;
    } else {
      console.log("Financing calculator: Cannot find options!");
      console.log(`Financing calculator: Retrying ${b + 1} / 5 ...`);
      b += 1;
      if (!g) {
        g = setInterval(w, 5e3);
      }
      return false;
    }
  }

  // Expose the w() function as window.opLaskuInit
  window.opLaskuInit = w;

  N((t) => {
    setTimeout(() => {
      w();
    }, 10);
  });
})();
