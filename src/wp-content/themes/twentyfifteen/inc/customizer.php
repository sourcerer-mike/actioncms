<?php

/**
 * Twenty Fifteen Customizer functionality
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
/**
 * Add postMessage support for site title and description for the Customizer.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function twentyfifteen_customize_register($wp_customize)
{
    $color_scheme = twentyfifteen_get_color_scheme();
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial('blogname', array('selector' => '.site-title a', 'container_inclusive' => false, 'render_callback' => 'twentyfifteen_customize_partial_blogname'));
        $wp_customize->selective_refresh->add_partial('blogdescription', array('selector' => '.site-description', 'container_inclusive' => false, 'render_callback' => 'twentyfifteen_customize_partial_blogdescription'));
    }
    // Add color scheme setting and control.
    $wp_customize->add_setting('color_scheme', array('default' => 'default', 'sanitize_callback' => 'twentyfifteen_sanitize_color_scheme', 'transport' => 'postMessage'));
    $wp_customize->add_control('color_scheme', array('label' => __('Base Color Scheme', 'twentyfifteen'), 'section' => 'colors', 'type' => 'select', 'choices' => twentyfifteen_get_color_scheme_choices(), 'priority' => 1));
    // Add custom header and sidebar text color setting and control.
    $wp_customize->add_setting('sidebar_textcolor', array('default' => $color_scheme[4], 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sidebar_textcolor', array('label' => __('Header and Sidebar Text Color', 'twentyfifteen'), 'description' => __('Applied to the header on small screens and the sidebar on wide screens.', 'twentyfifteen'), 'section' => 'colors')));
    // Remove the core header textcolor control, as it shares the sidebar text color.
    $wp_customize->remove_control('header_textcolor');
    // Add custom header and sidebar background color setting and control.
    $wp_customize->add_setting('header_background_color', array('default' => $color_scheme[1], 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_background_color', array('label' => __('Header and Sidebar Background Color', 'twentyfifteen'), 'description' => __('Applied to the header on small screens and the sidebar on wide screens.', 'twentyfifteen'), 'section' => 'colors')));
    // Add an additional description to the header image section.
    $wp_customize->get_section('header_image')->description = __('Applied to the header on small screens and the sidebar on wide screens.', 'twentyfifteen');
}
add_action('customize_register', 'twentyfifteen_customize_register', 11);
/**
 * Render the site title for the selective refresh partial.
 *
 * @since Twenty Fifteen 1.5
 * @see twentyfifteen_customize_register()
 *
 * @return void
 */
function twentyfifteen_customize_partial_blogname()
{
    bloginfo('name');
}
/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since Twenty Fifteen 1.5
 * @see twentyfifteen_customize_register()
 *
 * @return void
 */
function twentyfifteen_customize_partial_blogdescription()
{
    bloginfo('description');
}
/**
 * Register color schemes for Twenty Fifteen.
 *
 * Can be filtered with {@see 'twentyfifteen_color_schemes'}.
 *
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Sidebar Background Color.
 * 3. Box Background Color.
 * 4. Main Text and Link Color.
 * 5. Sidebar Text and Link Color.
 * 6. Meta Box Background Color.
 *
 * @since Twenty Fifteen 1.0
 *
 * @return array An associative array of color scheme options.
 */
