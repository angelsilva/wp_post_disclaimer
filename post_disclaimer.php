<?php

/**
* Plugin Name: PostDisclaimer
* Plugin URI: http://www.example.com
* Description: Add Disclaimer for yours Posts
* Version: 1.1.1
* Author: Example
* Author URI: http://www.example.com
* License: GPL2
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

add_action('admin_menu', 'post_disclaimer_menu');
add_action('add_meta_boxes', 'post_disclaimer_metabox' );
add_action( 'save_post', 'post_disclaimer_metabox_save' );
add_filter('the_content','post_disclaimer_add');

function post_disclaimer_menu(){

	add_menu_page( 'Disclaimer - Settings', 'Disclaimer','manage_options', 'global_disclaimer_menu_item' , '_global_disclaimer_settings', 'dashicons-info' );

}

function _global_disclaimer_settings(){


	$global_disclaimer = get_option('global_disclaimer');

	if(!$global_disclaimer){
		update_option('global_disclaimer',"");		
	}

	if ( isset($_POST['global_disclaimer_val']) ){
		check_admin_referer( 'nonce_global_disclaimer', 'nonce_global_disclaimer_field' );
		$global_disclaimer = sanitize_text_field( htmlentities( $_POST['global_disclaimer_val'] ) );
		$global_disclaimer = html_entity_decode( $global_disclaimer );

		update_option('global_disclaimer',$global_disclaimer);
	}

?>
<div class="wrap">
	<h1>Global Disclaimer - Settings</h1>
	<form method="post">
		<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Save Disclaimer" type="submit"></p>
		<?php wp_nonce_field( 'nonce_global_disclaimer', 'nonce_global_disclaimer_field' ); ?>
		<?php wp_editor( stripslashes( $global_disclaimer ), "global_disclaimer_val" ); ?>
		<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Save Disclaimer" type="submit"></p>
	</form>
</div>
<?php
	
}

function post_disclaimer_add($content) {
	$post_id = get_the_ID();
	$post_disclaimer = get_post_meta($post_id, "post_disclaimer", true);
	if(!$post_disclaimer){
		$post_disclaimer = get_option('global_disclaimer');
	}
	$text = isset($post_disclaimer) ? stripslashes( $post_disclaimer ) : '';

	if(is_single() && !is_home() && strstr($content, $text)===FALSE) {
		$content .= $text;
	}

	return $content;
}



function post_disclaimer_metabox() {
	if(current_user_can('manage_options'))
    	add_meta_box( 'post_disclaimer_metabox', "Post Disclaimer", 'post_disclaimer_metabox_show', 'post' );
}

function post_disclaimer_metabox_show(){
	global $post;
	$post_disclaimer = get_post_meta($post->ID, "post_disclaimer", true);
	if(!$post_disclaimer){
		$post_disclaimer = get_option('global_disclaimer');
	}
?>
	<div class="wrap">
		<?php wp_nonce_field( 'nonce_post_disclaimer', 'nonce_post_disclaimer_field' ); ?>
		<?php wp_editor( stripslashes( $post_disclaimer ), "post_disclaimer_val" ); ?>
	</div>
<?php
}

function post_disclaimer_metabox_save($post_id){
	if ( isset($_POST['post_disclaimer_val']) ){
		check_admin_referer( 'nonce_post_disclaimer', 'nonce_post_disclaimer_field' );
		$disclaimer = sanitize_text_field( htmlentities( $_POST['post_disclaimer_val'] ) );
		$disclaimer = html_entity_decode( $disclaimer );

		update_post_meta( $post_id, "post_disclaimer", $disclaimer );
	}
}

add_action( 'init', 'post_disclaimer_update' );
function post_disclaimer_update()
{
	if (!class_exists('WP_AutoUpdate')) 
		require_once ( 'wp_autoupdate.php' );

	$plugin_current_version = '1.1.1';
	$plugin_remote_path = 'https://wpdev.yoemprendo.online/update.php?plugin=post_disclaimer';
	$plugin_slug = plugin_basename( __FILE__ );
	$license_user = 'user';
	$license_key = 'abcd';
	new WP_AutoUpdate ( $plugin_current_version, $plugin_remote_path, $plugin_slug, $license_user, $license_key );
}