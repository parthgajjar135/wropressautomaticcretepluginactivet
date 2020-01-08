modre detailes
<a href="https://www.monarchdigital.com/blog/2018-10-01/developing-sql-crud-ajax-functionality-wordpress-plugins">more</a></a>



Here is the full functioning pluing repo in case you'd like to reference things as you follow along: https://github.com/jah-ith-ber/hvac_contact
The concept for our example plugin is a simple one: a custom contact form.

Submissions will be validated/sanitized and saved to the database. There will be a submissions viewing page on the admin side that will have a simple AJAX 'delete' button that will remove entries without having to reload the page.

 In short we'll have a few moving parts:

    A client facing contact form
    An admin facing 'View Submissions' page
    An admin 'settings' page for updating the address the emails are sent to, just to get some experience passing variables around in a plugin.

While the plugin itself it simple, this will get you familiar with quite a few things in wordpress.
Now that we're aware of what we want to achieve, let's get started.

    Create a new folder in wp-content/plugins with a unique plugin name. In my case, we're creating a form for a mock HVAC company website, so we'll name it hvac_contact.
    Create wp-content/plugins/hvac_contact/hvac_contact.php with a plugin header at the top of the file. I've pruned quite a few things here in our example plugin, but there are best practices to follow. 
    Note the security best practice for preventing public users to directly access your file through a URL.
    Here is the completed file in case you need a reference

/*
Plugin Name: HVAC Contact Plugin
Plugin URI: https://www.monarchdigital.com
Description: Basic WordPress Plugin for a contact form.
Version: 1.0.0
Author: Michael Williams
Author URI: https://www.monarchdigital.com
*/. 

// Prevent public access
if ( !defined( 'ABSPATH' ) ) exit;

First we'll create activation and deactivation hooks, then register them so they fire when the plugin is activated/deactivated.

    Note - it's a good practice to prefix our functions with the name of our plugin to avoid global namespacing issues.
    One for our install function:
<?php
function hvac_contact_install() {
  global $wpdb;
  global $hvac_contact_version;

  $table_name = $wpdb->prefix . "hvac_contact";

  $charset_collate = $wpdb->get_charset_collate();
  $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(320) NOT NULL,
    phone VARCHAR(120) NOT NULL,
    message TEXT NOT NULL,
    time DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY (id)
  ) " . $charset_collate . ";";

  $wpdb->query( $sql );
  add_option('hvac_contact_version', $hvac_contact_version);
}
?>
    One for our uninstall function - we're just going to delete the table and remove all data if we deactivate the plugin:
<?php
function hvac_contact_uninstall() {
  global $wpdb;
  $table_name = $wpdb->prefix . "hvac_contact";
  $sql = "DROP TABLE IF EXISTS " . $table_name;
  $wpdb->query( $sql );
  delete_option('hvac_contact_version');
}
?>
    One for creating the contact page if it doesn't exist:
<?php
function hvac_contact_create_form() {
  $form = array(
    'post_title' => wp_strip_all_tags( 'HVAC Contact Page' ),
    'post_name' => 'hvac-contact-form',
    'post_status' => 'publish',
    'post_author' => 1,
    'post_type' => 'page',
  );

  // Check if page exists
  $page = get_page_by_path('hvac-contact-form');
  if ($page == NULL) {
    // Create a page upon creation.
    wp_insert_post( $form );
  }
}
?>
    And finally one for adding a mock entry to the newly created table:
<?php
function hvac_contact_install_data() {
  global $wpdb;
  $table_name = $wpdb->prefix . "hvac_contact";
  $test_name = 'Michael Williams';
  $test_email = 'mikew@monarchdigital.com';
  $test_message = 'This is my test message. My heater needs repair.';
  $test_phone = '(719) 344-2118';

  $wpdb->insert(
    $table_name,
    array(
      'name' => $test_name,
      'email' => $test_email,
      'message' => $test_message,
      'phone' => $test_phone,
      'time' => current_time( 'mysql' )
    )
  );
}
?>
    Finally, we'll register our activation/deactivation hooks so they fire when the plugin is.. well, activated and deactivated:
<?php
// Register our activation/deactivation hooks - https://developer.wordpress.org/plugins/the-basics/activation-deactivation-hooks/
register_activation_hook( __FILE__, 'hvac_contact_install' );
register_activation_hook( __FILE__, 'hvac_contact_create_form');
register_activation_hook( __FILE__, 'hvac_contact_install_data' );
register_deactivation_hook( __FILE__, 'hvac_contact_uninstall' );
?>
Next let's take care of our menu callbacks (Plugin Options page, View submissions page)

    First we'll create a folder named assets, and then another folder inside of that named templates (wp-content/plugins/hvac_contact/assets/templates).
    Inside of our templates folder, we'll create a file named hvac_contact_form.php.
    Let's go ahead and include our new template file within the main hvac_contact.php file we've been working in (if you're confused, have a look at the repo).
    <?php
/* - wp-content/plugins/hvac_contact/hvac_contact.php - */

// Hook up our template file for the contact form - https://codex.wordpress.org/Plugin_API/Filter_Reference/page_template

add_filter( 'page_template', 'hvac_contact_form_template' );

function hvac_contact_form_template( $page_template ) {
  if ( is_page( 'HVAC Contact Page' ) ) {
    $page_template = dirname( __FILE__ ) . '/assets/templates/hvac_contact_form.php';
  }
  return $page_template;
}
?>

    With the template file now being included, we can create our contact form. Open wp-content/plugins/hvac_content/assets/templates/hvac_contact_form.php.
    First let's create the html and css for the form.
<?php
/* - Add google's recaptcha - */
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div class="wrap">
  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
      <style type="text/css">
      .error{
        padding: 5px 9px;
        border: 1px solid red;
        color: red;
        border-radius: 3px;
      }
      .success{
        padding: 5px 9px;
        border: 1px solid green;
        color: green;
        border-radius: 3px;
      }
      form span{
        color: red;
      }
     </style>

     <div id="respond">
       <?php echo $response; ?>
       <form action="/wp-admin/admin-post.php" method="post">
         <p><label for="name">Name: <span>*</span> <br><input type="text" name="message_name" value="<?php echo esc_attr($_POST['message_name']); ?>"></label></p>
         <p><label for="message_email">Email: <span>*</span> <br><input type="text" name="message_email" value="<?php echo esc_attr($_POST['message_email']); ?>"></label></p>
         <p><label for="message_phone">Phone: <span>*</span> <br><input type="text" name="message_phone"></label></p>
         <p><label for="message_text">Message: <span>*</span> <br><textarea type="text" name="message_text"><?php echo esc_textarea($_POST['message_text']); ?></textarea></label></p>
         <div class="g-recaptcha" data-sitekey="mygoogleapirecaptchasitekey"></div>
         <input type="hidden" name="action" value="hvac_contact_submit">
         <p><input type="submit"></p>
       </form>
      </div>
    </main>
  </div>
</div>
?>
    Now we'll handle the form submissions back in hvac_contact.php
    First we'll add our callback actions that will handle the post request for admin/non-admins
<?php
add_action( 'admin_post_hvac_contact_submit', 'hvac_contact_submit' ); 
add_action( 'admin_post_nopriv_hvac_contact_submit', 'hvac_contact_submit' );
?>
    Then we'll actually create our hvac_contact_submit callback and a function to handle our database entry name hvac_contact_create_entry:
