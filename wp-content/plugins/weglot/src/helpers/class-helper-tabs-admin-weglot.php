<?php

namespace WeglotWP\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Name pages
 *
 * @since 2.0
 */
abstract class Helper_Tabs_Admin_Weglot {

	/**
	 * @var string
	 */
	const SETTINGS = 'settings';

	/**
	 * @var string
	 */
	const ADVANCED = 'advanced';

	/**
	 * @var string
	 */
	const STATUS = 'status';

	/**
	 * @var string
	 */
	const APPEARANCE = 'appearance';

	/**
	 * Get tabs constant
	 *
	 * @since 2.0
	 * @static
	 * @return array
	 */
	public static function get_tabs() {
		return [
			self::SETTINGS,
			self::ADVANCED,
			self::STATUS,
			self::APPEARANCE,
		];
	}

	/**
	 * Get full tabs information
	 * @static
	 * @since 2.0
	 *
	 * @return void
	 */
	public static function get_full_tabs() {
		return [
			self::SETTINGS => [
				'title' => __( 'General', 'weglot' ),
				'url'   => get_admin_url( null, sprintf( 'admin.php?page=%s&tab=%s', Helper_Pages_Weglot::SETTINGS, self::SETTINGS ) ),
			],
			self::APPEARANCE   => [
				'title' => __( 'Language button', 'weglot' ),
				'url'   => get_admin_url( null, sprintf( 'admin.php?page=%s&tab=%s', Helper_Pages_Weglot::SETTINGS, self::APPEARANCE ) ),
			],
			self::ADVANCED => [
				'title' => __( 'Advanced', 'weglot' ),
				'url'   => get_admin_url( null, sprintf( 'admin.php?page=%s&tab=%s', Helper_Pages_Weglot::SETTINGS, self::ADVANCED ) ),
			],
			self::STATUS   => [
				'title' => __( 'Status', 'weglot' ),
				'url'   => get_admin_url( null, sprintf( 'admin.php?page=%s&tab=%s', Helper_Pages_Weglot::SETTINGS, self::STATUS ) ),
			],
		];
	}
}
