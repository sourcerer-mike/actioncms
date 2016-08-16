<?php

/**
 * WordPress Administration Scheme API
 *
 * Here we keep the DB structure and option values.
 *
 * @package WordPress
 * @subpackage Administration
 */
/**
 * Declare these as global in case schema.php is included from a function.
 *
 * @global wpdb   $wpdb
 * @global array  $wp_queries
 * @global string $charset_collate
 */
global $wpdb, $wp_queries, $charset_collate;
/**
 * The database character collate.
 */
$charset_collate = $wpdb->get_charset_collate();
/**
 * Retrieve the SQL for creating database tables.
 *
 * @since 3.3.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $scope Optional. The tables for which to retrieve SQL. Can be all, global, ms_global, or blog tables. Defaults to all.
 * @param int $blog_id Optional. The site ID for which to retrieve SQL. Default is the current site ID.
 * @return string The SQL needed to create the requested tables.
 */
function wp_get_db_schema($scope = 'all', $blog_id = null)
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    if ($blog_id && $blog_id != $wpdb->blogid) {
        $old_blog_id = $wpdb->set_blog_id($blog_id);
    }
    // Engage multisite if in the middle of turning it on from network.php.
    $is_multisite = is_multisite() || defined('WP_INSTALLING_NETWORK') && WP_INSTALLING_NETWORK;
    /*
     * Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
     * As of 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
     * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
     */
    $max_index_length = 191;
    // Blog specific tables.
    $blog_tables = "CREATE TABLE {$wpdb->termmeta} (\n  meta_id bigint(20) unsigned NOT NULL auto_increment,\n  term_id bigint(20) unsigned NOT NULL default '0',\n  meta_key varchar(255) default NULL,\n  meta_value longtext,\n  PRIMARY KEY  (meta_id),\n  KEY term_id (term_id),\n  KEY meta_key (meta_key({$max_index_length}))\n) {$charset_collate};\nCREATE TABLE {$wpdb->terms} (\n term_id bigint(20) unsigned NOT NULL auto_increment,\n name varchar(200) NOT NULL default '',\n slug varchar(200) NOT NULL default '',\n term_group bigint(10) NOT NULL default 0,\n PRIMARY KEY  (term_id),\n KEY slug (slug({$max_index_length})),\n KEY name (name({$max_index_length}))\n) {$charset_collate};\nCREATE TABLE {$wpdb->term_taxonomy} (\n term_taxonomy_id bigint(20) unsigned NOT NULL auto_increment,\n term_id bigint(20) unsigned NOT NULL default 0,\n taxonomy varchar(32) NOT NULL default '',\n description longtext NOT NULL,\n parent bigint(20) unsigned NOT NULL default 0,\n count bigint(20) NOT NULL default 0,\n PRIMARY KEY  (term_taxonomy_id),\n UNIQUE KEY term_id_taxonomy (term_id,taxonomy),\n KEY taxonomy (taxonomy)\n) {$charset_collate};\nCREATE TABLE {$wpdb->term_relationships} (\n object_id bigint(20) unsigned NOT NULL default 0,\n term_taxonomy_id bigint(20) unsigned NOT NULL default 0,\n term_order int(11) NOT NULL default 0,\n PRIMARY KEY  (object_id,term_taxonomy_id),\n KEY term_taxonomy_id (term_taxonomy_id)\n) {$charset_collate};\nCREATE TABLE {$wpdb->commentmeta} (\n  meta_id bigint(20) unsigned NOT NULL auto_increment,\n  comment_id bigint(20) unsigned NOT NULL default '0',\n  meta_key varchar(255) default NULL,\n  meta_value longtext,\n  PRIMARY KEY  (meta_id),\n  KEY comment_id (comment_id),\n  KEY meta_key (meta_key({$max_index_length}))\n) {$charset_collate};\nCREATE TABLE {$wpdb->comments} (\n  comment_ID bigint(20) unsigned NOT NULL auto_increment,\n  comment_post_ID bigint(20) unsigned NOT NULL default '0',\n  comment_author tinytext NOT NULL,\n  comment_author_email varchar(100) NOT NULL default '',\n  comment_author_url varchar(200) NOT NULL default '',\n  comment_author_IP varchar(100) NOT NULL default '',\n  comment_date datetime NOT NULL default '0000-00-00 00:00:00',\n  comment_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',\n  comment_content text NOT NULL,\n  comment_karma int(11) NOT NULL default '0',\n  comment_approved varchar(20) NOT NULL default '1',\n  comment_agent varchar(255) NOT NULL default '',\n  comment_type varchar(20) NOT NULL default '',\n  comment_parent bigint(20) unsigned NOT NULL default '0',\n  user_id bigint(20) unsigned NOT NULL default '0',\n  PRIMARY KEY  (comment_ID),\n  KEY comment_post_ID (comment_post_ID),\n  KEY comment_approved_date_gmt (comment_approved,comment_date_gmt),\n  KEY comment_date_gmt (comment_date_gmt),\n  KEY comment_parent (comment_parent),\n  KEY comment_author_email (comment_author_email(10))\n) {$charset_collate};\nCREATE TABLE {$wpdb->links} (\n  link_id bigint(20) unsigned NOT NULL auto_increment,\n  link_url varchar(255) NOT NULL default '',\n  link_name varchar(255) NOT NULL default '',\n  link_image varchar(255) NOT NULL default '',\n  link_target varchar(25) NOT NULL default '',\n  link_description varchar(255) NOT NULL default '',\n  link_visible varchar(20) NOT NULL default 'Y',\n  link_owner bigint(20) unsigned NOT NULL default '1',\n  link_rating int(11) NOT NULL default '0',\n  link_updated datetime NOT NULL default '0000-00-00 00:00:00',\n  link_rel varchar(255) NOT NULL default '',\n  link_notes mediumtext NOT NULL,\n  link_rss varchar(255) NOT NULL default '',\n  PRIMARY KEY  (link_id),\n  KEY link_visible (link_visible)\n) {$charset_collate};\nCREATE TABLE {$wpdb->options} (\n  option_id bigint(20) unsigned NOT NULL auto_increment,\n  option_name varchar(191) NOT NULL default '',\n  option_value longtext NOT NULL,\n  autoload varchar(20) NOT NULL default 'yes',\n  PRIMARY KEY  (option_id),\n  UNIQUE KEY option_name (option_name)\n) {$charset_collate};\nCREATE TABLE {$wpdb->postmeta} (\n  meta_id bigint(20) unsigned NOT NULL auto_increment,\n  post_id bigint(20) unsigned NOT NULL default '0',\n  meta_key varchar(255) default NULL,\n  meta_value longtext,\n  PRIMARY KEY  (meta_id),\n  KEY post_id (post_id),\n  KEY meta_key (meta_key({$max_index_length}))\n) {$charset_collate};\nCREATE TABLE {$wpdb->posts} (\n  ID bigint(20) unsigned NOT NULL auto_increment,\n  post_author bigint(20) unsigned NOT NULL default '0',\n  post_date datetime NOT NULL default '0000-00-00 00:00:00',\n  post_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',\n  post_content longtext NOT NULL,\n  post_title text NOT NULL,\n  post_excerpt text NOT NULL,\n  post_status varchar(20) NOT NULL default 'publish',\n  comment_status varchar(20) NOT NULL default 'open',\n  ping_status varchar(20) NOT NULL default 'open',\n  post_password varchar(20) NOT NULL default '',\n  post_name varchar(200) NOT NULL default '',\n  to_ping text NOT NULL,\n  pinged text NOT NULL,\n  post_modified datetime NOT NULL default '0000-00-00 00:00:00',\n  post_modified_gmt datetime NOT NULL default '0000-00-00 00:00:00',\n  post_content_filtered longtext NOT NULL,\n  post_parent bigint(20) unsigned NOT NULL default '0',\n  guid varchar(255) NOT NULL default '',\n  menu_order int(11) NOT NULL default '0',\n  post_type varchar(20) NOT NULL default 'post',\n  post_mime_type varchar(100) NOT NULL default '',\n  comment_count bigint(20) NOT NULL default '0',\n  PRIMARY KEY  (ID),\n  KEY post_name (post_name({$max_index_length})),\n  KEY type_status_date (post_type,post_status,post_date,ID),\n  KEY post_parent (post_parent),\n  KEY post_author (post_author)\n) {$charset_collate};\n";
    // Single site users table. The multisite flavor of the users table is handled below.
    $users_single_table = "CREATE TABLE {$wpdb->users} (\n  ID bigint(20) unsigned NOT NULL auto_increment,\n  user_login varchar(60) NOT NULL default '',\n  user_pass varchar(255) NOT NULL default '',\n  user_nicename varchar(50) NOT NULL default '',\n  user_email varchar(100) NOT NULL default '',\n  user_url varchar(100) NOT NULL default '',\n  user_registered datetime NOT NULL default '0000-00-00 00:00:00',\n  user_activation_key varchar(255) NOT NULL default '',\n  user_status int(11) NOT NULL default '0',\n  display_name varchar(250) NOT NULL default '',\n  PRIMARY KEY  (ID),\n  KEY user_login_key (user_login),\n  KEY user_nicename (user_nicename),\n  KEY user_email (user_email)\n) {$charset_collate};\n";
    // Multisite users table
    $users_multi_table = "CREATE TABLE {$wpdb->users} (\n  ID bigint(20) unsigned NOT NULL auto_increment,\n  user_login varchar(60) NOT NULL default '',\n  user_pass varchar(255) NOT NULL default '',\n  user_nicename varchar(50) NOT NULL default '',\n  user_email varchar(100) NOT NULL default '',\n  user_url varchar(100) NOT NULL default '',\n  user_registered datetime NOT NULL default '0000-00-00 00:00:00',\n  user_activation_key varchar(255) NOT NULL default '',\n  user_status int(11) NOT NULL default '0',\n  display_name varchar(250) NOT NULL default '',\n  spam tinyint(2) NOT NULL default '0',\n  deleted tinyint(2) NOT NULL default '0',\n  PRIMARY KEY  (ID),\n  KEY user_login_key (user_login),\n  KEY user_nicename (user_nicename),\n  KEY user_email (user_email)\n) {$charset_collate};\n";
    // Usermeta.
    $usermeta_table = "CREATE TABLE {$wpdb->usermeta} (\n  umeta_id bigint(20) unsigned NOT NULL auto_increment,\n  user_id bigint(20) unsigned NOT NULL default '0',\n  meta_key varchar(255) default NULL,\n  meta_value longtext,\n  PRIMARY KEY  (umeta_id),\n  KEY user_id (user_id),\n  KEY meta_key (meta_key({$max_index_length}))\n) {$charset_collate};\n";
    // Global tables
    if ($is_multisite) {
        $global_tables = $users_multi_table . $usermeta_table;
    } else {
        $global_tables = $users_single_table . $usermeta_table;
    }
    // Multisite global tables.
    $ms_global_tables = "CREATE TABLE {$wpdb->blogs} (\n  blog_id bigint(20) NOT NULL auto_increment,\n  site_id bigint(20) NOT NULL default '0',\n  domain varchar(200) NOT NULL default '',\n  path varchar(100) NOT NULL default '',\n  registered datetime NOT NULL default '0000-00-00 00:00:00',\n  last_updated datetime NOT NULL default '0000-00-00 00:00:00',\n  public tinyint(2) NOT NULL default '1',\n  archived tinyint(2) NOT NULL default '0',\n  mature tinyint(2) NOT NULL default '0',\n  spam tinyint(2) NOT NULL default '0',\n  deleted tinyint(2) NOT NULL default '0',\n  lang_id int(11) NOT NULL default '0',\n  PRIMARY KEY  (blog_id),\n  KEY domain (domain(50),path(5)),\n  KEY lang_id (lang_id)\n) {$charset_collate};\nCREATE TABLE {$wpdb->blog_versions} (\n  blog_id bigint(20) NOT NULL default '0',\n  db_version varchar(20) NOT NULL default '',\n  last_updated datetime NOT NULL default '0000-00-00 00:00:00',\n  PRIMARY KEY  (blog_id),\n  KEY db_version (db_version)\n) {$charset_collate};\nCREATE TABLE {$wpdb->registration_log} (\n  ID bigint(20) NOT NULL auto_increment,\n  email varchar(255) NOT NULL default '',\n  IP varchar(30) NOT NULL default '',\n  blog_id bigint(20) NOT NULL default '0',\n  date_registered datetime NOT NULL default '0000-00-00 00:00:00',\n  PRIMARY KEY  (ID),\n  KEY IP (IP)\n) {$charset_collate};\nCREATE TABLE {$wpdb->site} (\n  id bigint(20) NOT NULL auto_increment,\n  domain varchar(200) NOT NULL default '',\n  path varchar(100) NOT NULL default '',\n  PRIMARY KEY  (id),\n  KEY domain (domain(140),path(51))\n) {$charset_collate};\nCREATE TABLE {$wpdb->sitemeta} (\n  meta_id bigint(20) NOT NULL auto_increment,\n  site_id bigint(20) NOT NULL default '0',\n  meta_key varchar(255) default NULL,\n  meta_value longtext,\n  PRIMARY KEY  (meta_id),\n  KEY meta_key (meta_key({$max_index_length})),\n  KEY site_id (site_id)\n) {$charset_collate};\nCREATE TABLE {$wpdb->signups} (\n  signup_id bigint(20) NOT NULL auto_increment,\n  domain varchar(200) NOT NULL default '',\n  path varchar(100) NOT NULL default '',\n  title longtext NOT NULL,\n  user_login varchar(60) NOT NULL default '',\n  user_email varchar(100) NOT NULL default '',\n  registered datetime NOT NULL default '0000-00-00 00:00:00',\n  activated datetime NOT NULL default '0000-00-00 00:00:00',\n  active tinyint(1) NOT NULL default '0',\n  activation_key varchar(50) NOT NULL default '',\n  meta longtext,\n  PRIMARY KEY  (signup_id),\n  KEY activation_key (activation_key),\n  KEY user_email (user_email),\n  KEY user_login_email (user_login,user_email),\n  KEY domain_path (domain(140),path(51))\n) {$charset_collate};";
    switch ($scope) {
        case 'blog':
            $queries = $blog_tables;
            break;
        case 'global':
            $queries = $global_tables;
            if ($is_multisite) {
                $queries .= $ms_global_tables;
            }
            break;
        case 'ms_global':
            $queries = $ms_global_tables;
            break;
        case 'all':
        default:
            $queries = $global_tables . $blog_tables;
            if ($is_multisite) {
                $queries .= $ms_global_tables;
            }
            break;
    }
    if (isset($old_blog_id)) {
        $wpdb->set_blog_id($old_blog_id);
    }
    return $queries;
}
// Populate for back compat.
$wp_queries = wp_get_db_schema('all');
/**
 * Create WordPress options and set the default values.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @global int  $wp_db_version
 * @global int  $wp_current_db_version
 */
