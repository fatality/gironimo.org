<?php
/**
 * @package Akismet Privacy Policies
 */
/*
Plugin Name: Akismet Privacy Policies
Plugin URI: http://wordpress-deutschland.org/
Description: Ergänzt das Kommentarformular um datenschutzrechtliche Hinweise bei Nutzung des Plugins Akismet.
Version: 1.1.0
Author: Inpsyde GmbH
Author URI: http://inpsyde.com/
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class Akismet_Privacy_Policies {
	
	static private $classobj;
	
	// default for active checkbox on comment form
	public $checkbox = 1;
	// default for nitoce on comment form
	public $notice = '<strong>Achtung:</strong> Ich erkläre mich damit einverstanden, dass alle 
	eingegebenen Daten und meine IP-Adresse nur zum Zweck der Spamvermeidung durch das Programm 
	<a href="http://akismet.com/">Akismet</a> in den USA überprüft und gespeichert werden.<br />
	<a href="[LINK ZU DER DATENSCHUTZERKLÄRUNG EINSETZEN]">Weitere Informationen zu Akismet und Widerrufsmöglichkeiten</a>.';
	// default for error message, if checkbox is not active on comment form
	public $error_message = '<p><strong>Achtung:</strong> 
	Du hast die datenschutzrechtlichen Hinweise nicht akzeptiert.</p>';
	// default style to float checkbox
	public $style = 'input#akismet_privacy_check { float: left; margin: 7px 7px 7px 0; width: 13px; }';
	
	/**
	 * construct
	 * 
	 * @uses add_filter
	 * @access public
	 * @since 0.0.1
	 * @return void
	 */
	public function __construct() {
		
		register_deactivation_hook( __FILE__,	array( &$this, 'unregister_settings' ) );
		register_uninstall_hook( __FILE__,		array( 'Akismet_Privacy_Policies', 'unregister_settings' ) );
		
		add_filter( 'comment_form_defaults',		array( $this, 'add_comment_notice' ), 11, 1 );
		add_action( 'akismet_privacy_policies',	array( $this, 'add_comment_notice' ) );
		
		$options = get_option( 'akismet_privacy_notice_settings' );
		if ( empty( $options['checkbox'] ) )
			$options['checkbox'] = $this->checkbox;
		if ( $options['checkbox'] )
			add_action( 'pre_comment_on_post',	array( $this, 'error_message' ) );
		if ( !isset($options['style']) )
			$options['style'] = $this->style;
		if ( $options['style'] )
			add_action( 'wp_head',				array( $this, 'add_style' ) );
			
		// for settings
		add_action( 'admin_menu',				array( $this, 'add_settings_page' ) );
		add_filter( 'plugin_action_links',		array( $this, 'plugin_action_links' ), 10, 2 );
		add_action( 'admin_init',				array( $this, 'register_settings' ) );
	}
	
	/**
	 * Handler for the action 'init'. Instantiates this class.
	 *
	 * @since 0.0.2
	 * @access public
	 * @return $classobj
	 */
	public function get_object() {
		if ( null === self::$classobj ) {
			self::$classobj = new self;
		}
	
		return self::$classobj;
	}
	
	/**
	 * return plugin comment data
	 * 
	 * @since 0.0.2
	 * @access public
	 * @param $value string, default = 'Version'
	 *		Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
	 * @return string
	 */
	public function get_plugin_data( $value = 'Version' ) {
		
		$plugin_data  = get_plugin_data( __FILE__ );
		$plugin_value = $plugin_data[$value];
		
		return $plugin_value;
	}
	
	/**
	 * return content for policies inlcude markup
	 * use filter hook akismet_privacy_notice_options for change markup or notice
	 * 
	 * @access public
	 * @uses apply_filters
	 * @since 0.0.1
	 * @param array string $arr_comment_defaults
	 * @return array | string $arr_comment_defaults or 4html
	 */
	public function add_comment_notice($arr_comment_defaults) {
		
		if ( is_user_logged_in() )
			return $arr_comment_defaults;
		
		$options = get_option( 'akismet_privacy_notice_settings' );
		if ( ! isset( $options['checkbox'] ) || empty( $options['checkbox'] ) && 0 != $options['checkbox'] )
			$options['checkbox'] = $this->checkbox;
		if ( empty( $options['notice'] ) )
			$options['notice'] = $this->notice;
		
		$defaults = array(
			  'css_class'    => 'privacy-notice'
			, 'html_element' => 'p'
			, 'text'         => $options['notice']
			, 'checkbox'     => $options['checkbox']
			, 'position'     => 'comment_notes_after'
		);
		
		// Make it filterable
		$params = apply_filters( 'akismet_privacy_notice_options', $defaults );
		
		// Create the output
		$html  = "\n" . '<' . $params['html_element'];
		if ( !empty( $params['css_class'] ) )
			$html .= ' class="' . $params['css_class'] . '"';
		$html .= '>' . "\n";
		if ( (bool) $params['checkbox'] ) {
			$html .= '<input type="checkbox" id="akismet_privacy_check" name="akismet_privacy_check" value="1" aria-required="true" />' . "\n";
			$html .= '<label for="akismet_privacy_check">';
		}
		$html .= $params['text'];
		if ( (bool) $params['checkbox'] )
			$html .= '</label>';
		$html .='</' . $params['html_element'] . '>' . "\n";
		
		// Add the text to array
		if ( isset($arr_comment_defaults['comment_notes_after']) ) {
			$arr_comment_defaults['comment_notes_after'] .= $html;
			return $arr_comment_defaults;
		} else { // for custom hook in theme
			$arr_comment_defaults = $html;
			echo $arr_comment_defaults;
		}
	}
	
	/**
	 * Return Message on inactive checkbox
	 * Use filter akismet_privacy_error_message for change text or markup
	 * 
	 * @uses wp_die
	 * @access public
	 * @since 0.0.2
	 * @return void
	 */
	public function error_message() {
		
		if ( is_user_logged_in() )
			return NULL;
		
		$options = get_option( 'akismet_privacy_notice_settings' );
		if ( empty( $options['error_message'] ) )
			$options['error_message'] = $this->error_message;
		
		// check for checkbox active
		if ( isset( $_POST['comment'] ) && ( ! isset( $_POST['akismet_privacy_check'] ) ) ) {
			$message = apply_filters( 'akismet_privacy_error_message', $options['error_message'] );
			wp_die( $message );
		}
	}
	
	/**
	 * Echo style in wp_head
	 * 
	 * @uses get_option, plugin_action_links, plugin_basename
	 * @access public
	 * @since 0.0.2
	 * @return string $links
	 */
	public function add_style() {
		
		if ( is_user_logged_in() )
			return NULL;
		
		$options = get_option( 'akismet_privacy_notice_settings' );
		if ( empty( $options['style'] ) )
			$options['style'] = $this->style;
		
		echo '<style type="text/css" media="screen">' . $options['style'] . '</style>';
	}
	
	/**
	 * Add settings link on plugins.php in backend
	 * 
	 * @uses plugin_basename
	 * @access public
	 * @param array $links, string $file
	 * @since 0.0.2
	 * @return string $links
	 */
	public function plugin_action_links( $links, $file ) {
		if ( plugin_basename( dirname(__FILE__).'/akismet-privacy-policies.php' ) == $file  ) {
			$links[] = '<a href="options-general.php?page=akismet_privacy_notice_settings_group">' . __('Settings') . '</a>';
		}
	
		return $links;
	}
	
	/**
	 * Add settings page in WP backend
	 * 
	 * @uses add_options_page
	 * @access public
	 * @since 0.0.2
	 * @return void
	 */
	public function add_settings_page() {
		
		add_options_page( 
			'Akismet Privacy Policies Settings', 
			'Akismet Privacy Policies', 
			'manage_options', 
			'akismet_privacy_notice_settings_group', 
			array( $this, 'get_settings_page' )
		);
		
		add_action( 'contextual_help', array( $this, 'contextual_help' ), 10, 3 );
	}
	
	/**
	 * Return form and markup on settings page
	 * 
	 * @uses settings_fields, normalize_whitespace
	 * @access public
	 * @since 0.0.2
	 * @return void
	 */
	public function get_settings_page() {
	?>
	<div class="wrap">
	<h2><?php echo $this->get_plugin_data('Name'); ?></h2>
	
	<form method="post" action="options.php">
		<?php
		settings_fields( 'akismet_privacy_notice_settings_group' );
		$options = get_option( 'akismet_privacy_notice_settings' );
		if ( ! isset($options['checkbox']) || empty( $options['checkbox'] ) && 0 != $options['checkbox'] )
			$options['checkbox'] = $this->checkbox;
		if ( empty( $options['notice'] ) )
			$options['notice'] = normalize_whitespace( $this->notice );
		if ( empty( $options['error_message'] ) )
			$options['error_message'] = normalize_whitespace( $this->error_message );
		if ( empty( $options['style'] ) )
			$options['style'] = normalize_whitespace( $this->style );
		?>
		
		<table class="form-table">
			<tr valign="top">
				<td scope="row"><label for="akismet_privacy_checkbox">Aktives Prüfen via Checkbox</label></td>
				<td><input type="checkbox" id="akismet_privacy_checkbox" name="akismet_privacy_notice_settings[checkbox]" value="1" 
					<?php if ( isset( $options['checkbox'] ) ) checked( '1', $options['checkbox'] ); ?> />
				</td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="akismet_privacy_notice">Datenschutzrechtlicher Hinweis</label></td>
				<td><textarea id="akismet_privacy_notice" name="akismet_privacy_notice_settings[notice]" cols="80" rows="10" 
					aria-required="true" ><?php if ( isset($options['notice']) ) echo $options['notice']; ?></textarea>
					<br /><strong>Hinweis:</strong> HTML möglich
					<br /><strong>Achtung:</strong> Im Hinweistext musst du manuell den Link zu deiner Datenschutzerklärung einfügen. Einen Mustertext für die Datenschutzerklärung findest du im Reiter "Hilfe", rechts oben auf dieser Seite.

					<br /><strong>Beispiel:</strong> <?php echo esc_html( $this->notice ); ?>
				</td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="akismet_privacy_error_message">Fehler-Hinweis</label></td>
				<td><textarea id="akismet_privacy_error_message" name="akismet_privacy_notice_settings[error_message]" cols="80" 
					rows="10" aria-required="true" ><?php if ( isset($options['error_message']) ) echo $options['error_message']; ?></textarea>
					<br /><strong>Hinweis:</strong> HTML möglich
					<br /><strong>Beispiel:</strong> <?php echo esc_html( $this->error_message ); ?>
				</td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="akismet_privacy_style">Stylesheet</label></td>
				<td><textarea id="akismet_privacy_style" name="akismet_privacy_notice_settings[style]" cols="80" 
					rows="10" aria-required="true" ><?php if ( isset($options['style']) ) echo $options['style']; ?></textarea>
					<br /><strong>Hinweis:</strong> CSS notwendig
					<br /><strong>Beispiel:</strong> <?php echo esc_html( $this->style ); ?>
				</td>
			</tr>
		</table>
	
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
		<p>Weitere Informationen zum Thema findest du in <a href="http://faq.wordpress-deutschland.org/hinweise-zum-datenschutz-beim-einsatz-von-akismet-in-deutschland/">der WordPress Deutschland FAQ</a>. Dieses Plugin wurde entwickelt von der <a href="http://inpsyde.com/" title="Besuch die Homepage der Inpsyde GmbH">Inpsyde GmbH</a> mit rechtlicher Unterstützung durch die Rechtsanwaltskanzlei <a href="http://spreerecht.de/" title="Besuch die Homepage der Kanzlei Schwenke und Dramburg">SCHWENKE &amp; DRAMBURG.</a></p>

	</form>
	</div>
	<?php
	}
	
	/**
	 * Validate settings for options
	 * 
	 * @uses normalize_whitespace
	 * @access public
	 * @param array $value
	 * @since 0.0.2
	 * @return string $value
	 */
	public function validate_settings( $value ) {
		
		if ( isset($value['checkbox']) && 1 == $value['checkbox'] )
			$value['checkbox'] = 1;
		else 
			$value['checkbox'] = 0;
		$value['notice']          = normalize_whitespace( $value['notice'] );
		$value['error_message']   = normalize_whitespace( $value['error_message'] );
		$value['style']           = normalize_whitespace( $value['style'] );
		
		return $value;
	}
	
	/**
	 * Register settings for options
	 * 
	 * @uses register_setting
	 * @access public
	 * @since 0.0.2
	 * @return void
	 */
	public function register_settings() {
		
		register_setting( 'akismet_privacy_notice_settings_group', 'akismet_privacy_notice_settings', array( $this, 'validate_settings' ) );
	}
	
	/**
	 * Unregister and delete settings; clean database
	 * 
	 * @uses unregister_setting, delete_option
	 * @access public
	 * @since 0.0.2
	 * @return void
	 */
	public function unregister_settings() {
		
		unregister_setting( 'akismet_privacy_notice_settings_group', 'akismet_privacy_notice_settings' );
		delete_option( 'akismet_privacy_notice_settings' );
	}
	
	/**
	 * Add help text
	 * 
	 * @uses normalize_whitespace
	 * @param string $contextual_help
	 * @param string $screen_id
	 * @param string $screen
	 * @since 0.0.2
	 * @return string $contextual_help
	 */
	public function contextual_help($contextual_help, $screen_id, $screen) {
			
			if ( 'settings_page_akismet_privacy_notice_settings_group' !== $screen_id )
				return $contextual_help;
			
			$contextual_help = 
				'<p>' . __( 'Das Plugin ergänzt das Kommentarformular um datenschutzrechtliche Hinweise, 
				die erforderlich sind, wenn du das Plugin Akismet einsetzt.' ) . '</p>'
				. '<ul>'
				. '<li>' . __( 'Du kannst diverse Einstellungen vornehmen, nutze dazu die Möglichkeiten innerhalb der Einstellungen.' ) . '</li>'
				. '<li>' . __( 'Eingeloggte Anwender sehen den Hinweis am Kommentarformular nicht.' ) . '</li>'
				. '<li><strong>' . __( 'Im Hinweistext musst du den Link zu deiner Datenschutzerklärung manuell einfügen.' ) . '</strong></li>'
				. '<li>' . __( 'Für die Datenschutzerklärung kannst du folgende Vorlage verwenden: <br/>
<code>&lt;strong&gt;Akismet Anti-Spam&lt;/strong&gt;
Diese Seite nutzt das&nbsp;&lt;a href="http://akismet.com/"&gt;Akismet</a>-Plugin der&nbsp;&lt;a href="http://automattic.com/"&gt;Automattic&lt;/a&gt; Inc., 60 29th Street #343, San Francisco, CA 94110-4929, USA. Mit Hilfe dieses Plugins werden Kommentare von echten Menschen von Spam-Kommentaren unterschieden. Dazu werden alle Kommentarangaben an einen Server in den USA verschickt, wo sie analysiert und für Vergleichszwecke vier Tage lang gespeichert werden. Ist ein Kommentar als Spam eingestuft worden, werden die Daten über diese Zeit hinaus gespeichert. Zu diesen Angaben gehören der eingegebene Name, die Emailadresse, die IP-Adresse, der Kommentarinhalt, der Referrer, Angaben zum verwendeten Browser sowie dem Computersystem und die Zeit des Eintrags. Sie können gerne Pseudonyme nutzen, oder auf die Eingabe des Namens oder der Emailadresse verzichten. Sie können die Übertragung der Daten komplett verhindern, in dem Sie unser Kommentarsystem nicht nutzen. Das wäre schade, aber leider sehen wir sonst keine Alternativen, die ebenso effektiv arbeiten. Sie können der Nutzung Ihrer Daten für die Zukunft unter&nbsp;&lt;a href="mailto:support@wordpress.com" target="_blank"&gt;support@wordpress.com&lt;/a&gt;, Betreff “Deletion of Data stored by Akismet” unter Angabe/Beschreibung der gespeicherten Daten&nbsp;widersprechen.</code>' ) . '</li>'
				. '<li>' . __( 'Weitere Informationen zum Thema findest du in <a href="http://faq.wordpress-deutschland.org/hinweise-zum-datenschutz-beim-einsatz-von-akismet-in-deutschland/">diesem Artikel der WordPress Deutschland FAQ</a>' ) . '</li>'
				. '<li>' . __( 'Dieses Plugin wurde entwickelt von der <a href="http://inpsyde.com/" title="Besuch die Homepage der Inpsyde GmbH">Inpsyde GmbH</a> mit rechtlicher Unterstützung durch die Rechtsanwaltskanzlei <a href="http://spreerecht.de/" title="Besuch die Homepage der Kanzlei Schwenke und Dramburg">SCHWENKE &amp; DRAMBURG</a>.') . '</li>'
				. '</ul>';
			
			return normalize_whitespace( $contextual_help );
		}
	
} // end class

if ( function_exists('add_action') && class_exists('Akismet_Privacy_Policies') ) {
	add_action( 'plugins_loaded', array( 'Akismet_Privacy_Policies', 'get_object' ) );
} else {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
?>
