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
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}
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
            <div class="reserva-search-field">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="pickup_ubicacion">Lugar de recogida / devolución</label>
              <select id="pickup_ubicacion" name="pickup_ubicacion" class="" placeholder="Ubicación">
                <option value="bariloche" selected>Bariloche Aeropuerto</option>
              </select>
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
