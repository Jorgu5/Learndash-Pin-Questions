<?php
	/**
	 * Plugin Name:     LD Pinned Questions
	 * Plugin URI:      https://nautigo.org/
	 * Description:     Plugin is used to pin questions from quizzes in Learndash.
	 * Author:          Sobolew.ski
	 * Author URI:      https://github.com/jorgu5
	 * Text Domain:     lms-pinned-questions
	 * Domain Path:     /languages
	 * Version:         0.1.0
	 *
	 * @package         LD_Pinned_Questions
	 */

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	define( 'LDPQ_PLUGIN_VERSION', '1.0.0' );
	define( 'LDPQ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'LDPQ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

	register_activation_hook( __FILE__, [ 'LD_Pinned_Questions', 'activation' ] );
	register_deactivation_hook( __FILE__, [ 'LD_Pinned_Questions', 'deactivation' ] );

	require_once LDPQ_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . 'ldpq_handler.php';

	class LD_Pinned_Questions {
		private static LD_Pinned_Questions $instance;
		private LDPQ_Handler $ldpq_handler;

		public static function get_instance(): LD_Pinned_Questions {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			spl_autoload_register( [ $this, 'autoload' ] );
			add_action( 'plugins_loaded', [ $this, 'init' ] );
		}

		public function init(): void {
			$this->ldpq_handler = LDPQ_Handler::get_instance();
		}

		public function autoload( $class ): void {
			// Convert namespace separator to directory separator.
			$class = str_replace( '\\', DIRECTORY_SEPARATOR, $class );

			// Attempt to load the class file.
			$file = LDPQ_PLUGIN_DIR . 'includes' . DIRECTORY_SEPARATOR . $class . '.php';

			if ( file_exists( $file ) ) {
				require_once( $file );
			}
		}

		public static function activation(): void {
			// Check if LearnDash is active.
			if ( ! class_exists( 'SFWD_LMS' ) ) {
				// LearnDash is not active, so deactivate your plugin.
				deactivate_plugins( plugin_basename( __FILE__ ) );
				wp_die( __( 'Sorry, but this plugin requires LearnDash to be active.', 'ldpq' ) );
			}
		}

		public static function deactivation(): void {
			// Code to run on plugin deactivation.
		}
	}

	LD_Pinned_Questions::get_instance();
