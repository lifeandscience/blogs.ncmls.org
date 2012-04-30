<?php
/*
Plugin Name: Merge Tags
Version: 1.2
Description: Allows you to combine tags and categories from the tag editing interface
Tags: admin, management, category, tag, term, taxonomy
Author: scribu
Author URI: http://scribu.net/
Plugin URI: http://scribu.net/wordpress/merge-tags
Text Domain: merge-tags
Domain Path: /lang

Copyright (C) 2010 scribu.net (scribu@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
( at your option ) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class Merge_Tags {

	function init() {
		add_action( 'load-edit-tags.php', array( __CLASS__, 'handler' ) );
		add_action( 'load-edit-tags.php', array( __CLASS__, 'notice' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'script' ) );

		load_plugin_textdomain( 'merge-tags', '', basename( dirname( __FILE__ ) ) . '/lang' );
	}

	function handler() {
		$from_ids = @$_REQUEST['delete_tags'];

		foreach ( array( '', '2' ) as $a )
			if ( 'bulk-merge-tag' == @$_REQUEST['action' . $a] ) {
				$term_name = $_REQUEST['bulk_to_tag' . $a];
				break;
			}

		if ( empty( $from_ids ) || empty( $term_name ) )
			return;

		check_admin_referer( 'bulk-tags' );

		$location = 'edit-tags.php';
		if ( $referer = wp_get_referer() ) {
			if ( false !== strpos( $referer, 'edit-tags.php' ) )
				$location = $referer;
		}

		$taxonomy = @$_REQUEST['taxonomy'];

		if ( empty( $taxonomy ) )
			$taxonomy = 'post_tag';

		if ( !taxonomy_exists( $taxonomy ) )
			wp_die( __( 'Invalid taxonomy' ) );

		$tax = get_taxonomy( $taxonomy );

		if ( !current_user_can( $tax->cap->manage_terms ) )
			return;

		if ( !$term = term_exists( $term_name, $taxonomy ) )
			$term = wp_insert_term( $term_name, $taxonomy );

		if ( is_wp_error( $term ) ) {
			wp_redirect( add_query_arg( 'message', 'not-merged', $location ) );
			exit;
		}

		$term_id = $term['term_id'];

		foreach ( $from_ids as $from_id ) {
			if ( $from_id == $term_id )
				continue;

			$ret = wp_delete_term( $from_id, $taxonomy, array( 'default' => $term_id, 'force_default' => true ) );

			if ( is_wp_error( $ret ) ) {
				wp_redirect( add_query_arg( 'message', 'not-merged', $location ) );
				exit;
			}
		}

		wp_redirect( add_query_arg( 'message', 'merged', $location ) );
		exit;
	}

	function notice() {
		global $messages;

		$messages['not-merged'] = __( 'Tags not merged.', 'merge-tags' );
		$messages['merged'] = __( 'Tags merged.', 'merge-tags' );
	}

	function script() {
		global $pagenow, $taxonomy;

		if ( 'edit-tags.php' != $pagenow )
			return;

		$js_dev = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';

		wp_enqueue_script( 'merge-tags', plugins_url( "script$js_dev.js",__FILE__ ), array( 'jquery' ), '1.2' );

		wp_localize_script( 'merge-tags', 'mergeTagsL10n', array(
			'action' => esc_attr__( 'Merge', 'merge-tags' ),
			'to_tag' => __( 'To tag', 'merge-tags' ),
			'taxonomy' => $taxonomy
		) );
	}
}

Merge_Tags::init();

