<?php
/**
 * Crunchify Hello World Plugin is the simplest WordPress plugin for beginner.
 * Take this as a base plugin and modify as per your need.
 *
 
 *
 *            @wordpress-plugin
 *            Plugin Name: Crunchify Hello World Plugin
 *            Plugin URI: #
 *            Description: Crunchify Hello World Plugin is the simplest WordPress plugin for beginner. Take this as a base plugin and modify as per your need.
 *            Version: 3.0
 *            Author: Crunchify
 *            Author URI: https://crunchify.com/
 *            Text Domain: crunchify-hello-world
 *            Contributors: Crunchify
 *            License: GPL-2.0+
 *            License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

function crunchify_add_menu() {
	add_submenu_page("options-general.php", "Crunchify Plugin", "Crunchify Plugin", "manage_options", "crunchify-hello-world", "crunchify_hello_world_page");
}
add_action("admin_menu", "crunchify_add_menu");

function crunchify_hello_world_page()
{
?>
<div class="wrap">
	<h1>
		Hello World Plugin Template By <a
			href="https://crunchify.com/optimized-sharing-premium/" target="_blank">Crunchify</a>
	</h1>
 
	<form method="post" action="options.php">
            <?php
	settings_fields("crunchify_hello_world_config");
	do_settings_sections("crunchify-hello-world");
	submit_button();
?>
         </form>
</div>
 
<?php
}
 
function crunchify_hello_world_settings() {
	add_settings_section("crunchify_hello_world_config", "", null, "crunchify-hello-world");
	add_settings_field("crunchify-hello-world-text", "This is sample Textbox", "crunchify_hello_world_options", "crunchify-hello-world", "crunchify_hello_world_config");
	register_setting("crunchify_hello_world_config", "crunchify-hello-world-text");
}
add_action("admin_init", "crunchify_hello_world_settings");
 
function crunchify_hello_world_options() {
?>
<div class="postbox" style="width: 65%; padding: 30px;">
	<input type="text" name="crunchify-hello-world-text"
		value="<?php
	echo stripslashes_deep(esc_attr(get_option('crunchify-hello-world-text'))); ?>" />
	provideby jd sir<br />
</div>
<?php
}
add_filter('the_content', 'crunchify_com_content');
function crunchify_com_content($content) {
	return $content . stripslashes_deep(esc_attr(get_option('crunchify-hello-world-text')));
}