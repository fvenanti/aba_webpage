<?php
if (!defined('ABSPATH'))
  exit;
/** @var string $action */

if (!function_exists('aba_reserva_render_time_options')) {
  function aba_reserva_render_time_options(string $selected = '12:00'): string
  {
    $output = '';
    for ($hour = 0; $hour < 24; $hour++) {
      for ($minute = 0; $minute < 60; $minute += 15) {
        $value = sprintf('%02d:%02d', $hour, $minute);
        $is_selected = selected($selected, $value, false);
        $output .= sprintf('<option value="%1$s" %3$s>%2$s</option>', esc_attr($value), esc_html($value), $is_selected);
      }
    }

    return $output;
  }
}
?>
<style>
.aba-fields-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
}
.aba-lugar-field { grid-column: span 2; }
.aba-lugar-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; align-items: start; }
.aba-devo-check { display: flex; align-items: center; gap: 8px; margin-top: 12px; font-size: 13px; font-weight: 600; color: #1A202C; cursor: pointer; user-select: none; }
.aba-devo-check input { width: 16px; height: 16px; cursor: pointer; accent-color: #679938; }
.reserva-search-field {
  border: 2px solid #679938 !important;
  border-radius: 8px !important;
  padding: 3px 10px 3px !important;
  background: #fff !important;
}
.reserva-search-field label {
  color: #679938 !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 0.04em !important;
  margin-bottom: 2px !important;
  line-height: 1.2 !important;
  height: 28px !important;
  overflow: hidden !important;
  display: block !important;
  text-align: left !important;
}
.reserva-search-field .ss-main {
  min-height: unset !important;
  height: auto !important;
  line-height: 1.4 !important;
  padding: 2px 0 !important;
  text-align: left !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  color: #679938 !important;
}
.reserva-search-field .ss-single-selected .placeholder {
  color: #679938 !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  text-align: left !important;
}
.reserva-search-field input[type="hidden"] {
  display: none !important;
}
.reserva-search-field .flatpickr-wrapper {
  margin: 0 !important;
  padding: 0 !important;
  line-height: 0 !important;
}
.reserva-search-field input[type="text"],
#reserva_rango {
  display: block !important;
  width: 100% !important;
  height: auto !important;
  min-height: unset !important;
  line-height: 1.4 !important;
  padding: 2px 0 !important;
  text-align: left !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  color: #679938 !important;
  box-shadow: none !important;
  border: none !important;
  background: transparent !important;
}
@media (max-width: 767px) {
  .aba-fields-grid { grid-template-columns: 1fr !important; }
  .aba-lugar-field { grid-column: span 1 !important; }
  .aba-lugar-inner { grid-template-columns: 1fr !important; }
  .aba-form-inner { flex-direction: column !important; }
  .aba-form-btn   { width: 100% !important; }
  .aba-form-btn button { width: 100% !important; }
  .reserva-search-card { margin-left: 16px !important; margin-right: 16px !important; }
  .reserva-search-field input[type="text"],
  .reserva-search-field .ss-main,
  .reserva-search-field select { font-size: 16px !important; }
  .flatpickr-calendar input,
  .flatpickr-calendar select,
  .flatpickr-monthDropdown-months,
  .flatpickr-current-month input { font-size: 16px !important; }
}
@media (min-width: 768px) {
  .reserva-search-card.is-sticky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 99999;
    border-radius: 0 !important;
    box-shadow: 0 2px 16px rgba(0,0,0,0.18);
    padding: 8px 32px !important;
    background: #fff !important;
  }
}
</style>
<section style="padding:0 0 24px;">
  <form action="<?php echo esc_url($action); ?>" method="get">

    <div class="bg-white rounded-lg reserva-search-card" style="padding:6px 8px 8px;">
      <div class="aba-form-inner" style="display:flex;flex-direction:row;align-items:stretch;gap:8px;">

        <div style="flex:1;min-width:0;">
          <div class="aba-fields-grid">
            <div class="reserva-search-field aba-lugar-field">
              <div class="aba-lugar-inner">
                <div>
                  <label id="aba-lbl-retiro" class="block mb-2 font-bold text-[#1A202C]!" for="pickup_ubicacion">Lugar de retiro/devolución</label>
                  <select id="pickup_ubicacion" name="pickup_ubicacion" class="" placeholder="Ubicación">
                    <option value="bariloche" selected>Bariloche Aeropuerto</option>
                    <option value="bariloche_centro">Bariloche Centro</option>
                  </select>
                </div>
                <div id="aba-devo-half" style="display:none;">
                  <label class="block mb-2 font-bold text-[#1A202C]!" for="dropoff_ubicacion">Lugar de devolución</label>
                  <select id="dropoff_ubicacion" name="dropoff_ubicacion" class="" placeholder="Devolución" disabled>
                    <option value="bariloche" selected>Bariloche Aeropuerto</option>
                    <option value="bariloche_centro">Bariloche Centro</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="reserva-search-field">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="reserva_rango">Fecha de recogida / devolución</label>
              <input type="text" id="reserva_rango"
                placeholder="Seleccionar rango" autocomplete="off" />
              <input type="hidden" id="pickup_fecha" name="pickup_fecha" value="" />
              <input type="hidden" id="dropoff_fecha" name="dropoff_fecha" value="" />
            </div>

            <div class="reserva-search-field">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="pickup_horario">Hora de entrega</label>
              <select id="pickup_horario" name="pickup_horario" class="" placeholder="Hora de entrega">
                <?php echo aba_reserva_render_time_options('12:00'); ?>
              </select>
            </div>

            <div class="reserva-search-field">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="dropoff_horario">Hora de devolución</label>
              <select id="dropoff_horario" name="dropoff_horario" class="" placeholder="Hora de devolución">
                <?php echo aba_reserva_render_time_options('12:00'); ?>
              </select>
            </div>
          </div>
          <label class="aba-devo-check">
            <input type="checkbox" id="aba-devo-toggle" />
            Devolver en otro lugar
          </label>
        </div>

        <div class="aba-form-btn" style="flex-shrink:0;display:flex;align-items:center;">
          <button type="submit"
            class="btn font-semibold! uppercase! bg-[#679938]! text-white! hover:bg-[#50d0bf]! text-sm! transition-colors duration-200 border-0!"
            style="white-space:nowrap;padding:10px 28px;min-width:120px;">
            Consultar
          </button>
        </div>

      </div>
    </div>

  </form>
