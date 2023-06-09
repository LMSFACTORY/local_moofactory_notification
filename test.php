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
 * Manage module notifications.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once('lib.php');

global $DB;

echo $OUTPUT->header();
echo $OUTPUT->heading("Page test", 2);
echo("<pre>");

echo(time()."<br>");
var_dump(date("d/m/Y H:i:s", time()));
echo("<br><br>");

// local_moofactory_notification_send_coursesevents_notification();
// local_moofactory_notification_send_coursesenroll_notification();
// local_moofactory_notification_send_coursesaccess_notification();
// local_moofactory_notification_send_siteevents_notification();

echo("</pre>");
echo $OUTPUT->footer();