function populate_options()
{
    global $wpdb, $wp_db_version, $wp_current_db_version;
    $guessurl = wp_guess_url();
    /**
     * Fires before creating WordPress options and populating their default values.
     *
     * @since 2.6.0
     */
    do_action('populate_options');
    if (ini_get('safe_mode')) {
        // Safe mode can break mkdir() so use a flat structure by default.
        $uploads_use_yearmonth_folders = 0;
    } else {
        $uploads_use_yearmonth_folders = 1;
    }
    // If WP_DEFAULT_THEME doesn't exist, fall back to the latest core default theme.
    $stylesheet = $template = WP_DEFAULT_THEME;
    $theme = wp_get_theme(WP_DEFAULT_THEME);
    if (!$theme->exists()) {
        $theme = WP_Theme::get_core_default_theme();
    }
    // If we can't find a core default theme, WP_DEFAULT_THEME is the best we can do.
    if ($theme) {
        $stylesheet = $theme->get_stylesheet();
        $template = $theme->get_template();
    }
    $timezone_string = '';
    $gmt_offset = 0;
    /* translators: default GMT offset or timezone string. Must be either a valid offset (-12 to 14)
    	   or a valid timezone string (America/New_York). See https://secure.php.net/manual/en/timezones.php
    	   for all timezone strings supported by PHP.
    	*/
    $offset_or_tz = _x('0', 'default GMT offset or timezone string');
    if (is_numeric($offset_or_tz)) {
        $gmt_offset = $offset_or_tz;
    } elseif ($offset_or_tz && in_array($offset_or_tz, timezone_identifiers_list())) {
        $timezone_string = $offset_or_tz;
    }
    $options = array('siteurl' => $guessurl, 'home' => $guessurl, 'blogname' => __('My Site'), 'blogdescription' => __('Just another WordPress site'), 'users_can_register' => 0, 'admin_email' => 'you@example.com', 'start_of_week' => _x('1', 'start of week'), 'use_balanceTags' => 0, 'use_smilies' => 1, 'require_name_email' => 1, 'comments_notify' => 1, 'posts_per_rss' => 10, 'rss_use_excerpt' => 0, 'mailserver_url' => 'mail.example.com', 'mailserver_login' => 'login@example.com', 'mailserver_pass' => 'password', 'mailserver_port' => 110, 'default_category' => 1, 'default_comment_status' => 'open', 'default_ping_status' => 'open', 'default_pingback_flag' => 1, 'posts_per_page' => 10, 'date_format' => __('F j, Y'), 'time_format' => __('g:i a'), 'links_updated_date_format' => __('F j, Y g:i a'), 'comment_moderation' => 0, 'moderation_notify' => 1, 'permalink_structure' => '', 'rewrite_rules' => '', 'hack_file' => 0, 'blog_charset' => 'UTF-8', 'moderation_keys' => '', 'active_plugins' => array(), 'category_base' => '', 'ping_sites' => 'http://rpc.pingomatic.com/', 'comment_max_links' => 2, 'gmt_offset' => $gmt_offset, 'default_email_category' => 1, 'recently_edited' => '', 'template' => $template, 'stylesheet' => $stylesheet, 'comment_whitelist' => 1, 'blacklist_keys' => '', 'comment_registration' => 0, 'html_type' => 'text/html', 'use_trackback' => 0, 'default_role' => 'subscriber', 'db_version' => $wp_db_version, 'uploads_use_yearmonth_folders' => $uploads_use_yearmonth_folders, 'upload_path' => '', 'blog_public' => '1', 'default_link_category' => 2, 'show_on_front' => 'posts', 'tag_base' => '', 'show_avatars' => '1', 'avatar_rating' => 'G', 'upload_url_path' => '', 'thumbnail_size_w' => 150, 'thumbnail_size_h' => 150, 'thumbnail_crop' => 1, 'medium_size_w' => 300, 'medium_size_h' => 300, 'avatar_default' => 'mystery', 'large_size_w' => 1024, 'large_size_h' => 1024, 'image_default_link_type' => 'none', 'image_default_size' => '', 'image_default_align' => '', 'close_comments_for_old_posts' => 0, 'close_comments_days_old' => 14, 'thread_comments' => 1, 'thread_comments_depth' => 5, 'page_comments' => 0, 'comments_per_page' => 50, 'default_comments_page' => 'newest', 'comment_order' => 'asc', 'sticky_posts' => array(), 'widget_categories' => array(), 'widget_text' => array(), 'widget_rss' => array(), 'uninstall_plugins' => array(), 'timezone_string' => $timezone_string, 'page_for_posts' => 0, 'page_on_front' => 0, 'default_post_format' => 0, 'link_manager_enabled' => 0, 'finished_splitting_shared_terms' => 1, 'site_icon' => 0, 'medium_large_size_w' => 768, 'medium_large_size_h' => 0);
    // 3.3
    if (!is_multisite()) {
        $options['initial_db_version'] = !empty($wp_current_db_version) && $wp_current_db_version < $wp_db_version ? $wp_current_db_version : $wp_db_version;
    }
    // 3.0 multisite
    if (is_multisite()) {
        /* translators: site tagline */
        $options['blogdescription'] = sprintf(__('Just another %s site'), get_current_site()->site_name);
        $options['permalink_structure'] = '/%year%/%monthnum%/%day%/%postname%/';
    }
    // Set autoload to no for these options
    $fat_options = array('moderation_keys', 'recently_edited', 'blacklist_keys', 'uninstall_plugins');
    $keys = "'" . implode("', '", array_keys($options)) . "'";
    $existing_options = $wpdb->get_col("SELECT option_name FROM {$wpdb->options} WHERE option_name in ( {$keys} )");
    $insert = '';
    foreach ($options as $option => $value) {
        if (in_array($option, $existing_options)) {
            continue;
        }
        if (in_array($option, $fat_options)) {
            $autoload = 'no';
        } else {
            $autoload = 'yes';
        }
        if (is_array($value)) {
            $value = serialize($value);
        }
        if (!empty($insert)) {
            $insert .= ', ';
        }
        $insert .= $wpdb->prepare("(%s, %s, %s)", $option, $value, $autoload);
    }
    if (!empty($insert)) {
        $wpdb->query("INSERT INTO {$wpdb->options} (option_name, option_value, autoload) VALUES " . $insert);
    }
    // In case it is set, but blank, update "home".
    if (!__get_option('home')) {
        update_option('home', $guessurl);
    }
    // Delete unused options.
    $unusedoptions = array('blodotgsping_url', 'bodyterminator', 'emailtestonly', 'phoneemail_separator', 'smilies_directory', 'subjectprefix', 'use_bbcode', 'use_blodotgsping', 'use_phoneemail', 'use_quicktags', 'use_weblogsping', 'weblogs_cache_file', 'use_preview', 'use_htmltrans', 'smilies_directory', 'fileupload_allowedusers', 'use_phoneemail', 'default_post_status', 'default_post_category', 'archive_mode', 'time_difference', 'links_minadminlevel', 'links_use_adminlevels', 'links_rating_type', 'links_rating_char', 'links_rating_ignore_zero', 'links_rating_single_image', 'links_rating_image0', 'links_rating_image1', 'links_rating_image2', 'links_rating_image3', 'links_rating_image4', 'links_rating_image5', 'links_rating_image6', 'links_rating_image7', 'links_rating_image8', 'links_rating_image9', 'links_recently_updated_time', 'links_recently_updated_prepend', 'links_recently_updated_append', 'weblogs_cacheminutes', 'comment_allowed_tags', 'search_engine_friendly_urls', 'default_geourl_lat', 'default_geourl_lon', 'use_default_geourl', 'weblogs_xml_url', 'new_users_can_blog', '_wpnonce', '_wp_http_referer', 'Update', 'action', 'rich_editing', 'autosave_interval', 'deactivated_plugins', 'can_compress_scripts', 'page_uris', 'update_core', 'update_plugins', 'update_themes', 'doing_cron', 'random_seed', 'rss_excerpt_length', 'secret', 'use_linksupdate', 'default_comment_status_page', 'wporg_popular_tags', 'what_to_show', 'rss_language', 'language', 'enable_xmlrpc', 'enable_app', 'embed_autourls', 'default_post_edit_rows', 'gzipcompression', 'advanced_edit');
    foreach ($unusedoptions as $option) {
        delete_option($option);
    }
    // Delete obsolete magpie stuff.
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name REGEXP '^rss_[0-9a-f]{32}(_ts)?\$'");
    /*
     * Deletes all expired transients. The multi-table delete syntax is used
     * to delete the transient record from table a, and the corresponding
     * transient_timeout record from table b.
     */
    $time = time();
    $sql = "DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b\n\t\tWHERE a.option_name LIKE %s\n\t\tAND a.option_name NOT LIKE %s\n\t\tAND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )\n\t\tAND b.option_value < %d";
    $wpdb->query($wpdb->prepare($sql, $wpdb->esc_like('_transient_') . '%', $wpdb->esc_like('_transient_timeout_') . '%', $time));
    if (is_main_site() && is_main_network()) {
        $sql = "DELETE a, b FROM {$wpdb->options} a, {$wpdb->options} b\n\t\t\tWHERE a.option_name LIKE %s\n\t\t\tAND a.option_name NOT LIKE %s\n\t\t\tAND b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, 17 ) )\n\t\t\tAND b.option_value < %d";
        $wpdb->query($wpdb->prepare($sql, $wpdb->esc_like('_site_transient_') . '%', $wpdb->esc_like('_site_transient_timeout_') . '%', $time));
    }
}
/**
 * Execute WordPress role creation for the various WordPress versions.
 *
 * @since 2.0.0
 */
