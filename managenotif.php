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
 * local_moofactory_notification plugin
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->libdir . '/adminlib.php');
require_once('lib.php');

require_once('managenotif_form.php');

global $PAGE;

admin_externalpage_setup('local_moofactory_notification_managenotif');

$id = optional_param('id', 0, PARAM_INT); // Id de la notification.

$returnurl = new moodle_url($CFG->wwwroot . '/admin/category.php?category=moofactory_notification');

if (!empty($id)) {
    $mform = new managenotif_form(null, array('id' => $id), 'post', '', array('id' => 'notificationsform'));
} else {
    $mform = new managenotif_form(null, null, 'post', '', array('id' => 'notificationsform'));
}

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {
    $typeinitial = $fromform->typeinitial;
    $type = $fromform->notificationtype;
    
    // Les cours
    $sql = "SELECT id, fullname FROM {course} WHERE id <> 1";
    $courses = $DB->get_records_sql($sql, array());
    
    // Changement de type de notification
    $customfieldname = '';
    if($type <> $typeinitial){
        switch($typeinitial){
            case "courseenroll":
                $customfieldinitialname = 'courseenrollmentsnotification';
                break;
            case "courseaccess":
                $customfieldinitialname = 'courseaccessnotification';
                break;
            case "courseevent":
                $customfieldinitialname = 'courseeventsnotification';
                break;
        }

        // Notification par défaut et notification déplacée
        $records = $DB->get_records('local_mf_notification', array('type'=>$typeinitial), 'base DESC, name ASC');
        $index = 0;
        foreach($records as $record) {
            $index++;
            if($record->base == 1){
                $basenotif = $index;
            }
            if($record->id == $fromform->selectnotifications){
                $movednotif = $index;
            }
        }

        // Pour tous les cours
        foreach ($courses as $course) {
            $courseid = $course->id;
            // La notification n'est plus dans le customfield initial, il faut réorganiser
            $coursenotif = (int)local_moofactory_notification_getCustomfield($courseid, $customfieldinitialname, 'select');
        
            // Si la notif du cours est celle qui change de type, il faut la remplacer par la notif de base
            if($coursenotif == $movednotif){
                local_moofactory_notification_setCustomfield($courseid, $customfieldinitialname, 'select', $basenotif);
            }
            // Sinon, il faut mettre le nouvel index si la notif qui change de type a un index inférieur à la notif du cours
            elseif($coursenotif > $movednotif){
                local_moofactory_notification_setCustomfield($courseid, $customfieldinitialname, 'select', $coursenotif - 1);
            }
        }

        // Il faut réorganiser le customfield de destination
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

        if (!empty($fromform->submitbutton)) {
            // Update de la notification.
            $data = new stdClass;
            $data->id = $fromform->selectnotifications;
            $data->type = $type;
            $data->name = $fromform->notificationname;
            $data->subject = $fromform->notificationsubject;
            $data->bodyhtml = $fromform->notificationbodyhtml['text'];
            $DB->update_record('local_mf_notification', $data);
        }

        // La nouvelle notification
        $records = $DB->get_records('local_mf_notification', array('type'=>$type), 'base DESC, name ASC');
        $newnotif = 0;
        $index = 0;
        foreach($records as $record) {
            $index++;
            if($record->id == $fromform->selectnotifications){
                $newnotif = $index;
            }
        }

        // Pour tous les cours
        foreach ($courses as $course) {
            $courseid = $course->id;
            $coursenotif = (int)local_moofactory_notification_getCustomfield($courseid, $customfieldname, 'select');
            
            if($coursenotif >= $newnotif){
                local_moofactory_notification_setCustomfield($courseid, $customfieldname, 'select', $coursenotif + 1);
            }
        }
    }

    // Pas de changement de type
    else{
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

        // Position avant update
        $indexbefore = 0;
        $records = $DB->get_records('local_mf_notification', array('type'=>$type), 'base DESC, name ASC');
        $index = 0;
        foreach($records as $record) {
            $index++;
            if($record->id == $fromform->selectnotifications){
                $indexbefore = $index;
            }
        }
        
        if (!empty($fromform->submitbutton)) {
            // Update de la notification.
            $data = new stdClass;
            $data->id = $fromform->selectnotifications;
            $data->type = $fromform->notificationtype;
            $data->name = $fromform->notificationname;
            $data->subject = $fromform->notificationsubject;
            $data->bodyhtml = $fromform->notificationbodyhtml['text'];
            $DB->update_record('local_mf_notification', $data);
        }
        
        // Position après update
        $indexafter = 0;
        $records = $DB->get_records('local_mf_notification', array('type'=>$type), 'base DESC, name ASC');
        $index = 0;
        foreach($records as $record) {
            $index++;
            if($record->id == $fromform->selectnotifications){
                $indexafter = $index;
            }
        }

        foreach ($courses as $course) {
            $courseid = $course->id;
            $coursenotif = (int)local_moofactory_notification_getCustomfield($courseid, $customfieldname, 'select');

            if($indexafter != $indexbefore){
                if($coursenotif < $indexbefore && $coursenotif >= $indexafter){
                    local_moofactory_notification_setCustomfield($courseid, $customfieldname, 'select', $coursenotif + 1);
                }
                if($coursenotif > $indexbefore && $coursenotif <= $indexafter){
                    local_moofactory_notification_setCustomfield($courseid, $customfieldname, 'select', $coursenotif - 1);
                }
                if($coursenotif == $indexbefore){
                    $offset = $indexafter - $indexbefore;
                    local_moofactory_notification_setCustomfield($courseid, $customfieldname, 'select', $coursenotif + $offset);
                }
            }
        }
    }

    $nexturl = new moodle_url($CFG->wwwroot . '/local/moofactory_notification/managenotif.php', array('id' => $fromform->selectnotifications));
    // Typically you finish up by redirecting to somewhere where the user
    // can see what they did.
    redirect($nexturl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managenotif', 'local_moofactory_notification'), 2);

$mform->display();

$PAGE->requires->string_for_js('copied', 'local_moofactory_notification');
$PAGE->requires->js('/local/moofactory_notification/util.js');
echo $OUTPUT->footer();
