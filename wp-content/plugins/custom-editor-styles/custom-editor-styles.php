<?php
/*
Plugin Name: Custom Editor Styles
Plugin URI: http://alisothegeek.com/2011/05/tinymce-styles-dropdown-wordpress-visual-editor/
Description: Make a custom styles dropdown menu for the visual editor in WordPress.
Author: Alison Barrett
Version: 1.1
Author URI: http://alisothegeek.com/

Copyright 2012 Alison Barrett (email: alison@brts.us)

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

/**
 * Custom Editor Styles main plugin class
 *
 * @author Neville Longbottom <nlongbottom@gryffindor.hogwarts.edu>
 * @author Alison Barrett <alison@brts.us>
 */
class Custom_Editor_Styles {

	/**
	 * Server path to the plugin folder
	 *
	 * @var string
	 */
	public $plugin_dir;

	/**
	 * URL to the plugin folder
	 *
	 * @var string
	 */
	public $plugin_url;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_dir = dirname( __FILE__ );
		$this->plugin_url = plugins_url( basename( dirname( __FILE__ ) ) );

		add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ) );
		add_filter( 'mce_buttons_2', array( $this, 'mce_buttons_2' ) );
		add_action( 'admin_init', array( $this, 'add_editor_style' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_styles' ) );
	}

	/**
	 * Define custom styles for the dropdown menu
	 *
	 * @param array $settings Existing custom styles in TinyMCE
	 * @return array
	 */
	public function tiny_mce_before_init( $settings ) {
		$style_formats = array(
			array(
				'title'    => 'Button',
				'selector' => 'a',
				'classes'  => 'button'
			),
			array(
				'title'   => 'Callout Box',
				'block'   => 'div',
				'classes' => 'callout',
				'wrapper' => true
			),
			array(
				'title'  => 'Bold Red Text',
				'inline' => 'span',
				'styles' => array(
					'color'      => '#f00',
					'fontWeight' => 'bold'
				)
			),
		);

		$settings['style_formats'] = json_encode( $style_formats );

		return $settings;
	}

	/**
	 * Add the Styles dropdown to the visual editor
	 *
	 * @param array $buttons Array of buttons already registered
	 * @return array
	 */
	public function mce_buttons_2( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}

	/**
	 * Load a custom stylesheet in the visual editor
	 *
	 * The path in the add_editor_style function is relative to the theme root.
	 *
	 * @return void
	 */
	public function add_editor_style() {
		add_editor_style( '../../plugins/' . basename( dirname( __FILE__ ) ) . '/custom-styles.css' );
	}

	/**
	 * Load a custom stylesheet on the website
	 *
	 * @return void
	 */
	public function enqueue_custom_styles() {
		wp_enqueue_style(
			'custom-editor-styles',
			$this->plugin_url . '/custom-styles.css',
			array(),
			'1.1',
			'all'
		);
	}

}

// Developers, start your engines.
$Custom_Editor_Styles = new Custom_Editor_Styles();

/*
Style Format Options

=========================================================================================
Array Key                     Meaning
=========================================================================================
title [required]              label for this dropdown item
-----------------------------------------------------------------------------------------
selector|block|inline         "selector" limits the style to a specific HTML
[required]                    tag, and will apply the style to an existing tag
                              instead of creating one

                              "block" creates a new block-level element with the
                              style applied, and will replace the existing block
                              element around the cursor

                              "inline" creates a new inline element with the style
                              applied, and will wrap whatever is selected in the
                              editor, not replacing any tags
-----------------------------------------------------------------------------------------
classes [optional]            space-separated list of classes to apply to the
                              element
-----------------------------------------------------------------------------------------
styles [optional]             array of inline styles to apply to the element
                              (two-word attributes, like font-weight, are written
                              in Javascript-friendly camel case: fontWeight)
-----------------------------------------------------------------------------------------
attributes [optional]         assigns attributes to the element (same syntax as styles)
-----------------------------------------------------------------------------------------
wrapper [optional,            if set to true, creates a new block-level element
default = false]              around any selected block-level elements
-----------------------------------------------------------------------------------------
exact [optional,              disables the "merge similar styles" feature, needed
default = false]              for some CSS inheritance issues
=========================================================================================

See this blog post for more details: http://alisothegeek.com/2011/05/tinymce-styles-dropdown-wordpress-visual-editor/

*/