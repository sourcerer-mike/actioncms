<?php
/**
 * Stop nagging, be compatible - lesser deprecated functions.
 *
 * Often there are failures about deprecated functions in WordPress that shall no longer be used.
 * Instead of nagging some got a workaround for backward compatibility
 * and some were dropped.
 *
 * ## Deprecated functions should do something
 * 
 * In WordPress there are a few deprecated functions.
 * Those are functions that made sense up to some version like `wpmu_menu()`.
 * But in WordPress 3.0.0 this function became deprecated (see https://core.trac.wordpress.org/ticket/11763 ).
 * This happens from time to time with other funtions too.
 *
 * But instead of removing deprecated functions like `wpmu_menu()`
 * this dead code is kept for years without any purpose.
 * For seven years now the `wpmu_menu` has no body except an error via `_deprecated_function`.
 *
 * ## A lot of dead code in WordPress
 *
 * It is about time that unnecessary functions like that get dropped or have a body with a workaround.
 * Some functions will be torn into workarounds for a better backward compatibility.
 * But if it is impossible to make such workaround
 * or the costs for that are to high, the function will be dropped.
 *
 *
 * ## Dropping deprecated functions won't harm
 *
 * We do semantic versions to let the user and developer know of such breaking changes.
 * In addition dropping a function is the last thing that WordPress Core developer want.
 * And the Action CMS team is one instance more to create possible workarounds for each "deprecated" function.
 * There is a lot of fallback for the not so experienced developer and user.
 *
 * As Action CMS is meant for experienced developers,
 * those are the ones that have another profit of dropping deprecations.
 * You will no longer get silly auto-complete in your favourite IDE.
 * There will be a regular notice about upcoming deprecation
 * and later the function will just be dropped.
 * Everything as expected like in other bigger frameworks.
 */

$deprecatedFunctions = [
    'get_current_site_name',
    'get_dashboard_blog',
    'get_page',
    'get_postdata',
    'install_blog_defaults',
    'install_themes_feature_list',
    'logIO',
    'tinymce_include',
    'twentyten_remove_gallery_css',
    'wp_ajax_wp_fullscreen_save_post',
    'wp_cache_reset',
    'wp_clone',
    'wpmu_menu',
];

foreach ( WpPhpParser::getInstance()->getFileList() as $fileName => $nodes ) {
	var_dump($nodes);
	exit;
}