</section>
<script>
/* "Devolver en otro lugar": muestra el 2º selector y lleva la devolución por cookie */
(function () {
  var cb   = document.getElementById('aba-devo-toggle');
  var half = document.getElementById('aba-devo-half');
  var lbl  = document.getElementById('aba-lbl-retiro');
  if (!cb || !half) return;
  function devoSel(){ return document.getElementById('dropoff_ubicacion'); }
  function setCookie(v){ document.cookie = 'aba_devo=' + encodeURIComponent(v || '') + ';path=/;max-age=3600;SameSite=Lax'; }
  function clearCookie(){ document.cookie = 'aba_devo=;path=/;max-age=0;SameSite=Lax'; }
  function sync(){
    var s = devoSel();
    if (cb.checked) {
      half.style.display = '';
      if (lbl) lbl.textContent = 'Lugar de retiro';
      if (s) setCookie(s.value);
    } else {
      half.style.display = 'none';
      if (lbl) lbl.textContent = 'Lugar de retiro/devolución';
      clearCookie();
    }
  }
  cb.addEventListener('change', sync);
  document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'dropoff_ubicacion' && cb.checked) setCookie(e.target.value);
  });
  sync();
})();
</script>
<script>
(function () {
  function initStickyForm() {
    if (window.innerWidth < 768) return;
    var card = document.querySelector('.reserva-search-card');
    if (!card || card.dataset.stickyInit) return;
    card.dataset.stickyInit = '1';

    var placeholder = document.createElement('div');
    placeholder.style.display = 'none';
    card.parentNode.insertBefore(placeholder, card);

    var originalTop = card.getBoundingClientRect().top + window.scrollY;

    function update() {
      if (window.innerWidth < 768) {
        if (card.classList.contains('is-sticky')) {
          card.classList.remove('is-sticky');
          placeholder.style.display = 'none';
        }
        return;
      }
      if (window.scrollY > originalTop) {
        if (!card.classList.contains('is-sticky')) {
          placeholder.style.height = card.offsetHeight + 'px';
          placeholder.style.display = 'block';
          card.classList.add('is-sticky');
        }
      } else {
        if (card.classList.contains('is-sticky')) {
          card.classList.remove('is-sticky');
          placeholder.style.display = 'none';
        }
      }
    }

    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', function () {
      if (card.classList.contains('is-sticky')) {
        card.classList.remove('is-sticky');
        placeholder.style.display = 'none';
      }
      originalTop = card.getBoundingClientRect().top + window.scrollY;
      update();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStickyForm);
  } else {
    initStickyForm();
  }
})();
</script>
<script>
(function () {
  function getBa()       { return new Date(Date.now() - 10800 * 1000); }
  function toISO(d)      { return d.toISOString().slice(0, 10); }
  function addDays(d, n) { var r = new Date(d); r.setUTCDate(r.getUTCDate() + n); return r; }
  function addMonths(d,n){ var r = new Date(d); r.setUTCMonth(r.getUTCMonth() + n); return r; }

  // Mínimo 3 días de facturación:
  // - 3+ días calendario: siempre OK
  // - 2 días calendario: OK solo si devolución es MÁS de 4hs después del retiro
  // - 1 día o menos: nunca OK
  function meetsMinDays(pickupDateObj, dropoffDateObj, pickupTime, dropoffTime) {
    var diff = Math.round((dropoffDateObj - pickupDateObj) / (1000 * 60 * 60 * 24));
    if (diff >= 3) return true;
    if (diff <= 1) return false;
    var pParts = (pickupTime  || '12:00').split(':');
    var dParts = (dropoffTime || '12:00').split(':');
    var pMins  = parseInt(pParts[0], 10) * 60 + parseInt(pParts[1] || 0, 10);
    var dMins  = parseInt(dParts[0], 10) * 60 + parseInt(dParts[1] || 0, 10);
    return (dMins - pMins) >= 240;
  }

  function getMinPickupHour(pickupDateStr) {
    var ba    = getBa();
    var baDay  = ba.getUTCDay();
    var baHour = ba.getUTCHours() + ba.getUTCMinutes() / 60;
    // Regla 1: después de 18hs → mañana mínimo 14hs
    if (baHour >= 18 && pickupDateStr === toISO(addDays(ba, 1))) return 14;
    // Regla 2: sábado después de 12hs → lunes mínimo 14hs
    if (baDay === 6 && baHour >= 12 && pickupDateStr === toISO(addDays(ba, 2))) return 14;
    return 0;
  }

  function showError(msg) {
    var el = document.getElementById('aba-reglas-error');
    if (!el) {
      el = document.createElement('p');
      el.id = 'aba-reglas-error';
      el.style.cssText = 'color:#c00;background:#fee2e2;border-radius:6px;padding:8px 14px;margin-top:8px;font-size:13px;font-weight:600;';
      var card = document.querySelector('.reserva-search-card');
      if (card) card.insertAdjacentElement('afterend', el);
    }
    el.textContent = msg;
    el.style.display = 'block';
    clearTimeout(el._t);
    el._t = setTimeout(function () { el.style.display = 'none'; }, 5000);
  }

  function initRules() {
    var rangeEl = document.querySelector('#reserva_rango');
    if (!rangeEl || !rangeEl._flatpickr) { setTimeout(initRules, 150); return; }

    var fp     = rangeEl._flatpickr;
    var ba     = getBa();
    var baDay  = ba.getUTCDay();
    var baHour = ba.getUTCHours() + ba.getUTCMinutes() / 60;

    // Regla 3: maxDate = hoy + 4 meses
    fp.set('maxDate', toISO(addMonths(ba, 4)));

    // Regla 2: sábado después de 12hs → minDate = lunes próximo
    if (baDay === 6 && baHour >= 12) {
      fp.set('minDate', toISO(addDays(ba, 2)));
    }

    // Reglas 4 y 5: máximo 30 días / mínimo 3 días de alquiler
    fp.config.onChange.push(function (sel) {
      var cap = toISO(addMonths(getBa(), 4));
      if (sel.length === 1) {
        var max30 = toISO(addDays(sel[0], 30));
        fp.set('maxDate', max30 < cap ? max30 : cap);
      } else if (sel.length === 2) {
        var horaRetiro = (document.querySelector('#pickup_horario')  || {}).value || '12:00';
        var horaDev    = (document.querySelector('#dropoff_horario') || {}).value || '12:00';
        if (!meetsMinDays(sel[0], sel[1], horaRetiro, horaDev)) {
          fp.clear();
          showError('Mínimo 3 días de alquiler. Con 2 días calendario, la devolución debe ser más de 4 hs después del retiro.');
        }
        fp.set('maxDate', cap);
      } else {
        fp.set('maxDate', cap);
      }
    });

    // Validaciones al enviar el formulario
    var form = rangeEl.closest('form');
    if (form) {
      form.addEventListener('submit', function (e) {
        var fechaRetiroEl  = document.querySelector('#pickup_fecha');
        var fechaDevEl     = document.querySelector('#dropoff_fecha');
        var horaRetiroEl   = document.querySelector('#pickup_horario');
        var horaDevEl      = document.querySelector('#dropoff_horario');
        if (!fechaRetiroEl || !fechaRetiroEl.value) return;

        // Reglas 1 y 2: hora mínima de retiro
        var minH = getMinPickupHour(fechaRetiroEl.value);
        if (minH) {
          var selH = parseInt(horaRetiroEl.value.split(':')[0], 10);
          if (selH < minH) {
            e.preventDefault();
            showError('Para esa fecha el horario mínimo de retiro es las ' + minH + ':00 hs.');
            return;
          }
        }

        // Regla 5: mínimo 3 días de facturación
        if (fechaDevEl && fechaDevEl.value) {
          var pDate = new Date(fechaRetiroEl.value);
          var dDate = new Date(fechaDevEl.value);
          var pTime = horaRetiroEl ? horaRetiroEl.value : '12:00';
          var dTime = horaDevEl   ? horaDevEl.value    : '12:00';
          if (!meetsMinDays(pDate, dDate, pTime, dTime)) {
            e.preventDefault();
            showError('Mínimo 3 días de alquiler. Con 2 días calendario, la devolución debe ser más de 4 hs después del retiro.');
          }
        }
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { setTimeout(initRules, 200); });
  } else {
    setTimeout(initRules, 200);
  }
})();
</script>
