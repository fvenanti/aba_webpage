<?php
if (!defined('ABSPATH'))
  exit;
/** @var array $params */
/** @var array $modelos */

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

function aba_parse_ars($money)
{
  // "$2.381.850" => 2381850
  $digits = preg_replace('/[^\d]/', '', (string) $money);
  return (int) ($digits ?: 0);
}

$cars = [];
$cats = [];
$price_range = ['min' => null, 'max' => null];

if (!empty($modelos)) {
  foreach ($modelos as $modelo) {
    $cars[] = [
      'category'     => esc_html($modelo['Categoría']),
      'price'        => esc_html($modelo['Tarifa_Final']),
      'cash_price'   => esc_html($modelo['Tarifa_Efectivo']),
      'model'        => esc_html($modelo['MODELO']),
      'image'        => esc_url($modelo['Imagen']),
      'details'      => wpautop(wp_kses_post($modelo['Detalle'])),
      'passengers'   => isset($modelo['Pasajeros'])   ? intval($modelo['Pasajeros'])      : null,
      'bags'         => isset($modelo['Valijas'])     ? intval($modelo['Valijas'])        : null,
      'transmission' => isset($modelo['Transmision']) ? esc_html($modelo['Transmision']) : null,
    ];
    $cats[esc_html($modelo['Categoría'])] = isset($cats[esc_html($modelo['Categoría'])]) ? $cats[esc_html($modelo['Categoría'])] + 1 : 1;
    $price = aba_parse_ars($modelo['Tarifa_Final']);
    if (is_null($price_range['min']) || $price < $price_range['min']) {
      $price_range['min'] = $price;
    }
    if (is_null($price_range['max']) || $price > $price_range['max']) {
      $price_range['max'] = $price;
    }
  }
}


?>

