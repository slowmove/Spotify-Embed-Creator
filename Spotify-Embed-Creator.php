<?php

/**
  Plugin Name: Spotify Embed Creator
  Plugin URI: http://slowmove.se/wordpress-plugin-spotify-embed-creator/
  Description: Search for Album or Song on Spotify and create a Spotify Play Button
  Version: 1.0.5
  Author: Erik Johansson
  Author URI: http://slowmove.se
  License: GPL2
 */
/*
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
class SpotifyEmbedCreator
{
	
	function __construct() {
	
	}
	
	/**
	* install function, ie create or update the database
	*/
	public static function install() {
	
	}
	
	/**
	* checks if a database table update is needed
	*/
	public static function update() {
	
	}

}
/* Shortcode setup */
function spotifyplaybutton_shortcode_func( $atts ) {
	extract( shortcode_atts( array(
		'play' => 'spotify:album:7JggdVIipgSShK1uk7N1hP',
		'view' => get_option('spotifyplaybutton_view',''),
		'size' => get_option('spotifyplaybutton_size',500),
		'sizetype' => get_option('spotifyplaybutton_sizetype','width'),
		'theme' => get_option('spotifyplaybutton_theme','')
	), $atts ) );
	
	$size = round($size);
	if ($sizetype == "width") {
		$width = $size;
		$height = $size+80;
	} elseif ($sizetype == "height") {
		$height = $size;
		$width = $size-80;
	} elseif ($sizetype == "compact") {
		$height = 80;
		$width = $size;
	}
	if ($height < 80) {
		$height = 80;
	}

	return "<iframe src=\"https://embed.spotify.com/?uri={$play}&view={$view}&theme={$theme}\" style=\"width:{$width}px; height:{$height}px;\" frameborder=\"0\" allowTransparency=\"true\"></iframe>";
}
add_shortcode( 'spotify', 'spotifyplaybutton_shortcode_func' );

/**
 * Define the custom box
 **/ 
add_action( 'add_meta_boxes', 'myplugin_add_custom_box' );
// backwards compatible (before WP 3.0)
// add_action( 'admin_init', 'myplugin_add_custom_box', 1 );
/* Adds a box to the main column on the Post and Page edit screens */
function myplugin_add_custom_box() {
    add_meta_box( 
        'spotify_embed_creator_sectionid',
        __( 'Spotify Embed Creator', 'spotify_embed_creator_textdomain' ),
        'spotify_embed_creator_custom_box',
        'post' 
    );
    add_meta_box(
        'spotify_embed_creator_sectionid',
        __( 'Spotify Embed Creator', 'spotify_embed_creator_textdomain' ),
        'spotify_embed_creator_custom_box',
        'page'
    );
}

/* Prints the box content */
function spotify_embed_creator_custom_box( $post ) {
  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );
  // The actual fields for data entry
  include('Admin-pages/edit-post-box.php');
}


// hooks for install and update
register_activation_hook(__FILE__, 'SpotifyEmbedCreator::install');
add_action('plugins_loaded', 'SpotifyEmbedCreator::update');

/**
 * Add admin page
 */
add_action('admin_menu', 'SpotifyEmbedCreator_add_page');

function SpotifyEmbedCreator_add_page() {
	add_menu_page('Create Spotify Play Button', 'Create Spotify Play Button', 'manage_options', __FILE__, 'SpotifyEmbedCreatorSearchPage');
}

function SpotifyEmbedCreatorSearchPage() {
	include 'Admin-pages/Spotify-Search-Page.php';
}

