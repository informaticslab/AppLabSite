<?php
/**
 * @package Custom Password Protected Text
 */
/*
Plugin Name: Custom Password Protected Text
Plugin URI: http://www.marcelloscacchetti.it/en/
Description: Custom text message for password protected pages
Version: 1.0.0
Author: Marcello Scacchetti
Author URI: http://www.marcelloscacchetti.it/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define('CPPT_VERSION', '1.0.0');
define('CPPT_PLUGIN_URL', plugin_dir_url( __FILE__ ));


add_action('admin_menu', 'sampleoptions_add_page_fn');
// Add sub page to the Settings Menu
function sampleoptions_add_page_fn() {
	add_options_page('Custom Password Protected Text', 'Custom Password Protected Text', 'administrator', __FILE__, 'options_page_fn');
}

function options_page_fn() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Custom Password Protected Text Options</h2>
		Set in the field below the text you want to display on password protected pages/posts instead of the WordPress default one.
		<form action="options.php" method="post">
		<?php settings_fields('cppt_options'); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

add_action('admin_init', 'sampleoptions_init_fn' );
// Register our settings. Add the settings section, and settings fields
function sampleoptions_init_fn(){
	register_setting('cppt_options', 'cppt_options' );
	add_settings_section('main_section', 'Main Settings', 'section_text_fn', __FILE__);
	add_settings_field('plugin_wp_editor', 'Custom Password Protected Text', 'setting_wp_editor_fn', __FILE__, 'main_section');
}

function section_text_fn() {
	//echo "<h1>Section!!!</h1>";
}

function setting_wp_editor_fn() {
	$options = get_option('cppt_options');
	$settings = array(
		'tinymce' => array( 'plugins' => 'wordpress' ),
		'textarea_name' => 'cppt_options[plugin_wp_editor]' ,
		'textarea_rows' => 15
	);
	wp_editor($options['plugin_wp_editor'], 'plugin_wp_editor', $settings);
}


function replace_the_password_form($content) {
 global $post;
 $options = get_option('cppt_options');
 // if there's a password and it doesn't match the cookie
 if ( !empty($post->post_password) && stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH])!=$post->post_password ) {
   $label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
       $output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
       <p>' . $options['plugin_wp_editor'] . '</p>
       <p><label for="' . $label . '">' . __("Password:") . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr__("Submit") . '" /></p>
       </form>
       ';
   return $output;
 }
 else return $content;
}
add_filter('the_password_form','replace_the_password_form');

?>