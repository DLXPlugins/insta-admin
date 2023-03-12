<?php
/**
 * Uninstall script.
 *
 * @package HAS
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
delete_option( 'insta-admin-landing-page' );
delete_option( 'highlight-and-share-email-settings' );
delete_option( 'highlight-and-share-social-networks' );
delete_option( 'highlight-and-share-theme-options' );
delete_option( 'highlight-and-share-block-editor-options' );
