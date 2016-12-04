<?php

/*
Plugin Name: Expiring Posts
Plugin URI: http://www.10up.com
Description: Add new status for expired posts.
Author: Tanner Moushey, Ivan Kruchkoff (10up LLC)
Version: 1.1
Author URI: http://www.10up.com

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

namespace Expiring_Posts;

spl_autoload_register(function ( $class_name ) {
	if ( false === strpos( $class_name, __NAMESPACE__ ) ) {
		return false;
	}

	$file_name = dirname( __FILE__ ) . '/php/class-' . strtolower( str_replace( __NAMESPACE__ . '\\', '', $class_name ) ) . '.php';
	if ( file_exists( $file_name ) ) {
		require_once( $file_name );
		return true;
	}
	return false;
});

$GLOBALS['expiring_posts_plugin'] = new Plugin();

/**
 * @return Plugin
 */
function get_instance() {
	return $GLOBALS['expiring_posts_plugin'];
}
