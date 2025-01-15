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
 * Delete notification.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->libdir . '/adminlib.php');
require_once('deletenotif_form.php');
require_once('lib.php');

admin_externalpage_setup('local_moofactory_notification_managenotif');

$id = optional_param('id', 0, PARAM_INT); // Id de la notification à supprimer.

$returnurl = new moodle_url($CFG->wwwroot . '/local/moofactory_notification/managenotif.php', array('id' => $id));
$nexturl = new moodle_url($CFG->wwwroot . '/local/moofactory_notification/managenotif.php');

if (!empty($id)) {
    $mform = new deletenotif_form( null, array('id' => $id));
} else {
    redirect($nexturl);
}

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($fromform = $mform->get_data()) {
    // On recherche la notif définie au niveau des paramètres du plugin correspondant au type de la notif supprimée.
    $record = $DB->get_record('local_mf_notification', array('id'=>$fromform->id));
    $type = $record->type;
    switch($type){
        case "courseenroll":
            $notifvalue = get_config('local_moofactory_notification', 'coursesenrollmentsnotification');
            $customfieldname = 'courseenrollmentsnotification';
            break;
        case "courseaccess":
            $notifvalue = get_config('local_moofactory_notification', 'coursesaccessnotification');
            $customfieldname = 'courseaccessnotification';
            break;
        case "courseevent":
            $notifvalue = get_config('local_moofactory_notification', 'courseseventsnotification');
            $customfieldname = 'courseeventsnotification';
            break;
    }
    // Si c'est la même, il faut remplacer par la notif de base dans les paramètres du plugin.
    if($notifvalue == $fromform->id){
        $record = $DB->get_record('local_mf_notification', array('type'=>$type, 'base'=>1));
        switch($type){
            case "courseenroll":
                set_config('coursesenrollmentsnotification', $record->id, 'local_moofactory_notification');
                break;
            case "courseaccess":
                set_config('coursesaccessnotification', $record->id, 'local_moofactory_notification');
                break;
            case "courseevent":
                set_config('courseseventsnotification', $record->id, 'local_moofactory_notification');
                break;
        }
    }

    // Notification par défaut et notification supprimée.
    $records = $DB->get_records('local_mf_notification', array('type'=>$type), 'base DESC, name ASC');
    $index = 0;
    foreach($records as $record) {
        $index++;
        if($record->base == 1){
            $basenotifid = $record->id;
            $basenotif = $index;
        }
        if($record->id == $fromform->id){
            $deletednotif = $index;
        }
    }

    // Pour tous les cours.
    $sql = "SELECT id, fullname FROM {course} WHERE id <> 1";
    $courses = $DB->get_records_sql($sql, array());
    foreach ($courses as $course) {
        $courseid = $course->id;
        $coursenotif = (int)local_moofactory_notification_getCustomfield($courseid, $customfieldname, 'select');

        // Si la notif du cours est celle qui est supprimée, il faut la remplacer par la notif de base.
        if($coursenotif == $deletednotif){
            local_moofactory_notification_setCustomfield($courseid, $customfieldname, 'select', $basenotif);
        }
        // Sinon, il faut mettre le nouvel index si la notif supprimée a un index inférieur à la notif du cours.
        elseif($coursenotif > $deletednotif){
            local_moofactory_notification_setCustomfield($courseid, $customfieldname, 'select', $coursenotif - 1);
        }

        // Pour les activités.
        $activities = local_moofactory_notification_get_all_activities($courseid);

        foreach($activities as $activity){
            $moduleid = $activity["id"];
            $name = 'modulenotification_'.$courseid.'_'.$moduleid;
            $notifid = get_config('local_moofactory_notification', $name);
            
            // Si la notif de l'activité est celle qui est supprimée, il faut la remplacer par la notif de base.
            if($fromform->id == $notifid){
                set_config($name, $basenotifid, 'local_moofactory_notification');
            }
        }
    }

    $DB->delete_records('local_mf_notification', array('id' => $fromform->id));

    // Typically you finish up by redirecting to somewhere where the user can see what they did.
    redirect($nexturl);
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('deletenotification', 'local_moofactory_notification'), 2);
$mform->display();

echo $OUTPUT->footer();
