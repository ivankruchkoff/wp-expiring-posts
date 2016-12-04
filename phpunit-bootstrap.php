<?php

$_plugin_dir = realpath( __DIR__ );
$_tests_dir = getenv( 'WP_TESTS_DIR' );

//Load Patchwork for WP_Mock, so we can mock internal Wordpress functions like get_posts() in unit tests
if ( file_exists( $_tests_dir . '../../../../vendor/antecedent/patchwork/Patchwork.php' ) ) {
	//Suppressing warning due to https://github.com/sebastianbergmann/phpunit/issues/2182, so that ci works
	@require_once( $_tests_dir . '../../../../vendor/antecedent/patchwork/Patchwork.php' );
}

//Load composer
if ( file_exists( $_tests_dir . '../../../../vendor/autoload.php' ) ) {
	require_once( $_tests_dir . '../../../../vendor/autoload.php' );
}

if ( file_exists( __DIR__ . '/phpunit-bootstrap.project.php' ) ) {
	require_once( __DIR__ . '/phpunit-bootstrap.project.php' );
}

//Load XWP bootstrap
if ( file_exists( $_tests_dir . '../../../../vendor/newscorpau/spp-dev-tools/xwp-phpunit-bootstrap.php' ) ) {
	require_once( $_tests_dir . '../../../../vendor/newscorpau/spp-dev-tools/xwp-phpunit-bootstrap.php' );
}