function populate_roles()
{
    populate_roles_160();
    populate_roles_210();
    populate_roles_230();
    populate_roles_250();
    populate_roles_260();
    populate_roles_270();
    populate_roles_280();
    populate_roles_300();
}
/**
 * Create the roles for WordPress 2.0
 *
 * @since 2.0.0
 */
function populate_roles_160()
{
    // Add roles
    // Dummy gettext calls to get strings in the catalog.
    /* translators: user role */
    _x('Administrator', 'User role');
    /* translators: user role */
    _x('Editor', 'User role');
    /* translators: user role */
    _x('Author', 'User role');
    /* translators: user role */
    _x('Contributor', 'User role');
    /* translators: user role */
    _x('Subscriber', 'User role');
    add_role('administrator', 'Administrator');
    add_role('editor', 'Editor');
    add_role('author', 'Author');
    add_role('contributor', 'Contributor');
    add_role('subscriber', 'Subscriber');
    // Add caps for Administrator role
    $role = get_role('administrator');
    $role->add_cap('switch_themes');
    $role->add_cap('edit_themes');
    $role->add_cap('activate_plugins');
    $role->add_cap('edit_plugins');
    $role->add_cap('edit_users');
    $role->add_cap('edit_files');
    $role->add_cap('manage_options');
    $role->add_cap('moderate_comments');
    $role->add_cap('manage_categories');
    $role->add_cap('manage_links');
    $role->add_cap('upload_files');
    $role->add_cap('import');
    $role->add_cap('unfiltered_html');
    $role->add_cap('edit_posts');
    $role->add_cap('edit_others_posts');
    $role->add_cap('edit_published_posts');
    $role->add_cap('publish_posts');
    $role->add_cap('edit_pages');
    $role->add_cap('read');
    $role->add_cap('level_10');
    $role->add_cap('level_9');
    $role->add_cap('level_8');
    $role->add_cap('level_7');
    $role->add_cap('level_6');
    $role->add_cap('level_5');
    $role->add_cap('level_4');
    $role->add_cap('level_3');
    $role->add_cap('level_2');
    $role->add_cap('level_1');
    $role->add_cap('level_0');
    // Add caps for Editor role
    $role = get_role('editor');
    $role->add_cap('moderate_comments');
    $role->add_cap('manage_categories');
    $role->add_cap('manage_links');
    $role->add_cap('upload_files');
    $role->add_cap('unfiltered_html');
    $role->add_cap('edit_posts');
    $role->add_cap('edit_others_posts');
    $role->add_cap('edit_published_posts');
    $role->add_cap('publish_posts');
    $role->add_cap('edit_pages');
    $role->add_cap('read');
    $role->add_cap('level_7');
    $role->add_cap('level_6');
    $role->add_cap('level_5');
    $role->add_cap('level_4');
    $role->add_cap('level_3');
    $role->add_cap('level_2');
    $role->add_cap('level_1');
    $role->add_cap('level_0');
    // Add caps for Author role
    $role = get_role('author');
    $role->add_cap('upload_files');
    $role->add_cap('edit_posts');
    $role->add_cap('edit_published_posts');
    $role->add_cap('publish_posts');
    $role->add_cap('read');
    $role->add_cap('level_2');
    $role->add_cap('level_1');
    $role->add_cap('level_0');
    // Add caps for Contributor role
    $role = get_role('contributor');
    $role->add_cap('edit_posts');
    $role->add_cap('read');
    $role->add_cap('level_1');
    $role->add_cap('level_0');
    // Add caps for Subscriber role
    $role = get_role('subscriber');
    $role->add_cap('read');
    $role->add_cap('level_0');
}
/**
 * Create and modify WordPress roles for WordPress 2.1.
 *
 * @since 2.1.0
 */
