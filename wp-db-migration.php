<?php
/**
 * Plugin Name: DB migrator
 * Description: Copies queries and saves them to a file. In wp-config add define( 'SAVEQUERIES', true );
 * Version: 0.1
 * Author: Amelia Lang
 * Author URI: amelia@amelialang.com
 * License: GPL2
 */

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
defined('ABSPATH') or die("No script kiddies please!");

add_action('shutdown', 'sql_logger');
function sql_logger()
{
    global $wpdb;
    $log_file = fopen(ABSPATH.'/wp-content/plugins/db-migration/logs/'. date("Y-m (M)") .'.sql', 'a');
    $date_added = false;

    foreach($wpdb->queries as $q)
    {
        $q[0] = preg_replace('/\s+/', ' ', $q[0]);

    	if (preg_match('/^\s*(CREATE|ALTER|RENAME|TRUNCATE|CALL|DELETE|INSERT|REPLACE|UPDATE).*$/', $q[0]))
    	{
    		if (!$date_added)
    		{
    			$date_added = true;
			    fwrite($log_file, "\n-- " . date("F j, Y, g:i:s a")."\n");
    		}
	        fwrite($log_file, $q[0] . " -- ($q[1] s)" . "\n");
    	}
    }

    fclose($log_file);
}

?>
