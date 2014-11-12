<?php
if(!defined('ABSPATH'))	exit; //exit if accessed directly

new Download_Attachments_Media();

class Download_Attachments_Media
{
	private $options = array();


	/**
	 * Class constructor
	*/
	public function __construct()
	{
		//settings
		$this->options = array_merge(
			array('general' => get_option('download_attachments_general'))
		);

		//actions
		add_action('manage_media_custom_column', array(&$this, 'custom_media_column_content'), 10, 2);

		//filters
		add_filter('manage_media_columns', array(&$this, 'downloads_media_column_title'));
		add_filter('manage_upload_sortable_columns', array(&$this, 'register_sortable_custom_column'));
		add_filter('request', array(&$this, 'sort_custom_columns'));
	}


	/**
	 * Displays attachments download count
	*/
	public function custom_media_column_content($column, $id)
	{
		if($this->options['general']['downloads_in_media_library'] === TRUE && $column === 'downloads_count')
			echo (int)get_post_meta($id, '_da_downloads', TRUE);
	}


	/**
	 * Adds new custom column to Media Library
	*/
	public function downloads_media_column_title($columns)
	{
		if($this->options['general']['downloads_in_media_library'] === TRUE)
		{
			$two_last = array_slice($columns, -2, 2, TRUE);

			foreach($two_last as $column => $name)
			{
				unset($columns[$column]);
			}

			$columns['downloads_count'] = __('Downloads', 'download-attachments');

			foreach($two_last as $column => $name)
			{
				$columns[$column] = $name;
			}
		}

		return $columns;
	}


	/**
	 * Sorts new custom column in Media Library
	*/
	function sort_custom_columns($vars)
	{
		if($this->options['general']['downloads_in_media_library'] === TRUE && isset($vars['orderby']) && $vars['orderby'] === 'downloads')
			$vars = array_merge(
				$vars, array(
					'meta_key' => '_da_downloads',
					'orderby' => 'meta_value_num'
				)
			);

		return $vars;
	}


	/**
	 * Registers sortable custom column in Media Library
	*/
	function register_sortable_custom_column($columns)
	{
		if($this->options['general']['downloads_in_media_library'] === TRUE)
			$columns['downloads_count'] = 'downloads';

		return $columns;
	}
}
?>