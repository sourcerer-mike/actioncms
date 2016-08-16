<?php
/**
 * Script which executes all migrations.
 *
 * Meant to help forking the Action CMS from WordPress via:
 *
 * - Migration
 * - Replacements
 * - Injection
 *
 * The migration comes first and adapts code of the WordPress Core.
 * Replacements are made afterwards to exchange classes,
 * functions and more that do not show a reliable pattern for proper migration.
 * At last some injections are made to enhance the WordPress Core with additional files
 * or listeners for actions / filter.
 *
 * ## Migration
 *
 * The folder `migration` contains several scripts to adapt WordPress to the principles of Action CMS.
 * Objectives like "no final-classes" and other enhancements have a pattern and can be done via script.
 *
 * ## Replacements
 *
 * The folder `replacements` will contain constants, functions, classes
 * and more which totally overwrite the given ones in the WordPress Core.
 * These are breaking changes which shall not be made or lead to an error,
 * if the core made changed in such constants, functions or classes.
 *
 * ## Injection
 *
 * The WordPress Core can be extended by other files, functions, classes and more.
 * Additional actions and filter can be hooked in the WordPress Core
 * to enhance it for a better WordPress called Action CMS.
 *
 */

require_once 'lib/bootstrap.php';

echo "Loading ...\n";

$currentHash = trim( `git -C opt/automattic/wordpress rev-parse --verify HEAD` );

echo "WP Build " . $currentHash . " ...\n";

$srcSerializedFile = ACTION_CMS_ROOT_PATH . '/tmp/' . $currentHash . '.src';
if ( ! file_exists( $srcSerializedFile ) ) {
	foreach ( WpPhpParser::getInstance()->getFileList() as $item ) {
	};
	file_put_contents( $srcSerializedFile, serialize( WpPhpParser::getInstance() ) );
	echo "Stored build.\n";
} else {
	WpPhpParser::setInstance( unserialize( file_get_contents( $srcSerializedFile ) ) );
	echo "Loaded build.\n";
}


foreach ( glob( ACTION_CMS_ROOT_PATH . '/lib/migration/*.php' ) as $migration_file ) {
	echo $migration_file . "\n";
	$func = require_once $migration_file;
	foreach ( WpPhpParser::getInstance()->getFileList() as $fileName => $nodes ) {
		WpPhpParser::getInstance()->setFile( $fileName, $func( $nodes ) );
	}
}

echo "Writing ...";
$prettyPrinter = new PhpParser\PrettyPrinter\Standard();

foreach ( WpPhpParser::getInstance()->getFileList() as $fileName => $nodes ) {
	echo ".";
	$dirName = ACTION_CMS_ROOT_PATH . '/src/' . dirname( $fileName );
	if ( ! is_dir( $dirName ) ) {
		mkdir( $dirName, 0755, true );
	}

	$targetPath = $dirName . '/' . basename( $fileName );

	file_put_contents( $targetPath, $prettyPrinter->prettyPrintFile( $nodes ) );
}

echo "\nCopy others ..";

$fs = new \Symfony\Component\Filesystem\Filesystem();

// normal and hidden files "{,.}*"
foreach ( glob( ACTION_CMS_WP_CORE_PATH . '/../{,.}*', GLOB_BRACE ) as $wpFile ) {
	$basename = basename( $wpFile );

	if ( $basename == 'src' || $basename == '.' || $basename == '..' ) {
		continue;
	}

	// clean up
	$destination = ACTION_CMS_ROOT_PATH . '/' . $basename;
	$fs->remove( $destination );

	// rebuild
	$source = ACTION_CMS_WP_CORE_PATH . '/../' . $basename;
	if ( is_dir( $source ) ) {
		$fs->mirror( $source, $destination );
	} else {
		$fs->copy( $source, $destination );
	}

	echo ".";
}

echo "\n";