<style>
@media (min-width: 768px) {
  section.aba-results-layout { grid-template-columns: 220px 1fr !important; }
}
</style>
<section class="aba-results-layout grid gap-8">
  <!-- Filters -->
  <div class="px-8 py-6 md:p-8 max-md:rounded-lg! bg-white space-y-12.5 relative z-0">
    <button id="toggleFilters" class="md:hidden text-2xl! text-[#1A202C]! font-bold mb-0! flex! items-center! justify-between! w-full!">
      Filtros
      <i class="fas fa-chevron-down"></i>
    </button>
    <div class="max-md:pt-6">
      <span class='text-xl! text-[#1A202C]! font-bold mb-7! mt-0! p-0! block'>Categoría</span>
      <div class="flex flex-col gap-8">
        <?php
        foreach ($cats as $name => $count): ?>
          <label class="inline-flex items-center gap-3">
            <input type="checkbox" class="sr-only peer" name="category_filter" value="<?php echo esc_attr($name); ?>" />
            <div
              class="size-5.5 box-content flex items-center justify-center bg-white border rounded-md border-[#90A3BF] peer-checked:border-[#719846] peer-checked:bg-[#719846]">
              <i class="text-white fas fa-check"></i>
            </div>
            <span class="text-xl text-[#596780]! font-semibold! flex gap-1"><?php echo esc_html($name); ?><span
                class='text-[#90A3BF]!'>(<?php echo $count; ?>)</span></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="">
      <span class='text-xl! text-[#1A202C]! font-bold mb-14! mt-0! p-0! block'>Precio</span>
      <div class="mx-6">
        <div id="slider"></div>
      </div>
    </div>
  </div>

  <!-- Results -->
  <div class="space-y-12">
    <div class="">
      <form action="<?php echo esc_url($action); ?>" method="get" class="grid grid-cols-1 gap-6">
        <div class="px-6 py-6 bg-white rounded-lg md:px-8 reserva-search-card">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4 md:gap-6">
            <div class="reserva-search-field md:col-span-1">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="pickup_ubicacion">Lugar de entrega</label>
              <select id="pickup_ubicacion" name="pickup_ubicacion" class="" placeholder="Ubicación">
                <option value="bariloche" <?php selected(($params['pickup_ubicacion'] ?? ''), 'bariloche'); ?>>
                  Bariloche Aeropuerto
                </option>
              </select>
            </div>

            <div class="reserva-search-field md:col-span-2">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="reserva_rango">Fecha de Retiro/Devolución</label>
              <input type="text" class="w-full! py-2! px-0! h-10! shadow-none! placeholder:text-[#90A3BF]! text-sm!" id="reserva_rango"
                placeholder="Seleccionar rango" autocomplete="off" />
              <input type="hidden" id="pickup_fecha" name="pickup_fecha"
                value="<?php echo esc_attr($params['pickup_fecha'] ?? ''); ?>" />
              <input type="hidden" id="dropoff_fecha" name="dropoff_fecha"
                value="<?php echo esc_attr($params['dropoff_fecha'] ?? ''); ?>" />
            </div>

            <div class="reserva-search-field md:col-span-1">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="pickup_horario">Hora de entrega</label>
              <select id="pickup_horario" name="pickup_horario" class="" placeholder="Hora de entrega">
                <?php echo aba_reserva_render_time_options($params['pickup_horario'] ?? '12:00'); ?>
              </select>
            </div>

            <div class="reserva-search-field md:col-span-1">
              <label class="block mb-2 font-bold text-[#1A202C]!" for="dropoff_horario">Hora de devolución</label>
              <select id="dropoff_horario" name="dropoff_horario" class="" placeholder="Hora de devolución">
                <?php echo aba_reserva_render_time_options($params['dropoff_horario'] ?? '12:00'); ?>
              </select>
            </div>
          </div>
        </div>

        <div class="flex justify-center">
          <button type="submit"
            class="btn font-semibold! uppercase! bg-[#679938]! text-white! hover:bg-[#50d0bf]! text-sm! transition-colors duration-200 border-0!">
            Actualizar búsqueda
          </button>
        </div>
      </form>
    </div>

    <!-- Listado de modelos -->
    <div class="grid grid-cols-1 gap-x-8 gap-y-12 md:grid-cols-3">
      <?php if (!empty($cars)): ?>
        <?php foreach ($cars as $car): ?>
          <article data-cat='<?php echo esc_attr($car['category']); ?>'
            data-price='<?php echo aba_parse_ars($car['price']); ?>'
            data-price-label="<?php echo esc_attr($car['price']); ?>"
            data-cash-label="<?php echo esc_attr($car['cash_price']); ?>"
            data-model="<?php echo esc_attr($car['model']); ?>"
            data-image="<?php echo esc_url($car['image']); ?>"
            data-details="<?php echo $car['details']; ?>"
            data-passengers="<?php echo esc_attr($car['passengers'] ?? ''); ?>"
            data-bags="<?php echo esc_attr($car['bags'] ?? ''); ?>"
            data-transmission="<?php echo esc_attr($car['transmission'] ?? ''); ?>"
            class="flex flex-col justify-between gap-5 p-6 bg-white rounded-lg">
            <div class="">
              <h3 class="text-xl! text-[#1A202C]! font-bold mb-1! mt-0! p-0!">
                Categoría <?php echo esc_html($car['category']); ?>
              </h3>
              <p class="text-sm text-[#90A3BF] font-bold mb-4!">
                <?php echo esc_html($car['model']); ?>
              </p>
              <img src="<?php echo esc_url($car['image']); ?>" alt="<?php echo esc_html($car['model']); ?>">
              <?php if ($car['passengers'] !== null || $car['bags'] !== null || $car['transmission'] !== null): ?>
              <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;">
                <?php
                $tx_labels = ['automatica' => 'Automática', 'manual' => 'Manual'];
                $badges = [
                  ['fa-user',     $car['passengers'] !== null ? $car['passengers'] . ' pas.' : '—'],
                  ['fa-suitcase', $car['bags']        !== null ? $car['bags'] . ' val.'       : '—'],
                  ['fa-cog',      $car['transmission'] !== null ? ($tx_labels[$car['transmission']] ?? ucfirst($car['transmission'])) : '—'],
                ];
                foreach ($badges as [$icon, $label]):
                ?>
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#596780;background:#F6F7F9;padding:3px 8px;border-radius:20px;">
                  <i class="fas <?php echo esc_attr($icon); ?>" style="font-size:10px;"></i>
                  <?php echo esc_html($label); ?>
                </span>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
            <div class="">
              <p class="mb-5! text-xl! font-bold! text-[#1A202C]!"><?php echo esc_html($car['price']); ?></p>
              <p class="text-sm! text-[#90A3BF]! mb-4!">
                <?php echo esc_html($car['cash_price']); ?><br />
                Tarifa abonando en efectivo
              </p>
              <button id='toggleModal'
                class='aba-open-modal btn font-semibold! rounded-sm! uppercase! bg-[#679938]! text-white! hover:bg-[#50d0bf]! text-sm! transition-colors duration-200 border-0! w-full!'>Reservar
                ahora</button>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-sm text-gray-600 col-span-full">
          No encontramos vehículos disponibles con los filtros seleccionados.
          Probá ajustando las fechas o categoría.
        </p>
      <?php endif; ?>
    </div>
  </div>

  <div id="aba-modal" class="fixed inset-0 hidden z-9999999999">
    <!-- overlay -->
    <div class="absolute inset-0 aba-modal-overlay bg-black/60"></div>

    <!-- panel -->
    <div class="relative mx-auto my-10 max-w-[90vw] w-full bg-white rounded-xl shadow-2xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b">
        <button type="button" class="px-2 text-2xl leading-none aba-modal-close" aria-label="Cerrar">×</button>
      </div>

      <div class="p-6 overflow-y-auto grid grid-cols-1 md:grid-cols-[2fr_1fr] gap-12 h-[calc(80vh-64px)]">
        <div class="pr-4">
          <h3 class="text-lg! text-[#1A202C]! font-bold mb-10! mt-0! p-0!">Descripción</h3>
          <div id="aba-modal-details" class=""></div>
        </div>
        <div class="">
          <h3 class="text-xl! text-[#1A202C]! font-bold mb-10! mt-0! p-0!">Detalles de la reserva</h3>
          <img id="aba-modal-img" src="" alt="" class="object-contain w-full h-auto mb-8" />
          <div class="space-y-3 mb-6 pb-6 border-b border-[#C3D4E966]">
            <h3 id="aba-modal-subtitle" class="text-3xl! text-[#1A202C]! font-bold mb-1! mt-0! p-0!"></h3>
            <p id="aba-modal-cat" class="font-medium text-[#596780] m-0"></p>
            <div id="aba-modal-badges" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;"></div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <span class='font-medium text-[#90A3BF]'>Total</span>
            <p id="aba-modal-price" class="font-semibold text-[#1A202C] m-0! p-0! justify-self-end"></p>
            <span class='font-medium text-[#90A3BF]'>Total (efectivo)</span>
            <p id="aba-modal-cash" class="font-semibold text-[#1A202C] m-0! p-0! justify-self-end"></p>
            <span class="col-span-full text-sm text-[#90A3BF]">La tarifa deberá ser confirmada con uno de nuestros
              ejecutivos de ventas</span>
          </div>

          <div class="flex flex-col gap-3 pt-4">
            <a id="aba-modal-wa" href="#" target="_blank" rel="noopener"
              class="btn font-semibold! uppercase! bg-[#679938]! text-white! hover:bg-[#50d0bf]! text-sm! transition-colors duration-200 border-0! text-center!">
              Reservar ahora
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>


</section>