function populate_roles_210()
{
    $roles = array('administrator', 'editor');
    foreach ($roles as $role) {
        $role = get_role($role);
        if (empty($role)) {
            continue;
        }
        $role->add_cap('edit_others_pages');
        $role->add_cap('edit_published_pages');
        $role->add_cap('publish_pages');
        $role->add_cap('delete_pages');
        $role->add_cap('delete_others_pages');
        $role->add_cap('delete_published_pages');
        $role->add_cap('delete_posts');
        $role->add_cap('delete_others_posts');
        $role->add_cap('delete_published_posts');
        $role->add_cap('delete_private_posts');
        $role->add_cap('edit_private_posts');
        $role->add_cap('read_private_posts');
        $role->add_cap('delete_private_pages');
        $role->add_cap('edit_private_pages');
        $role->add_cap('read_private_pages');
    }
    $role = get_role('administrator');
    if (!empty($role)) {
        $role->add_cap('delete_users');
        $role->add_cap('create_users');
    }
    $role = get_role('author');
    if (!empty($role)) {
        $role->add_cap('delete_posts');
        $role->add_cap('delete_published_posts');
    }
    $role = get_role('contributor');
    if (!empty($role)) {
        $role->add_cap('delete_posts');
    }
}
/**
 * Create and modify WordPress roles for WordPress 2.3.
 *
 * @since 2.3.0
 */
