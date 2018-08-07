<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

$options_available = [
	'api_key' => [
		'key'         => 'api_key',
		'label'       => __( 'API Key', 'weglot' ),
		'description' => __( 'Log in to <a target="_blank" href="https://weglot.com/register-wordpress">Weglot</a> to get your API key. <span class="weglot-info">Why?<span class="wg-tooltip"><span class="arrow-up"></span>A Weglot account is needed to access and manage your translations. It takes nothing more than 20 seconds !</span></span>', 'weglot' ),
	],
	'original_language' => [
		'key'         => 'original_language',
		'label'       => __( 'Original language', 'weglot' ),
		'description' => 'What is the original (current) language of your website?',
	],
	'destination_language' => [
		'key'         => 'destination_language',
		'label'       => __( 'Destination language', 'weglot' ),
		'description' => 'Choose languages you want to translate into. Supported languages can be found <a target="_blank" href="https://weglot.com/translation-api#languages_code">here</a>.',
	],
	'exclude_urls' => [
		'key'         => 'exclude_urls',
		'label'       => __( 'Exclusion URL', 'weglot' ),
		'description' => __( 'Add URL that you want to exclude from translations. You can use regular expression to match multiple URLs. ', 'weglot' ),
	],
	'exclude_blocks' => [
		'key'         => 'exclude_blocks',
		'label'       => __( 'Exclusion Blocks', 'weglot' ),
		'description' => __( 'Enter the CSS selector of blocks you don\'t want to translate (like a sidebar, a menu, a paragraph etc...', 'weglot' ),
	],
];


$languages          = $this->language_services->get_languages_available();
$user_info          = $this->user_api_services->get_user_info();
$plans              = $this->user_api_services->get_plans();

?>

<h3><?php esc_html_e( 'Main configuration', 'weglot' ); ?></h3>
<hr>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['api_key']['key'] ); ?>">
					<?php echo esc_html( $options_available['api_key']['label'] ); ?>
				</label>
				<p class="sub-label"><?php echo $options_available['api_key']['description']; //phpcs:ignore ?></p>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['api_key']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['api_key']['key'] ); ?>"
					type="text"
					placeholder="wg_XXXXXXXXXXXX"
					value="<?php echo esc_attr( $this->options[ $options_available['api_key']['key'] ] ); ?>"
				>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['original_language']['key'] ); ?>">
					<?php echo esc_html( $options_available['original_language']['label'] ); ?>
				</label>
					<p class="sub-label"><?php echo $options_available['original_language']['description']; //phpcs:ignore ?></p>
			</th>
			<td class="forminp forminp-text">
				<select
					class="weglot-select weglot-select-original"
					style="display:none"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['original_language']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['original_language']['key'] ); ?>"
				>
					<?php foreach ( $languages as $language ) : ?>
						<option
							value="<?php echo esc_attr( $language->getIso639() ); ?>"
							<?php selected( $language->getIso639(), $this->options[ $options_available['original_language']['key'] ] ); ?>
						>
							<?php echo esc_html__( $language->getEnglishName() ); //phpcs:ignore ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['destination_language']['key'] ); ?>">
					<?php echo esc_html( $options_available['destination_language']['label'] ); ?>
				</label>
				<p class="sub-label"><?php echo $options_available['destination_language']['description']; //phpcs:ignore ?></p>
			</th>
			<td class="forminp forminp-text">
				<select
					class="weglot-select weglot-select-destination"
					style="display:none"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['destination_language']['key'] ) ); ?>[]"
					id="<?php echo esc_attr( $options_available['destination_language']['key'] ); ?>"
					multiple="true"
					required
				>
					<?php foreach ( $languages as $language ) : ?>
						<option
							value="<?php echo esc_attr( $language->getIso639() ); ?>"
							<?php selected( true, in_array( $language->getIso639(), $this->options[ $options_available['destination_language']['key'] ], true ) ); ?>
						>
							<?php echo esc_html( $language->getLocalName() ); ?>
						</option>
					<?php endforeach; ?>
				</select>

				<?php
				if ( $user_info['plan'] <= 0 ) {
					?>
						<p class="description">
							<?php // translators: 1 HTML Tag, 2 HTML Tag ?>
							<?php echo sprintf( esc_html__( 'On the free plan, you can only choose one language and a maximum of 2000 words. If you want to use more than 1 language and 2000 words, please %1$supgrade your plan%2$s.', 'weglot' ), '<a target="_blank" href="https://weglot.com/change-plan">', '</a>' ); ?>
						</p>
					<?php
				} elseif ( in_array( $user_info['plan'], $plans['starter_free']['ids'] ) ) { //phpcs:ignore
					?>
						<p class="description">
							<?php // translators: 1 HTML Tag, 2 HTML Tag ?>
							<?php echo sprintf( esc_html__( 'On the Starter plan, you can choose one language. If you want to use more than 1 language, please %1$supgrade your plan%2$s.', 'weglot' ), '<a target="_blank" href="https://weglot.com/change-plan">', '</a>' ); ?>
						</p>
					<?php
				} elseif ( in_array( $user_info['plan'], $plans['business']['ids'] ) ) { //phpcs:ignore
					?>
						<p class="description">
							<?php // translators: 1 HTML Tag, 2 HTML Tag ?>
							<?php echo sprintf( esc_html__( 'On the Business plan, you can choose five languages. If you want to use more than language, please %1$supgrade your plan%2$s.', 'weglot' ), '<a target="_blank" href="https://weglot.com/change-plan">', '</a>' ); ?>
						</p>
					<?php
				}
				?>
			</td>
		</tr>
	</tbody>
