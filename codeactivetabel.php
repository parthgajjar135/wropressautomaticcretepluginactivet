$your_db_name = $wpdb-&gt;prefix . 'your_db_name';
 
// function to create the DB / Options / Defaults					
function your_plugin_options_install() {
   	global $wpdb;
  	global $your_db_name;
 
	// create the ECPT metabox database table
	if($wpdb-&gt;get_var("show tables like '$your_db_name'") != $your_db_name) 
	{
		$sql = "CREATE TABLE " . $your_db_name . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`field_1` mediumtext NOT NULL,
		`field_2` tinytext NOT NULL,
		`field_3` tinytext NOT NULL,
		`field_4` tinytext NOT NULL,
		UNIQUE KEY id (id)
		);";
 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
 
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'your_plugin_options_install');