<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Client;

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

$options_available = [
	'auto_redirect' => [
		'key'         => 'auto_redirect',
		'label'       => __( 'Auto redirection', 'weglot' ),
		'description' => __( 'Check if you want to redirect users based on their browser language.', 'weglot' ),
	],
	'email_translate' => [
		'key'         => 'email_translate',
		'label'       => __( 'Translate email', 'weglot' ),
		'description' => __( 'Check to translate all emails who use function wp_mail', 'weglot' ),
	],
	'translate_amp' => [
		'key'         => 'translate_amp',
		'label'       => __( 'Translate AMP', 'weglot' ),
		'description' => __( 'Translate AMP page', 'weglot' ),
	],
];

?>


<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['auto_redirect']['key'] ); ?>">
					<?php echo esc_html( $options_available['auto_redirect']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['auto_redirect']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['auto_redirect']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['auto_redirect']['key'] ], 1 ); ?>
				>
				<p class="description"><?php echo esc_html( $options_available['auto_redirect']['description'] ); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['email_translate']['key'] ); ?>">
					<?php echo esc_html( $options_available['email_translate']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['email_translate']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['email_translate']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['email_translate']['key'] ], 1 ); ?>
				>
				<p class="description"><?php echo esc_html( $options_available['email_translate']['description'] ); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['translate_amp']['key'] ); ?>">
					<?php echo esc_html( $options_available['translate_amp']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['translate_amp']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['translate_amp']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['translate_amp']['key'] ], 1 ); ?>
				>
				<p class="description"><?php echo esc_html( $options_available['translate_amp']['description'] ); ?></p>
			</td>
		</tr>
	</tbody>
</table>

<div class="notice notice-info is-dismissible">
	<p>
		<?php
			// translators: 1 HTML Tag, 2 HTML Tag
			echo sprintf( esc_html__( 'If you need any help, you can contact us via email us at support@weglot.com.', 'weglot' ), '<a href="https://weglot.com/" target="_blank">', '</a>' );
		?>
	</p>
	<p>
		<?php esc_html_e( 'You can also return to version 1.13.1 by clicking on the button below', 'weglot' ); ?>
	</p>
	<p>
		<a href="<?php echo wp_nonce_url( admin_url( 'admin-post.php?action=weglot_rollback' ), 'weglot_rollback' ); //phpcs:ignore ?>" class="button">
			<?php echo esc_html__( 'Re-install version 1.13.1', 'weglot' ); ?>
		</a>
	</p>
</div>

