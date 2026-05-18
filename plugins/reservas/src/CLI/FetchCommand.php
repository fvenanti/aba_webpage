<?php

namespace Upbrands\Reservas\CLI;

class FetchCommand
{
  public static function register(): void
  {
    if (!defined('WP_CLI') || !\WP_CLI)
      return;

    \WP_CLI::add_command('test', function ($args, $assoc_args) {
      return 'It works!';
    });
  }
}