function twentyfifteen_get_color_schemes()
{
    /**
     * Filter the color schemes registered for use with Twenty Fifteen.
     *
     * The default schemes include 'default', 'dark', 'yellow', 'pink', 'purple', and 'blue'.
     *
     * @since Twenty Fifteen 1.0
     *
     * @param array $schemes {
     *     Associative array of color schemes data.
     *
     *     @type array $slug {
     *         Associative array of information for setting up the color scheme.
     *
     *         @type string $label  Color scheme label.
     *         @type array  $colors HEX codes for default colors prepended with a hash symbol ('#').
     *                              Colors are defined in the following order: Main background, sidebar
     *                              background, box background, main text and link, sidebar text and link,
     *                              meta box background.
     *     }
     * }
     */
    return apply_filters('twentyfifteen_color_schemes', array('default' => array('label' => __('Default', 'twentyfifteen'), 'colors' => array('#f1f1f1', '#ffffff', '#ffffff', '#333333', '#333333', '#f7f7f7')), 'dark' => array('label' => __('Dark', 'twentyfifteen'), 'colors' => array('#111111', '#202020', '#202020', '#bebebe', '#bebebe', '#1b1b1b')), 'yellow' => array('label' => __('Yellow', 'twentyfifteen'), 'colors' => array('#f4ca16', '#ffdf00', '#ffffff', '#111111', '#111111', '#f1f1f1')), 'pink' => array('label' => __('Pink', 'twentyfifteen'), 'colors' => array('#ffe5d1', '#e53b51', '#ffffff', '#352712', '#ffffff', '#f1f1f1')), 'purple' => array('label' => __('Purple', 'twentyfifteen'), 'colors' => array('#674970', '#2e2256', '#ffffff', '#2e2256', '#ffffff', '#f1f1f1')), 'blue' => array('label' => __('Blue', 'twentyfifteen'), 'colors' => array('#e9f2f9', '#55c3dc', '#ffffff', '#22313f', '#ffffff', '#f1f1f1'))));
}
if (!function_exists('twentyfifteen_get_color_scheme')) {
    /**
     * Get the current Twenty Fifteen color scheme.
     *
     * @since Twenty Fifteen 1.0
     *
     * @return array An associative array of either the current or default color scheme hex values.
     */
    function twentyfifteen_get_color_scheme()
    {
        $color_scheme_option = get_theme_mod('color_scheme', 'default');
        $color_schemes = twentyfifteen_get_color_schemes();
        if (array_key_exists($color_scheme_option, $color_schemes)) {
            return $color_schemes[$color_scheme_option]['colors'];
        }
        return $color_schemes['default']['colors'];
    }
}
// twentyfifteen_get_color_scheme
if (!function_exists('twentyfifteen_get_color_scheme_choices')) {
    /**
     * Returns an array of color scheme choices registered for Twenty Fifteen.
     *
     * @since Twenty Fifteen 1.0
     *
     * @return array Array of color schemes.
     */
    function twentyfifteen_get_color_scheme_choices()
    {
        $color_schemes = twentyfifteen_get_color_schemes();
        $color_scheme_control_options = array();
        foreach ($color_schemes as $color_scheme => $value) {
            $color_scheme_control_options[$color_scheme] = $value['label'];
        }
        return $color_scheme_control_options;
    }
}
// twentyfifteen_get_color_scheme_choices
if (!function_exists('twentyfifteen_sanitize_color_scheme')) {
    /**
     * Sanitization callback for color schemes.
     *
     * @since Twenty Fifteen 1.0
     *
     * @param string $value Color scheme name value.
     * @return string Color scheme name.
     */
    function twentyfifteen_sanitize_color_scheme($value)
    {
        $color_schemes = twentyfifteen_get_color_scheme_choices();
        if (!array_key_exists($value, $color_schemes)) {
            $value = 'default';
        }
        return $value;
    }
}
// twentyfifteen_sanitize_color_scheme
/**
 * Enqueues front-end CSS for color scheme.
 *
 * @since Twenty Fifteen 1.0
 *
 * @see wp_add_inline_style()
 */
