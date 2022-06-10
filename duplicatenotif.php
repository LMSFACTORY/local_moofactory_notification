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
 * Duplicate notification.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once('lib.php');
global $DB;

$id = optional_param('id', 0, PARAM_INT); // Id de la notification à dupliquer.

$record = $DB->get_record('local_mf_notification', array('id' => $id), 'base, type, name, subject, bodyhtml');

$data = new stdClass;
$data->base = 0;
$data->type = $record->type;
$data->name = $record->name." (2)";
$data->subject = $record->subject;
$data->bodyhtml = $record->bodyhtml;

$newid = $DB->insert_record('local_mf_notification', $data);

$type = $record->type;
switch($type){
    case "courseenroll":
        $customfieldname = 'courseenrollmentsnotification';
        break;
    case "courseaccess":
        $customfieldname = 'courseaccessnotification';
        break;
    case "courseevent":
        $customfieldname = 'courseeventsnotification';
        break;
}

// La nouvelle notification
$records = $DB->get_records('local_mf_notification', array('type'=>$type), 'base DESC, name ASC');
$index = 0;
foreach($records as $record) {
    $index++;
    if($record->id == $newid){
        $newnotif = $index;
    }
}

// Pour tous les cours
$sql = "SELECT id, fullname FROM {course} WHERE id <> 1";
$courses = $DB->get_records_sql($sql, array());
foreach ($courses as $course) {
    $courseid = $course->id;
    $coursenotif = (int)local_moofactory_notification_getCustomfield($courseid, $customfieldname, 'select');

    if($coursenotif >= $newnotif){
        local_moofactory_notification_setCustomfield($courseid, $customfieldname, 'select', $coursenotif + 1);
    }
}

$nexturl = new moodle_url($CFG->wwwroot . '/local/moofactory_notification/managenotif.php', array('id' => $newid));
redirect($nexturl);
