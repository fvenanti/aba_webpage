<?php
if (!defined('ABSPATH')) exit;
/** @var array|null $cotizacion */
/** @var string|null $error_code */
/** @var array $params */

$WA_NUMBER = '5492944604766';

$fmt_precio = fn(float $n): string => '$ ' . number_format($n, 0, ',', '.');
$modo_label = fn(string $modo): string => $modo === 'dia' ? 'Por día' : 'Por estadía';
?>
<style>
.aba-toggle-wrap { position:relative; display:inline-block; width:44px; height:24px; flex-shrink:0; }
.aba-toggle-wrap input { opacity:0; width:0; height:0; position:absolute; }
.aba-toggle-slider {
  position:absolute; cursor:pointer; inset:0;
  background:#CBD5E0; border-radius:24px; transition:background .2s;
}
.aba-toggle-slider::before {
  content:''; position:absolute; width:18px; height:18px;
  left:3px; bottom:3px; background:#fff; border-radius:50%; transition:transform .2s;
}
.aba-toggle-wrap input:checked + .aba-toggle-slider { background:#679938; }
.aba-toggle-wrap input:checked + .aba-toggle-slider::before { transform:translateX(20px); }

.aba-counter { display:inline-flex; align-items:center; border:1.5px solid #CBD5E0; border-radius:8px; overflow:hidden; }
.aba-counter button {
  width:32px; height:32px; background:#f7f7f7; border:none; cursor:pointer;
  font-size:18px; color:#596780; display:flex; align-items:center; justify-content:center; transition:background .15s;
}
.aba-counter button:hover:not(:disabled) { background:#e2e8f0; }
.aba-counter button:disabled { opacity:.35; cursor:default; }
.aba-counter-val { min-width:32px; text-align:center; font-weight:700; font-size:14px; color:#1A202C; }

.aba-breakdown-row { display:flex; justify-content:space-between; font-size:13px; color:#596780; margin-bottom:5px; }
.aba-breakdown-row.extra { color:#679938; font-weight:600; }
.aba-breakdown-row.total-row { font-size:16px; font-weight:700; color:#1A202C; margin-top:10px; padding-top:10px; border-top:1px solid #E2E8F0; }
.aba-breakdown-row.efectivo-row { color:#679938; font-size:13px; }

@media (min-width: 1024px) {
  .aba-cot-grid { grid-template-columns: 1fr 360px !important; }
  .aba-cot-sticky { position:sticky; top:20px; }
}
</style>

<?php if ($error_code === 'sin_tarifa'): ?>
<div style="text-align:center;padding:80px 24px;">
  <p style="font-size:20px;font-weight:700;color:#1A202C;margin:0 0 10px;">Sin tarifa configurada</p>
  <p style="color:#596780;margin:0 0 32px;">Este vehículo no tiene tarifa cargada aún. Contactanos y te cotizamos.</p>
  <a href="https://wa.me/<?php echo esc_attr($WA_NUMBER); ?>" target="_blank" rel="noopener"
     class="btn font-semibold! uppercase! bg-[#679938]! text-white! hover:bg-[#50d0bf]! text-sm! border-0!">
    Consultar por WhatsApp
  </a>
</div>

<?php elseif ($error_code || !$cotizacion): ?>
<div style="text-align:center;padding:80px 24px;">
  <p style="color:#596780;margin:0 0 20px;">No se pudo cargar la cotización. Intentá nuevamente.</p>
  <a href="javascript:history.back()" style="color:#679938;font-weight:600;">← Volver</a>
</div>

<?php else:
  $v     = $cotizacion['vehiculo'];
  $res   = $cotizacion['reserva'];
  $tar   = $cotizacion['tarifa'];
  $franq = $cotizacion['franquicias'];
  $coberturas  = $cotizacion['coberturas']  ?? [];
  $adicionales = $cotizacion['adicionales'] ?? [];
  $dias  = intval($res['dias_cobrables']);

  // Filtrar adicionales ocultos
  $claves_ocultas = ['fueraderadio_km', 'dropoff_km'];
  $adicionales = array_values(array_filter($adicionales, fn($a) => !in_array($a['clave'] ?? '', $claves_ocultas, true)));

  // Renombrar
  foreach ($adicionales as &$ad) {
    if (($ad['clave'] ?? '') === 'entrega_fuera_hora') $ad['nombre'] = 'Entrega fuera de hora';
  }
  unset($ad);

  // Cantidades automáticas
  $hora_ini_int  = intval($params['hora_inicio'] ?? 9);
  $hora_fin_int  = intval($params['hora_fin']    ?? 9);
  $ubicacion_raw = strtolower($params['ubicacion_raw'] ?? '');
  $autoQtys = [];
  foreach ($adicionales as $ad) {
    if (($ad['clave'] ?? '') === 'entrega_fuera_hora') {
      $qty = ($hora_ini_int < 8 || $hora_ini_int >= 20 ? 1 : 0)
           + ($hora_fin_int  < 8 || $hora_fin_int  >= 20 ? 1 : 0);
      if ($qty > 0) $autoQtys[$ad['id']] = $qty;
    }
    if (($ad['clave'] ?? '') === 'entrega_aeropuerto' && $ubicacion_raw === 'bariloche') {
      $autoQtys[$ad['id']] = 1;
    }
  }

  $fecha_retiro = date_format(date_create($res['fecha_retiro']),    'd/m/Y') . ' ' . substr($res['hora_retiro'],    0, 5);
  $fecha_devol  = date_format(date_create($res['fecha_devolucion']), 'd/m/Y') . ' ' . substr($res['hora_devolucion'], 0, 5);
?>

<div style="max-width:1100px;margin:0 auto;padding:24px 16px;">
  <div class="aba-cot-grid" style="display:grid;gap:24px;">

    <!-- ═══ IZQUIERDA ═══ -->
    <div style="display:flex;flex-direction:column;gap:24px;">

      <?php if (!empty($coberturas)): ?>
      <div class="bg-white rounded-lg" style="padding:24px;">
        <h2 style="font-size:18px;font-weight:700;color:#1A202C;margin:0 0 4px;">Coberturas</h2>
        <p style="font-size:13px;color:#90A3BF;margin:0 0 20px;">Elegí la cobertura para tu viaje</p>
        <?php foreach ($coberturas as $i => $cob): ?>
        <div class="aba-cob-row"
             data-clave="<?php echo esc_attr($cob['clave']); ?>"
             data-precio="<?php echo esc_attr($cob['precio']); ?>"
             data-modo="<?php echo esc_attr($cob['modo']); ?>"
             style="display:flex;align-items:center;gap:16px;padding:16px 0;<?php echo $i > 0 ? 'border-top:1px solid #F0F0F0;' : ''; ?>">
          <div style="flex:1;min-width:0;">
            <p style="font-size:14px;font-weight:700;color:#1A202C;margin:0 0 3px;"><?php echo esc_html($cob['nombre']); ?></p>
            <?php if (!empty($cob['descripcion'])): ?>
            <p style="font-size:12px;color:#596780;margin:0;line-height:1.5;"><?php echo esc_html($cob['descripcion']); ?></p>
            <?php endif; ?>
          </div>
          <div style="text-align:right;flex-shrink:0;margin-right:14px;">
            <p style="font-size:14px;font-weight:700;color:#1A202C;margin:0;"><?php echo $fmt_precio($cob['precio']); ?></p>
            <p style="font-size:11px;color:#90A3BF;font-weight:600;margin:0;"><?php echo $modo_label($cob['modo']); ?></p>
          </div>
          <label class="aba-toggle-wrap">
            <input type="checkbox" class="aba-cob-toggle" value="<?php echo esc_attr($cob['clave']); ?>" />
            <span class="aba-toggle-slider"></span>
          </label>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($adicionales)): ?>
      <div class="bg-white rounded-lg" style="padding:24px;">
        <h2 style="font-size:18px;font-weight:700;color:#1A202C;margin:0 0 20px;">Adicionales</h2>
        <?php foreach ($adicionales as $i => $ad): ?>
        <div class="aba-ad-row"
             data-id="<?php echo esc_attr($ad['id']); ?>"
             data-precio="<?php echo esc_attr($ad['precio']); ?>"
             data-modo="<?php echo esc_attr($ad['modo']); ?>"
             style="display:flex;align-items:center;gap:16px;padding:16px 0;<?php echo $i > 0 ? 'border-top:1px solid #F0F0F0;' : ''; ?>">
          <div style="flex:1;min-width:0;">
            <p style="font-size:14px;font-weight:700;color:#1A202C;margin:0 0 3px;"><?php echo esc_html($ad['nombre']); ?></p>
            <?php if (!empty($ad['descripcion'])): ?>
            <p style="font-size:12px;color:#596780;margin:0;line-height:1.5;"><?php echo esc_html($ad['descripcion']); ?></p>
            <?php endif; ?>
          </div>
          <?php $init_qty = $autoQtys[$ad['id']] ?? 0; $is_auto = $init_qty > 0; ?>
          <div style="text-align:right;flex-shrink:0;margin-right:14px;">
            <p style="font-size:14px;font-weight:700;color:#1A202C;margin:0;"><?php echo $fmt_precio($ad['precio']); ?></p>
            <p style="font-size:11px;color:<?php echo $is_auto ? '#679938' : '#90A3BF'; ?>;font-weight:600;margin:0;"><?php echo $is_auto ? 'Automático' : $modo_label($ad['modo']); ?></p>
          </div>
          <?php if ($is_auto): ?>
          <div style="display:inline-flex;align-items:center;justify-content:center;min-width:56px;height:32px;background:#f0f7e8;border-radius:8px;border:1.5px solid #679938;font-size:13px;font-weight:700;color:#679938;padding:0 10px;">
            ×<?php echo $init_qty; ?>
          </div>
          <?php else: ?>
          <div class="aba-counter">
            <button type="button" class="aba-dec" data-id="<?php echo esc_attr($ad['id']); ?>" disabled>−</button>
            <span class="aba-counter-val" id="qty-<?php echo esc_attr($ad['id']); ?>">0</span>
            <button type="button" class="aba-inc" data-id="<?php echo esc_attr($ad['id']); ?>">+</button>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div><!-- /izquierda -->

    <!-- ═══ DERECHA ═══ -->
    <div class="aba-cot-sticky">
      <div class="bg-white rounded-lg" style="padding:24px;">

        <!-- Vehículo -->
        <div style="display:flex;gap:12px;align-items:center;padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid #F0F0F0;">
          <img src="<?php echo esc_url($v['Imagen']); ?>" alt="<?php echo esc_attr($v['MODELO']); ?>"
               style="width:80px;height:56px;object-fit:contain;background:#F6F7F9;border-radius:6px;flex-shrink:0;" />
          <div>
            <p style="font-size:15px;font-weight:700;color:#1A202C;margin:0 0 2px;"><?php echo esc_html($v['MODELO']); ?></p>
            <p style="font-size:12px;color:#90A3BF;margin:0;">Categoría <?php echo esc_html($v['Categoría']); ?></p>
          </div>
        </div>

        <!-- Fechas -->
        <div style="padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid #F0F0F0;">
          <div style="display:flex;gap:8px;margin-bottom:6px;align-items:baseline;">
            <span style="font-size:11px;font-weight:700;color:#679938;text-transform:uppercase;letter-spacing:.04em;min-width:76px;">Retiro</span>
            <span style="font-size:12px;color:#596780;"><?php echo esc_html($res['sucursal_retiro']); ?> — <?php echo esc_html($fecha_retiro); ?></span>
          </div>
          <div style="display:flex;gap:8px;align-items:baseline;">
            <span style="font-size:11px;font-weight:700;color:#679938;text-transform:uppercase;letter-spacing:.04em;min-width:76px;">Devolución</span>
            <span style="font-size:12px;color:#596780;"><?php echo esc_html($res['sucursal_devolucion']); ?> — <?php echo esc_html($fecha_devol); ?></span>
          </div>
        </div>

        <!-- Breakdown -->
        <div style="padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid #F0F0F0;">
          <div class="aba-breakdown-row">
            <span>Tarifa base (<?php echo $dias; ?> días)</span>
            <span>
              <?php echo $fmt_precio($tar['subtotal']); ?>
              <?php if ($tar['iva_incluido']): ?>
              <small style="font-size:10px;color:#90A3BF;"> IVA inc.</small>
              <?php endif; ?>
            </span>
          </div>
          <div id="aba-extras-breakdown"></div>
          <div class="aba-breakdown-row total-row">
            <span>Total tarjeta</span>
            <span id="aba-total-tarjeta"><?php echo $fmt_precio($tar['total_tarjeta']); ?></span>
          </div>
          <?php if (!empty($tar['descuento_efectivo']) && $tar['descuento_efectivo']['monto'] != 0): ?>
          <div class="aba-breakdown-row efectivo-row" style="margin-top:6px;">
            <span>Total efectivo (<?php echo abs(intval($tar['descuento_efectivo']['pct'])); ?>% dto.)</span>
            <span id="aba-total-efectivo"><?php echo $fmt_precio($tar['total_efectivo']); ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($tar['sena_pct'])): ?>
          <div class="aba-breakdown-row" style="margin-top:10px;padding-top:10px;border-top:1px solid #E2E8F0;color:#679938;font-weight:700;font-size:14px;">
            <span>Seña requerida (<?php echo intval($tar['sena_pct']); ?>%)</span>
            <span id="aba-sena-monto"><?php echo $fmt_precio($tar['total_tarjeta'] * $tar['sena_pct'] / 100); ?></span>
          </div>
          <?php endif; ?>
        </div>

        <!-- Franquicias -->
        <div style="padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid #F0F0F0;">
          <p style="font-size:11px;font-weight:700;color:#90A3BF;text-transform:uppercase;letter-spacing:.04em;margin:0 0 8px;">Franquicias</p>
          <?php foreach ([['Daños', $franq['danos']], ['Vuelco', $franq['vuelco']], ['Robo', $franq['robo']]] as [$label, $val]): ?>
          <div style="display:flex;justify-content:space-between;font-size:12px;color:#596780;margin-bottom:3px;">
            <span><?php echo esc_html($label); ?></span>
            <span><?php echo $fmt_precio($val); ?></span>
          </div>
          <?php endforeach; ?>
        </div>

        <button id="aba-continuar"
          class="btn font-semibold! uppercase! bg-[#679938]! text-white! hover:bg-[#50d0bf]! text-sm! transition-colors duration-200 border-0! w-full!">
          Continuar
        </button>

      </div>
    </div><!-- /derecha -->

  </div>
</div>

<script>
window.abaCotizacion = <?php echo wp_json_encode([
  'dias'        => $dias,
  'tarifa'      => $tar,
  'sena_pct'    => isset($tar['sena_pct']) ? intval($tar['sena_pct']) : null,
  'coberturas'  => $coberturas,
  'adicionales' => $adicionales,
  'autoQtys'    => $autoQtys,
  'vehiculo'    => $v,
  'reserva'     => $res,
  'waNumber'    => $WA_NUMBER,
]); ?>;
</script>
<?php endif; ?>
