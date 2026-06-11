<?php
if (!defined('ABSPATH')) exit;
/** @var bool $aprobado */
/** @var string $oid */
?>
<div style="min-height:120px;"></div>
<script>
(function () {
  var aprobado = <?php echo $aprobado ? 'true' : 'false'; ?>;
  var oid      = <?php echo wp_json_encode($oid); ?>;
  if (window.parent && window.parent !== window) {
    window.parent.postMessage(
      { type: 'aba-pago-resultado', aprobado: aprobado, oid: oid },
      window.location.origin
    );
  } else {
    window.location.href = aprobado ? '/' : '/';
  }
})();
</script>
