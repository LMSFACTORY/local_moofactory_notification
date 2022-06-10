<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Uninstallation code for course format "Moofactory"
 *
 * @package    format_moofactory
 * @copyright  2020 Patrick ROCHET <patrick.rochet@learning-conseil.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Uninstall the plugin.
 *
 * @return boolean Always true (indicating success).
 */
function xmldb_local_moofactory_notification_uninstall() {
    global $DB;

    // Suppression de la catégorie 'Notifications' et des champs personnalisés de cours associés. 
    $categoryid = $DB->get_field('customfield_category', 'id', array('name' =>get_string('notifications_category', 'local_moofactory_notification')));
    if ($categoryid){
        $category = \core_customfield\category_controller::create($categoryid);
        $handler = core_course\customfield\course_handler::create();
        $handler->delete_category($category);
    }

    return true;
}
