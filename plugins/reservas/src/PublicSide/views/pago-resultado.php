<?php
if (!defined('ABSPATH')) exit;
/** @var bool $aprobado */
/** @var string $oid */
?>
<div style="max-width:500px;margin:60px auto;padding:40px 32px;background:#fff;border-radius:16px;text-align:center;">
<?php if ($aprobado): ?>
  <div style="width:72px;height:72px;background:#f0f7e8;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:32px;color:#679938;">✓</div>
  <h2 style="font-size:22px;font-weight:700;color:#1A202C;margin:0 0 12px;">¡Seña recibida!</h2>
  <p style="color:#596780;font-size:15px;line-height:1.7;margin:0 0 32px;">
    Tu reserva quedó confirmada.<br>
    Te enviamos la confirmación a tu email.<br>
    Nos vemos en la fecha del retiro.
  </p>
  <a href="/" style="display:inline-block;padding:12px 32px;background:#679938;color:#fff;border-radius:8px;font-size:14px;font-weight:700;text-decoration:none;text-transform:uppercase;letter-spacing:.06em;">
    Volver al inicio
  </a>
<?php else: ?>
  <div style="width:72px;height:72px;background:#FFF5F5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:32px;color:#E53E3E;">✗</div>
  <h2 style="font-size:22px;font-weight:700;color:#1A202C;margin:0 0 12px;">Error en el pago</h2>
  <p style="color:#596780;font-size:15px;line-height:1.7;margin:0 0 32px;">
    No se pudo procesar el pago.<br>
    Podés intentarlo nuevamente o contactarnos.
  </p>
  <a href="javascript:history.back()" style="display:inline-block;padding:12px 32px;background:#679938;color:#fff;border-radius:8px;font-size:14px;font-weight:700;text-decoration:none;text-transform:uppercase;letter-spacing:.06em;">
    Intentar de nuevo
  </a>
<?php endif; ?>
</div>