</table>

<h3><?php esc_html_e( 'Translation Exclusion (Optional)', 'weglot' ); ?> </h3>
<hr>
<p><?php esc_html__( 'By default, every page is translated. You can exclude parts of a page or a full page here.', 'weglot' ); ?></p>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['exclude_urls']['key'] ); ?>">
					<?php echo esc_html( $options_available['exclude_urls']['label'] ); ?>
				</label>
				<p class="sub-label"><?php echo esc_html( $options_available['exclude_urls']['description'] ); ?></p>
			</th>
			<td class="forminp forminp-text">
				<div id="container-<?php echo esc_attr( $options_available['exclude_urls']['key'] ); ?>">
					<?php
					if ( ! empty( $this->options[ $options_available['exclude_urls']['key'] ] ) ) :
						foreach ( $this->options[ $options_available['exclude_urls']['key'] ] as $option ) :
							?>
						<div class="item-exclude">
							<input
								type="text"
								placeholder="/my-awesome-url"
								name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['exclude_urls']['key'] ) ); ?>[]"
								value="<?php echo esc_attr( $option ); ?>"
							>
							<button class="js-btn-remove js-btn-remove-exclude-url">
								<span class="dashicons dashicons-minus"></span>
							</button>
						</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
				<button id="js-add-exclude-url" class="btn btn-soft"><?php esc_html_e( 'Add an URL to exclude', 'weglot' ); ?></button>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['exclude_blocks']['key'] ); ?>">
					<?php echo esc_html( $options_available['exclude_blocks']['label'] ); ?>
				</label>
				<p class="sub-label"><?php echo esc_html( $options_available['exclude_blocks']['description'] ); ?></p>
			</th>
			<td class="forminp forminp-text">
				<div id="container-<?php echo esc_attr( $options_available['exclude_blocks']['key'] ); ?>">
					<?php
					if ( ! empty( $this->options[ $options_available['exclude_blocks']['key'] ] ) ) :
						foreach ( $this->options[ $options_available['exclude_blocks']['key'] ] as $option ) :
							?>
						<div class="item-exclude">
							<input
								type="text"
								placeholder=".my-class"
								name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['exclude_blocks']['key'] ) ); ?>[]"
								value="<?php echo esc_attr( $option ); ?>"
							>
							<button class="js-btn-remove js-btn-remove-exclude">
								<span class="dashicons dashicons-minus"></span>
							</button>
						</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
				<button id="js-add-exclude-block" class="btn btn-soft"><?php esc_html_e( 'Add a block to exclude', 'weglot' ); ?></button>
			</td>
		</tr>
	</tbody>
</table>


<?php if ( ! $this->options['has_first_settings'] && $this->options['show_box_first_settings'] ) : ?>
	<?php $this->option_services->set_option_by_key( 'show_box_first_settings', false ); // remove showbox ?>
	<div id="weglot-box-first-settings" class="weglot-box-overlay">
		<div class="weglot-box">
			<a class="weglot-btn-close"><?php esc_html_e( 'Close', 'weglot' ); ?></a>
			<h3 class="weglot-box--title"><?php esc_html_e( 'Well done! Your website is now multilingual.', 'weglot' ); ?></h3>
			<p class="weglot-box--text"><?php esc_html_e( 'Go on your website, there is a language switcher. Try it :)', 'weglot' ); ?></p>
			<a class="button button-primary" href="<?php echo esc_attr( home_url() ); ?>" target="_blank">
				<?php esc_html_e( 'Go on my front page.', 'weglot' ); ?>
			</a>
			<p class="weglot-box--subtext"><?php esc_html_e( 'Next step, edit your translations directly in your Weglot account.', 'weglot' ); ?></p>
		</div>
	</div>
	<?php
	if ( $this->options[ $options_available['destination_language']['key'] ] && count( $this->options[ $options_available['destination_language']['key'] ] ) > 0 ) :
		?>
		<iframe
			style="visibility:hidden;"
			src="<?php echo esc_url( sprintf( '%s/%s', home_url(), $this->options[ $options_available['destination_language']['key'] ][0] ) ); ?>/" width="1" height="1">
		</iframe>
	<?php endif; ?>
<?php endif; ?>

<template id="tpl-exclusion-url">
	<div class="item-exclude">
		<input
			type="text"
			placeholder="/my-awesome-url"
			name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['exclude_urls']['key'] ) ); ?>[]"
			value=""
		>
		<button class="js-btn-remove js-btn-remove-exclude">
			<span class="dashicons dashicons-minus"></span>
		</button>
	</div>
</template>

<template id="tpl-exclusion-block">
	<div class="item-exclude">
		<input
			type="text"
			placeholder=".my-class"
			name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['exclude_blocks']['key'] ) ); ?>[]"
			value=""
		>
		<button class="js-btn-remove js-btn-remove-exclude">
			<span class="dashicons dashicons-minus"></span>
		</button>
	</div>
</template>
