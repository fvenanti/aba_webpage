<?php

namespace Upbrands\Reservas\PublicSide;

use Kucrut\Vite;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PublicSide
{
  private const CPT_CONSULTA = 'aba_reserva_consulta';
  public function __construct()
  {
    add_shortcode('aba_reserva_form', [$this, 'shortcode_form_reserva']);
    add_shortcode('aba_reserva_resultados', [$this, 'shortcode_resultados_reserva']);

    add_action('init', [$this, 'register_cpt']);

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

    wp_localize_script(
      'main',
      'abaReservas',
      [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('aba_reservas'),
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
        'public' => true,
        'publicly_queryable' => true,
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
      'sucursal' => $params['pickup_ubicacion'],
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
    return is_array($body) ? ($body['vehiculos'] ?? []) : [];
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
}
