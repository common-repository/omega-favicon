<?php
/*
 * Plugin Name: Omega Favicon
 * Plugin URI: http://themehall.com/product/omega-favicon
 * Description: Simply add favicon to your Omega Powered site and the WordPress admin
 * Version: 1.0
 * Author: ThemeHall
 * Author URI: http://themehall.com/about
 *
 * @package Omega
 * @subpackage Functions
 * @author ThemeHall <hello@themehall.com>
 * @copyright Copyright (c) 2013, themehall.com
 * @link http://themehall.com/product/omega-favicon
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

register_activation_hook( __FILE__, 'omega_favicon_activation_check' );

/**
 * Checks for activated Omega Theme before allowing plugin to activate.
 *
 * @since 1.0
 *
 * @uses  get_template_directory()
 * @uses  deactivate_plugins()
 * @uses  wp_die()
 */
function omega_favicon_activation_check() {

	/** Check for activated Omega Theme (= template/parent theme) */
	if ( basename( TEMPLATEPATH ) != 'omega' ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
        wp_die( sprintf( __( 'Sorry, you can\'t activate %1$s plugin unless you have installed  %2$sOmega Theme%3$s', 'omega-favicon' ), 'Omega Favicon', '<a href="http://themehall.com/omega" target="_new"><strong><em>', '</em></strong></a>' ) );
    }

}  // end of function genesis_favicon_activation_check

/**
 * Omega Favicon Class
 *
 * @since 1.0
 */

class Omega_Favicon {

	/**
	 * Initializes the plugin by setting filters, and administration functions.
	 */
	function __construct() {

		// Adding Plugin Menu
		add_action( 'admin_menu', array( &$this, 'omega_favicon_menu' ) );		

		// Add Favicon to wp front
		add_action( 'wp_head', array( &$this, 'omega_favicon_display' ) );

		// Add Favicon to wp admin
		add_action( 'admin_head', array( &$this, 'omega_favicon_display' ) );
		add_action( 'login_head', array( &$this, 'omega_favicon_display' ) );

	} // end constructor


	function omega_favicon_menu()
	{
		global $theme_settings_page;

		/* Get the theme prefix. */
		$prefix = hybrid_get_prefix();

		/* Create a settings meta box only on the theme settings page. */
		add_action( 'load-appearance_page_theme-settings', array( &$this, 'omega_favicon_theme_settings') );

		/* Sanitize the scripts settings before adding them to the database. */
		add_filter( "sanitize_option_{$prefix}_theme_settings", array( &$this, 'omega_favicon_theme_validate') );

		// Enqueue styles and script
    	add_action( 'admin_enqueue_scripts', array( &$this, 'omega_favicon_assets' ) );

		/* Adds my_help_tab when my_admin_page loads */
	    add_action('load-'.$theme_settings_page, array( &$this, 'omega_favicon_help') );

	}	//omega_favicon_menu


	/**
	 * Add omega favicon meta box to the theme settings page in the admin.
	 *
	 * @since 1.0
	 * @return void
	 */
	function omega_favicon_theme_settings() {

		add_meta_box( 
			'omega-theme-favicon', 
			__( 'Favicon', 'omega-favicon' ), 
			array( &$this, 'omega_favicon_meta_box'), 
			'appearance_page_theme-settings', 'normal', 'high' );

	}

	/**
	 * Callback for Theme Settings Post Archives meta box.
	 */
	function omega_favicon_meta_box() {
	?>
		<p>
			<label for="<?php echo hybrid_settings_field_id( 'favicon_url' ); ?>"><?php _e( 'Favicon URL:', 'omega-favicon' ); ?></label> 
			<input type="text" name="<?php echo hybrid_settings_field_name( 'favicon_url' ); ?>" id="<?php echo hybrid_settings_field_id( 'favicon_url' ); ?>" value="<?php echo esc_attr( hybrid_get_setting( 'favicon_url' ) ); ?>" size="50" />
			<input type='button' id='<?php echo hybrid_settings_field_id( 'favicon_url' ); ?>_button' class='button button-upload' value='Upload'/> 
	        <?php 
	        if (hybrid_get_setting( 'favicon_url' )) {
	        ?>
	        <img style='max-height: 25px;vertical-align: middle;' src='<?php echo esc_attr( hybrid_get_setting( 'favicon_url' ) ); ?>' class='preview-upload' />
			<?php
		    }
		    ?>
		</p>
	<?php }


	/**
	 * Saves the scripts meta box settings by filtering the "sanitize_option_{$prefix}_theme_settings" hook.
	 *
	 * @since 1.0
	 * @param array $settings Array of theme settings passed by the Settings API for validation.
	 * @return array $settings
	 */
	function omega_favicon_theme_validate( $settings ) {

		/* Return the theme settings. */
		return $settings;
	}

	/**
	 * Contextual help content.
	 */
	function omega_favicon_help() {

		$screen = get_current_screen();

		$archives_help =
			'<h3>' . __( 'Favicon', 'omega-favicon' ) . '</h3>' .
			'<p>'  . __( 'Add a Favicon to your site and the WordPress admin. Put favicon URL into the textbox or Click the Upload button and upload favicon file', 'omega-favicon' ) . '</p>' .

		$screen->add_help_tab( array(
			'id'      => 'omega-settings' . '-archives',
			'title'   => __( 'Content Archives', 'omega-favicon' ),
			'content' => $archives_help,
		) );

	}


	/* Enqueue scripts (and related stylesheets) */
	function omega_favicon_assets($hook_suffix) {
	    global $theme_settings_page;

		if ( $theme_settings_page == $hook_suffix ) {

			
			wp_enqueue_media();

	        wp_register_script('omega_favicon', WP_PLUGIN_URL.'/omega-favicon/js/omega-favicon.js');
	        wp_enqueue_script('omega_favicon');
	    }
	}


	/* Load Favicon to website frontend */
	function omega_favicon_display() {

		if( "" != hybrid_get_setting( 'favicon_url' ) ) {
	        echo '<link rel="shortcut icon" href="'.  esc_attr( hybrid_get_setting( 'favicon_url' ) )  .'"/>'."\n";
	    }
	}


} // End Class


// Initiation call of plugin
$omega_fav = new Omega_Favicon();

?>