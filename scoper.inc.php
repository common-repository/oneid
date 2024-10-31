<?php
/**
 * PHP Scoper configurastion file.
 *
 * @see https://github.com/humbug/php-scoper#configuration
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

use Isolated\Symfony\Component\Finder\Finder;

return [
	// For more see: https://github.com/humbug/php-scoper#finders-and-paths.
	'finders' => [
		Finder::create()->files()->in( 'vendor/jumbojett/openid-connect-php' )->name( [ 'OpenIDConnectClient.php', 'LICENSE', 'composer.json' ] ),
		Finder::create()->files()->in( 'vendor/phpseclib/phpseclib' )->name( [ '*.php', 'LICENSE', 'composer.json' ] ),
	],

	// For more see: https://github.com/humbug/php-scoper#patchers.
	'patchers' => [
		function ( string $file_path, string $prefix, string $contents ): string {
			// Change the contents here.
			return $contents;
		},
	],

	// Fore more see https://github.com/humbug/php-scoper#whitelist.
	'whitelist' => [],

	// If `true` then the user defined constants belonging to the global namespace will not be prefixed.
	//
	// For more see https://github.com/humbug/php-scoper#constants--constants--functions-from-the-global-namespace.
	'whitelist-global-constants' => true,

	// If `true` then the user defined classes belonging to the global namespace will not be prefixed.
	//
	// For more see https://github.com/humbug/php-scoper#constants--constants--functions-from-the-global-namespace.
	'whitelist-global-classes' => true,

	// If `true` then the user defined functions belonging to the global namespace will not be prefixed.
	//
	// For more see https://github.com/humbug/php-scoper#constants--constants--functions-from-the-global-namespace.
	'whitelist-global-functions' => true,
];