function twentyfifteen_color_scheme_css()
{
    $color_scheme_option = get_theme_mod('color_scheme', 'default');
    // Don't do anything if the default color scheme is selected.
    if ('default' === $color_scheme_option) {
        return;
    }
    $color_scheme = twentyfifteen_get_color_scheme();
    // Convert main and sidebar text hex color to rgba.
    $color_textcolor_rgb = twentyfifteen_hex2rgb($color_scheme[3]);
    $color_sidebar_textcolor_rgb = twentyfifteen_hex2rgb($color_scheme[4]);
    $colors = array('background_color' => $color_scheme[0], 'header_background_color' => $color_scheme[1], 'box_background_color' => $color_scheme[2], 'textcolor' => $color_scheme[3], 'secondary_textcolor' => vsprintf('rgba( %1$s, %2$s, %3$s, 0.7)', $color_textcolor_rgb), 'border_color' => vsprintf('rgba( %1$s, %2$s, %3$s, 0.1)', $color_textcolor_rgb), 'border_focus_color' => vsprintf('rgba( %1$s, %2$s, %3$s, 0.3)', $color_textcolor_rgb), 'sidebar_textcolor' => $color_scheme[4], 'sidebar_border_color' => vsprintf('rgba( %1$s, %2$s, %3$s, 0.1)', $color_sidebar_textcolor_rgb), 'sidebar_border_focus_color' => vsprintf('rgba( %1$s, %2$s, %3$s, 0.3)', $color_sidebar_textcolor_rgb), 'secondary_sidebar_textcolor' => vsprintf('rgba( %1$s, %2$s, %3$s, 0.7)', $color_sidebar_textcolor_rgb), 'meta_box_background_color' => $color_scheme[5]);
    $color_scheme_css = twentyfifteen_get_color_scheme_css($colors);
    wp_add_inline_style('twentyfifteen-style', $color_scheme_css);
}
add_action('wp_enqueue_scripts', 'twentyfifteen_color_scheme_css');
/**
 * Binds JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_customize_control_js()
{
    wp_enqueue_script('color-scheme-control', get_template_directory_uri() . '/js/color-scheme-control.js', array('customize-controls', 'iris', 'underscore', 'wp-util'), '20141216', true);
    wp_localize_script('color-scheme-control', 'colorScheme', twentyfifteen_get_color_schemes());
}
add_action('customize_controls_enqueue_scripts', 'twentyfifteen_customize_control_js');
/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_customize_preview_js()
{
    wp_enqueue_script('twentyfifteen-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array('customize-preview'), '20141216', true);
}
add_action('customize_preview_init', 'twentyfifteen_customize_preview_js');
/**
 * Returns CSS for the color schemes.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param array $colors Color scheme colors.
 * @return string Color scheme CSS.
 */
