<?php

namespace Upbrands\Obituarios\Api;

class GrupoFloresMapper
{
  /**
   * Mapea un registro crudo de la API a la estructura que espera el Importer.
   *
   * @param array $row
   * @return array
   */
  public static function mapServicio(array $row): array
  {
    $apellido = isset($row['apellido']) ? trim($row['apellido']) : '';
    $nombre = isset($row['nombre']) ? trim($row['nombre']) : '';

    $name = trim(sprintf('%s, %s', $apellido, $nombre), ' ,');

    // Fechas (ISO UTC -> zona horaria de WP)
    $start_at = self::toLocalDateTime($row['inicio_servicio'] ?? null);
    $end_at = self::toLocalDateTime($row['fin_servicio'] ?? null);

    // date_raw y date_ts a partir de inicio_servicio
    $date_raw = '';
    $date_ts = null;
    if ($start_at) {
      $ts = strtotime($start_at);
      if ($ts) {
        $date_raw = date('d-m-Y', $ts);
        // 00:00 de ese día
        $date_ts = strtotime(date('Y-m-d 00:00:00', $ts));
      }
    }

    // Ubicación del velatorio
    $partes = [];
    if (!empty($row['sucursal']))
      $partes[] = $row['sucursal'];
    if (!empty($row['sala']))
      $partes[] = $row['sala'];
    if (!empty($row['sucursal_domicilio']))
      $partes[] = $row['sucursal_domicilio'];
    if (!empty($row['sucursal_ciudad']))
      $partes[] = $row['sucursal_ciudad'];
    if (!empty($row['sucursal_provincia']))
      $partes[] = $row['sucursal_provincia'];

    $wake_location = implode(', ', array_filter($partes));

    // Texto “crudo” como fallback de contenido
    $wake_raw = self::buildWakeRaw($row, $start_at, $end_at, $wake_location);

    return [
      'external_id' => 'flores:' . ($row['servicios_id'] ?? ''),
      'name' => $name,
      'date_raw' => $date_raw,
      'date_ts' => $date_ts,
      'wake_location' => $wake_location,
      'wake_room' => $row['sala'],
      'wake_start_at' => $start_at,
      'wake_end_at' => $end_at,
      'wake_start_is_ambiguous' => 0,
      'wake_end_is_ambiguous' => 0,
      'image' => $row['imagen'],
      'source' => 'https://empresaflores.com/',
      'wake_raw' => $wake_raw,
      'own' => 1, // marca como obituario propio
    ];
  }

  /**
   * Convierte fecha ISO (UTC) a string 'Y-m-d H:i:s' en la TZ de WordPress.
   *
   * @param string|null $iso
   * @return string|null
   */
  private static function toLocalDateTime(?string $iso): ?string
  {
    $iso = trim((string) $iso);
    if ('' === $iso) {
      return null;
    }

    try {
      $dt = new \DateTime($iso, new \DateTimeZone('UTC'));
      // wp_timezone() está disponible en WP >= 5.3
      if (function_exists('wp_timezone')) {
        $dt->setTimezone(wp_timezone());
      }
      return $dt->format('Y-m-d H:i:s');
    } catch (\Exception $e) {
      return null;
    }
  }

  /**
   * Arma un texto legible para usar como contenido de fallback.
   */
  private static function buildWakeRaw(array $row, ?string $start_at, ?string $end_at, string $wake_location): string
  {
    $lineas = [];

    $apellido = isset($row['apellido']) ? trim($row['apellido']) : '';
    $nombre = isset($row['nombre']) ? trim($row['nombre']) : '';
    $edad = isset($row['edad']) ? (int) $row['edad'] : null;

    $lineaNombre = trim(sprintf('%s, %s', $apellido, $nombre), ' ,');
    if ($edad) {
      $lineaNombre .= ' (' . $edad . ' años)';
    }
    if ($lineaNombre !== '') {
      $lineas[] = $lineaNombre;
    }

    if ($wake_location) {
      $lineas[] = 'Velatorio: ' . $wake_location;
    }

    if ($start_at) {
      $ts = strtotime($start_at);
      if ($ts) {
        $lineas[] = 'Inicio del servicio: ' . date_i18n('d/m/Y H:i', $ts);
      }
    }

    if ($end_at) {
      $ts = strtotime($end_at);
      if ($ts) {
        $lineas[] = 'Fin del servicio: ' . date_i18n('d/m/Y H:i', $ts);
      }
    }

    if (!empty($row['cementerio_nombre'])) {
      $lineas[] = 'Cementerio: ' . $row['cementerio_nombre'];
    }

    return implode("\n", array_filter($lineas));
  }
}