function populate_roles_230()
{
    $role = get_role('administrator');
    if (!empty($role)) {
        $role->add_cap('unfiltered_upload');
    }
}
/**
 * Create and modify WordPress roles for WordPress 2.5.
 *
 * @since 2.5.0
 */
function populate_roles_250()
{
    $role = get_role('administrator');
    if (!empty($role)) {
        $role->add_cap('edit_dashboard');
    }
}
/**
 * Create and modify WordPress roles for WordPress 2.6.
 *
 * @since 2.6.0
 */
function populate_roles_260()
{
    $role = get_role('administrator');
    if (!empty($role)) {
        $role->add_cap('update_plugins');
        $role->add_cap('delete_plugins');
    }
}
/**
 * Create and modify WordPress roles for WordPress 2.7.
 *
 * @since 2.7.0
 */
function populate_roles_270()
{
    $role = get_role('administrator');
    if (!empty($role)) {
        $role->add_cap('install_plugins');
        $role->add_cap('update_themes');
    }
}
/**
 * Create and modify WordPress roles for WordPress 2.8.
 *
 * @since 2.8.0
 */
function populate_roles_280()
{
    $role = get_role('administrator');
    if (!empty($role)) {
        $role->add_cap('install_themes');
    }
}
/**
 * Create and modify WordPress roles for WordPress 3.0.
 *
 * @since 3.0.0
 */
