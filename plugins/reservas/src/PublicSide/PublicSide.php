<?php

namespace Upbrands\Reservas\PublicSide;

use Kucrut\Vite;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PublicSide
{
  private const CPT_CONSULTA = 'aba_reserva_consulta';

  /**
   * Normaliza el valor de sucursal que llega del form (slug o nombre) al
   * nombre EXACTO que espera la API de benvert. Un solo lugar para los strings.
   * Fallback: deja pasar cualquier otro valor (otras sucursales del sistema).
   */
  private function normalizar_sucursal(string $raw): string
  {
    $key = strtolower(trim($raw));
    $map = [
      'bariloche'                 => 'Bariloche Aeropuerto', // compat valor histórico
      'bariloche_aeropuerto'      => 'Bariloche Aeropuerto',
      'bariloche aeropuerto'      => 'Bariloche Aeropuerto',
      'bariloche_centro'          => 'Bariloche Centro',
      'bariloche centro'          => 'Bariloche Centro',
      'bariloche_otros'           => 'Bariloche - Otros lugares',
      'bariloche - otros lugares' => 'Bariloche - Otros lugares',
    ];
    return $map[$key] ?? $raw;
  }

  public function __construct()
  {
    add_shortcode('aba_reserva_form', [$this, 'shortcode_form_reserva']);
    add_shortcode('aba_reserva_resultados', [$this, 'shortcode_resultados_reserva']);
    add_shortcode('aba_cotizacion', [$this, 'shortcode_cotizacion']);
    add_shortcode('aba_pago_resultado', [$this, 'shortcode_pago_resultado']);

    add_action('init', [$this, 'register_cpt']);

    add_action('wp_ajax_aba_fiserv_init',        [$this, 'ajax_fiserv_init']);
    add_action('wp_ajax_nopriv_aba_fiserv_init', [$this, 'ajax_fiserv_init']);

    add_action('wp_ajax_aba_reservas_create_consulta', [$this, 'ajax_create_consulta']);
    add_action('wp_ajax_nopriv_aba_reservas_create_consulta', [$this, 'ajax_create_consulta']);

    add_filter('template_include', [$this, 'template_include']);
  }

  public function enqueue_scripts(): void
  {
    Vite\enqueue_asset(
      __DIR__ . '/../../dist',
      'assets/js/main.js',
      [
        'handle' => 'main',
        'dependencies' => ['jquery'],
      ]
    );

    $cotizacion_page = get_page_by_path('cotizacion');
    wp_localize_script(
      'main',
      'abaReservas',
      [
        'ajaxUrl'        => admin_url('admin-ajax.php'),
        'nonce'          => wp_create_nonce('aba_reservas'),
        'adicionalesUrl' => $cotizacion_page
          ? get_permalink($cotizacion_page->ID)
          : home_url('/cotizacion/'),
      ]
    );
  }

  public function register_cpt(): void
  {
    $labels = [
      'name' => 'Consultas de Reserva',
      'singular_name' => 'Consulta de Reserva',
    ];

    register_post_type(
      self::CPT_CONSULTA,
      [
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'exclude_from_search' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 26,
        'menu_icon' => 'dashicons-calendar-alt',
        'show_in_rest' => false,
        'has_archive' => false,
        'rewrite' => [
          'slug' => 'consulta-reserva',
          'with_front' => false,
        ],
        'supports' => ['title', 'editor'],
      ]
    );
  }

  /**
   * Shortcode [aba_reserva_form]
   */
  public function shortcode_form_reserva($atts = [], $content = null): string
  {
    if (get_option('aba_reservas_disabled')) {
      return '';
    }

    // Podés definir la página de resultados vía options, ACF, etc.
    // De momento lo dejo como ejemplo hardcodeado:
    // $resultados_page_id = get_option('aba_reservas_resultados_page_id'); // o lo que uses
    $resultados_page_id = 1284;
    $resultados_url = $resultados_page_id ? get_permalink($resultados_page_id) : home_url('/resultados-de-busqueda');

    $data = [
      'action' => $resultados_url,
    ];

    return $this->render_view('form-reserva.php', $data);
  }

  /**
   * Shortcode [aba_reserva_resultados]
   */
  public function shortcode_resultados_reserva($atts = [], $content = null): string
  {
    // Acá levantás los datos que vinieron por GET o POST
    $params = [
      'pickup_ubicacion' => sanitize_text_field($_GET['pickup_ubicacion'] ?? ''),
      'pickup_fecha' => sanitize_text_field($_GET['pickup_fecha'] ?? ''),
      'pickup_horario' => sanitize_text_field($_GET['pickup_horario'] ?? ''),
      'dropoff_fecha' => sanitize_text_field($_GET['dropoff_fecha'] ?? ''),
      'dropoff_horario' => sanitize_text_field($_GET['dropoff_horario'] ?? ''),
    ];

    // Ejemplo: acá haces la query de modelos disponibles según $params
    $modelos = $this->buscar_modelos_disponibles($params);

    $resultados_page_id = 1284;
    $resultados_url = $resultados_page_id ? get_permalink($resultados_page_id) : home_url('/resultados-reservas');

    $data = [
      'params' => $params,
      'modelos' => $modelos,
      'action' => $resultados_url,
    ];

    return $this->render_view('resultados-reserva.php', $data);
  }

  /**
   * Render genérico de vistas
   */
  private function render_view(string $view, array $data = []): string
  {
    // Ruta absoluta al directorio correctamente
    $views_dir = plugin_dir_path(__FILE__) . 'views/'; // __FILE__ = src/publicSide/class-public-side.php
    $file = $views_dir . $view;

    if (!file_exists($file)) {
      return '';
    }

    ob_start();
    extract($data, EXTR_SKIP);
    include $file;
    return ob_get_clean();
  }

  /**
   * Búsqueda de modelos (stub — acá va tu lógica real)
   */
  private function buscar_modelos_disponibles(array $params): array
  {
    // Validar parámetros obligatorios
    $required = [
      'pickup_ubicacion',
      'pickup_fecha',
      'pickup_horario',
      'dropoff_fecha',
      'dropoff_horario',
    ];

    //     $json = json_decode(
    //       '{
    //     "vehiculos": [
    //         {
    //             "Categoría": "I",
    //             "Descripcion": "FAM 7 PAX / AIRBAG / ABS",
    //             "Detalle": "Para esos días te podemos ofrecer un vehículo CATEGORIA I a una tarifa especial de $ 2381850 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 240.\n\n**Cotización válida por 5 días, sujeta a disponibilidad.**\n\n*Opciones de pago:*\n\n* Promoción 10 % de descuento por pago en efectivo (cash).\n* Pago en 1 cuota (10% de recargo)\n* 6 cuotas (20% de recargo)\n* 12 cuotas (No vigente - % de recargo)\n* 18 cuotas (No vigente - % de recargo)\n* Transferencia o débito.\n\n*Requisitos:*\n\n* Ser mayor de 21 años.\n* Poseer licencia de conducir vigente.\n* Disponer de tarjeta de crédito para la garantía del seguro con un límite superior al de la franquicia menor. No se realiza bloqueo de tarjeta, solo se toman los datos.\n\n*Incluye:*\n\n* Todos los impuestos y tasas.\n* Devolución del vehículo sin cargo adicional en el mismo horario en el que será entregado, con una tolerancia de hasta cuatro horas. Pasado ese límite se deberá considerar un día mas de alquiler\n* Neumáticos especiales para hielo y nieve (solo en temporada invernal).\n* Asistencia mecánica las 24hs.\n* 2 conductores adicionales sin cargo.\n* Seguro contra todo riesgo con franquicia de $ 2318000 por daños parciales y de $ 6218200 por daños totales (es decir que ante cualquier tipo de siniestro, el cliente debe abonar hasta dichos importes dependiendo del grado de rotura y tiempo de arreglo). Este seguro incluye rotura de cristales, parabrisas y cerraduras. NO CUBRE ROTURA DE NEUMATICOS. (los valores podrán ser actualizados al momento de la entrega del vehículo)\n* También te podemos ofrecer un seguro Premium, en el cual, abonando $ 26400 diarios adicionales, se reduce la franquicia menor en un 50%. NO CUBRE ROTURA DE NEUMATICOS.\n* En caso de cancelación, se dejará abierta la reserva para cambios de fecha sin penalidad para viajes con finalización hasta el 1/12/2025. Luego de esa fecha se ajustará la tarifa al nuevo alquiler. La seña efectuada por la reserva no es reembolsable por cancelación.\n\n*Servicios adicionales con cargo y previa solicitud (los valores podrán ser actualizados al momento de la entrega del vehículo):*\n\n* Entrega y devolución fuera de horario (después de las 21hs y hasta las 7hs): $30000 C/U\n* Entrega y devolución en otros lugares: Consultar precio.\n* GPS: $30000 /Estadia\n* Portaequipaje: $80000 /Estadia\n* Cadenas para hielo y nieve: $60000 /Estadia (incluidas en vehículo, solo se abona si se utilizan)\n* Sillas para bebé / Booster: $30000 /Estadia\n* Pase a Chile: $70000 /Estadia\n* Servicio de entrega y/o devolucion en Aeropuerto de Bariloche: $22000 /Estadia\n\nSi lo querés reservar, avisanos y te enviaremos un número de pre reserva y los datos para finalizar la misma. Te pediremos una seña del 35 % ($ 833647,5.-) para garantizar la reserva, el saldo restante ($ 1548202,5.- ) lo abonas con la entrega del vehículo.\n\nMuchas gracias!",
    //             "Detalle_Breve": "Para esos días te podemos ofrecer un vehículo CATEGORIA I a una tarifa especial de $ 2381850 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 240.",
    //             "IdAutos": 469,
    //             "Imagen": "https://ik.imagekit.io/urp2h5zfg/CAT%20I.jpg?updatedAt=1753390395010",
    //             "MATRICULA": "C 127 YD",
    //             "MODELO": "Spin Low Cost",
    //             "Sucursal": "Bariloche",
    //             "Tarifa_Efectivo": " $2.143.665",
    //             "Tarifa_Final": " $2.381.850"
    //         },
    //         {
    //             "Categoría": "J",
    //             "Descripcion": "4X2 MAN / SUV / MEDIANA",
    //             "Detalle": "Para esos días te podemos ofrecer un vehículo CATEGORIA J a una tarifa especial de $ 2223060 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 240.\n\n**Cotización válida por 5 días, sujeta a disponibilidad.**\n\n*Opciones de pago:*\n\n* Promoción 10 % de descuento por pago en efectivo (cash).\n* Pago en 1 cuota (10% de recargo)\n* 6 cuotas (20% de recargo)\n* 12 cuotas (No vigente - % de recargo)\n* 18 cuotas (No vigente - % de recargo)\n* Transferencia o débito.\n\n*Requisitos:*\n\n* Ser mayor de 21 años.\n* Poseer licencia de conducir vigente.\n* Disponer de tarjeta de crédito para la garantía del seguro con un límite superior al de la franquicia menor. No se realiza bloqueo de tarjeta, solo se toman los datos.\n\n*Incluye:*\n\n* Todos los impuestos y tasas.\n* Devolución del vehículo sin cargo adicional en el mismo horario en el que será entregado, con una tolerancia de hasta cuatro horas. Pasado ese límite se deberá considerar un día mas de alquiler\n* Neumáticos especiales para hielo y nieve (solo en temporada invernal).\n* Asistencia mecánica las 24hs.\n* 2 conductores adicionales sin cargo.\n* Seguro contra todo riesgo con franquicia de $ 2318000 por daños parciales y de $ 6218200 por daños totales (es decir que ante cualquier tipo de siniestro, el cliente debe abonar hasta dichos importes dependiendo del grado de rotura y tiempo de arreglo). Este seguro incluye rotura de cristales, parabrisas y cerraduras. NO CUBRE ROTURA DE NEUMATICOS. (los valores podrán ser actualizados al momento de la entrega del vehículo)\n* También te podemos ofrecer un seguro Premium, en el cual, abonando $ 26400 diarios adicionales, se reduce la franquicia menor en un 50%. NO CUBRE ROTURA DE NEUMATICOS.\n* En caso de cancelación, se dejará abierta la reserva para cambios de fecha sin penalidad para viajes con finalización hasta el 1/12/2025. Luego de esa fecha se ajustará la tarifa al nuevo alquiler. La seña efectuada por la reserva no es reembolsable por cancelación.\n\n*Servicios adicionales con cargo y previa solicitud (los valores podrán ser actualizados al momento de la entrega del vehículo):*\n\n* Entrega y devolución fuera de horario (después de las 21hs y hasta las 7hs): $30000 C/U\n* Entrega y devolución en otros lugares: Consultar precio.\n* GPS: $30000 /Estadia\n* Portaequipaje: $80000 /Estadia\n* Cadenas para hielo y nieve: $60000 /Estadia (incluidas en vehículo, solo se abona si se utilizan)\n* Sillas para bebé / Booster: $30000 /Estadia\n* Pase a Chile: $70000 /Estadia\n* Servicio de entrega y/o devolucion en Aeropuerto de Bariloche: $22000 /Estadia\n\nSi lo querés reservar, avisanos y te enviaremos un número de pre reserva y los datos para finalizar la misma. Te pediremos una seña del 35 % ($ 778071.-) para garantizar la reserva, el saldo restante ($ 1444989.- ) lo abonas con la entrega del vehículo.\n\nMuchas gracias!",
    //             "Detalle_Breve": "Para esos días te podemos ofrecer un vehículo CATEGORIA J a una tarifa especial de $ 2223060 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 240.",
    //             "IdAutos": 241,
    //             "Imagen": "https://ik.imagekit.io/urp2h5zfg/CAT%20J.jpg?updatedAt=1753390395146",
    //             "MATRICULA": "LML 070",
    //             "MODELO": "Ford Ecosport o Similar",
    //             "Sucursal": "Bariloche",
    //             "Tarifa_Efectivo": " $2.000.754",
    //             "Tarifa_Final": " $2.223.060"
    //         },
    //         {
    //             "Categoría": "L",
    //             "Descripcion": "4X4 AUT / SUV / MEDIANA",
    //             "Detalle": "Para esos días te podemos ofrecer un vehículo CATEGORIA L a una tarifa especial de $ 2560740 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 240.\n\n**Cotización válida por 5 días, sujeta a disponibilidad.**\n\n*Opciones de pago:*\n\n* Promoción 10 % de descuento por pago en efectivo (cash).\n* Pago en 1 cuota (10% de recargo)\n* 6 cuotas (20% de recargo)\n* 12 cuotas (No vigente - % de recargo)\n* 18 cuotas (No vigente - % de recargo)\n* Transferencia o débito.\n\n*Requisitos:*\n\n* Ser mayor de 21 años.\n* Poseer licencia de conducir vigente.\n* Disponer de tarjeta de crédito para la garantía del seguro con un límite superior al de la franquicia menor. No se realiza bloqueo de tarjeta, solo se toman los datos.\n\n*Incluye:*\n\n* Todos los impuestos y tasas.\n* Devolución del vehículo sin cargo adicional en el mismo horario en el que será entregado, con una tolerancia de hasta cuatro horas. Pasado ese límite se deberá considerar un día mas de alquiler\n* Neumáticos especiales para hielo y nieve (solo en temporada invernal).\n* Asistencia mecánica las 24hs.\n* 2 conductores adicionales sin cargo.\n* Seguro contra todo riesgo con franquicia de $ 2646000 por daños parciales y de $ 7050000 por daños totales (es decir que ante cualquier tipo de siniestro, el cliente debe abonar hasta dichos importes dependiendo del grado de rotura y tiempo de arreglo). Este seguro incluye rotura de cristales, parabrisas y cerraduras. NO CUBRE ROTURA DE NEUMATICOS. (los valores podrán ser actualizados al momento de la entrega del vehículo)\n* También te podemos ofrecer un seguro Premium, en el cual, abonando $ 33600 diarios adicionales, se reduce la franquicia menor en un 50%. NO CUBRE ROTURA DE NEUMATICOS.\n* En caso de cancelación, se dejará abierta la reserva para cambios de fecha sin penalidad para viajes con finalización hasta el 1/12/2025. Luego de esa fecha se ajustará la tarifa al nuevo alquiler. La seña efectuada por la reserva no es reembolsable por cancelación.\n\n*Servicios adicionales con cargo y previa solicitud (los valores podrán ser actualizados al momento de la entrega del vehículo):*\n\n* Entrega y devolución fuera de horario (después de las 21hs y hasta las 7hs): $30000 C/U\n* Entrega y devolución en otros lugares: Consultar precio.\n* GPS: $30000 /Estadia\n* Portaequipaje: $80000 /Estadia\n* Cadenas para hielo y nieve: $60000 /Estadia (incluidas en vehículo, solo se abona si se utilizan)\n* Sillas para bebé / Booster: $30000 /Estadia\n* Pase a Chile: $70000 /Estadia\n* Servicio de entrega y/o devolucion en Aeropuerto de Bariloche: $22000 /Estadia\n\nSi lo querés reservar, avisanos y te enviaremos un número de pre reserva y los datos para finalizar la misma. Te pediremos una seña del 35 % ($ 896259.-) para garantizar la reserva, el saldo restante ($ 1664481.- ) lo abonas con la entrega del vehículo.\n\nMuchas gracias!",
    //             "Detalle_Breve": "Para esos días te podemos ofrecer un vehículo CATEGORIA L a una tarifa especial de $ 2560740 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 240.",
    //             "IdAutos": 518,
    //             "Imagen": "https://ik.imagekit.io/urp2h5zfg/CAT%20L.jpg?updatedAt=1753390394934",
    //             "MATRICULA": "D 972 TK",
    //             "MODELO": "Ford Ecosport 4x4",
    //             "Sucursal": "Bariloche",
    //             "Tarifa_Efectivo": " $2.304.666",
    //             "Tarifa_Final": " $2.560.740"
    //         },
    //         {
    //             "Categoría": "M",
    //             "Descripcion": "4X4 / AUT / SUV / GDE",
    //             "Detalle": "Para esos días te podemos ofrecer un vehículo CATEGORIA M a una tarifa especial de $ 3425040 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 300.\n\n**Cotización válida por 5 días, sujeta a disponibilidad.**\n\n*Opciones de pago:*\n\n* Promoción 10 % de descuento por pago en efectivo (cash).\n* Pago en 1 cuota (10% de recargo)\n* 6 cuotas (20% de recargo)\n* 12 cuotas (No vigente - % de recargo)\n* 18 cuotas (No vigente - % de recargo)\n* Transferencia o débito.\n\n*Requisitos:*\n\n* Ser mayor de 21 años.\n* Poseer licencia de conducir vigente.\n* Disponer de tarjeta de crédito para la garantía del seguro con un límite superior al de la franquicia menor. No se realiza bloqueo de tarjeta, solo se toman los datos.\n\n*Incluye:*\n\n* Todos los impuestos y tasas.\n* Devolución del vehículo sin cargo adicional en el mismo horario en el que será entregado, con una tolerancia de hasta cuatro horas. Pasado ese límite se deberá considerar un día mas de alquiler\n* Neumáticos especiales para hielo y nieve (solo en temporada invernal).\n* Asistencia mecánica las 24hs.\n* 2 conductores adicionales sin cargo.\n* Seguro contra todo riesgo con franquicia de $ 3360000 por daños parciales y de $ 8960000 por daños totales (es decir que ante cualquier tipo de siniestro, el cliente debe abonar hasta dichos importes dependiendo del grado de rotura y tiempo de arreglo). Este seguro incluye rotura de cristales, parabrisas y cerraduras. NO CUBRE ROTURA DE NEUMATICOS. (los valores podrán ser actualizados al momento de la entrega del vehículo)\n* También te podemos ofrecer un seguro Premium, en el cual, abonando $ 36000 diarios adicionales, se reduce la franquicia menor en un 50%. NO CUBRE ROTURA DE NEUMATICOS.\n* En caso de cancelación, se dejará abierta la reserva para cambios de fecha sin penalidad para viajes con finalización hasta el 1/12/2025. Luego de esa fecha se ajustará la tarifa al nuevo alquiler. La seña efectuada por la reserva no es reembolsable por cancelación.\n\n*Servicios adicionales con cargo y previa solicitud (los valores podrán ser actualizados al momento de la entrega del vehículo):*\n\n* Entrega y devolución fuera de horario (después de las 21hs y hasta las 7hs): $30000 C/U\n* Entrega y devolución en otros lugares: Consultar precio.\n* GPS: $30000 /Estadia\n* Portaequipaje: $80000 /Estadia\n* Cadenas para hielo y nieve: $60000 /Estadia (incluidas en vehículo, solo se abona si se utilizan)\n* Sillas para bebé / Booster: $30000 /Estadia\n* Pase a Chile: $70000 /Estadia\n* Servicio de entrega y/o devolucion en Aeropuerto de Bariloche: $22000 /Estadia\n\nSi lo querés reservar, avisanos y te enviaremos un número de pre reserva y los datos para finalizar la misma. Te pediremos una seña del 35 % ($ 1198764.-) para garantizar la reserva, el saldo restante ($ 2226276.- ) lo abonas con la entrega del vehículo.\n\nMuchas gracias!",
    //             "Detalle_Breve": "Para esos días te podemos ofrecer un vehículo CATEGORIA M a una tarifa especial de $ 3425040 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 300.",
    //             "IdAutos": 525,
    //             "Imagen": "https://ik.imagekit.io/urp2h5zfg/CAT%20M.jpg?updatedAt=1753390396670",
    //             "MATRICULA": "LFW 429",
    //             "MODELO": "Honda CRV o Similar 4x4 - LC",
    //             "Sucursal": "Bariloche",
    //             "Tarifa_Efectivo": " $3.082.536",
    //             "Tarifa_Final": " $3.425.040"
    //         },
    //         {
    //             "Categoría": "N+",
    //             "Descripcion": "VAN / 12 PAX / FULL",
    //             "Detalle": "Para esos días te podemos ofrecer un vehículo CATEGORIA N+ a una tarifa especial de $ 4586820 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 300.\n\n**Cotización válida por 5 días, sujeta a disponibilidad.**\n\n*Opciones de pago:*\n\n* Promoción 10 % de descuento por pago en efectivo (cash).\n* Pago en 1 cuota (10% de recargo)\n* 6 cuotas (20% de recargo)\n* 12 cuotas (No vigente - % de recargo)\n* 18 cuotas (No vigente - % de recargo)\n* Transferencia o débito.\n\n*Requisitos:*\n\n* Ser mayor de 21 años.\n* Poseer licencia de conducir vigente.\n* Disponer de tarjeta de crédito para la garantía del seguro con un límite superior al de la franquicia menor. No se realiza bloqueo de tarjeta, solo se toman los datos.\n\n*Incluye:*\n\n* Todos los impuestos y tasas.\n* Devolución del vehículo sin cargo adicional en el mismo horario en el que será entregado, con una tolerancia de hasta cuatro horas. Pasado ese límite se deberá considerar un día mas de alquiler\n* Neumáticos especiales para hielo y nieve (solo en temporada invernal).\n* Asistencia mecánica las 24hs.\n* 2 conductores adicionales sin cargo.\n* Seguro contra todo riesgo con franquicia de $ 4030000 por daños parciales y de $ 10750000 por daños totales (es decir que ante cualquier tipo de siniestro, el cliente debe abonar hasta dichos importes dependiendo del grado de rotura y tiempo de arreglo). Este seguro incluye rotura de cristales, parabrisas y cerraduras. NO CUBRE ROTURA DE NEUMATICOS. (los valores podrán ser actualizados al momento de la entrega del vehículo)\n* También te podemos ofrecer un seguro Premium, en el cual, abonando $ 48000 diarios adicionales, se reduce la franquicia menor en un 50%. NO CUBRE ROTURA DE NEUMATICOS.\n* En caso de cancelación, se dejará abierta la reserva para cambios de fecha sin penalidad para viajes con finalización hasta el 1/12/2025. Luego de esa fecha se ajustará la tarifa al nuevo alquiler. La seña efectuada por la reserva no es reembolsable por cancelación.\n\n*Servicios adicionales con cargo y previa solicitud (los valores podrán ser actualizados al momento de la entrega del vehículo):*\n\n* Entrega y devolución fuera de horario (después de las 21hs y hasta las 7hs): $30000 C/U\n* Entrega y devolución en otros lugares: Consultar precio.\n* GPS: $30000 /Estadia\n* Portaequipaje: $80000 /Estadia\n* Cadenas para hielo y nieve: $60000 /Estadia (incluidas en vehículo, solo se abona si se utilizan)\n* Sillas para bebé / Booster: $30000 /Estadia\n* Pase a Chile: $70000 /Estadia\n* Servicio de entrega y/o devolucion en Aeropuerto de Bariloche: $22000 /Estadia\n\nSi lo querés reservar, avisanos y te enviaremos un número de pre reserva y los datos para finalizar la misma. Te pediremos una seña del 35 % ($ 1605387.-) para garantizar la reserva, el saldo restante ($ 2981433.- ) lo abonas con la entrega del vehículo.\n\nMuchas gracias!",
    //             "Detalle_Breve": "Para esos días te podemos ofrecer un vehículo CATEGORIA N+ a una tarifa especial de $ 4586820 por 10 días con 2000 km libres. El km excedente tiene un costo de $ 300.",
    //             "IdAutos": 364,
    //             "Imagen": "https://ik.imagekit.io/urp2h5zfg/CAT%20N+.jpg?updatedAt=1753390397293",
    //             "MATRICULA": "OGM 321",
    //             "MODELO": "Hyundai H1",
    //             "Sucursal": "Bariloche",
    //             "Tarifa_Efectivo": " $4.128.138",
    //             "Tarifa_Final": " $4.586.820"
    //         }
    //     ]
    // }'
    //       ,
    //       true
    //     );

    //     return $json['vehiculos'];

    foreach ($required as $key) {
      if (empty($params[$key])) {
        return [];
      }
    }

    // Normalizar fechas (por si vienen Y-m-d del input)
    $inicio = $this->format_fecha_api($params['pickup_fecha']);
    $fin = $this->format_fecha_api($params['dropoff_fecha']);

    if (!$inicio || !$fin) {
      return [];
    }

    $query = [
      'inicio' => $inicio,
      'fin' => $fin,
      'hora_inicio' => $this->parse_hora($params['pickup_horario']),
      'hora_fin' => $this->parse_hora($params['dropoff_horario']),
      'sucursal' => $this->normalizar_sucursal($params['pickup_ubicacion'] ?? ''),
    ];

    $url = 'https://aba.benvert.com.ar/api/disponibilidad?' . http_build_query($query);
    $response = wp_remote_get($url, [
      'headers' => [
        'X-API-Key' => get_option('aba_reservas_api_key', ''),
        'Accept'    => 'application/json',
      ],
      'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
      error_log('[aba_reservas] ' . $response->get_error_message());
      return [];
    }

    if (wp_remote_retrieve_response_code($response) !== 200) {
      return [];
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($body)) return [];

    $api_base = 'https://aba.benvert.com.ar';
    $vehiculos = $body['vehiculos'] ?? [];
    foreach ($vehiculos as &$v) {
      if (!empty($v['Imagen']) && str_starts_with($v['Imagen'], '/')) {
        $v['Imagen'] = $api_base . $v['Imagen'];
      }
    }
    return $vehiculos;
  }

  public function template_include(string $template): string
  {
    if (is_singular(self::CPT_CONSULTA)) {
      $tpl = plugin_dir_path(__FILE__) . 'views/consulta-reserva.php';
      if (file_exists($tpl)) {
        return $tpl;
      }
    }

    return $template;
  }

  public function ajax_create_consulta(): void
  {
    check_ajax_referer('aba_reservas', 'nonce');

    if (!$this->check_rate_limit('create_consulta', 5, 60)) {
      wp_send_json_error(['msg' => 'Demasiadas solicitudes. Intentá en un momento.'], 429);
    }

    $model = sanitize_text_field($_POST['model'] ?? '');
    $cat = sanitize_text_field($_POST['cat'] ?? '');
    $price_label = sanitize_text_field($_POST['priceLabel'] ?? '');
    $cash_label = sanitize_text_field($_POST['cashLabel'] ?? '');

    $pickup_ubicacion = sanitize_text_field($_POST['pickup_ubicacion'] ?? '');
    $pickup_fecha = sanitize_text_field($_POST['pickup_fecha'] ?? '');
    $pickup_horario = sanitize_text_field($_POST['pickup_horario'] ?? '');
    $dropoff_fecha = sanitize_text_field($_POST['dropoff_fecha'] ?? '');
    $dropoff_horario = sanitize_text_field($_POST['dropoff_horario'] ?? '');

    if (
      $model === '' ||
      $cat === '' ||
      $price_label === '' ||
      $pickup_ubicacion === '' ||
      $pickup_fecha === '' ||
      $pickup_horario === '' ||
      $dropoff_fecha === '' ||
      $dropoff_horario === ''
    ) {
      wp_send_json_error(['message' => 'Missing required fields'], 400);
    }

    $text =
      "Hola! Quiero reservar este vehículo:\n" .
      "• Modelo: {$model}\n" .
      "• Categoría: {$cat}\n" .
      "• Tarifa: {$price_label}\n\n" .
      "Datos de la reserva:\n" .
      "• Pick-up: {$pickup_ubicacion} — {$pickup_fecha} {$pickup_horario}hs\n" .
      "• Drop-off: {$dropoff_fecha} {$dropoff_horario}hs";

    $title = sprintf(
      'Consulta %s - %s (%s)',
      wp_date('Y-m-d H:i'),
      $model,
      $pickup_ubicacion
    );

    $post_id = wp_insert_post(
      [
        'post_type' => self::CPT_CONSULTA,
        'post_status' => 'publish',
        'post_title' => $title,
        'post_content' => $text,
      ],
      true
    );

    if (is_wp_error($post_id)) {
      wp_send_json_error(['message' => 'Could not create consulta'], 500);
    }

    update_post_meta($post_id, 'aba_model', $model);
    update_post_meta($post_id, 'aba_segmento', $cat);
    update_post_meta($post_id, 'aba_tarifa', $price_label);
    update_post_meta($post_id, 'aba_tarifa_efectivo', $cash_label);

    update_post_meta($post_id, 'aba_pickup_ubicacion', $pickup_ubicacion);
    update_post_meta($post_id, 'aba_pickup_fecha', $pickup_fecha);
    update_post_meta($post_id, 'aba_pickup_horario', $pickup_horario);
    update_post_meta($post_id, 'aba_dropoff_fecha', $dropoff_fecha);
    update_post_meta($post_id, 'aba_dropoff_horario', $dropoff_horario);

    $consulta_url = get_permalink($post_id);

    // Asegurar que quede público (por si hay plugins que filtren estados)
    $consulta_url = $consulta_url ? esc_url_raw($consulta_url) : '';

    wp_send_json_success(
      [
        'postId' => $post_id,
        'consultaUrl' => $consulta_url,
      ]
    );
  }

  private function format_fecha_api(string $fecha): ?string
  {
    // Ya viene como Y-m-d
    if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $fecha)) {
      return $fecha;
    }

    // Viene como d/m/Y (flatpickr legacy)
    $dt = \DateTime::createFromFormat('d/m/Y', $fecha);
    if ($dt) {
      return $dt->format('Y-m-d');
    }

    return null;
  }

  private function parse_hora(string $horario): int
  {
    // Acepta '10:00', '10:30', '10' -> devuelve el entero 10
    if (preg_match('/^(\d{1,2})/', $horario, $m)) {
      return (int) $m[1];
    }
    return 9;
  }

  /**
   * Shortcode [aba_cotizacion]
   */
  public function shortcode_cotizacion(): string
  {
    $id_autos        = intval($_GET['id_autos']        ?? 0);
    $inicio          = sanitize_text_field($_GET['inicio']          ?? '');
    $fin             = sanitize_text_field($_GET['fin']             ?? '');
    $hora_inicio     = intval($_GET['hora_inicio']     ?? 9);
    $hora_fin        = intval($_GET['hora_fin']        ?? 9);
    $sucursal        = sanitize_text_field($_GET['sucursal']        ?? 'Bariloche');
    $ubicacion_raw   = strtolower(sanitize_text_field($_GET['ubicacion'] ?? ''));
    $pago_anticipado = !empty($_GET['pago_anticipado']);
    // "Devolver en otro lugar" viaja por cookie desde el form. Vacío = devolución igual al retiro.
    $ubicacion_devolucion = strtolower(sanitize_text_field($_COOKIE['aba_devo'] ?? ''));

    if (!$id_autos || !$inicio || !$fin) {
      return $this->render_view('adicionales.php', [
        'cotizacion' => null,
        'error_code' => 'params',
        'params'     => [],
      ]);
    }

    $result = $this->obtener_cotizacion($id_autos, $inicio, $fin, $hora_inicio, $hora_fin, $sucursal, $ubicacion_devolucion);

    return $this->render_view('adicionales.php', [
      'cotizacion' => $result['data'] ?? null,
      'error_code' => $result['error'] ?? null,
      'params'     => compact('id_autos', 'inicio', 'fin', 'hora_inicio', 'hora_fin', 'sucursal', 'ubicacion_raw', 'ubicacion_devolucion', 'pago_anticipado'),
    ]);
  }

  public function ajax_fiserv_init(): void
  {
    check_ajax_referer('aba_reservas', 'nonce');

    if (!$this->check_rate_limit('fiserv_init', 3, 60)) {
      wp_send_json_error(['msg' => 'Demasiadas solicitudes. Intentá en un momento.'], 429);
    }

    $nombre      = sanitize_text_field($_POST['nombre']   ?? '');
    $apellido    = sanitize_text_field($_POST['apellido'] ?? '');
    $dni         = sanitize_text_field($_POST['dni']      ?? '');
    $email       = sanitize_email($_POST['email']         ?? '');
    $telefono    = sanitize_text_field($_POST['telefono'] ?? '');
    $payload_raw = sanitize_textarea_field(stripslashes($_POST['payload'] ?? ''));

    if (!$nombre || !$apellido || !$dni || !is_email($email)) {
      wp_send_json_error(['message' => 'Nombre, apellido, DNI y email son obligatorios.'], 400);
      return;
    }

    // [C-1] Validar payload y recalcular monto server-side (no confiar en $_POST['monto'])
    $payload = json_decode($payload_raw, true);
    if (!is_array($payload)) {
      wp_send_json_error(['message' => 'Datos de reserva inválidos.'], 400);
      return;
    }

    $id_autos     = intval($payload['id_autos']         ?? 0);
    $fecha_retiro = sanitize_text_field($payload['fecha_retiro']     ?? '');
    $fecha_dev    = sanitize_text_field($payload['fecha_devolucion'] ?? '');
    $hora_ret     = intval($payload['hora_retiro']       ?? 9);
    $hora_dev     = intval($payload['hora_devolucion']   ?? 9);
    $sucursal     = sanitize_text_field($payload['sucursal_retiro']  ?? '');
    $sucursal_dev = sanitize_text_field($payload['sucursal_devolucion'] ?? '');

    if (!$id_autos || !$fecha_retiro || !$fecha_dev) {
      wp_send_json_error(['message' => 'Datos de reserva incompletos.'], 400);
      return;
    }

    $cotizacion = $this->obtener_cotizacion($id_autos, $fecha_retiro, $fecha_dev, $hora_ret, $hora_dev, $sucursal, $sucursal_dev);
    if (isset($cotizacion['error'])) {
      wp_send_json_error(['message' => 'No se pudo verificar el precio. Intentá de nuevo.'], 500);
      return;
    }

    $cot       = $cotizacion['data'];
    $tar       = $cot['tarifa']  ?? [];
    $dias      = intval($cot['dias'] ?? $cot['reserva']['dias_cobrables'] ?? 1);
    $sena_pct  = floatval($tar['sena_pct'] ?? 35);
    $base      = floatval($tar['total_tarjeta'] ?? 0);

    // Sumar adicionales seleccionados verificando precios desde la API.
    // IMPORTANTE: esta normalización de 'modo' debe ser IDÉNTICA a la de la vista
    // adicionales.php, o el cobro difiere del total mostrado (ver Sillita Grande = plano).
    $ads_api = [];
    $claves_estadia_override  = ['barras_portaequipaje', 'gps', 'silla_bebes'];
    $nombres_estadia_override = ['booster', 'sillita grande'];
    foreach ($cot['adicionales'] ?? [] as $ad) {
      $clave_ad = $ad['clave'] ?? null;
      $es_estadia = (isset($clave_ad) && in_array($clave_ad, $claves_estadia_override, true))
                    || in_array(strtolower($ad['nombre'] ?? ''), $nombres_estadia_override, true);
      $modo_ad  = $es_estadia ? 'estadia' : ($ad['modo'] ?? 'total');
      if ($clave_ad !== null) {
        $ads_api[$clave_ad] = ['precio' => floatval($ad['precio'] ?? 0), 'modo' => $modo_ad];
      }
    }
    $extras = 0.0;
    foreach ($payload['adicionales'] ?? [] as $sel) {
      $clave = sanitize_text_field($sel['clave'] ?? '');
      $qty   = max(1, intval($sel['cantidad'] ?? 1));
      if ($clave && isset($ads_api[$clave])) {
        $extras += $ads_api[$clave]['precio'] * $qty * ($ads_api[$clave]['modo'] === 'dia' ? $dias : 1);
      }
    }

    // Sumar cobertura seleccionada verificando precio desde la API
    $cobs_api = [];
    foreach ($cot['coberturas'] ?? [] as $cob) {
      $cobs_api[$cob['clave']] = ['precio' => floatval($cob['precio'] ?? 0), 'modo' => $cob['modo'] ?? 'dia'];
    }
    $cob_sel = $payload['cobertura'] ?? null;
    if (is_array($cob_sel) && isset($cob_sel['clave'], $cobs_api[$cob_sel['clave']])) {
      $c = $cobs_api[$cob_sel['clave']];
      $extras += $c['precio'] * ($c['modo'] === 'dia' ? $dias : 1);
    }

    $pago_anticipado = !empty($payload['pago_anticipado']);

    if ($pago_anticipado) {
      $monto = (int) round($base * 0.80 + $extras);
    } else {
      $monto = (int) round(($base + $extras) * $sena_pct / 100);
    }

    if ($monto <= 0) {
      wp_send_json_error(['message' => $pago_anticipado ? 'Monto de pago inválido.' : 'Monto de seña inválido.'], 400);
      return;
    }

    $store_id      = get_option('aba_fiserv_store_id', '');
    $shared_secret = get_option('aba_fiserv_shared_secret', '');
    $gateway_url   = get_option('aba_fiserv_url', 'https://test.ipg-online.com/connect/gateway/processing');

    if (!$store_id || !$shared_secret) {
      wp_send_json_error(['message' => 'Pasarela no configurada.'], 500);
      return;
    }

    // [C-3] OID criptográficamente aleatorio (reemplaza uniqid() que era predecible)
    $oid         = 'ABA-' . strtoupper(bin2hex(random_bytes(10)));
    $tz          = new \DateTimeZone('America/Buenos_Aires');
    $txndatetime = (new \DateTime('now', $tz))->format('Y:m:d-H:i:s');
    $chargetotal = number_format($monto, 2, '.', '');
    $currency    = '032'; // ARS

    $result_page = get_page_by_path('pago-resultado');
    $result_url  = $result_page ? get_permalink($result_page->ID) : home_url('/pago-resultado/');

    // Fiserv IPG HMAC-SHA256: todos los campos ordenados alfa, valores con | entre ellos
    $fields = [
      'bname'              => $nombre,
      'chargetotal'        => $chargetotal,
      'currency'           => $currency,
      'email'              => $email,
      'hash_algorithm'     => 'HMACSHA256',
      'oid'                => $oid,
      'phone'              => $telefono,
      'responseFailURL'    => add_query_arg(['oid' => $oid, 'result' => 'fail'], $result_url),
      'responseSuccessURL' => add_query_arg(['oid' => $oid, 'result' => 'ok'],   $result_url),
      'storename'          => $store_id,
      'authenticateTransaction'            => 'true',
      'threeDSRequestorChallengeIndicator' => '01',
      'timezone'                           => 'America/Buenos_Aires',
      'txndatetime'                        => $txndatetime,
      'txntype'                            => 'sale',
    ];
    ksort($fields);
    $hash = base64_encode(hash_hmac('sha256', implode('|', $fields), $shared_secret, true));
    $fields['hashExtended'] = $hash;

    set_transient('aba_pago_' . $oid, [
      'nombre'          => $nombre,
      'apellido'        => $apellido,
      'dni'             => $dni,
      'email'           => $email,
      'telefono'        => $telefono,
      'monto'           => $monto,
      'payload'         => $payload,
      'pago_anticipado' => $pago_anticipado,
    ], 30 * MINUTE_IN_SECONDS);

    wp_send_json_success([
      'url'    => $gateway_url,
      'fields' => $fields,
    ]);
  }

  public function shortcode_pago_resultado(): string
  {
    $result = sanitize_text_field($_GET['result'] ?? '');
    $oid    = sanitize_text_field($_GET['oid']    ?? '');

    $aprobado   = false;
    $id_reserva = null;
    $api_ok     = false;

    if ($result === 'ok' && $oid) {
      // [C-2] Verificar firma HMAC-SHA256 de Fiserv
      // Usamos QUERY_STRING directamente para evitar que wp_magic_quotes() corrompa los valores
      $shared_secret = get_option('aba_fiserv_shared_secret', '');
      parse_str($_SERVER['QUERY_STRING'] ?? '', $raw_params);
      $response_hash = $raw_params['response_hash'] ?? '';
      if ($shared_secret && $response_hash) {
        // Excluir: el hash en sí y 'result' (parámetro nuestro, no de Fiserv)
        $fiserv_exclude = ['response_hash', 'result'];
        $fields = array_filter(
          $raw_params,
          fn($k) => !in_array($k, $fiserv_exclude, true),
          ARRAY_FILTER_USE_KEY
        );
        $fields = array_filter($fields, fn($v) => $v !== '' && $v !== null);
        ksort($fields);
        $computed = base64_encode(hash_hmac('sha256', implode('|', $fields), $shared_secret, true));
        error_log('[aba_reservas C-2] OID=' . $oid . ' fields=' . implode(',', array_keys($fields)) . ' match=' . ($computed === $response_hash ? 'YES' : 'NO'));
        if (!hash_equals($computed, $response_hash)) {
          error_log('[aba_reservas C-2] Hash MISMATCH — bloqueando. OID=' . $oid);
          return $this->render_view('pago-resultado.php', [
            'aprobado'   => false,
            'oid'        => $oid,
            'id_reserva' => null,
            'api_ok'     => false,
          ]);
        }
      }

      $datos = get_transient('aba_pago_' . $oid);

      if ($datos && !get_transient('aba_pago_creado_' . $oid)) {
        $approval_code = sanitize_text_field($_REQUEST['approval_code'] ?? '');
        $ccbrand       = sanitize_text_field($_REQUEST['ccbrand']       ?? '');
        [$id_reserva, $api_ok] = $this->crear_reserva_desde_pago($oid, $datos, $approval_code, $ccbrand);
        delete_transient('aba_pago_' . $oid);
        // Guardar [id_reserva, api_ok] para si el cliente recarga la página
        set_transient('aba_pago_creado_' . $oid, [$id_reserva, $api_ok], 24 * HOUR_IN_SECONDS);
      } else {
        $guardado   = get_transient('aba_pago_creado_' . $oid);
        $id_reserva = is_array($guardado) ? ($guardado[0] ?? null) : null;
        $api_ok     = is_array($guardado) ? ($guardado[1] ?? false) : false;
      }

      $aprobado = true;
    }

    return $this->render_view('pago-resultado.php', compact('aprobado', 'oid', 'id_reserva', 'api_ok'));
  }

  /**
   * Registra el pago en WordPress, llama a la API y envía los emails correspondientes.
   * Devuelve [?int $id_reserva_backend, bool $api_ok].
   * Si la API falla: $id_reserva_backend = null, $api_ok = false.
   */
  private function check_rate_limit(string $action, int $max = 5, int $window = 60): bool
  {
    $ip  = md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $key = 'aba_rl_' . $action . '_' . $ip;
    $count = (int) get_transient($key);
    if ($count >= $max) return false;
    set_transient($key, $count + 1, $window);
    return true;
  }

  private function ccbrand_to_tipo_pago(string $brand): string
  {
    return match (strtoupper(trim($brand))) {
      'MASTER', 'MASTERCARD' => 'credito_master',
      'AMEX', 'AMERICAN EXPRESS' => 'credito_amex',
      default => 'credito_visa',
    };
  }

  private function crear_reserva_desde_pago(string $oid, array $datos, string $approval_code, string $ccbrand = ''): array
  {
    $payload         = is_array($datos['payload']) ? $datos['payload'] : (json_decode($datos['payload'] ?? '', true) ?: []);
    $resumen         = $payload['resumen'] ?? '';
    $pago_anticipado = !empty($datos['pago_anticipado']) || !empty($payload['pago_anticipado']);
    $nombre_completo = trim(($datos['nombre'] ?? '') . ' ' . ($datos['apellido'] ?? ''));
    $tipo_pago_label = $pago_anticipado ? 'Pago anticipado (100% con 20% dto. en tarifa)' : 'Seña';

    $text = ($pago_anticipado ? "PAGO ANTICIPADO cobrado vía Fiserv/Postnet\n" : "Seña cobrada vía Fiserv/Postnet\n")
          . "OID: {$oid}\n"
          . "Approval code: {$approval_code}\n"
          . "Monto {$tipo_pago_label}: $" . number_format($datos['monto'], 2, ',', '.') . "\n\n"
          . "Cliente: {$nombre_completo}\n"
          . "DNI: " . ($datos['dni'] ?? '') . "\n"
          . "Email: {$datos['email']}\n"
          . "Teléfono: {$datos['telefono']}\n";

    if ($resumen) {
      $text .= "\n--- Detalle reserva ---\n" . $resumen;
    }

    $post_id = wp_insert_post([
      'post_type'    => self::CPT_CONSULTA,
      'post_status'  => 'publish',
      'post_title'   => "Reserva {$oid} — {$nombre_completo}",
      'post_content' => $text,
    ], true);

    if (is_wp_error($post_id)) return [null, false];

    update_post_meta($post_id, 'aba_oid',           $oid);
    update_post_meta($post_id, 'aba_approval_code', $approval_code);
    update_post_meta($post_id, 'aba_monto_sena',    $datos['monto']);
    update_post_meta($post_id, 'aba_nombre',        $datos['nombre'] ?? '');
    update_post_meta($post_id, 'aba_apellido',      $datos['apellido'] ?? '');
    update_post_meta($post_id, 'aba_dni',           $datos['dni'] ?? '');
    update_post_meta($post_id, 'aba_email',         $datos['email']);
    update_post_meta($post_id, 'aba_telefono',      $datos['telefono']);

    if ($payload) {
      update_post_meta($post_id, 'aba_payload', wp_json_encode($payload));
    }
    if ($ccbrand) {
      update_post_meta($post_id, 'aba_ccbrand', $ccbrand);
    }

    // Llamar al backend — si falla, el pago quedó registrado en WP pero sin número de reserva
    $id_reserva = $this->llamar_api_reservas($oid, $datos, $approval_code, $payload, $ccbrand);

    if ($id_reserva) {
      update_post_meta($post_id, 'aba_id_reserva_backend', $id_reserva);
      // API OK: email de confirmación al cliente + notificación al admin
      $this->enviar_emails_reserva($id_reserva, $oid, $datos, $resumen, $approval_code);
      return [$id_reserva, true];
    }

    // API falló: alerta al admin para que procese manualmente; sin email al cliente aún
    $this->enviar_email_admin_alerta($post_id, $oid, $datos, $resumen, $approval_code);
    return [null, false];
  }

  private function llamar_api_reservas(string $oid, array $datos, string $approval_code, array $payload, string $ccbrand = ''): ?int
  {
    if (empty($payload['categoria']) || empty($payload['fecha_retiro'])) return null;

    $api_key = get_option('aba_reservas_api_key', '');
    if (!$api_key) return null;

    $body = [
      'cliente' => [
        'dni'      => $datos['dni']      ?? '',
        'nombre'   => $datos['nombre']   ?? '',
        'apellido' => $datos['apellido'] ?? '',
        'mail'     => $datos['email'],
        'telefono' => $datos['telefono'],
      ],
      'reserva' => [
        'categoria'           => $payload['categoria']           ?? '',
        'fecha_retiro'        => $payload['fecha_retiro']        ?? '',
        'hora_retiro'         => intval($payload['hora_retiro']  ?? 9),
        'fecha_devolucion'    => $payload['fecha_devolucion']    ?? '',
        'hora_devolucion'     => intval($payload['hora_devolucion'] ?? 9),
        'sucursal_retiro'     => $this->normalizar_sucursal($payload['sucursal_retiro'] ?? ''),
        'sucursal_devolucion' => $this->normalizar_sucursal($payload['sucursal_devolucion'] ?? $payload['sucursal_retiro'] ?? ''),
        'tarifa_total'        => intval($payload['tarifa_total']    ?? 0),
        'dias_cobrables'      => intval($payload['dias_cobrables']  ?? 0),
        'km_libres'           => $payload['km_libres']           ?? 'Ilimitados',
        'adicionales'         => $payload['adicionales']         ?? [],
        'cobertura'           => $payload['cobertura']           ?? null,
      ],
      'pago_sena' => [
        'importe'             => intval($datos['monto']),
        'fecha'               => wp_date('Y-m-d'),
        'tipo_pago'           => $this->ccbrand_to_tipo_pago($ccbrand),
        'moneda'              => 'ARS',
        'cuotas'              => 1,
        'concepto'            => 'Seña reserva web',
        'tipo_cambio'         => 1,
        'ref_externa'         => $oid,
        'codigo_autorizacion' => $approval_code,
      ],
      'origen' => 'web',
    ];

    $response = wp_remote_post('https://aba.benvert.com.ar/api/reservas', [
      'headers' => [
        'X-API-Key'    => $api_key,
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
      ],
      'body'    => wp_json_encode($body),
      'timeout' => 20,
    ]);

    if (is_wp_error($response)) {
      error_log('[aba_reservas] POST /api/reservas error: ' . $response->get_error_message());
      return null;
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code !== 201) {
      error_log('[aba_reservas] POST /api/reservas HTTP ' . $code . ': ' . wp_remote_retrieve_body($response));
      return null;
    }

    $body_r = json_decode(wp_remote_retrieve_body($response), true);
    $id = intval($body_r['id_reserva'] ?? 0);
    return $id > 0 ? $id : null;
  }

  private function enviar_emails_reserva(int $post_id, string $oid, array $datos, string $resumen, string $approval_code): void
  {
    $site_name       = get_bloginfo('name');
    $admin_mail      = get_option('aba_reservas_email', get_option('admin_email'));
    $headers         = ['Content-Type: text/html; charset=UTF-8'];
    $monto_fmt       = '$ ' . number_format($datos['monto'], 0, ',', '.');
    $resumen_html    = nl2br(esc_html($resumen));
    $pago_anticipado = !empty($datos['pago_anticipado']);
    $tipo_pago_label = $pago_anticipado ? 'Pago anticipado (20% dto. en tarifa)' : 'Seña';

    // Email al cliente
    $subject_cliente = "Confirmación de reserva #{$post_id} — {$site_name}";
    $intro_cliente   = $pago_anticipado
      ? 'Tu pago anticipado fue procesado exitosamente. Tu reserva está confirmada.'
      : 'Tu seña fue procesada exitosamente. Tu reserva está confirmada.';
    $body_cliente = "
<p>Hola <strong>" . esc_html($datos['nombre']) . "</strong>,</p>
<p>{$intro_cliente}</p>
<table cellpadding='4' style='border-collapse:collapse;font-family:sans-serif;font-size:14px'>
  <tr><td><strong>N° de reserva</strong></td><td>#{$post_id}</td></tr>
  <tr><td><strong>Tipo de pago</strong></td><td>{$tipo_pago_label}</td></tr>
  <tr><td><strong>Monto abonado</strong></td><td>{$monto_fmt}</td></tr>"
  . ($approval_code ? "<tr><td><strong>Código aprobación</strong></td><td>{$approval_code}</td></tr>" : '') . "
</table>
" . ($resumen_html ? "<hr><p><strong>Detalle:</strong><br>{$resumen_html}</p>" : '') . "
<p>Tu reserva está confirmada. Te contactaremos para completar tus datos.<br><strong>{$site_name}</strong></p>";

    wp_mail($datos['email'], $subject_cliente, $body_cliente, $headers);

    // Email al admin
    $subject_admin = ($pago_anticipado ? '[PAGO ANTICIPADO] ' : '') . "Nueva reserva #{$post_id} — " . esc_html($datos['nombre']);
    $body_admin = "
<p>Se registró una nueva reserva" . ($pago_anticipado ? " con <strong>pago anticipado (20% dto.)</strong>." : " con seña cobrada.") . "</p>
<table cellpadding='4' style='border-collapse:collapse;font-family:sans-serif;font-size:14px'>
  <tr><td><strong>N° de reserva</strong></td><td>#{$post_id}</td></tr>
  <tr><td><strong>OID</strong></td><td>{$oid}</td></tr>
  <tr><td><strong>Tipo de pago</strong></td><td>{$tipo_pago_label}</td></tr>
  <tr><td><strong>Approval</strong></td><td>" . ($approval_code ?: '—') . "</td></tr>
  <tr><td><strong>Monto cobrado</strong></td><td>{$monto_fmt}</td></tr>
  <tr><td><strong>Cliente</strong></td><td>" . esc_html($datos['nombre']) . "</td></tr>
  <tr><td><strong>Email</strong></td><td>" . esc_html($datos['email']) . "</td></tr>
  <tr><td><strong>Teléfono</strong></td><td>" . esc_html($datos['telefono']) . "</td></tr>
</table>
" . ($resumen_html ? "<hr><p><strong>Detalle:</strong><br>{$resumen_html}</p>" : '');

    wp_mail($admin_mail, $subject_admin, $body_admin, $headers);
  }

  private function enviar_email_admin_alerta(int $post_id, string $oid, array $datos, string $resumen, string $approval_code): void
  {
    $site_name  = get_bloginfo('name');
    $admin_mail = get_option('aba_reservas_email', get_option('admin_email'));
    $headers    = ['Content-Type: text/html; charset=UTF-8'];
    $monto_fmt  = '$ ' . number_format($datos['monto'], 0, ',', '.');
    $resumen_html = nl2br(esc_html($resumen));

    $subject = "⚠️ PAGO COBRADO SIN RESERVA — {$oid} — " . esc_html($datos['nombre']);
    $body = "
<p style='color:#c0392b;font-weight:bold'>⚠️ La seña fue cobrada pero la API no pudo crear la reserva en el sistema. Procesar manualmente.</p>
<table cellpadding='4' style='border-collapse:collapse;font-family:sans-serif;font-size:14px'>
  <tr><td><strong>OID Fiserv</strong></td><td>{$oid}</td></tr>
  <tr><td><strong>Approval</strong></td><td>" . ($approval_code ?: '—') . "</td></tr>
  <tr><td><strong>Monto seña</strong></td><td>{$monto_fmt}</td></tr>
  <tr><td><strong>Cliente</strong></td><td>" . esc_html($datos['nombre']) . " " . esc_html($datos['apellido'] ?? '') . "</td></tr>
  <tr><td><strong>DNI</strong></td><td>" . esc_html($datos['dni'] ?? '') . "</td></tr>
  <tr><td><strong>Email</strong></td><td>" . esc_html($datos['email']) . "</td></tr>
  <tr><td><strong>Teléfono</strong></td><td>" . esc_html($datos['telefono']) . "</td></tr>
  <tr><td><strong>WP Post ID</strong></td><td>#{$post_id} (referencia interna)</td></tr>
</table>
" . ($resumen_html ? "<hr><p><strong>Detalle reserva:</strong><br>{$resumen_html}</p>" : '') . "
<p>Una vez creada la reserva manualmente, enviar la confirmación al cliente.</p>";

    wp_mail($admin_mail, $subject, $body, $headers);
  }

  private function obtener_cotizacion(int $id_autos, string $inicio, string $fin, int $hora_inicio, int $hora_fin, string $sucursal, ?string $sucursal_devolucion = null): array
  {
    $sucursal = $this->normalizar_sucursal($sucursal);
    // Devolución: si no se pasó (o viene vacía), es igual al retiro.
    $sucursal_dev = ($sucursal_devolucion !== null && $sucursal_devolucion !== '')
      ? $this->normalizar_sucursal($sucursal_devolucion)
      : $sucursal;
    $url = "https://aba.benvert.com.ar/api/cotizacion-detalle/{$id_autos}?" . http_build_query([
      'inicio'              => $inicio,
      'fin'                 => $fin,
      'hora_inicio'         => $hora_inicio,
      'hora_fin'            => $hora_fin,
      'sucursal_retiro'     => $sucursal,
      'sucursal_devolucion' => $sucursal_dev,
    ]);

    $response = wp_remote_get($url, [
      'headers' => [
        'X-API-Key' => get_option('aba_reservas_api_key', ''),
        'Accept'    => 'application/json',
      ],
      'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
      error_log('[aba_cotizacion] ' . $response->get_error_message());
      return ['error' => 'api_error'];
    }

    $code = wp_remote_retrieve_response_code($response);

    if ($code === 409) {
      return ['error' => 'sin_tarifa'];
    }

    if ($code !== 200) {
      return ['error' => 'api_error'];
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($body)) return ['error' => 'api_error'];

    // Prefijo dominio API en imagen del vehículo (la API devuelve rutas relativas)
    $api_base = 'https://aba.benvert.com.ar';
    if (!empty($body['vehiculo']['Imagen']) && str_starts_with($body['vehiculo']['Imagen'], '/')) {
      $body['vehiculo']['Imagen'] = $api_base . $body['vehiculo']['Imagen'];
    }

    return ['data' => $body];
  }
}
