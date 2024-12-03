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
 * Add event handlers for the notification local plugin.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    array(
        'eventname' => 'core\event\user_enrolment_created',
        'callback' => 'local_moofactory_notification_user_enrolment_created',
        'includefile'   => '/local/moofactory_notification/lib.php',
    ),
    array(
        'eventname' => '\local_moofactory_resetmod\event\user_enrolment_reactivated',
        'callback'  => 'local_moofactory_notification_user_enrolment_created',
        'includefile'   => '/local/moofactory_notification/lib.php',
    ),
    array(
        'eventname' => '\core\event\user_enrolment_updated',
        'callback'  => 'local_moofactory_notification_user_enrolment_updated',
        'includefile'   => '/local/moofactory_notification/lib.php',
    ),
    array(
        'eventname' => '\core\event\user_enrolment_deleted',
        'callback'  => 'local_moofactory_notification_user_enrolment_deleted',
        'includefile'   => '/local/moofactory_notification/lib.php',
    ),
    array(
        'eventname' => 'core\event\course_viewed',
        'callback' => 'local_moofactory_notification_course_viewed',
        'includefile'   => '/local/moofactory_notification/lib.php',
    ),
    array(
        'eventname' => 'core\event\course_updated',
        'callback' => 'local_moofactory_notification_course_updated',
        'includefile'   => '/local/moofactory_notification/lib.php',
    ),
    array(
        'eventname' => 'core\event\course_module_updated',
        'callback' => 'local_moofactory_notification_module_updated',
        'includefile'   => '/local/moofactory_notification/lib.php',
    ),
];