function populate_roles_300()
{
    $role = get_role('administrator');
    if (!empty($role)) {
        $role->add_cap('update_core');
        $role->add_cap('list_users');
        $role->add_cap('remove_users');
        $role->add_cap('promote_users');
        $role->add_cap('edit_theme_options');
        $role->add_cap('delete_themes');
        $role->add_cap('export');
    }
}
/**
 * Install Network.
 *
 * @since 3.0.0
 *
 */
if (!function_exists('install_network')) {
    function install_network()
    {
        if (!defined('WP_INSTALLING_NETWORK')) {
            define('WP_INSTALLING_NETWORK', true);
        }
        dbDelta(wp_get_db_schema('global'));
    }
}
/**
 * Populate network settings.
 *
 * @since 3.0.0
 *
 * @global wpdb       $wpdb
 * @global object     $current_site
 * @global int        $wp_db_version
 * @global WP_Rewrite $wp_rewrite
 *
 * @param int    $network_id        ID of network to populate.
 * @param string $domain            The domain name for the network (eg. "example.com").
 * @param string $email             Email address for the network administrator.
 * @param string $site_name         The name of the network.
 * @param string $path              Optional. The path to append to the network's domain name. Default '/'.
 * @param bool   $subdomain_install Optional. Whether the network is a subdomain install or a subdirectory install.
 *                                  Default false, meaning the network is a subdirectory install.
 * @return bool|WP_Error True on success, or WP_Error on warning (with the install otherwise successful,
 *                       so the error code must be checked) or failure.
 */
