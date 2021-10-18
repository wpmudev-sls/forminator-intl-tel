<?php
/**
 * Plugin Name: [Forminator] -Forminator intl tel input.
 * Description: [Forminator] -Forminator intl tel input.
 * Task: SLS-2731
 * Author: Thobk @ WPMUDEV
 * Author URI: https://premium.wpmudev.org
 * License: GPLv2 or later
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} elseif ( defined( 'WP_CLI' ) && WP_CLI ) {
	return;
}
add_action( 'plugins_loaded', 'wpmudev_forminator_intl_tel_func', 100 );

register_activation_hook( __FILE__, '__return_true ' );
register_deactivation_hook( __FILE__, '__return_true' );

function wpmudev_forminator_intl_tel_func() {
	if ( defined( 'FORMINATOR_PRO' ) && class_exists( 'Forminator' ) ) {
		class WPMUDEV_FM_Intl_Tel {
			public function __construct() {
				add_action( 'print_styles_array', array( $this, 'load_css' ), 2 );
				add_action( 'print_scripts_array', array( $this, 'load_js' ), 2 );
				// ajax load.
				add_filter( 'forminator_enqueue_form_styles', array( $this, 'ajax_load_css' ) );
				add_filter( 'forminator_enqueue_form_scripts', array( $this, 'ajax_load_js' ) );

				add_action( 'wp_head', array( $this, 'custom_css' ), 21 );
			}
			public function get_assets_url( $path = '' ) {
				static $asset_url;
				if ( is_null( $asset_url ) ) {
					$asset_url = plugins_url( 'assets/', __FILE__ );
				}

				return $asset_url . $path;
			}

			public function custom_css() {
				?>
				<style>
					.forminator-custom-form .iti{
						width:100%;
					}
					.forminator-custom-form .iti.iti--allow-dropdown .forminator-input{
						padding-left:51px!important;
					}
				</style>
				<?php
			}

			public function load_css( $handles ) {
				// Load int-tels.
				$style_src     = $this->get_assets_url( 'css/intlTelInput.min.css' );
				$version = '17.0.0';

				wp_dequeue_style( 'intlTelInput-forminator-css' );
				wp_enqueue_style( 'intlTelInput-forminator-css', $style_src, array(), $version ); // intlTelInput
				return $handles;
			}

			public function load_js( $handles ) {
				// Load int-tels.
				$version = '17.0.0';
				$script_src = $this->get_assets_url( 'js/intlTelInput-jquery.min.js' );
				ob_start();
				?>
				<script>
					ForminatorFront.cform.intlTelInput_utils_script = "<?php echo $this->get_assets_url( 'js/utils.js' ); ?>"
				</script>
				<?php

				wp_add_inline_script( 'forminator-front-scripts', ob_get_clean(), array() );

				wp_dequeue_style( 'forminator-intlTelInput' );
				wp_enqueue_script( 'forminator-intlTelInput', $script_src, array( 'jquery' ), $version, false ); // intlTelInput
				return $handles;
			}

			public function ajax_load_css( $styles ) {
				if ( isset( $styles['intlTelInput-forminator-css'] ) ) {
					$styles['intlTelInput-forminator-css'] = array(
						'src'  => add_query_arg( 'ver', '17.0.0', $this->get_assets_url( '/assets/css/intlTelInput.min.css' ) ),
						'on'   => '$',
						'load' => 'intlTelInput',
					);
				}
				return $styles;
			}

			public function ajax_load_js( $scripts ) {
				if ( isset( $scripts['forminator-intlTelInput'] ) ) {

					$scripts['forminator-intlTelInput'] = array(
						'src'  => add_query_arg( 'ver', '17.0.0', $this->get_assets_url( '/assets/js/intlTelInput-jquery.min.js' ) ),
						'on'   => '$',
						'load' => 'intlTelInput',
					);
				}
				return $scripts;
			}
		}
		// run.
		new WPMUDEV_FM_Intl_Tel();
	}
}
