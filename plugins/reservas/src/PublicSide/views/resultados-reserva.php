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
      'id_autos'     => intval($modelo['IdAutos']     ?? 0),
      'sena_pct'     => isset($modelo['Sena_Pct'])   ? intval($modelo['Sena_Pct'])  : null,
      'km_incluidos' => (isset($modelo['Detalle']) && preg_match('/([\d.]+)\s*km\s*libres/i', $modelo['Detalle'], $mkm)) ? intval(str_replace('.', '', $mkm[1])) : 0,
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
/* Expandir la página de resultados al ancho completo */
.article .colLeft { width: 100% !important; }
.article .colRight { display: none !important; }
.article .newBox .box { width: 100% !important; max-width: 100% !important; padding: 0 24px !important; box-sizing: border-box; }
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
          <?php $aba_devo_cookie = strtolower(sanitize_text_field($_COOKIE['aba_devo'] ?? '')); $aba_devo_on = $aba_devo_cookie !== ''; ?>
          <style>
          .aba-lugar-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; align-items: start; }
          .aba-devo-check { display: flex; align-items: center; gap: 8px; margin-top: 14px; font-size: 13px; font-weight: 600; color: #1A202C; cursor: pointer; user-select: none; }
          .aba-devo-check input { width: 16px; height: 16px; cursor: pointer; accent-color: #679938; }
          @media (max-width: 767px) { .aba-lugar-inner { grid-template-columns: 1fr; } }
          </style>
          <div class="grid grid-cols-1 md:grid-cols-6 gap-4 md:gap-6">
            <div class="reserva-search-field md:col-span-2">
              <div class="aba-lugar-inner">
                <div>
                  <label id="aba-lbl-retiro" class="block mb-2 font-bold text-[#1A202C]!" for="pickup_ubicacion"><?php echo $aba_devo_on ? 'Lugar de retiro' : 'Lugar de retiro/devolución'; ?></label>
                  <select id="pickup_ubicacion" name="pickup_ubicacion" class="" placeholder="Ubicación">
                    <option value="bariloche" <?php selected(($params['pickup_ubicacion'] ?? ''), 'bariloche'); ?>>Bariloche Aeropuerto</option>
                    <option value="bariloche_centro" <?php selected(($params['pickup_ubicacion'] ?? ''), 'bariloche_centro'); ?>>Bariloche Centro</option>
                  </select>
                </div>
                <div id="aba-devo-half" style="display:<?php echo $aba_devo_on ? 'block' : 'none'; ?>;">
                  <label class="block mb-2 font-bold text-[#1A202C]!" for="dropoff_ubicacion">Lugar de devolución</label>
                  <select id="dropoff_ubicacion" name="dropoff_ubicacion" class="" placeholder="Devolución">
                    <option value="bariloche" <?php selected($aba_devo_cookie, 'bariloche'); ?>>Bariloche Aeropuerto</option>
                    <option value="bariloche_centro" <?php selected($aba_devo_cookie, 'bariloche_centro'); ?>>Bariloche Centro</option>
                  </select>
                </div>
              </div>
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
          <label class="aba-devo-check">
            <input type="checkbox" id="aba-devo-toggle" <?php checked($aba_devo_on); ?> />
            Devolver en otro lugar
          </label>
        </div>

        <div class="flex justify-center">
          <button type="submit"
            class="btn font-semibold! uppercase! bg-[#679938]! text-white! hover:bg-[#50d0bf]! text-sm! transition-colors duration-200 border-0!">
            Actualizar búsqueda
          </button>
        </div>
      </form>
      <script>
      /* "Devolver en otro lugar" (resultados): mismo comportamiento que la home */
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
    </div>

    <!-- Listado de modelos -->
    <div class="flex flex-col gap-5">
      <?php if (!empty($cars)): ?>
        <?php
        // Texto explicativo Low Cost por idioma (categoría terminada en "-")
        $aba_lang = function_exists('qtranxf_getLanguage') ? qtranxf_getLanguage() : 'es';
        $aba_lc_txt = [
          'es' => '¿Qué es Low Cost? Son unidades de años anteriores, revisadas y con el mantenimiento al día. Ofrecen el mismo nivel de seguridad, seguro y asistencia que toda nuestra flota, a una tarifa más conveniente.',
          'en' => 'What is Low Cost? Vehicles from earlier years, fully serviced and maintained. Same level of safety, insurance and assistance as our entire fleet, at a more convenient rate.',
          'pt' => 'O que é Low Cost? Veículos de anos anteriores, revisados e com manutenção em dia. Mesmo nível de segurança, seguro e assistência que toda a frota, a uma tarifa mais conveniente.',
        ];
        $aba_lc_text = $aba_lc_txt[$aba_lang] ?? $aba_lc_txt['es'];
        ?>
        <style>
        .aba-lc-badge{position:relative;display:inline-flex;align-items:center;gap:4px;background:#679938;color:#fff !important;font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:.05em;cursor:help;outline:none;vertical-align:middle;}
        .aba-lc-badge .aba-lc-tip{display:none;position:absolute;bottom:calc(100% + 8px);left:0;width:280px;max-width:72vw;background:#1A202C;color:#fff;font-size:12px;font-weight:400;line-height:1.5;text-transform:none;letter-spacing:normal;padding:10px 13px;border-radius:8px;box-shadow:0 6px 22px rgba(0,0,0,.28);z-index:60;}
        .aba-lc-badge:hover .aba-lc-tip,.aba-lc-badge:focus .aba-lc-tip,.aba-lc-badge:focus-within .aba-lc-tip{display:block;}
        </style>
        <?php foreach ($cars as $car): ?>
          <article data-cat='<?php echo esc_attr($car['category']); ?>'
            data-price='<?php echo aba_parse_ars($car['price']); ?>'
            data-price-label="<?php echo esc_attr($car['price']); ?>"
            data-cash-label="<?php echo esc_attr($car['cash_price']); ?>"
            data-model="<?php echo esc_attr($car['model']); ?>"
            data-image="<?php echo esc_url($car['image']); ?>"
            data-details="<?php echo esc_attr($car['details']); ?>"
            data-passengers="<?php echo esc_attr($car['passengers'] ?? ''); ?>"
            data-bags="<?php echo esc_attr($car['bags'] ?? ''); ?>"
            data-transmission="<?php echo esc_attr($car['transmission'] ?? ''); ?>"
            data-idautos="<?php echo esc_attr($car['id_autos']); ?>"
            data-senapct="<?php echo esc_attr($car['sena_pct'] ?? ''); ?>"
            style="display:flex;align-items:center;gap:24px;padding:20px 24px;background:#fff;border-radius:12px;">

            <!-- Imagen -->
            <div style="flex-shrink:0;width:160px;text-align:center;">
              <img src="<?php echo esc_url($car['image']); ?>"
                   alt="<?php echo esc_html($car['model']); ?>"
                   style="width:160px;height:100px;object-fit:contain;">
            </div>

            <!-- Info centro -->
            <div style="flex:1;min-width:0;">
              <p style="font-size:11px;font-weight:700;color:#679938;text-transform:uppercase;letter-spacing:.06em;margin:0 0 2px;">
                Categoría <?php echo esc_html($car['category']); ?>
              </p>
              <h3 style="font-size:17px;font-weight:700;color:#1A202C;margin:0 0 10px;">
                <?php echo esc_html($car['model']); ?>
              </h3>
              <?php if (str_ends_with(trim($car['category']), '-')): ?>
              <span class="aba-lc-badge" tabindex="0" role="button" aria-label="<?php echo esc_attr($aba_lc_text); ?>" style="margin:-4px 0 10px;">
                LOW COST <i class="fa fa-info-circle" style="font-size:11px;"></i>
                <span class="aba-lc-tip"><?php echo esc_html($aba_lc_text); ?></span>
              </span>
              <?php endif; ?>
              <?php if ($car['passengers'] !== null || $car['bags'] !== null || $car['transmission'] !== null): ?>
              <div style="display:flex;flex-wrap:wrap;gap:6px;">
                <?php
                $badges = [
                  ['fa-user',     $car['passengers']  !== null ? (string) $car['passengers'] : '—'],
                  ['fa-suitcase', $car['bags']         !== null ? (string) $car['bags']       : '—'],
                  ['fa-cog',      $car['transmission'] !== null ? ($car['transmission'] === 'automatica' ? 'Auto' : 'Manual') : '—'],
                ];
                foreach ($badges as [$icon, $label]):
                ?>
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#596780;background:#F6F7F9;padding:3px 8px;border-radius:20px;">
                  <i class="fa <?php echo esc_attr($icon); ?>" style="font-size:10px;"></i>
                  <?php echo esc_html($label); ?>
                </span>
                <?php endforeach; ?>
                <?php if (!empty($car['km_incluidos'])): ?>
                <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;color:#679938;background:#f0f7e8;padding:3px 8px;border-radius:20px;" title="Kilómetros incluidos">
                  <i class="fa fa-road" style="font-size:10px;"></i>
                  <?php echo esc_html(number_format($car['km_incluidos'], 0, ',', '.')); ?> km
                </span>
                <?php endif; ?>
              </div>
              <?php endif; ?>
            </div>

            <!-- Botones con precio integrado -->
            <?php
              $precio_card      = aba_parse_ars($car['price']);
              $sena_monto       = $car['sena_pct'] !== null ? round($precio_card * $car['sena_pct'] / 100) : null;
              $precio_anticipado = round($precio_card * 0.80);
            ?>
            <div style="flex-shrink:0;min-width:200px;display:flex;flex-direction:column;gap:10px;">

              <!-- Botón Reservar con seña -->
              <button data-tipo="sena"
                class='aba-open-modal'
                style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;
                       padding:14px 16px;background:#679938;color:#fff;border:none;border-radius:8px;
                       cursor:pointer;width:100%;text-align:center;transition:background .2s;"
                onmouseover="this.style.background='#50d0bf'" onmouseout="this.style.background='#679938'">
                <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.9;">Reservar con seña</span>
                <span style="font-size:19px;font-weight:800;line-height:1.2;"><?php echo esc_html($car['price']); ?></span>
                <?php if ($sena_monto !== null): ?>
                <span style="font-size:10px;font-weight:600;opacity:.85;">
                  Seña <?php echo $car['sena_pct']; ?>% · $ <?php echo number_format($sena_monto, 0, ',', '.'); ?>
                </span>
                <?php endif; ?>
              </button>

              <!-- Botón Pago anticipado -->
              <button data-tipo="anticipado"
                class='aba-open-modal'
                style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;
                       padding:14px 16px;background:#2B8A7A;color:#fff;border:none;border-radius:8px;
                       cursor:pointer;width:100%;text-align:center;transition:background .2s;"
                onmouseover="this.style.background='#236b5e'" onmouseout="this.style.background='#2B8A7A'">
                <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.9;">Pago anticipado</span>
                <span style="font-size:19px;font-weight:800;line-height:1.2;">$ <?php echo number_format($precio_anticipado, 0, ',', '.'); ?></span>
                <span style="font-size:10px;font-weight:600;opacity:.85;">Pago total · 20% dto. en tarifa</span>
              </button>

            </div>

          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-sm text-gray-600">
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
              Seleccionar adicionales →
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>


</section>
