<?php

/**
 * @package Hello_Dolly
 * @version 1.6
 */
/*
Plugin Name: Hello Dolly
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Matt Mullenweg
Version: 1.6
Author URI: http://ma.tt/
*/
function hello_dolly_get_lyric()
{
    /** These are the lyrics to Hello Dolly */
    $lyrics = "Hello, Dolly\nWell, hello, Dolly\nIt's so nice to have you back where you belong\nYou're lookin' swell, Dolly\nI can tell, Dolly\nYou're still glowin', you're still crowin'\nYou're still goin' strong\nWe feel the room swayin'\nWhile the band's playin'\nOne of your old favourite songs from way back when\nSo, take her wrap, fellas\nFind her an empty lap, fellas\nDolly'll never go away again\nHello, Dolly\nWell, hello, Dolly\nIt's so nice to have you back where you belong\nYou're lookin' swell, Dolly\nI can tell, Dolly\nYou're still glowin', you're still crowin'\nYou're still goin' strong\nWe feel the room swayin'\nWhile the band's playin'\nOne of your old favourite songs from way back when\nGolly, gee, fellas\nFind her a vacant knee, fellas\nDolly'll never go away\nDolly'll never go away\nDolly'll never go away again";
    // Here we split it into lines
    $lyrics = explode("\n", $lyrics);
    // And then randomly choose a line
    return wptexturize($lyrics[mt_rand(0, count($lyrics) - 1)]);
}
// This just echoes the chosen line, we'll position it later
function hello_dolly()
{
    $chosen = hello_dolly_get_lyric();
    echo "<p id='dolly'>{$chosen}</p>";
}
// Now we set that function up to execute when the admin_notices action is called
add_action('admin_notices', 'hello_dolly');
// We need some CSS to position the paragraph
function dolly_css()
{
    // This makes sure that the positioning is also good for right-to-left languages
    $x = is_rtl() ? 'left' : 'right';
    echo "\n\t<style type='text/css'>\n\t#dolly {\n\t\tfloat: {$x};\n\t\tpadding-{$x}: 15px;\n\t\tpadding-top: 5px;\t\t\n\t\tmargin: 0;\n\t\tfont-size: 11px;\n\t}\n\t</style>\n\t";
}
add_action('admin_head', 'dolly_css');