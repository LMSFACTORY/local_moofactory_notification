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
 * Manage module notifications form.
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

class module_form extends moodleform {
    public function definition() {
        global $CFG, $DB, $PAGE;

        $mform = $this->_form;
        $courseid = $this->_customdata['courseid'];
        $id = $this->_customdata['id'];


        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_RAW);
        $mform->setConstant('courseid', $courseid);

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_RAW);

        $mform->addElement('html', '<br>');

        $moduleeventsname = 'moduleevents_'.$courseid.'_'.$id;
        $mform->addElement('checkbox', $moduleeventsname, get_string('moduleevents', 'local_moofactory_notification'));
        $moduleeventsvalue = get_config('local_moofactory_notification', $moduleeventsname);
        $mform->setDefault($moduleeventsname, $moduleeventsvalue);

        $modulecheckavailabilityname = 'modulecheckavailability_'.$courseid.'_'.$id;
        if(empty($moduleeventsvalue)){
            $mform->addElement('checkbox', $modulecheckavailabilityname, get_string('modulecheckavailability', 'local_moofactory_notification'), '', array('disabled' => 'disabled'));
        }
        else{
            $mform->addElement('checkbox', $modulecheckavailabilityname, get_string('modulecheckavailability', 'local_moofactory_notification'));
        }

        $value = get_config('local_moofactory_notification', $modulecheckavailabilityname);
        if($value === false){
            $value = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckavailability', 'checkbox');
        }
        $mform->setDefault($modulecheckavailabilityname, $value);

        $modulecheckdateavailabilityname = 'modulecheckdateavailability_'.$courseid.'_'.$id;
        if(empty($moduleeventsvalue)){
            $mform->addElement('checkbox', $modulecheckdateavailabilityname, get_string('modulecheckdateavailability', 'local_moofactory_notification'), '', array('disabled' => 'disabled'));
        }
        else{
            $mform->addElement('checkbox', $modulecheckdateavailabilityname, get_string('modulecheckdateavailability', 'local_moofactory_notification'));
        }

        $value = get_config('local_moofactory_notification', $modulecheckdateavailabilityname);
        if($value === false){
            $value = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckdateavailability', 'checkbox');
        }
        $mform->setDefault($modulecheckdateavailabilityname, $value);

        $modulecheckgroupavailabilityname = 'modulecheckgroupavailability_'.$courseid.'_'.$id;
        if(empty($moduleeventsvalue)){
            $mform->addElement('checkbox', $modulecheckgroupavailabilityname, get_string('modulecheckgroupavailability', 'local_moofactory_notification'), '', array('disabled' => 'disabled'));
        }
        else{
            $mform->addElement('checkbox', $modulecheckgroupavailabilityname, get_string('modulecheckgroupavailability', 'local_moofactory_notification'));
        }

        $value = get_config('local_moofactory_notification', $modulecheckgroupavailabilityname);
        if($value === false){
            $value = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckgroupavailability', 'checkbox');
        }
        $mform->setDefault($modulecheckgroupavailabilityname, $value);

        $modulenotificationname = 'modulenotification_'.$courseid.'_'.$id;
        $records = $DB->get_records('local_mf_notification', array('type'=>'courseevent'));
        foreach($records as $record) {
            $options[$record->id] = $record->name;
        }
        if(empty($moduleeventsvalue)){
            $mform->addElement('select', $modulenotificationname, get_string('usednotification', 'local_moofactory_notification'), $options, array('disabled' => 'disabled'));
        }
        else{
            $mform->addElement('select', $modulenotificationname, get_string('usednotification', 'local_moofactory_notification'), $options);
        }

        $value = get_config('local_moofactory_notification', $modulenotificationname);
        if(empty($value)){
            $value = (int)local_moofactory_notification_getCustomfield($courseid, 'courseeventsnotification', 'select');
            if(!empty($value)){
                $value--;
                $courseeventsnotifications = array_values($records);
                $value = $courseeventsnotifications[$value]->id;
            }
            else{
                $value = get_config('local_moofactory_notification', 'courseseventsnotification');
            }
        }
        $mform->setDefault($modulenotificationname, $value);

        $mform->addElement('html', '<hr>');

        $configvars = ['daysbeforeevents1', 'hoursbeforeevents1', 'daysbeforeevents2', 'hoursbeforeevents2', 'daysbeforeevents3', 'hoursbeforeevents3'];
        foreach($configvars as $configvar){
            $name = 'module'.$configvar.'_'.$courseid.'_'.$id;
            if(empty($moduleeventsvalue)){
                $mform->addElement('text', $name, get_string($configvar, 'local_moofactory_notification'), array('maxlength' => 3, 'size' => 3, 'disabled' => 'disabled'));
            }
            else{
                $mform->addElement('text', $name, get_string($configvar, 'local_moofactory_notification'), array('maxlength' => 3, 'size' => 3));
            }
            $mform->setType($name, PARAM_TEXT);
            
            $value = get_config('local_moofactory_notification', $name);
            if($value === false){
                $value = local_moofactory_notification_getCustomfield($courseid, $configvar, 'text');
            }
            $mform->setDefault($name, $value);
            $mform->addRule($name, get_string('notanumber', 'local_moofactory_notification'), 'numeric', null, 'client');
            $mform->addElement('html', html_writer::start_tag('div', array('class' => 'form-group row fitem')));
            $mform->addElement('html', html_writer::start_tag('div', array('class' => 'col-md-3')));
            $mform->addElement('html', html_writer::end_tag('div'));
            $mform->addElement('html', html_writer::start_tag('div', array('class' => 'col-md-9')));
            $mform->addElement('html', get_string($configvar.'_desc', 'local_moofactory_notification'));
            $mform->addElement('html', html_writer::end_tag('div'));
            $mform->addElement('html', html_writer::end_tag('div'));
            $mform->addElement('html', '<br>');
        }

        $mform->addElement('html', html_writer::start_tag('div', array('class' => 'form-group row fitem')));
        $mform->addElement('html', html_writer::start_tag('div', array('class' => 'col-md-12')));
        $mform->addElement('html', get_string('modulereset', 'local_moofactory_notification'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', '<br>');

        /*********** form notification après levée des restrictions d’accès ************/
        $mform->addElement('html', '<hr>');
        $mform->addElement('html', '<h3>'. get_string('moduleaccesstitle', 'local_moofactory_notification') .'</h3>');

        $moduleleveename = 'modulelevee_'.$courseid.'_'.$id;
        $mform->addElement('checkbox', $moduleleveename, get_string('moduleaccess', 'local_moofactory_notification'));
        $moduleleveevalue = get_config('local_moofactory_notification', $moduleleveename);
        $mform->setDefault($moduleleveename, $moduleleveevalue);

        $moduleleveenotificationname = 'moduleleveenotification_'.$courseid.'_'.$id;
        $records = $DB->get_records('local_mf_notification', array('type'=>'moduleaccess'));

        foreach($records as $record) {
            $optionslevee[$record->id] = $record->name;
        }
        if(empty($moduleleveevalue)){
            $mform->addElement('select', $moduleleveenotificationname, get_string('usednotification', 'local_moofactory_notification'), $optionslevee, array('disabled' => 'disabled'));
        }
        else{
            $mform->addElement('select', $moduleleveenotificationname, get_string('usednotification', 'local_moofactory_notification'), $optionslevee);
        }

        $value = get_config('local_moofactory_notification', $moduleleveenotificationname);

        if(!empty($value)){
            $value--;
            $courseleveenotifications = array_values($records);
            $value = $courseleveenotifications[$value]->id;
        }
        $mform->setDefault($moduleleveenotificationname, $value);
    

        $nameleveedelay = 'moduleleveedelai_'.$courseid.'_'.$id;
        if(empty($moduleleveevalue)){
            $mform->addElement('text', $nameleveedelay, get_string('leveetime', 'local_moofactory_notification'), array('maxlength' => 3, 'size' => 3, 'disabled' => 'disabled'));
        }
        else {
            $mform->addElement('text', $nameleveedelay, get_string('leveetime', 'local_moofactory_notification'), array('maxlength' => 3, 'size' => 3));
        }

        $mform->setType($nameleveedelay, PARAM_TEXT);
        $leveedelayvalue = get_config('local_moofactory_notification', $nameleveedelay);
        $mform->setDefault($nameleveedelay, $leveedelayvalue);

        $mform->addElement('html', html_writer::start_tag('div', array('class' => 'form-group row fitem')));
        $mform->addElement('html', html_writer::start_tag('div', array('class' => 'col-md-3')));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::start_tag('div', array('class' => 'col-md-9')));
        $mform->addElement('html', get_string('moduleleveetime_desc', 'local_moofactory_notification'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', '<br>');

        $configvarslevee = ['daysbeforelevee1', 'hoursbeforelevee1', 'daysbeforelevee2', 'hoursbeforelevee2', 'daysbeforelevee3', 'hoursbeforelevee3'];
        foreach($configvarslevee as $configvar){
            $name = 'module'.$configvar.'_'.$courseid.'_'.$id;
            if(empty($moduleleveevalue)){
                $mform->addElement('text', $name, get_string($configvar, 'local_moofactory_notification'), array('maxlength' => 3, 'size' => 3, 'disabled' => 'disabled'));
            }
            else{
                $mform->addElement('text', $name, get_string($configvar, 'local_moofactory_notification'), array('maxlength' => 3, 'size' => 3));
            }
            $mform->setType($name, PARAM_TEXT);
            
            $value = get_config('local_moofactory_notification', $name);
            if($value === false){
                $value = local_moofactory_notification_getCustomfield($courseid, $configvar, 'text');
            }
            $mform->setDefault($name, $value);
            $mform->addRule($name, get_string('notanumber', 'local_moofactory_notification'), 'numeric', null, 'client');
            $mform->addElement('html', html_writer::start_tag('div', array('class' => 'form-group row fitem')));
            $mform->addElement('html', html_writer::start_tag('div', array('class' => 'col-md-3')));
            $mform->addElement('html', html_writer::end_tag('div'));
            $mform->addElement('html', html_writer::start_tag('div', array('class' => 'col-md-9')));
            $mform->addElement('html', get_string($configvar.'_desc', 'local_moofactory_notification'));
            $mform->addElement('html', html_writer::end_tag('div'));
            $mform->addElement('html', html_writer::end_tag('div'));
            $mform->addElement('html', '<br>');
        }
        
        $this->add_action_buttons();


        $js = "$('#id_$moduleeventsname').change(function(){";
        $js .= "    if($('#id_$moduleeventsname').is(':checked')){";
        $js .= "        $('#id_$modulecheckavailabilityname').removeAttr('disabled');";
        $js .= "        if($('#id_$modulecheckavailabilityname').is(':checked')){";
        $js .= "            $('#id_$modulecheckdateavailabilityname').removeAttr('disabled');";
        $js .= "            $('#id_$modulecheckgroupavailabilityname').removeAttr('disabled');";
        $js .= "        }";
        $js .= "        $('#id_$modulenotificationname').removeAttr('disabled');";
        foreach($configvars as $configvar){
            $name = 'id_module'.$configvar.'_'.$courseid.'_'.$id;
            $js .= "        $('#$name').removeAttr('disabled');";
        }
        $js .= "    }";
        $js .= "    else{";
        $js .= "        $('#id_$modulecheckavailabilityname').attr('disabled', 'disabled');";
        $js .= "        $('#id_$modulecheckdateavailabilityname').attr('disabled', 'disabled');";
        $js .= "        $('#id_$modulecheckgroupavailabilityname').attr('disabled', 'disabled');";
        $js .= "        $('#id_$modulenotificationname').attr('disabled', 'disabled');";
            foreach($configvars as $configvar){
            $name = 'id_module'.$configvar.'_'.$courseid.'_'.$id;
            $js .= "        $('#$name').attr('disabled', 'disabled');";
        }
        $js .= "    }";
        $js .= "});";

        $js .= "$('#id_$modulecheckavailabilityname').change(function(){";
        $js .= "    if($('#id_$modulecheckavailabilityname').is(':checked')){";
        $js .= "        $('#id_$modulecheckdateavailabilityname').removeAttr('disabled');";
        $js .= "        $('#id_$modulecheckgroupavailabilityname').removeAttr('disabled');";
        $js .= "    }";
        $js .= "    else{";
        $js .= "        $('#id_$modulecheckdateavailabilityname').attr('disabled', 'disabled');";
        $js .= "        $('#id_$modulecheckgroupavailabilityname').attr('disabled', 'disabled');";
        $js .= "    }";
        $js .= "});";

        $js .= "initModuleCheckAvailability($('#id_$modulecheckavailabilityname').is(':checked'), '#id_$modulecheckdateavailabilityname', '#id_$modulecheckgroupavailabilityname');";

        //js for levee restriction
        $js .= "$('#id_$moduleleveename').change(function(){";
        $js .= "    if($('#id_$moduleleveename').is(':checked')){";
        $js .= "        $('#id_$nameleveedelay').removeAttr('disabled');";
        $js .= "        $('#id_$moduleleveenotificationname').removeAttr('disabled');";
        foreach($configvarslevee as $configvar){
            $name = 'id_module'.$configvar.'_'.$courseid.'_'.$id;
            $js .= "        $('#$name').removeAttr('disabled');";
        }
        $js .= "    }";
        $js .= "    else{";
        $js .= "        $('#id_$nameleveedelay').attr('disabled', 'disabled');";
        $js .= "        $('#id_$moduleleveenotificationname').attr('disabled', 'disabled');";
            foreach($configvarslevee as $configvar){
            $name = 'id_module'.$configvar.'_'.$courseid.'_'.$id;
            $js .= "        $('#$name').attr('disabled', 'disabled');";
        }
        $js .= "    }";
        $js .= "});";


        $PAGE->requires->js('/local/moofactory_notification/util.js');
        $PAGE->requires->js_init_code($js, true);
    }

    public function validation($data, $files) {
        /*global $DB;
        $errors = parent::validation($data, $files);


        $data  = (object)$data;
        foreach ($data as $key => $value) {
            if(strpos($key, "module") === 0){
                if(!(is_numeric($value) || $value == "")){
                    $errors[$key] = get_string('notanumber', 'local_moofactory_notification');
                }
            }
        }
        return $errors;*/
    }

}

