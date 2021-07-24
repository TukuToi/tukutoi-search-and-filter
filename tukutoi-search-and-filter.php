<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.tukutoi.com/
 * @since             1.0.0
 * @package           Tkt_search_filter
 *
 * @wordpress-plugin
 * Plugin Name:       TukuToi Search & Filter
 * Plugin URI:        https://www.tukutoi.com/program/tukutoi-search-and-filter/
 * Description:       Filter & Search WordPress Content Lists
 * Version:           1.0.1
 * Author:            TukuToi
 * Author URI:        https://www.tukutoi.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tkt-search-filter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TKT_SEARCH_FILTER_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tkt-search-filter-activator.php
 */
function activate_tkt_search_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tkt-search-filter-activator.php';
	Tkt_search_filter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tkt-search-filter-deactivator.php
 */
function deactivate_tkt_search_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tkt-search-filter-deactivator.php';
	Tkt_search_filter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tkt_search_filter' );
register_deactivation_hook( __FILE__, 'deactivate_tkt_search_filter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tkt-search-filter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tkt_search_filter() {

	$plugin = new Tkt_search_filter();
	$plugin->run();

}
run_tkt_search_filter();
