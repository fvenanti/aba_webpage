<?php

namespace Upbrands\Reservas\Infrastructure;

class I18n
{
  public function __construct(string $domain) {}

  public function load_plugin_textdomain(): void
  {
    load_plugin_textdomain(
      'aba-reservas',
      false,
      dirname(plugin_basename(\RESERVAS_FILE)) . '/languages/'
    );
  }
}
