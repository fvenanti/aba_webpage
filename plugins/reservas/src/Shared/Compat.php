<?php
// Carga de polyfills globales. PHP 8.0 carece de array_is_list.

if (!function_exists('array_is_list')) {
  /**
   * Polyfill de array_is_list para PHP < 8.1
   * Devuelve true si el array tiene claves 0..n-1 secuenciales.
   */
  function array_is_list(array $array): bool
  {
    $i = 0;
    foreach ($array as $k => $_) {
      if ($k !== $i) return false;
      $i++;
    }
    return true;
  }
}
