import SlimSelect from 'slim-select'
import flatpickr from "flatpickr";
import noUiSlider from 'nouislider';
import { Spanish } from "flatpickr/dist/l10n/es.js";
import 'slim-select/styles'
import "flatpickr/dist/flatpickr.min.css";
import 'nouislider/dist/nouislider.css';
import '../css/main.css';

const money = (n) => `$ ${Math.round(n).toLocaleString("es-AR")}`;

const clamp = (v, min, max) => Math.min(Math.max(v, min), max);

const getPrice = (el) => {
  // preferido: data-price-num
  const n = el.dataset.priceNum;
  if (n != null && n !== "") return Number(n);

  // fallback: data-price = "$2.381.850"
  const raw = el.dataset.price || "";
  const digits = raw.replace(/[^\d]/g, "");
  return Number(digits || 0);
};

const getCat = (el) => (el.dataset.cat || "").toLowerCase().trim();

const getSelectedCats = () =>
  Array.from(document.querySelectorAll('input[name="category_filter"]:checked')).map((i) =>
    (i.value || "").toLowerCase().trim()
  );

function computeMinMaxByCats(cards, selectedCats) {
  const pool = selectedCats.length
    ? cards.filter((c) => selectedCats.includes(getCat(c)))
    : cards;

  if (!pool.length) return null;

  let min = Infinity;
  let max = -Infinity;

  for (const c of pool) {
    const p = getPrice(c);
    if (p < min) min = p;
    if (p > max) max = p;
  }

  return { min, max };
}

function ymdToDmy(dateStr) {
  if (!dateStr) return "";
  const [y, m, d] = dateStr.split("-");
  if (!y || !m || !d) return dateStr; // fallback
  return `${d}/${m}/${y}`;
}

function setupMobileAccordion({ toggleEl, contentEls, defaultOpenMobile, mq }) {
  if (!toggleEl || !contentEls.length || !mq) return null;

  let isOpen = true;

  const setOpen = (open) => {
    isOpen = open;
    toggleEl.setAttribute("aria-expanded", String(open));
    contentEls.forEach((el) => el.classList.toggle("hidden", !open));
  };

  const applyMediaState = (isMobile) => {
    if (isMobile) {
      setOpen(defaultOpenMobile);
      return;
    }
    setOpen(true);
  };

  const handleToggle = () => {
    if (!mq.matches) return;
    setOpen(!isOpen);
  };

  if (toggleEl.tagName !== "BUTTON") {
    toggleEl.setAttribute("role", "button");
    toggleEl.setAttribute("tabindex", "0");
    toggleEl.addEventListener("keydown", (event) => {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        handleToggle();
      }
    });
  }

  toggleEl.addEventListener("click", handleToggle);

  return { applyMediaState };
}

function applyFilters({ cards, sliderEl, emptyEl }) {
  const selectedCats = getSelectedCats();
  const [minSel, maxSel] = sliderEl.noUiSlider.get().map(Number);

  let visible = 0;

  for (const card of cards) {
    const cat = getCat(card);
    const price = getPrice(card);

    const okCat = !selectedCats.length || selectedCats.includes(cat);
    const okPrice = price >= minSel && price <= maxSel;

    const ok = okCat && okPrice;

    card.classList.toggle("hidden", !ok);
    if (ok) visible++;
  }

  if (emptyEl) emptyEl.classList.toggle("hidden", visible !== 0);

  const countEl = document.getElementById("aba-count");
  if (countEl) countEl.textContent = String(visible);
}

function syncSliderRangeToCategories({ cards, sliderEl }) {
  const selectedCats = getSelectedCats();
  const range = computeMinMaxByCats(cards, selectedCats);
  if (!range) return;

  const { min, max } = range;

  // 1) actualizamos el rango del slider
  sliderEl.noUiSlider.updateOptions(
    {
      range: { min, max },
    },
    false // noUiSlider: no reset automático
  );

  // 2) SIEMPRE reseteamos los handles al nuevo min/max
  // (esto es lo que vos querés)
  sliderEl.noUiSlider.set([min, max]);
}

