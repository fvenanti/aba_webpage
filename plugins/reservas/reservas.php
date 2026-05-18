<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://upbrands.agency
 * @since             1.0.0
 * @package           Reservas
 *
 * @wordpress-plugin
 * Plugin Name:       Reservas
 * Plugin URI:        https://abarentacar.com.ar/
 * Description:       Plugin de reservas de vehiculos con API.
 * Version:           1.0.0
 * Author:            Upbrands
 * Author URI:        https://upbrands.agency/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aba-reservas
 * Domain Path:       /languages
 */

if (! defined('ABSPATH')) exit;

define('RESERVAS_VERSION', '1.0.0');
define('RESERVAS_FILE', __FILE__);

// 1) Autoload local del plugin (vendor de este plugin)
$plugin_autoload = __DIR__ . '/vendor/autoload.php';

// 2) Autoload global (Bedrock u otra raíz que cargue Composer en /vendor)
$project_autoloads = [
	dirname(__DIR__, 2) . '/vendor/autoload.php', // wp-content/ -> proyecto
	dirname(__DIR__, 3) . '/vendor/autoload.php', // bedrock: web/app/plugins/mi-plugin -> project root/vendor
];

$autoload_loaded = false;

if (file_exists($plugin_autoload)) {
	require_once $plugin_autoload;
	$autoload_loaded = true;
} else {
	foreach ($project_autoloads as $candidate) {
		if (file_exists($candidate)) {
			require_once $candidate;
			$autoload_loaded = true;
			break;
		}
	}
}

if (! $autoload_loaded) {
	// Fallback amable para entornos sin Composer
	add_action('admin_notices', function () {
		echo '<div class="notice notice-error"><p><strong>Mi Plugin:</strong> No se encontró el autoload de Composer. Ejecutá <code>composer install</code>.</p></div>';
	});
	return;
}

// Bootstrap del plugin (tu clase principal)
add_action('plugins_loaded', function () {
	(new Upbrands\Reservas\Plugin())->boot();
});

if (defined('WP_CLI') && WP_CLI) {
	\Upbrands\Reservas\CLI\FetchCommand::register();
}
