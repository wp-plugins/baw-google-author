<?php
/*
Plugin Name: BAW Google Author
Plugin URI: http://www.boiteaweb.fr/2929
Description: Add a meta tag AUTHOR for SEO
Version: 1.0
Author: Juliobox
Author URI: http://www.BoiteaWeb.fr
License: GPLv2
*/

DEFINE( 'BAWGA_PLUGIN_URL', trailingslashit( WP_PLUGIN_URL ) . basename( dirname( __FILE__ ) ) );
DEFINE( 'BAWGA_PLUGIN_NAME', 'Google Author' );

function bawga_l10n_init()
{
  load_plugin_textdomain( 'baw_ga', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
add_action( 'init','bawga_l10n_init' );

function bawga_register_settings()
{
  register_setting( 'bawga-settings-group', 'bawga_authorname', 'esc_attr' );
  register_setting( 'bawga-settings-group', 'bawga_authorstyle', 'esc_attr' );
}
add_action( 'admin_init', 'bawga_register_settings' );

function bawga_create_menu()
{
if ( !defined( 'BAW_MENU' ) ) {
  define( 'BAW_MENU', true );
  add_menu_page( 'BoiteAWeb.fr', 'BoiteAWeb', 'manage_options', 'baw_menu', 'baw_about', plugins_url('/images/icon.png', __FILE__) );
}
  add_submenu_page( 'baw_menu', BAWGA_PLUGIN_NAME, BAWGA_PLUGIN_NAME, 'install_plugins', 'baw_ga_config', 'bawga_page' );
}
add_action( 'admin_menu', 'bawga_create_menu' );

function bawga_page() {
  global $wpdb;
?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div>
<h2><?php echo BAWGA_PLUGIN_NAME; ?></h2>
<?php
if ( isset( $_GET['settings-updated'] ) ) {
  echo '<div class="updated"><p><strong>' . BAWGA_PLUGIN_NAME . ' : </strong> ' . __( 'Settings updated', 'baw_ga' ) .'</p></div>'; }
?>
<form method="post" action="options.php" id="bawga_form">

    <table class="form-table">
        <tr valign="top">
        <th scope="row"><h3><?php _e( 'META author style', 'baw_ga' ); ?></h3></th>
        <td>
        <p><label><input type="radio" name="bawga_authorstyle" value="name" <?php checked( get_option( 'bawga_authorstyle' ), 'name' ); ?> /> <input type="text" name="bawga_authorname" value="<?php echo esc_attr( get_option( 'bawga_authorname' ) ); ?>" /></label><br />
        <em><?php _e( 'Always use this name. Will be used too on every non post/page (index, 404, search, ...)', 'baw_ga' ); ?></em></p>
        <p><label><input type="radio" name="bawga_authorstyle" value="auto" <?php checked( get_option( 'bawga_authorstyle' ), 'auto' ); ?> /> Auto</label><br />
        <em><?php _e( 'Use the real author from post/page.', 'baw_ga' ); ?></em></p>
        </td>
        </tr>
    </table>

    <p class="submit bawasfleft">
    <input type="submit" tabindex="32767" class="button-primary" value="<?php _e( 'Save Changes', 'baw_ga' ); ?>" />
    </p>
    <?php settings_fields( 'bawga-settings-group' ); ?>

</form>

<?php
}

if ( !function_exists( 'baw_about' ) )
{
  function baw_about() {
    include( 'about.php' );
  }
}

function bawga_meta_author()
{
  global $post;
  $user_info = get_userdata($post->post_author);
  $author = $user_info->user_nicename;
  $author = $author != '' && get_option( 'bawga_authorstyle' ) == 'auto' && ( is_single() || is_page() )? $author : esc_attr( get_option( 'bawga_authorname' ) );
  echo '<meta rel="author" value="' . $author . '" />' . "\n";
}
add_action( 'wp_head', 'bawga_meta_author' );

function bawga_settings_action_links( $links, $file )
{
  if ( strstr( __FILE__, $file ) != '' ) {
   $settings_link = '<a href="' . admin_url( 'admin.php?page=baw_ga_config' ) . '">' . __( 'Settings', 'baw_ga' ) . '</a>';
   array_unshift( $links, $settings_link );
  }
  return $links;
}
add_filter( 'plugin_action_links', 'bawga_settings_action_links', 10, 2 );

function bawga_default_values()
{
  global $current_user;
  $user_login = $current_user->user_nicename;
  add_option( 'bawga_authorname', $user_login );
  add_option( 'bawga_authorstyle', 'auto' );
}
register_activation_hook( __FILE__, 'bawga_default_values' );

function bawga_uninstaller(){
  global $wpdb;
  delete_option( 'bawga_authorname' );
  delete_option( 'bawga_authorstyle' );
}
register_uninstall_hook( __FILE__, 'bawga_uninstaller' );

?>