function twentyfifteen_get_color_scheme_css($colors)
{
    $colors = wp_parse_args($colors, array('background_color' => '', 'header_background_color' => '', 'box_background_color' => '', 'textcolor' => '', 'secondary_textcolor' => '', 'border_color' => '', 'border_focus_color' => '', 'sidebar_textcolor' => '', 'sidebar_border_color' => '', 'sidebar_border_focus_color' => '', 'secondary_sidebar_textcolor' => '', 'meta_box_background_color' => ''));
    $css = <<<CSS
\t/* Color Scheme */

\t/* Background Color */
\tbody {
\t\tbackground-color: {$colors['background_color']};
\t}

\t/* Sidebar Background Color */
\tbody:before,
\t.site-header {
\t\tbackground-color: {$colors['header_background_color']};
\t}

\t/* Box Background Color */
\t.post-navigation,
\t.pagination,
\t.secondary,
\t.site-footer,
\t.hentry,
\t.page-header,
\t.page-content,
\t.comments-area,
\t.widecolumn {
\t\tbackground-color: {$colors['box_background_color']};
\t}

\t/* Box Background Color */
\tbutton,
\tinput[type="button"],
\tinput[type="reset"],
\tinput[type="submit"],
\t.pagination .prev,
\t.pagination .next,
\t.widget_calendar tbody a,
\t.widget_calendar tbody a:hover,
\t.widget_calendar tbody a:focus,
\t.page-links a,
\t.page-links a:hover,
\t.page-links a:focus,
\t.sticky-post {
\t\tcolor: {$colors['box_background_color']};
\t}

\t/* Main Text Color */
\tbutton,
\tinput[type="button"],
\tinput[type="reset"],
\tinput[type="submit"],
\t.pagination .prev,
\t.pagination .next,
\t.widget_calendar tbody a,
\t.page-links a,
\t.sticky-post {
\t\tbackground-color: {$colors['textcolor']};
\t}

\t/* Main Text Color */
\tbody,
\tblockquote cite,
\tblockquote small,
\ta,
\t.dropdown-toggle:after,
\t.image-navigation a:hover,
\t.image-navigation a:focus,
\t.comment-navigation a:hover,
\t.comment-navigation a:focus,
\t.widget-title,
\t.entry-footer a:hover,
\t.entry-footer a:focus,
\t.comment-metadata a:hover,
\t.comment-metadata a:focus,
\t.pingback .edit-link a:hover,
\t.pingback .edit-link a:focus,
\t.comment-list .reply a:hover,
\t.comment-list .reply a:focus,
\t.site-info a:hover,
\t.site-info a:focus {
\t\tcolor: {$colors['textcolor']};
\t}

\t/* Main Text Color */
\t.entry-content a,
\t.entry-summary a,
\t.page-content a,
\t.comment-content a,
\t.pingback .comment-body > a,
\t.author-description a,
\t.taxonomy-description a,
\t.textwidget a,
\t.entry-footer a:hover,
\t.comment-metadata a:hover,
\t.pingback .edit-link a:hover,
\t.comment-list .reply a:hover,
\t.site-info a:hover {
\t\tborder-color: {$colors['textcolor']};
\t}

\t/* Secondary Text Color */
\tbutton:hover,
\tbutton:focus,
\tinput[type="button"]:hover,
\tinput[type="button"]:focus,
\tinput[type="reset"]:hover,
\tinput[type="reset"]:focus,
\tinput[type="submit"]:hover,
\tinput[type="submit"]:focus,
\t.pagination .prev:hover,
\t.pagination .prev:focus,
\t.pagination .next:hover,
\t.pagination .next:focus,
\t.widget_calendar tbody a:hover,
\t.widget_calendar tbody a:focus,
\t.page-links a:hover,
\t.page-links a:focus {
\t\tbackground-color: {$colors['textcolor']}; /* Fallback for IE7 and IE8 */
\t\tbackground-color: {$colors['secondary_textcolor']};
\t}

\t/* Secondary Text Color */
\tblockquote,
\ta:hover,
\ta:focus,
\t.main-navigation .menu-item-description,
\t.post-navigation .meta-nav,
\t.post-navigation a:hover .post-title,
\t.post-navigation a:focus .post-title,
\t.image-navigation,
\t.image-navigation a,
\t.comment-navigation,
\t.comment-navigation a,
\t.widget,
\t.author-heading,
\t.entry-footer,
\t.entry-footer a,
\t.taxonomy-description,
\t.page-links > .page-links-title,
\t.entry-caption,
\t.comment-author,
\t.comment-metadata,
\t.comment-metadata a,
\t.pingback .edit-link,
\t.pingback .edit-link a,
\t.post-password-form label,
\t.comment-form label,
\t.comment-notes,
\t.comment-awaiting-moderation,
\t.logged-in-as,
\t.form-allowed-tags,
\t.no-comments,
\t.site-info,
\t.site-info a,
\t.wp-caption-text,
\t.gallery-caption,
\t.comment-list .reply a,
\t.widecolumn label,
\t.widecolumn .mu_register label {
\t\tcolor: {$colors['textcolor']}; /* Fallback for IE7 and IE8 */
\t\tcolor: {$colors['secondary_textcolor']};
\t}

\t/* Secondary Text Color */
\tblockquote,
\t.logged-in-as a:hover,
\t.comment-author a:hover {
\t\tborder-color: {$colors['textcolor']}; /* Fallback for IE7 and IE8 */
\t\tborder-color: {$colors['secondary_textcolor']};
\t}

\t/* Border Color */
\thr,
\t.dropdown-toggle:hover,
\t.dropdown-toggle:focus {
\t\tbackground-color: {$colors['textcolor']}; /* Fallback for IE7 and IE8 */
\t\tbackground-color: {$colors['border_color']};
\t}

\t/* Border Color */
\tpre,
\tabbr[title],
\ttable,
\tth,
\ttd,
\tinput,
\ttextarea,
\t.main-navigation ul,
\t.main-navigation li,
\t.post-navigation,
\t.post-navigation div + div,
\t.pagination,
\t.comment-navigation,
\t.widget li,
\t.widget_categories .children,
\t.widget_nav_menu .sub-menu,
\t.widget_pages .children,
\t.site-header,
\t.site-footer,
\t.hentry + .hentry,
\t.author-info,
\t.entry-content .page-links a,
\t.page-links > span,
\t.page-header,
\t.comments-area,
\t.comment-list + .comment-respond,
\t.comment-list article,
\t.comment-list .pingback,
\t.comment-list .trackback,
\t.comment-list .reply a,
\t.no-comments {
\t\tborder-color: {$colors['textcolor']}; /* Fallback for IE7 and IE8 */
\t\tborder-color: {$colors['border_color']};
\t}

\t/* Border Focus Color */
\ta:focus,
\tbutton:focus,
\tinput:focus {
\t\toutline-color: {$colors['textcolor']}; /* Fallback for IE7 and IE8 */
\t\toutline-color: {$colors['border_focus_color']};
\t}

\tinput:focus,
\ttextarea:focus {
\t\tborder-color: {$colors['textcolor']}; /* Fallback for IE7 and IE8 */
\t\tborder-color: {$colors['border_focus_color']};
\t}

\t/* Sidebar Link Color */
\t.secondary-toggle:before {
\t\tcolor: {$colors['sidebar_textcolor']};
\t}

\t.site-title a,
\t.site-description {
\t\tcolor: {$colors['sidebar_textcolor']};
\t}

\t/* Sidebar Text Color */
\t.site-title a:hover,
\t.site-title a:focus {
\t\tcolor: {$colors['secondary_sidebar_textcolor']};
\t}

\t/* Sidebar Border Color */
\t.secondary-toggle {
\t\tborder-color: {$colors['sidebar_textcolor']}; /* Fallback for IE7 and IE8 */
\t\tborder-color: {$colors['sidebar_border_color']};
\t}

\t/* Sidebar Border Focus Color */
\t.secondary-toggle:hover,
\t.secondary-toggle:focus {
\t\tborder-color: {$colors['sidebar_textcolor']}; /* Fallback for IE7 and IE8 */
\t\tborder-color: {$colors['sidebar_border_focus_color']};
\t}

\t.site-title a {
\t\toutline-color: {$colors['sidebar_textcolor']}; /* Fallback for IE7 and IE8 */
\t\toutline-color: {$colors['sidebar_border_focus_color']};
\t}

\t/* Meta Background Color */
\t.entry-footer {
\t\tbackground-color: {$colors['meta_box_background_color']};
\t}

\t@media screen and (min-width: 38.75em) {
\t\t/* Main Text Color */
\t\t.page-header {
\t\t\tborder-color: {$colors['textcolor']};
\t\t}
\t}

\t@media screen and (min-width: 59.6875em) {
\t\t/* Make sure its transparent on desktop */
\t\t.site-header,
\t\t.secondary {
\t\t\tbackground-color: transparent;
\t\t}

\t\t/* Sidebar Background Color */
\t\t.widget button,
\t\t.widget input[type="button"],
\t\t.widget input[type="reset"],
\t\t.widget input[type="submit"],
\t\t.widget_calendar tbody a,
\t\t.widget_calendar tbody a:hover,
\t\t.widget_calendar tbody a:focus {
\t\t\tcolor: {$colors['header_background_color']};
\t\t}

\t\t/* Sidebar Link Color */
\t\t.secondary a,
\t\t.dropdown-toggle:after,
\t\t.widget-title,
\t\t.widget blockquote cite,
\t\t.widget blockquote small {
\t\t\tcolor: {$colors['sidebar_textcolor']};
\t\t}

\t\t.widget button,
\t\t.widget input[type="button"],
\t\t.widget input[type="reset"],
\t\t.widget input[type="submit"],
\t\t.widget_calendar tbody a {
\t\t\tbackground-color: {$colors['sidebar_textcolor']};
\t\t}

\t\t.textwidget a {
\t\t\tborder-color: {$colors['sidebar_textcolor']};
\t\t}

\t\t/* Sidebar Text Color */
\t\t.secondary a:hover,
\t\t.secondary a:focus,
\t\t.main-navigation .menu-item-description,
\t\t.widget,
\t\t.widget blockquote,
\t\t.widget .wp-caption-text,
\t\t.widget .gallery-caption {
\t\t\tcolor: {$colors['secondary_sidebar_textcolor']};
\t\t}

\t\t.widget button:hover,
\t\t.widget button:focus,
\t\t.widget input[type="button"]:hover,
\t\t.widget input[type="button"]:focus,
\t\t.widget input[type="reset"]:hover,
\t\t.widget input[type="reset"]:focus,
\t\t.widget input[type="submit"]:hover,
\t\t.widget input[type="submit"]:focus,
\t\t.widget_calendar tbody a:hover,
\t\t.widget_calendar tbody a:focus {
\t\t\tbackground-color: {$colors['secondary_sidebar_textcolor']};
\t\t}

\t\t.widget blockquote {
\t\t\tborder-color: {$colors['secondary_sidebar_textcolor']};
\t\t}

\t\t/* Sidebar Border Color */
\t\t.main-navigation ul,
\t\t.main-navigation li,
\t\t.widget input,
\t\t.widget textarea,
\t\t.widget table,
\t\t.widget th,
\t\t.widget td,
\t\t.widget pre,
\t\t.widget li,
\t\t.widget_categories .children,
\t\t.widget_nav_menu .sub-menu,
\t\t.widget_pages .children,
\t\t.widget abbr[title] {
\t\t\tborder-color: {$colors['sidebar_border_color']};
\t\t}

\t\t.dropdown-toggle:hover,
\t\t.dropdown-toggle:focus,
\t\t.widget hr {
\t\t\tbackground-color: {$colors['sidebar_border_color']};
\t\t}

\t\t.widget input:focus,
\t\t.widget textarea:focus {
\t\t\tborder-color: {$colors['sidebar_border_focus_color']};
\t\t}

\t\t.sidebar a:focus,
\t\t.dropdown-toggle:focus {
\t\t\toutline-color: {$colors['sidebar_border_focus_color']};
\t\t}
\t}
CSS;
    return $css;
}
/**
 * Output an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the Customizer
 * preview.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_color_scheme_css_template()
{
    $colors = array('background_color' => '{{ data.background_color }}', 'header_background_color' => '{{ data.header_background_color }}', 'box_background_color' => '{{ data.box_background_color }}', 'textcolor' => '{{ data.textcolor }}', 'secondary_textcolor' => '{{ data.secondary_textcolor }}', 'border_color' => '{{ data.border_color }}', 'border_focus_color' => '{{ data.border_focus_color }}', 'sidebar_textcolor' => '{{ data.sidebar_textcolor }}', 'sidebar_border_color' => '{{ data.sidebar_border_color }}', 'sidebar_border_focus_color' => '{{ data.sidebar_border_focus_color }}', 'secondary_sidebar_textcolor' => '{{ data.secondary_sidebar_textcolor }}', 'meta_box_background_color' => '{{ data.meta_box_background_color }}');
    ?>
	<script type="text/html" id="tmpl-twentyfifteen-color-scheme">
		<?php 
    echo twentyfifteen_get_color_scheme_css($colors);
    ?>
	</script>
	<?php 
}
add_action('customize_controls_print_footer_scripts', 'twentyfifteen_color_scheme_css_template');