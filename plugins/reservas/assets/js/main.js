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
      return `<span style="${badgeStyle}"><i class="fas ${icon}" style="font-size:10px;"></i>${label}</span>`;
    };
    const txLabel = transmission !== "" ? (transmission === "automatica" ? "Auto" : "Manual") : "";
    badgesEl.innerHTML =
      badge("fa-user",     passengers) +
      badge("fa-suitcase", bags) +
      badge("fa-cog",      txLabel);
  }

  // Armar WhatsApp con datos del form + auto
  const pickup_ubicacion = getVal("pickup_ubicacion");
  const pickup_fecha = ymdToDmy(getVal("pickup_fecha"));
  const pickup_horario = getVal("pickup_horario");
  const dropoff_fecha = ymdToDmy(getVal("dropoff_fecha"));
  const dropoff_horario = getVal("dropoff_horario");

  const text =
    `Hola! Quiero reservar este vehículo:\n` +
    `• Modelo: ${model}\n` +
    `• Segmento: ${cat}\n` +
    `• Tarifa: ${priceLabel}\n\n` +
    `Datos de la reserva:\n` +
    `• Pick-up: ${pickup_ubicacion} — ${pickup_fecha} ${pickup_horario}hs\n` +
    `• Drop-off: ${dropoff_fecha} ${dropoff_horario}hs`;

  const waUrl = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(text)}`;
  const waBtn = document.getElementById("aba-modal-wa");
  waBtn.setAttribute("href", waUrl);

  // Guardar payload para crear consulta antes de ir a WhatsApp
  waBtn.dataset.baseText = text;
  waBtn.dataset.model = model;
  waBtn.dataset.cat = cat;
  waBtn.dataset.priceLabel = priceLabel;
  waBtn.dataset.cashLabel = cashLabel;
  waBtn.dataset.pickupUbicacion = pickup_ubicacion;
  waBtn.dataset.pickupFecha = pickup_fecha;
  waBtn.dataset.pickupHorario = pickup_horario;
  waBtn.dataset.dropoffFecha = dropoff_fecha;
  waBtn.dataset.dropoffHorario = dropoff_horario;

  // Mostrar modal
  modal.classList.remove("hidden");
  document.documentElement.classList.add("overflow-hidden"); // evita scroll del body
}

function closeModal() {
  const modal = document.getElementById("aba-modal");
  if (!modal) return;
  modal.classList.add("hidden");
  document.documentElement.classList.remove("overflow-hidden");
}

document.addEventListener("DOMContentLoaded", () => {
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

  // Abrir: delegación para que funcione aunque filtres/ocultes
  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".aba-open-modal");
    if (!btn) return;

    const card = btn.closest("article");
    if (!card) return;

    openModalFromCard(card);
  });

  // Click "Reservar ahora" (crear consulta + ir a WhatsApp)
  document.addEventListener("click", async (e) => {
    const waBtn = e.target.closest("#aba-modal-wa");
    if (!waBtn) return;

    // Si no tenemos config, dejamos el href como estaba
    if (!window.abaReservas?.ajaxUrl || !window.abaReservas?.nonce) return;

    e.preventDefault();

    const baseText = waBtn.dataset.baseText || "";

    const form = new URLSearchParams();
    form.set("action", "aba_reservas_create_consulta");
    form.set("nonce", window.abaReservas.nonce);

    form.set("model", waBtn.dataset.model || "");
    form.set("cat", waBtn.dataset.cat || "");
    form.set("priceLabel", waBtn.dataset.priceLabel || "");
    form.set("cashLabel", waBtn.dataset.cashLabel || "");

    form.set("pickup_ubicacion", waBtn.dataset.pickupUbicacion || "");
    form.set("pickup_fecha", waBtn.dataset.pickupFecha || "");
    form.set("pickup_horario", waBtn.dataset.pickupHorario || "");
    form.set("dropoff_fecha", waBtn.dataset.dropoffFecha || "");
    form.set("dropoff_horario", waBtn.dataset.dropoffHorario || "");

    try {
      waBtn.setAttribute("aria-busy", "true");

      const res = await fetch(window.abaReservas.ajaxUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8" },
        body: form.toString(),
      });

      const json = await res.json();
      const consultaUrl = json?.data?.consultaUrl;

      if (!res.ok || !json?.success || !consultaUrl) {
        // fallback al WhatsApp sin URL
        window.location.href = waBtn.getAttribute("href") || "#";
        return;
      }

      const finalText = `${baseText}\n\n${consultaUrl}`;
      const finalWaUrl = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(finalText)}`;
      window.location.href = finalWaUrl;
    } catch {
      window.location.href = waBtn.getAttribute("href") || "#";
    } finally {
      waBtn.removeAttribute("aria-busy");
    }
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
