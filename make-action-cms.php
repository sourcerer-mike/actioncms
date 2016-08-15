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

require_once 'vendor/autoload.php';

foreach ( glob( __DIR__ . '/migration/*.php' ) as $migration_file ) {
	require_once $migration_file;
}