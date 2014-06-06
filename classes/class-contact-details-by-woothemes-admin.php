<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Contact_Details_by_WooThemes_Admin Class
 *
 * @class Contact_Details_by_WooThemes_Admin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Contact_Details_by_WooThemes
 * @author Jeffikus
 */
final class Contact_Details_by_WooThemes_Admin {
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		// Register the settings with WordPress.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		// Register the settings screen within WordPress.
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );
	} // End __construct()

	/**
	 * Register the admin screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings_screen () {
		$this->_hook = add_submenu_page( 'options-general.php', __( 'Contact Details Settings', 'contact-details-by-woothemes' ), __( 'Contact Details', 'contact-details-by-woothemes' ), 'manage_options', 'contact-details-by-woothemes', array( $this, 'settings_screen' ) );
	} // End register_settings_screen()

	/**
	 * Output the markup for the settings screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function settings_screen () {
		global $title;
?>
		<div class="wrap contact-details-by-woothemes-wrap">
			<h2><?php echo $title; ?></h2>
			<form action="options.php" method="post">
				<?php
					settings_fields( 'contact-details-by-woothemes-settings' );
					do_settings_sections( 'contact-details-by-woothemes' );
					submit_button( __( 'Save Changes', 'contact-details-by-woothemes' ) );
				?>
			</form>
		</div><!--/.wrap-->
<?php
	} // End settings_screen()

	/**
	 * Register the settings within the Settings API.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings () {
		// Register the setting we'll use to store our information.
		register_setting( 'contact-details-by-woothemes-settings', 'contact-details-by-woothemes', array( $this, 'validate_settings' ) );

		// Register settings sections.
		$sections = Contact_Details_by_WooThemes()->settings->get_settings_sections();

		if ( 0 < count( $sections ) ) {
			foreach ( $sections as $k => $v ) {
				add_settings_section( $k, $v, array( $this, 'render_settings' ), 'contact-details-by-woothemes' );
			}
		}
	} // End register_settings()

	/**
	 * Render the settings.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function render_settings ( $args ) {
		$fields = Contact_Details_by_WooThemes()->settings->get_settings_fields();

		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				$args = $v;
				$args['id'] = $k;
				add_settings_field( $k, $v['name'], array( Contact_Details_by_WooThemes()->settings, 'render_field' ), 'contact-details-by-woothemes', $v['section'], $args );
			}
		}
	} // End render_settings()

	/**
	 * Validate the settings.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $input Inputted data.
	 * @return  array        Validated data.
	 */
	public function validate_settings ( $input ) {
		return Contact_Details_by_WooThemes()->settings->validate_settings( $input );
	} // End validate_settings()
} // End Class
?>
