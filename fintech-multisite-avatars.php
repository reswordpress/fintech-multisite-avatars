<?php if (! defined('ABSPATH')){ return; }

/*
Plugin Name: Fintech Multisite Avatars
Description: Custom Sub Site Avatars for Multisite Networks implemented using ACF Fields
Author: Graeme Conradie
Text Domain: fintech-multisite-avatars
Version: 0.1
Author URI: https://github.com/GraemeConradie/fintech-multisite-avatars
Licence: GPLv2
*/

/** Setup Avatar Field ------------------------------------------------ **/

if( function_exists('acf_add_local_field_group') ):
  $site_slug = get_field( 'sys_site_slug', 'option' );

acf_add_local_field_group(array(
  'key' => 'group_5e86a052aa58e',
  'title' => 'Profile Avatar',
  'fields' => array(
    array(
      'key' => 'field_5e86a20054261',
      'label' => 'Avatar',
      'name' => 'sys_' . $site_slug . '_avatar',
      'type' => 'image',
      'instructions' => 'Min width 512px, Max width 1024px, jpg or png Max size 2MB',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => array(
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'acfe_permissions' => '',
      'acfe_uploader' => 'basic',
      'acfe_thumbnail' => 0,
      'return_format' => 'url',
      'preview_size' => 'full',
      'library' => 'uploadedTo',
      'min_width' => 512,
      'min_height' => 512,
      'min_size' => '',
      'max_width' => 2048,
      'max_height' => 2048,
      'max_size' => 2,
      'mime_types' => 'jpg, jpeg, png',
      'show_column' => 0,
      'show_column_weight' => 1000,
      'allow_quickedit' => 0,
      'allow_bulkedit' => 0,
    ),
  ),
  'location' => array(
    array(
      array(
        'param' => 'user_form',
        'operator' => '==',
        'value' => 'all',
      ),
    ),
  ),
  'menu_order' => 0,
  'position' => 'normal',
  'style' => 'default',
  'label_placement' => 'left',
  'instruction_placement' => 'label',
  'hide_on_screen' => '',
  'active' => true,
  'description' => '',
  'acfe_display_title' => '',
  'acfe_autosync' => '',
  'acfe_permissions' => '',
  'acfe_form' => 0,
  'acfe_meta' => '',
  'acfe_note' => '',
));

endif;

/** Swap WP Avatar with ACF Avatar------------------------------------------------ **/

add_filter('get_avatar', 'sys_user_avatar', 10, 5);

function sys_user_avatar( $avatar, $id_or_email, $size, $default, $alt ) {

  if ( is_numeric( $id_or_email ) ) {

    $id   = (int) $id_or_email;
    $user = get_user_by( 'id' , $id );

  } elseif ( is_object( $id_or_email ) ) {

    if ( ! empty( $id_or_email->user_id ) ) {

      $id   = (int) $id_or_email->user_id;
      $user = get_user_by( 'id' , $id );
    }

  } else {

    $user = get_user_by( 'email', $id_or_email );

  }

  if ( ! $user ) {
    return $avatar;
  }

  $user_id = $user->ID;
  $site_slug = get_field( 'sys_site_slug', 'option' );
  $image_id = get_user_meta($user_id, 'sys_' . $site_slug . '_avatar', true);

  if ( ! $image_id ) {
    return $avatar;
  }

  $image_url  = wp_get_attachment_image_src( $image_id, 'full' );
  $avatar_url = $image_url[0];
  $avatar = '<img alt="' . $alt . '" src="' . $avatar_url . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '">';

  return $avatar;
}

/** Set Default Avatar------------------------------------------------ **/

add_filter( 'avatar_defaults', 'mytheme_default_avatar' );

function mytheme_default_avatar( $avatar_defaults ) {

    $avatar = get_option('avatar_default');

    $site_url = site_url( '/images/', 'https' );

    $new_avatar_url = $site_url . 'avatar.png';

    if( $avatar != $new_avatar_url ) {

        update_option( 'avatar_default', $new_avatar_url );
    }

    $avatar_defaults[ $new_avatar_url ] = 'System Avatar';

    return $avatar_defaults;
}

/** Get User Avatar ----------------------------------------------- **/

function shortcode_user_avatar() {

  if(is_user_logged_in()) {

    global $current_user;

    wp_get_current_user();

    return get_avatar( $current_user -> ID, 512 );

  } else {

    $site_url = site_url( '/images/', 'https' );

    return get_avatar( $site_url .  'avatar.png', 512 );
  }
}
add_shortcode('display-user-avatar','shortcode_user_avatar');