const WA_NUMBER = "5492944604766"; // <-- poné acá tu número (sin +, con código país)

function getVal(id) {
  return document.getElementById(id)?.value?.trim() || "";
}

function openModalFromCard(card) {
  const modal = document.getElementById("aba-modal");
  if (!modal) return;

  const model = card.dataset.model || "";
  const cat = card.dataset.cat || "";
  const priceLabel = card.dataset.priceLabel || "";
  const cashLabel = card.dataset.cashLabel || "";
  const img = card.dataset.image || "";
  const details = card.dataset.details || "";
  const passengers = card.dataset.passengers ?? "";
  const bags = card.dataset.bags ?? "";
  const transmission = card.dataset.transmission ?? "";
  const idAutos = card.dataset.idautos || "";

  // Rellenar UI
  document.getElementById("aba-modal-subtitle").textContent = `Segmento ${cat}`;
  document.getElementById("aba-modal-cat").textContent = model;
  document.getElementById("aba-modal-price").textContent = priceLabel;
  document.getElementById("aba-modal-cash").textContent = `${cashLabel}`;
  document.getElementById("aba-modal-details").innerHTML = details;

  const imgEl = document.getElementById("aba-modal-img");
  imgEl.src = img;
  imgEl.alt = model;

  const badgesEl = document.getElementById("aba-modal-badges");
  if (badgesEl) {
    const badgeStyle = "display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#596780;background:#F6F7F9;padding:3px 8px;border-radius:20px;";
    const badge = (icon, val) => {
      const label = val !== "" ? val : "—";
      return `<span style="${badgeStyle}"><i class="fa ${icon}" style="font-size:10px;"></i>${label}</span>`;
    };
    const txLabel = transmission !== "" ? (transmission === "automatica" ? "Auto" : "Manual") : "";
    badgesEl.innerHTML =
      badge("fa-user",     passengers) +
      badge("fa-suitcase", bags) +
      badge("fa-cog",      txLabel);
  }

  // Armar URL de adicionales
  const sucursalMap = { bariloche: "Bariloche", neuquen: "Neuquen", calafate: "Calafate" };
  const rawSuc = (getVal("pickup_ubicacion") || "").toLowerCase();
  const sucursal = sucursalMap[rawSuc] || rawSuc;

  const adicionalesBase = window.abaReservas?.adicionalesUrl || "/adicionales/";
  const adUrl = new URL(adicionalesBase, window.location.origin);
  adUrl.searchParams.set("id_autos",    idAutos);
  adUrl.searchParams.set("inicio",      getVal("pickup_fecha"));
  adUrl.searchParams.set("fin",         getVal("dropoff_fecha"));
  adUrl.searchParams.set("hora_inicio", (getVal("pickup_horario")  || "12:00").split(":")[0]);
  adUrl.searchParams.set("hora_fin",    (getVal("dropoff_horario") || "12:00").split(":")[0]);
  adUrl.searchParams.set("sucursal",    sucursal);

  const waBtn = document.getElementById("aba-modal-wa");
  waBtn.setAttribute("href", adUrl.toString());
  waBtn.removeAttribute("target");

  // Mostrar modal
  modal.classList.remove("hidden");
  document.documentElement.classList.add("overflow-hidden");
}

function closeModal() {
  const modal = document.getElementById("aba-modal");
  if (!modal) return;
  modal.classList.add("hidden");
  document.documentElement.classList.remove("overflow-hidden");
}

