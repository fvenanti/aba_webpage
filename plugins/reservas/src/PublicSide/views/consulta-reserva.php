<?php
if (!defined('ABSPATH')) {
  exit;
}

get_header();

the_post();

?>

<main class="aba-consulta-reserva" style="max-width: 900px; margin: 0 auto; padding: 24px 16px;">
  <h1 style="margin: 0 0 16px;">Consulta de reserva</h1>
  <div style="white-space: pre-wrap;"><?php echo esc_html(get_the_content()); ?></div>
</main>

<?php
get_footer();
