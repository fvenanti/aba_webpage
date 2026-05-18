<?php

namespace Upbrands\Reservas;

use Upbrands\Reservas\Infrastructure\Loader;
use Upbrands\Reservas\Infrastructure\I18n;
use Upbrands\Reservas\Admin\Admin as AdminSide;
use Upbrands\Reservas\PublicSide\PublicSide;
use Upbrands\Reservas\CPT\Obituario;
use Upbrands\Reservas\Cron\ObituariesCron;

class Plugin
{
  /**
   * @var Loader
   */
  protected Loader $loader;

  /**
   * @var string
   */
  protected string $plugin_name;

  /**
   * @var string
   */
  protected string $version;

  public function __construct()
  {
    // Mantiene compatibilidad con tu constante WPPB.
    $this->version = defined('RESERVAS_VERSION') ? RESERVAS_VERSION : '1.0.0';
    $this->plugin_name = 'reservas';

    // Con Composer ya no cargamos archivos a mano.
    $this->loader = new Loader();

    $this->set_locale();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }

  /**
   * Reemplaza al antiguo run(); engancha los hooks registrados por el Loader.
   */
  public function boot(): void
  {
    $this->loader->run();
  }

  /**
   * i18n usando una clase namespaced.
   */
  private function set_locale(): void
  {
    $i18n = new I18n($this->get_plugin_name());
    $this->loader->add_action('plugins_loaded', $i18n, 'load_plugin_textdomain');
  }

  /**
   * Hooks del admin.
   */
  private function define_admin_hooks(): void
  {
    $admin = new AdminSide();

    $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_styles');
    $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_scripts');

    // acá podés sumar más hooks del admin si hace falta…
    // $this->loader->add_action('admin_menu', $admin, 'register_menu');
  }

  /**
   * Hooks del front público.
   */
  private function define_public_hooks(): void
  {
    $public = new PublicSide();

    $this->loader->add_action('wp_enqueue_scripts', $public, 'enqueue_scripts');

    // más hooks públicos…
    // $this->loader->add_action('init', $public, 'register_cpt');
  }

  public function get_plugin_name(): string
  {
    return $this->plugin_name;
  }

  public function get_loader(): Loader
  {
    return $this->loader;
  }

  public function get_version(): string
  {
    return $this->version;
  }
}
