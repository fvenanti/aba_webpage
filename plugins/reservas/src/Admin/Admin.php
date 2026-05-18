<?php

namespace Upbrands\Reservas\Admin;

class Admin
{
  public function __construct() {}

  public function enqueue_styles(): void
  {
    // wp_enqueue_style( "{$this->plugin_name}-admin", plugins_url('admin/css/admin.css', \RESERVAS_FILE), [], $this->version );
  }

  public function enqueue_scripts(): void
  {
    // wp_enqueue_script( "{$this->plugin_name}-admin", plugins_url('admin/js/admin.js', \RESERVAS_FILE), ['jquery'], $this->version, true );
  }
}
