<?php
/*
Plugin Name: BAW Google Author
Plugin URI: http://www.boiteaweb.fr/?p=2929
Description: Add a meta tag AUTHOR for SEO
Version: 1.2
Author: Juliobox
Author URI: http://wp-rocket.me
License: GPLv2
*/

function bawga_l10n_init()
{
  load_plugin_textdomain( 'baw_ga', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
add_action( 'init', 'bawga_l10n_init' );

function bawga_register_settings()
{
	register_setting( 'bawga-settings-group', 'bawga_authorname', 'esc_attr' );
	register_setting( 'bawga-settings-group', 'bawga_authorstyle', 'esc_attr' );
}
add_action( 'admin_init', 'bawga_register_settings' );

function bawga_create_menu()
{
	add_options_page( 'Google Author', 'Google Author', 'manage_options', 'baw_ga_config', 'bawga_page' );
}
add_action( 'admin_menu', 'bawga_create_menu' );

function bawga_page()
{
	screen_icon( 'options-general' );
?>
<h2>Google Author</h2>
<?php
  settings_errors();
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

    <?php 
	submit_button();
	settings_fields( 'bawga-settings-group' );
	?>
</form>
<?php
}

function bawga_post_author( $atts, $content = '' )
{
	global $post;
	extract(shortcode_atts(array(
		"class" => '',
		"link" => get_the_author_meta( 'user_url', $post->user_id ),
		"text" => get_the_author_meta( 'user_nicename', $post->user_id )
	), $atts));
	$link = $link != '' ? $link : home_url( '/author/' . get_the_author_meta( 'user_nicename', $post->user_id ) );
	$content = $content != '' ? $content : $text;
	$content = '<a class="' . $class . '" rel="author" href="' . $link . '">' . $content . '</a>';
	return $content;
}
add_shortcode( 'bawga_author', 'bawga_post_author' );

function bawga_post_author2( $content )
{
	global $post;
	$content = $content . get_the_author_link();
	return $content;
}
add_action( 'the_content', 'bawga_post_author2' );

function bawga_meta_author()
{
	global $post;
	$user_info = get_userdata($post->post_author);
	$author = $user_info->user_nicename;
	$author = $author != '' && get_option( 'bawga_authorstyle' ) == 'auto' && ( is_single() || is_page() )? $author : esc_attr( get_option( 'bawga_authorname' ) );
	echo '<meta rel="author" content="' . $author . '" />' . "\n";
}
add_action( 'wp_head', 'bawga_meta_author' );

function bawga_settings_action_links( $links )
{
	$settings_link = '<a href="' . admin_url( 'admin.php?page=baw_ga_config' ) . '">' . __( 'Settings' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bawga_settings_action_links' );

function bawga_default_values()
{
	global $current_user;
	$user_login = $current_user->user_nicename;
	add_option( 'bawga_authorname', $user_login );
	add_option( 'bawga_authorstyle', 'auto' );
}
register_activation_hook( __FILE__, 'bawga_default_values' );

function bawga_uninstaller()
{
	delete_option( 'bawga_authorname' );
	delete_option( 'bawga_authorstyle' );
}
register_uninstall_hook( __FILE__, 'bawga_uninstaller' );