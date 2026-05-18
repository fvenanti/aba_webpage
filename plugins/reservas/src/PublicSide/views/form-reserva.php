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
@media (max-width: 767px) {
  .aba-form-inner { flex-direction: column !important; }
  .aba-form-btn   { width: 100% !important; }
  .aba-form-btn button { width: 100% !important; }
  .reserva-search-card { margin-left: 16px !important; margin-right: 16px !important; }
}
</style>
<section style="padding:0 0 24px;">
  <form action="<?php echo esc_url($action); ?>" method="get">

    <div class="bg-white rounded-lg reserva-search-card" style="padding:20px 8px 24px;">
      <div class="aba-form-inner" style="display:flex;flex-direction:row;align-items:stretch;gap:8px;">

        <div style="flex:1;min-width:0;">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 md:gap-4">
            <div class="reserva-search-field md:col-span-1">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="pickup_ubicacion">Lugar de recogida / devolución</label>
              <select id="pickup_ubicacion" name="pickup_ubicacion" class="" placeholder="Ubicación">
                <option value="bariloche" selected>Bariloche Aeropuerto</option>
              </select>
            </div>

            <div class="reserva-search-field md:col-span-1">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="reserva_rango">Fecha de recogida / devolución</label>
              <input type="text" class="w-full! py-2! px-0! h-10! shadow-none! placeholder:text-[#90A3BF]! text-sm!" id="reserva_rango"
                placeholder="Seleccionar rango" autocomplete="off" />
              <input type="hidden" id="pickup_fecha" name="pickup_fecha" value="" />
              <input type="hidden" id="dropoff_fecha" name="dropoff_fecha" value="" />
            </div>

            <div class="reserva-search-field md:col-span-1">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="pickup_horario">Hora de entrega</label>
              <select id="pickup_horario" name="pickup_horario" class="" placeholder="Hora de entrega">
                <?php echo aba_reserva_render_time_options('12:00'); ?>
              </select>
            </div>

            <div class="reserva-search-field md:col-span-1">
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
            style="white-space:nowrap;padding:20px 28px;min-width:120px;">
            Consultar
          </button>
        </div>

      </div>
    </div>

  </form>
</section>
