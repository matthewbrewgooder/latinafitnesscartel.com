<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Post_Meta_Weglot;

/**
 * Button services
 *
 * @since 2.0
 */
class Button_Service_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services           = weglot_get_service( 'Option_Service_Weglot' );
		$this->request_url_services      = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->language_services         = weglot_get_service( 'Language_Service_Weglot' );
		$this->amp_services              = weglot_get_service( 'Amp_Service_Weglot' );
	}


	/**
	 * Get html button switcher
	 *
	 * @since 2.0
	 * @return string
	 * @param string $add_class
	 */
	public function get_html( $add_class = '' ) {
		$options                          = $this->option_services->get_options();
		$is_fullname                      = $options['is_fullname'];
		$with_name                        = $options['with_name'];
		$is_dropdown                      = $options['is_dropdown'];
		$with_flags                       = $options['with_flags'];
		$type_flags                       = $options['type_flags'];
		$weglot_url                       = $this->request_url_services->get_weglot_url();

		$translate_amp = weglot_get_translate_amp_translation();
		$amp_regex     = $this->amp_services->get_regex( true );

		if ( $translate_amp && preg_match( '#' . $amp_regex . '#', $weglot_url->getUrl() ) === 1 ) {
			$add_class .= ' weglot-invert';
		}

		$destination_language             = weglot_get_destination_languages();
		$original_language                = $options['original_language'];
		$current_language                 = $this->request_url_services->get_current_language( false );

		$flag_class                       = $with_flags ? 'weglot-flags ' : '';
		$flag_class .= '0' === $type_flags ? '' : 'flag-' . $type_flags . ' ';

		$class_aside                      = $is_dropdown ? 'weglot-dropdown ' : 'weglot-inline ';

		$languages = $this->language_services->get_languages_available();

		$button_html = sprintf( '<!--Weglot %s-->', WEGLOT_VERSION );
		$button_html .= sprintf( "<aside data-wg-notranslate class='country-selector %s'>", $class_aside . $add_class );

		if ( ! empty( $original_language ) && ! empty( $destination_language ) ) {
			$name = '';
			if ( isset( $languages[ $current_language ] ) ) {
				$current_language_entry = $languages[ $current_language ];
			} else {
				$current_language_entry = apply_filters( 'weglot_current_language_entry', $current_language );
				if ( $current_language_entry === $current_language ) {
					throw new \Exception( 'You need create a language entry' );
				}
			}

			if ( $with_name ) {
				$name = ( $is_fullname ) ? $current_language_entry->getLocalName() : strtoupper( $current_language_entry->getIso639() );
			}

			global $post;

			$uniq_id = 'wg' . uniqid( strtotime( 'now' ) ) . rand( 1, 1000 );
			$button_html .= sprintf( '<input id="%s" class="weglot_choice" type="checkbox" name="menu"/><label for="%s" class="wgcurrent wg-li %s" data-code-language="%s"><span>%s</span></label>', $uniq_id, $uniq_id, $flag_class . $current_language, $current_language_entry->getIso639(), $name );

			$button_html .= '<ul>';

			array_unshift( $destination_language, $original_language );

			foreach ( $destination_language as $key => $key_code ) {
				if ( $key_code === $current_language ) {
					continue;
				}

				if ( isset( $languages[ $key_code ] ) ) {
					$current_language_entry = $languages[ $key_code ];
				} else {
					$current_language_entry = apply_filters( 'weglot_current_language_entry', $key_code );
					if ( $current_language_entry === $key_code ) {
						throw new \Exception( 'You need create a language entry' );
					}
				}

				$name = '';
				if ( $with_name ) {
					$name = ( $is_fullname ) ? $current_language_entry->getLocalName() : strtoupper( $current_language_entry->getIso639() );
				}

				$button_html .= sprintf( '<li class="wg-li %s" data-code-language="%s">', $flag_class . $key_code, $key_code );

				$url_lang                 = $weglot_url->getForLanguage( $key_code );

				// Custom URLS
				$request_without_language = array_filter( explode( '/', $weglot_url->getPath() ), 'strlen' );
				$index_entries            = count( $request_without_language );
				$custom_urls              = $this->option_services->get_option( 'custom_urls' );

				if ( isset( $request_without_language[ $index_entries ] ) && ! is_admin() && ! empty( $custom_urls ) ) {
					$slug_in_work             = $request_without_language[ $index_entries ];

					// Search from original slug
					$key_slug = false;
					if ( isset( $custom_urls[ $key_code ] ) && $post ) {
						$key_slug = array_search( $post->post_name, $custom_urls[ $key_code ] );
					}

					if ( false !== $key_slug ) {
						$url_lang = str_replace( $slug_in_work, $key_slug, $url_lang );
					} else {
						if ( $post ) {
							$url_lang = str_replace( $slug_in_work, $post->post_name, $url_lang );
						}
					}
				}

				$link_button = apply_filters( 'weglot_link_language', $url_lang, $key_code );

				$link_button = preg_replace( '#\?no_lredirect=true$#', '', $link_button ); // Remove ending "?no_lredirect=true"
				if ( weglot_has_auto_redirect() && strpos( $link_button, 'no_lredirect' ) === false && ( is_home() || is_front_page() ) && $key_code === $original_language ) {
					$link_button .= '?no_lredirect=true';
				}

				$button_html .= sprintf(
					'<a data-wg-notranslate href="%s">%s</a>',
					$link_button,
					$name
				);

				$button_html .= '</li>';
			}

			$button_html .= '</ul>';
		}

		$button_html .= '</aside>';

		return apply_filters( 'weglot_button_html', $button_html, $add_class );
	}
}
