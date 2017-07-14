<?php

/*
 * Plugin name: Displet Edit WPMU Users
 * Plugin URI: http://displet.com/wordpress-plugins/displet-edit-wpmu-users
 * Description: Allows site admins on a multi-site instance (WPMU) to edit users, only when that user belongs to the same site as the admin.
 * Version: 1.0
 * Author: Displet
 * Author URI: http://displet.com/
 * License: GPL2
 */

/*  Copyright 2011 Displet (email : dev@displet.com)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as 
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class DispletEditWPMUUsers{
	public function mc_admin_users_caps($caps, $cap, $user_id, $args){
		foreach ($caps as $key => $capability) {
			if ($capability != 'do_not_allow')
				continue;
			switch ($cap) {
				case 'edit_user':
				case 'edit_users':
					$caps[$key] = 'edit_users';
					break;
				case 'delete_user':
				case 'delete_users':
					$caps[$key] = 'delete_users';
					break;
				case 'create_users':
					$caps[$key] = $cap;
					break;
			}
		}
		return $caps;
	}

	public function mc_edit_permission_check() {
		global $current_user, $profileuser; 
		$screen = get_current_screen();
		get_currentuserinfo();
		if(!is_super_admin($current_user->ID) && in_array($screen->base, array('user-edit', 'user-edit-network'))) {
			if (is_super_admin($profileuser->ID)) {
				wp_die(__('You do not have permission to edit this user.'));
			}
			else if (!(is_user_member_of_blog($profileuser->ID, get_current_blog_id()) && is_user_member_of_blog($current_user->ID, get_current_blog_id()))) {
				wp_die(__('You do not have permission to edit this user.'));
			}
		}
	}
}

add_filter('map_meta_cap', array('DispletEditWPMUUsers', 'mc_admin_users_caps'), 1, 4);
remove_all_filters('enable_edit_any_user_configuration');
add_filter('enable_edit_any_user_configuration', '__return_true');
add_filter('admin_head', array('DispletEditWPMUUsers', 'mc_edit_permission_check'), 1, 4);

?>