function initAdicionalesPage(data) {
  const fmt = (n) => "$ " + Math.round(n).toLocaleString("es-AR");

  const cobSelections = new Set();
  const adQtys = new Map();

  function calcExtras() {
    let totalExtra = 0;
    const rows = [];

    data.coberturas.forEach((cob) => {
      if (cobSelections.has(cob.clave)) {
        const total = cob.precio * (cob.modo === "dia" ? data.dias : 1);
        rows.push({ nombre: cob.nombre, total, modo: cob.modo, qty: null });
        totalExtra += total;
      }
    });

    data.adicionales.forEach((ad) => {
      const qty = adQtys.get(ad.id) || 0;
      if (qty > 0) {
        const total = ad.precio * qty * (ad.modo === "dia" ? data.dias : 1);
        rows.push({ nombre: ad.nombre, total, modo: ad.modo, qty });
        totalExtra += total;
      }
    });

    return { rows, totalExtra };
  }

  function updateBreakdown() {
    const { rows, totalExtra } = calcExtras();

    const extrasEl = document.getElementById("aba-extras-breakdown");
    if (extrasEl) {
      extrasEl.innerHTML = rows.map((r) => {
        const label = r.qty !== null
          ? `${r.nombre} ×${r.qty}${r.modo === "dia" ? ` (${data.dias} días)` : ""}`
          : `${r.nombre}${r.modo === "dia" ? ` (${data.dias} días)` : ""}`;
        return `<div class="aba-breakdown-row extra"><span>${label}</span><span>+ ${fmt(r.total)}</span></div>`;
      }).join("");
    }

    const grandTarjeta = data.tarifa.total_tarjeta + totalExtra;
    const grandEfectivo = data.tarifa.total_efectivo + totalExtra;

    const elTarjeta = document.getElementById("aba-total-tarjeta");
    if (elTarjeta) elTarjeta.textContent = fmt(grandTarjeta);

    const elEfectivo = document.getElementById("aba-total-efectivo");
    if (elEfectivo) elEfectivo.textContent = fmt(grandEfectivo);

    const elSena = document.getElementById("aba-sena-monto");
    if (elSena && data.sena_pct) {
      elSena.textContent = fmt(Math.round(grandTarjeta * data.sena_pct / 100));
    }
  }

  // Inicializar cantidades automáticas
  if (data.autoQtys) {
    Object.entries(data.autoQtys).forEach(([idStr, qty]) => {
      const id = parseInt(idStr, 10);
      adQtys.set(id, qty);
      const decBtn = document.querySelector(`.aba-dec[data-id="${id}"]`);
      if (decBtn) decBtn.disabled = qty === 0;
    });
  }
  updateBreakdown();

  // Coberturas: toggles
  document.querySelectorAll(".aba-cob-toggle").forEach((chk) => {
    chk.addEventListener("change", () => {
      const clave = chk.value;
      if (chk.checked) cobSelections.add(clave);
      else cobSelections.delete(clave);
      updateBreakdown();
    });
  });

  // Adicionales: counters
  document.querySelectorAll(".aba-inc").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = parseInt(btn.dataset.id, 10);
      const qty = (adQtys.get(id) || 0) + 1;
      adQtys.set(id, qty);
      const valEl = document.getElementById(`qty-${id}`);
      if (valEl) valEl.textContent = qty;
      const decBtn = document.querySelector(`.aba-dec[data-id="${id}"]`);
      if (decBtn) decBtn.disabled = false;
      updateBreakdown();
    });
  });

  document.querySelectorAll(".aba-dec").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = parseInt(btn.dataset.id, 10);
      const qty = Math.max(0, (adQtys.get(id) || 0) - 1);
      adQtys.set(id, qty);
      const valEl = document.getElementById(`qty-${id}`);
      if (valEl) valEl.textContent = qty;
      btn.disabled = qty === 0;
      updateBreakdown();
    });
  });

  // ── CONTINUAR → Fiserv Connect ──────────────────────────────────────
  const continuar = document.getElementById("aba-continuar");
  const modal     = document.getElementById("aba-pago-modal");

  function showStep(id) {
    ["aba-paso-datos", "aba-paso-pago", "aba-paso-resultado"].forEach((s) => {
      const el = document.getElementById(s);
      if (el) el.style.display = "none";
    });
    const target = document.getElementById(id);
    if (target) target.style.display = id === "aba-paso-pago" ? "flex" : "block";
  }

  if (continuar && modal) {
    continuar.addEventListener("click", () => {
      const { rows, totalExtra } = calcExtras();
      const grandTarjeta = data.tarifa.total_tarjeta + totalExtra;
      const senaMonto = data.sena_pct
        ? Math.round(grandTarjeta * data.sena_pct / 100)
        : grandTarjeta;

      const montoEl = document.getElementById("aba-sena-modal-monto");
      if (montoEl) montoEl.textContent = fmt(senaMonto);

      const v = data.vehiculo;
      const r = data.reserva;
      continuar.dataset.senaMonto = senaMonto;
      continuar.dataset.payload = JSON.stringify({
        resumen:
          `Vehículo: ${v.MODELO} (Cat. ${v["Categoría"]})\n`
          + `Retiro: ${r.sucursal_retiro} — ${r.fecha_retiro} ${(r.hora_retiro || "").substring(0, 5)}\n`
          + `Devolución: ${r.sucursal_devolucion} — ${r.fecha_devolucion} ${(r.hora_devolucion || "").substring(0, 5)}\n`
          + `Días: ${data.dias}\n`
          + (rows.length ? `Extras: ${rows.map((x) => x.nombre).join(", ")}\n` : "")
          + `Total tarjeta: ${fmt(grandTarjeta)}\n`
          + `Seña: ${fmt(senaMonto)}`,
      });

      modal.style.display = "block";
      showStep("aba-paso-datos");
    });
  }

  // Cerrar modal
  document.getElementById("aba-pago-cerrar")?.addEventListener("click", () => {
    if (modal) modal.style.display = "none";
  });
  modal?.addEventListener("click", (e) => {
    if (e.target === modal) modal.style.display = "none";
  });

  // Reintentar
  document.getElementById("aba-reintentar")?.addEventListener("click", () => {
    document.getElementById("aba-res-error").style.display = "none";
    showStep("aba-paso-datos");
  });

  // Submit datos del cliente → AJAX → iframe Fiserv
  document.getElementById("aba-datos-submit")?.addEventListener("click", () => {
    const nombre = document.getElementById("aba-campo-nombre")?.value.trim() || "";
    const email  = document.getElementById("aba-campo-email")?.value.trim()  || "";
    const tel    = document.getElementById("aba-campo-tel")?.value.trim()    || "";
    const errEl  = document.getElementById("aba-datos-error");

    if (!nombre || !email) {
      errEl.textContent = "Nombre y email son obligatorios.";
      errEl.style.display = "block";
      return;
    }
    errEl.style.display = "none";

    const btn = document.getElementById("aba-datos-submit");
    btn.textContent = "Procesando…";
    btn.disabled = true;

    const monto   = parseFloat(continuar?.dataset.senaMonto || 0);
    const payload = continuar?.dataset.payload || "";

    fetch(window.abaReservas.ajaxUrl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        action: "aba_fiserv_init",
        nonce:  window.abaReservas.nonce,
        monto, nombre, email,
        telefono: tel,
        payload,
      }),
    })
      .then((r) => r.json())
      .then((res) => {
        btn.textContent = "Ir al pago →";
        btn.disabled = false;

        if (!res.success) {
          errEl.textContent = res.data?.message || "Error al iniciar el pago.";
          errEl.style.display = "block";
          return;
        }

        const { url, fields } = res.data;
        const form = document.createElement("form");
        form.method = "POST";
        form.action = url;
        form.target = "_top";
        form.style.display = "none";
        Object.entries(fields).forEach(([k, v]) => {
          const inp = document.createElement("input");
          inp.type = "hidden"; inp.name = k; inp.value = v;
          form.appendChild(inp);
        });
        document.body.appendChild(form);
        form.submit(); // redirect página completa a Fiserv
        form.remove();
      })
      .catch(() => {
        btn.textContent = "Ir al pago →";
        btn.disabled = false;
        errEl.textContent = "Error de conexión. Intentá de nuevo.";
        errEl.style.display = "block";
      });
  });

  // Resultado desde iframe (postMessage de pago-resultado.php)
  window.addEventListener("message", (e) => {
    if (e.data?.type !== "aba-pago-resultado") return;
    showStep("aba-paso-resultado");
    const okEl  = document.getElementById("aba-res-ok");
    const errEl = document.getElementById("aba-res-error");
    if (e.data.aprobado) {
      if (okEl)  okEl.style.display  = "block";
      if (errEl) errEl.style.display = "none";
    } else {
      if (okEl)  okEl.style.display  = "none";
      if (errEl) errEl.style.display = "block";
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  // Página de adicionales
  if (window.abaCotizacion) {
    initAdicionalesPage(window.abaCotizacion);
    return;
  }

  flatpickr.localize(Spanish);

  const pickupLocationSelect = document.querySelector('#pickup_ubicacion');
  const dropoffLocationSelect = document.querySelector('#dropoff_ubicacion');

  if (pickupLocationSelect) {
    new SlimSelect({
      select: '#pickup_ubicacion',
      settings: {
        showSearch: false,
        placeholderText: 'Ubicación',
      }
    })
  }

  if (dropoffLocationSelect) {
    new SlimSelect({
      select: '#dropoff_ubicacion',
      settings: {
        showSearch: false,
        placeholderText: 'Ubicación',
      }
    })
  }

  const rangeInput = document.querySelector('#reserva_rango');
  const pickupDateInput = document.querySelector('#pickup_fecha');
  const dropoffDateInput = document.querySelector('#dropoff_fecha');
  const pickupTimeSelect = document.querySelector('#pickup_horario');
  const dropoffTimeSelect = document.querySelector('#dropoff_horario');

  if (pickupTimeSelect) {
    new SlimSelect({
      select: '#pickup_horario',
      settings: {
        showSearch: false,
        placeholderText: 'Hora de entrega',
      },
    });
  }

  if (dropoffTimeSelect) {
    new SlimSelect({
      select: '#dropoff_horario',
      settings: {
        showSearch: false,
        placeholderText: 'Hora de devolución',
      },
    });
  }

  if (rangeInput && pickupDateInput && dropoffDateInput) {
    const initialDates = [];
    if (pickupDateInput.value) initialDates.push(pickupDateInput.value);
    if (dropoffDateInput.value) initialDates.push(dropoffDateInput.value);

    const setHiddenDates = (selectedDates) => {
      pickupDateInput.value = '';
      dropoffDateInput.value = '';

      if (!selectedDates.length) return;

      pickupDateInput.value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
      if (selectedDates[1]) {
        dropoffDateInput.value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
      }
    };

    flatpickr('#reserva_rango', {
      mode: 'range',
      minDate: 'today',
      dateFormat: 'Y-m-d',
      altInput: true,
      altFormat: 'd/m/Y',
      defaultDate: initialDates.length ? initialDates : undefined,
      onReady: (_selectedDates, _dateStr, instance) => {
        if (instance.altInput) {
          instance.altInput.setAttribute('placeholder', 'Seleccionar rango');
        }
      },
      onChange: (selectedDates) => {
        setHiddenDates(selectedDates);
      },
    });
  }

  const mq = window.matchMedia("(max-width: 767px)");
  const accordions = [];

  const toggleFilters = document.getElementById("toggleFilters");
  if (toggleFilters?.parentElement) {
    const contentEls = Array.from(toggleFilters.parentElement.children).filter(
      (el) => el !== toggleFilters
    );
    const accordion = setupMobileAccordion({
      toggleEl: toggleFilters,
      contentEls,
      defaultOpenMobile: false,
      mq,
    });
    if (accordion) accordions.push(accordion);
  }

  const togglePickup = document.getElementById("togglePickup");
  if (togglePickup?.parentElement) {
    const contentEls = Array.from(togglePickup.parentElement.children).filter(
      (el) => el !== togglePickup
    );
    const accordion = setupMobileAccordion({
      toggleEl: togglePickup,
      contentEls,
      defaultOpenMobile: false,
      mq,
    });
    if (accordion) accordions.push(accordion);
  }

  const toggleDropoff = document.getElementById("toggleDropoff");
  if (toggleDropoff?.parentElement) {
    const contentEls = Array.from(toggleDropoff.parentElement.children).filter(
      (el) => el !== toggleDropoff
    );
    const accordion = setupMobileAccordion({
      toggleEl: toggleDropoff,
      contentEls,
      defaultOpenMobile: false,
      mq,
    });
    if (accordion) accordions.push(accordion);
  }

  accordions.forEach((accordion) => accordion.applyMediaState(mq.matches));
  mq.addEventListener("change", (event) => {
    accordions.forEach((accordion) => accordion.applyMediaState(event.matches));
  });

  const sliderEl = document.getElementById("slider");
  if (!sliderEl) return;

  // agarramos todas las cards
  const cards = Array.from(document.querySelectorAll("article[data-cat]"));
  if (!cards.length) return;

  // calcular min/max global
  let globalMin = Infinity;
  let globalMax = -Infinity;
  for (const c of cards) {
    const p = getPrice(c);
    if (p < globalMin) globalMin = p;
    if (p > globalMax) globalMax = p;
  }

  // crear slider con min/max global (tu requerimiento 1)
  noUiSlider.create(sliderEl, {
    start: [globalMin, globalMax],
    connect: true,
    step: 1000,
    range: { min: globalMin, max: globalMax },
    tooltips: [
      { to: money, from: Number },
      { to: money, from: Number },
    ],
    format: {
      to: (v) => Math.round(v),
      from: (v) => Number(v),
    },
  });

  const emptyEl = document.getElementById("aba-no-results");

  // cuando cambia el slider → filtra (req 3)
  sliderEl.noUiSlider.on("update", () => {
    applyFilters({ cards, sliderEl, emptyEl });
  });

  // cuando cambian categorías → ajusta rango del slider y filtra (req 2 + 3)
  document.querySelectorAll('input[name="category_filter"]').forEach((cb) => {
    cb.addEventListener("change", () => {
      syncSliderRangeToCategories({ cards, sliderEl });
      // applyFilters no hace falta llamarlo aparte si ya estás escuchando "update" del slider,
      // pero no molesta si lo dejás.
      applyFilters({ cards, sliderEl, emptyEl });
    });
  });

  // primera pasada
  applyFilters({ cards, sliderEl, emptyEl });

  // "Reservar ahora" → ir directo a la página de adicionales
  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".aba-open-modal");
    if (!btn) return;

    const card = btn.closest("article");
    if (!card) return;

    const sucursalMap = { bariloche: "Bariloche", neuquen: "Neuquen", calafate: "Calafate" };
    const rawSuc = (getVal("pickup_ubicacion") || "").toLowerCase();
    const sucursal = sucursalMap[rawSuc] || rawSuc;

    const base = window.abaReservas?.adicionalesUrl || "/adicionales/";
    const url = new URL(base, window.location.origin);
    url.searchParams.set("id_autos",    card.dataset.idautos || "");
    url.searchParams.set("inicio",      getVal("pickup_fecha"));
    url.searchParams.set("fin",         getVal("dropoff_fecha"));
    url.searchParams.set("hora_inicio", (getVal("pickup_horario")  || "12:00").split(":")[0]);
    url.searchParams.set("hora_fin",    (getVal("dropoff_horario") || "12:00").split(":")[0]);
    url.searchParams.set("sucursal",    sucursal);
    url.searchParams.set("ubicacion",   rawSuc);

    window.location.href = url.toString();
  });

  // Click "Seleccionar adicionales" → navegar a la página de adicionales
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("#aba-modal-wa");
    if (!btn) return;
    e.preventDefault();
    const href = btn.getAttribute("href");
    if (href && href !== "#") window.location.href = href;
  });

  // Cerrar por overlay o botones
  document.addEventListener("click", (e) => {
    if (e.target.closest(".aba-modal-close")) {
      closeModal();
      return;
    }
    if (e.target.classList.contains("aba-modal-overlay")) {
      closeModal();
    }
  });

  // ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
  });
});