function populate_network($network_id = 1, $domain = '', $email = '', $site_name = '', $path = '/', $subdomain_install = false)
{
    global $wpdb, $current_site, $wp_db_version, $wp_rewrite;
    $errors = new WP_Error();
    if ('' == $domain) {
        $errors->add('empty_domain', __('You must provide a domain name.'));
    }
    if ('' == $site_name) {
        $errors->add('empty_sitename', __('You must provide a name for your network of sites.'));
    }
    // Check for network collision.
    if ($network_id == $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->site} WHERE id = %d", $network_id))) {
        $errors->add('siteid_exists', __('The network already exists.'));
    }
    if (!is_email($email)) {
        $errors->add('invalid_email', __('You must provide a valid email address.'));
    }
    if ($errors->get_error_code()) {
        return $errors;
    }
    // If a user with the provided email does not exist, default to the current user as the new network admin.
    $site_user = get_user_by('email', $email);
    if (false === $site_user) {
        $site_user = wp_get_current_user();
    }
    // Set up site tables.
    $template = get_option('template');
    $stylesheet = get_option('stylesheet');
    $allowed_themes = array($stylesheet => true);
    if ($template != $stylesheet) {
        $allowed_themes[$template] = true;
    }
    if (WP_DEFAULT_THEME != $stylesheet && WP_DEFAULT_THEME != $template) {
        $allowed_themes[WP_DEFAULT_THEME] = true;
    }
    // If WP_DEFAULT_THEME doesn't exist, also whitelist the latest core default theme.
    if (!wp_get_theme(WP_DEFAULT_THEME)->exists()) {
        if ($core_default = WP_Theme::get_core_default_theme()) {
            $allowed_themes[$core_default->get_stylesheet()] = true;
        }
    }
    if (1 == $network_id) {
        $wpdb->insert($wpdb->site, array('domain' => $domain, 'path' => $path));
        $network_id = $wpdb->insert_id;
    } else {
        $wpdb->insert($wpdb->site, array('domain' => $domain, 'path' => $path, 'id' => $network_id));
    }
    wp_cache_delete('networks_have_paths', 'site-options');
    if (!is_multisite()) {
        $site_admins = array($site_user->user_login);
        $users = get_users(array('fields' => array('ID', 'user_login')));
        if ($users) {
            foreach ($users as $user) {
                if (is_super_admin($user->ID) && !in_array($user->user_login, $site_admins)) {
                    $site_admins[] = $user->user_login;
                }
            }
        }
    } else {
        $site_admins = get_site_option('site_admins');
    }
    /* translators: Do not translate USERNAME, SITE_NAME, BLOG_URL, PASSWORD: those are placeholders. */
    $welcome_email = __('Howdy USERNAME,

Your new SITE_NAME site has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:

Username: USERNAME
Password: PASSWORD
Log in here: BLOG_URLwp-login.php

We hope you enjoy your new site. Thanks!

--The Team @ SITE_NAME');
    $misc_exts = array('jpg', 'jpeg', 'png', 'gif', 'mov', 'avi', 'mpg', '3gp', '3g2', 'midi', 'mid', 'pdf', 'doc', 'ppt', 'odt', 'pptx', 'docx', 'pps', 'ppsx', 'xls', 'xlsx', 'key');
    $audio_exts = wp_get_audio_extensions();
    $video_exts = wp_get_video_extensions();
    $upload_filetypes = array_unique(array_merge($misc_exts, $audio_exts, $video_exts));
    $sitemeta = array('site_name' => $site_name, 'admin_email' => $email, 'admin_user_id' => $site_user->ID, 'registration' => 'none', 'upload_filetypes' => implode(' ', $upload_filetypes), 'blog_upload_space' => 100, 'fileupload_maxk' => 1500, 'site_admins' => $site_admins, 'allowedthemes' => $allowed_themes, 'illegal_names' => array('www', 'web', 'root', 'admin', 'main', 'invite', 'administrator', 'files'), 'wpmu_upgrade_site' => $wp_db_version, 'welcome_email' => $welcome_email, 'first_post' => __('Welcome to %s. This is your first post. Edit or delete it, then start blogging!'), 'siteurl' => get_option('siteurl') . '/', 'add_new_users' => '0', 'upload_space_check_disabled' => is_multisite() ? get_site_option('upload_space_check_disabled') : '1', 'subdomain_install' => intval($subdomain_install), 'global_terms_enabled' => global_terms_enabled() ? '1' : '0', 'ms_files_rewriting' => is_multisite() ? get_site_option('ms_files_rewriting') : '0', 'initial_db_version' => get_option('initial_db_version'), 'active_sitewide_plugins' => array(), 'WPLANG' => get_locale());
    if (!$subdomain_install) {
        $sitemeta['illegal_names'][] = 'blog';
    }
    /**
     * Filters meta for a network on creation.
     *
     * @since 3.7.0
     *
     * @param array $sitemeta   Associative array of network meta keys and values to be inserted.
     * @param int   $network_id ID of network to populate.
     */
    $sitemeta = apply_filters('populate_network_meta', $sitemeta, $network_id);
    $insert = '';
    foreach ($sitemeta as $meta_key => $meta_value) {
        if (is_array($meta_value)) {
            $meta_value = serialize($meta_value);
        }
        if (!empty($insert)) {
            $insert .= ', ';
        }
        $insert .= $wpdb->prepare("( %d, %s, %s)", $network_id, $meta_key, $meta_value);
    }
    $wpdb->query("INSERT INTO {$wpdb->sitemeta} ( site_id, meta_key, meta_value ) VALUES " . $insert);
    /*
     * When upgrading from single to multisite, assume the current site will
     * become the main site of the network. When using populate_network()
     * to create another network in an existing multisite environment, skip
     * these steps since the main site of the new network has not yet been
     * created.
     */
    if (!is_multisite()) {
        $current_site = new stdClass();
        $current_site->domain = $domain;
        $current_site->path = $path;
        $current_site->site_name = ucfirst($domain);
        $wpdb->insert($wpdb->blogs, array('site_id' => $network_id, 'blog_id' => 1, 'domain' => $domain, 'path' => $path, 'registered' => current_time('mysql')));
        $current_site->blog_id = $blog_id = $wpdb->insert_id;
        update_user_meta($site_user->ID, 'source_domain', $domain);
        update_user_meta($site_user->ID, 'primary_blog', $blog_id);
        if ($subdomain_install) {
            $wp_rewrite->set_permalink_structure('/%year%/%monthnum%/%day%/%postname%/');
        } else {
            $wp_rewrite->set_permalink_structure('/blog/%year%/%monthnum%/%day%/%postname%/');
        }
        flush_rewrite_rules();
        if (!$subdomain_install) {
            return true;
        }
        $vhost_ok = false;
        $errstr = '';
        $hostname = substr(md5(time()), 0, 6) . '.' . $domain;
        // Very random hostname!
        $page = wp_remote_get('http://' . $hostname, array('timeout' => 5, 'httpversion' => '1.1'));
        if (is_wp_error($page)) {
            $errstr = $page->get_error_message();
        } elseif (200 == wp_remote_retrieve_response_code($page)) {
            $vhost_ok = true;
        }
        if (!$vhost_ok) {
            $msg = '<p><strong>' . __('Warning! Wildcard DNS may not be configured correctly!') . '</strong></p>';
            $msg .= '<p>' . sprintf(__('The installer attempted to contact a random hostname (%s) on your domain.'), '<code>' . $hostname . '</code>');
            if (!empty($errstr)) {
                /* translators: %s: error message */
                $msg .= ' ' . sprintf(__('This resulted in an error message: %s'), '<code>' . $errstr . '</code>');
            }
            $msg .= '</p>';
            $msg .= '<p>' . sprintf(__('To use a subdomain configuration, you must have a wildcard entry in your DNS. This usually means adding a %s hostname record pointing at your web server in your DNS configuration tool.'), '<code>*</code>') . '</p>';
            $msg .= '<p>' . __('You can still use your site but any subdomain you create may not be accessible. If you know your DNS is correct, ignore this message.') . '</p>';
            return new WP_Error('no_wildcard_dns', $msg);
        }
    }
    return true;
}