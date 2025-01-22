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
require_once('module_form.php');

$courseid = optional_param('courseid', 2, PARAM_INT); // Id du cours.
$id = optional_param('id', 0, PARAM_INT); // Id de l'activité.

// Empêcher l'accès pour les utilisateurs invités ou non connectés.
if (isguestuser() || !isloggedin()) {
    redirect(new moodle_url('/login/index.php'));
}

$returnurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $courseid));
$nexturl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $courseid));

$mform = new module_form( null, array('courseid' => $courseid, 'id' => $id));

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {
    // This branch is where you process validated data.
    // Do stuff ...
    $courseid = $fromform->courseid;
    $id = $fromform->id;

    $name = 'moduleevents_'.$courseid.'_'.$id;
    if(empty($fromform->$name)){
        $value = "";
    }
    else{
        $value = $fromform->$name;
    }
    set_config($name, $value, 'local_moofactory_notification');

    $name = 'modulecheckavailability_'.$courseid.'_'.$id;
    if(empty($fromform->$name)){
        $value = "";
    }
    else{
        $value = $fromform->$name;
    }
    set_config($name, $value, 'local_moofactory_notification');

    $name = 'modulecheckdateavailability_'.$courseid.'_'.$id;
    if(empty($fromform->$name)){
        $value = "";
    }
    else{
        $value = $fromform->$name;
    }
    set_config($name, $value, 'local_moofactory_notification');

    $name = 'modulecheckgroupavailability_'.$courseid.'_'.$id;
    if(empty($fromform->$name)){
        $value = "";
    }
    else{
        $value = $fromform->$name;
    }
    set_config($name, $value, 'local_moofactory_notification');

    $name = 'modulenotification_'.$courseid.'_'.$id;
    $value = $fromform->$name;
    set_config($name, $value, 'local_moofactory_notification');

    $configvars = ['moduledaysbeforeevents1', 'modulehoursbeforeevents1', 'moduledaysbeforeevents2', 'modulehoursbeforeevents2', 'moduledaysbeforeevents3', 'modulehoursbeforeevents3'];
    foreach($configvars as $configvar){
        $name = $configvar.'_'.$courseid.'_'.$id;
        // Si la valeur = '999', on reset avec les valeurs définies dans le cours
        if($fromform->$name != '999'){
            if($fromform->$name == ""){
                $value = "";
            }
            else{
                $value = $fromform->$name;
            }
            set_config($name, $value, 'local_moofactory_notification');        
        }
        else{
            unset_config($name, 'local_moofactory_notification');
        }
    }

    /*********** form notification après levée des restrictions d’accès ************/
    $name = 'modulelevee_'.$courseid.'_'.$id;
    if(empty($fromform->$name)){
        $value = "";
    }
    else{
        $value = $fromform->$name;
        if($value){
            set_config($name . '_date', time(), 'local_moofactory_notification');
        } else {
            unset_config($name . '_date', 'local_moofactory_notification');
        }

    }
    set_config($name, $value, 'local_moofactory_notification');

    $name = 'moduleleveenotification_'.$courseid.'_'.$id;
    $value = $fromform->$name;
    set_config($name, $value, 'local_moofactory_notification');

    $name = 'moduleleveedelai_'.$courseid.'_'.$id;
    if(empty($fromform->$name)){
        $value = "";
    }
    else{
        $value = $fromform->$name;
    }
    set_config($name, $value, 'local_moofactory_notification');

    $name = 'copiemaillevee_'.$courseid.'_'.$id;
    if(empty($fromform->$name)){
        $value = "";
    }
    else{
        $value = $fromform->$name;
    }
    set_config($name, $value, 'local_moofactory_notification');

    $configvars = ['moduledaysbeforelevee1', 'modulehoursbeforelevee1', 'moduledaysbeforelevee2', 'modulehoursbeforelevee2', 'moduledaysbeforelevee3', 'modulehoursbeforelevee3'];
    foreach($configvars as $configvar){
        $name = $configvar.'_'.$courseid.'_'.$id;

            if(empty($fromform->$name)){
                $value = "";
            }
            else{
                $value = $fromform->$name;
            }
            set_config($name, $value, 'local_moofactory_notification');
    }
    // Typically you finish up by redirecting to somewhere where the user
    // can see what they did.
    redirect($nexturl);
}

echo $OUTPUT->header();
$cm = get_coursemodule_from_id('', $id);
echo $OUTPUT->heading(get_string('module', 'local_moofactory_notification') . '"' . $cm->name . '"', 2);
$mform->display();

echo $OUTPUT->footer();
