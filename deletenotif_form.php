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
 * Delete notification form.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class deletenotif_form extends moodleform {
    public function definition() {
        global $CFG, $DB;

        $pluginalert = false;
        $coursealert = false;
        $courseslist = array();
        $activitiesalert = false;
        $activitieslist = array();

        $mform = $this->_form;
        $id = $this->_customdata['id'];

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_RAW);

        // On recherche la notif définie au niveau des paramètres du plugin correspondant au type de la notif supprimée.
        $record = $DB->get_record('local_mf_notification', array('id'=>$id));
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

        // Si la notif du plugin est celle qui est supprimée, il faut afficher une alerte.
        if($notifvalue == $id){
            $pluginalert = true;
        }

        // Notification supprimée.
        $records = $DB->get_records('local_mf_notification', array('type'=>$type), 'base DESC, name ASC');
        $index = 0;
        foreach($records as $record) {
            $index++;
            if($record->id == $id){
                $deletednotif = $index;
            }
        }

        // Pour tous les cours.
        $sql = "SELECT id, fullname FROM {course} WHERE id <> 1";
        $courses = $DB->get_records_sql($sql, array());
        foreach ($courses as $course) {
            $courseid = $course->id;
            $coursenotif = (int)local_moofactory_notification_getCustomfield($courseid, $customfieldname, 'select');

            // Si la notif du cours est celle qui est supprimée, il faut afficher une alerte.
            if($coursenotif == $deletednotif){
                $coursealert = true;
                $courseslist[] = $course->fullname;
            }

            // Pour les activités.
            $activities = local_moofactory_notification_get_all_activities($courseid);

            foreach($activities as $activity){
                $moduleid = $activity["id"];
                $name = 'modulenotification_'.$courseid.'_'.$moduleid;
                $notifid = get_config('local_moofactory_notification', $name);
                
                // Si la notif de l'activité est celle qui est supprimée, il faut afficher une alerte.
                if($id == $notifid){
                    $activitiesalert = true;
                    $activitieslist[] = $activity["name"] . " (" . $course->fullname . ")";
                }
            }
        }

        $name = $DB->get_field('local_mf_notification', 'name', array('id' => $id));
        
        $mform->addElement('html', '<fieldset class="clearfix">');
        if($pluginalert){
            $mform->addElement('html', '<h6 style="margin-top:20px;color:var(--danger);">'.get_string('deleteplugin', 'local_moofactory_notification').'</h6>');
        }
        if($coursealert){
            if(count($courseslist) > 1){
                $mform->addElement('html', '<h6 style="margin-top:20px;color:var(--danger);">'.get_string('deletecourses', 'local_moofactory_notification').'</h6>');
                foreach($courseslist as $item){
                    $mform->addElement('html', '<h6 style="margin-left:20px;color:var(--danger);">&bull; '.$item.'</h6>');
                }
            }
            else{
                $mform->addElement('html', '<h6 style="margin-top:20px;color:var(--danger);">'.get_string('deletecourse', 'local_moofactory_notification').' &laquo;&nbsp;'.$courseslist[0].'&nbsp;&raquo;.</h6>');
            }
        }
        if($activitiesalert){
            if(count($activitieslist) > 1){
                $mform->addElement('html', '<h6 style="margin-top:20px;color:var(--danger);">'.get_string('deleteactivities', 'local_moofactory_notification').'</h6>');
                foreach($activitieslist as $item){
                    $mform->addElement('html', '<h6 style="margin-left:20px;color:var(--danger);">&bull; '.$item.'</h6>');
                }
            }
            else{
                $mform->addElement('html', '<h6 style="margin-top:20px;color:var(--danger);">'.get_string('deleteactivity', 'local_moofactory_notification').' &laquo;&nbsp;'.$activitieslist[0].'&nbsp;&raquo;.</h6>');
            }
        }
        $mform->addElement('html', '<h5 style="margin-top:20px">'.get_string('deleteconfirm', 'local_moofactory_notification', '&laquo;&nbsp;'.$name.'&nbsp;&raquo;').'</h5>');
        $mform->addElement('html', '</fieldset>');

        $this->add_action_buttons();
    }
}