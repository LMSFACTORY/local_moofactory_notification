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
 * local_moofactory_notification plugin.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/moofactory_notification:managenotifications' => array(
        'riskbitmask'          => RISK_CONFIG,
        'captype'              => 'write',
        'contextlevel'         => CONTEXT_SYSTEM
    ),
    'local/moofactory_notification:setnotifications' => array(
        'riskbitmask'          => RISK_CONFIG,
        'captype'              => 'write',
        'contextlevel'         => CONTEXT_SYSTEM
    ),
    'local/moofactory_notification:coursesenrollments' => array(
        'riskbitmask'          => RISK_CONFIG,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,              
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
        ),
    ),
    'local/moofactory_notification:coursesevents' => array(
        'riskbitmask'          => RISK_CONFIG,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'student' => CAP_ALLOW
        ),
    ),
    'local/moofactory_notification:coursesaccess' => array(
        'riskbitmask'          => RISK_CONFIG,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'student' => CAP_ALLOW
        ),
    ),
    'local/moofactory_notification:modulesaccess' => array(
        'riskbitmask'          => RISK_CONFIG,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'student' => CAP_ALLOW
        ),
    ),
);
