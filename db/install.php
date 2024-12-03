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
 * Installation code for the notification local plugin
 *
 * @package    local_moofactory_notification
 * @copyright  2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_moofactory_notification_install() {
    global $DB;

    // Notifications par défaut.
    // Inscriptionx aux cours.
    $html = "{{firstname}} {{lastname}}<br><br>";
    $html .= "Vous êtes inscrit à la formation « {{coursename}} » dans la plateforme {{lmsname}}.<br>";
    $html .= "Pour vous connecter : <a href=\"{{lmsurl}}\"\>{{lmsurl}}</a><br><br>";
    $html .= "Votre identifiant : {{username}}";
  
    $record = new stdClass();
    $record->base = 1;
    $record->type = "courseenroll";
    $record->name = "Inscription par défaut";
    $record->subject = "Inscription à un cours";
    $record->bodyhtml = $html;

    $DB->insert_record('local_mf_notification', $record);

    // Non accès aux cours.
    $html = "{{firstname}} {{lastname}}<br><br>";
    $html .= "Vous êtes inscrit à la formation « \"<a href=\"{{courseurl}}\"\>{{coursename}}</a>\" » dans la plateforme {{lmsname}}.<br>";
    $html .= "Vous n’êtes pas venu dans ce cours depuis au moins {{interval}}. Rencontrez vous un problème ?<br>";
    $html .= "Nous vous invitons à vous reconnecter et à suivre votre formation.";
  
    $record = new stdClass();
    $record->base = 1;
    $record->type = "courseaccess";
    $record->name = "Non accès aux cours par défaut";
    $record->subject = "Non accès à un cours";
    $record->bodyhtml = $html;

    $DB->insert_record('local_mf_notification', $record);

    // Evènement de site.
    $html = "{{firstname}} {{lastname}}<br><br>";
    $html .= "Le {{eventdate}}, {{eventname}} sur la plateforme « <a href=\"{{lmsurl}}\"\>{{lmsname}}</a> », n’oubliez pas d’inscrire cet évènement dans votre calendrier.";
    
    $record = new stdClass();
    $record->base = 1;
    $record->type = "siteevent";
    $record->name = "Evènement de site par défaut";
    $record->subject = "Rappel d'évènement de site";
    $record->bodyhtml = $html;

    $DB->insert_record('local_mf_notification', $record);

    // Evènement de cours.
    $html = "{{firstname}} {{lastname}}<br><br>";
    $html .= "Le {{eventdate}}, un(e) {{eventname}} dans le cours « \"<a href=\"{{courseurl}}\"\>{{coursename}}</a>\" », n’oubliez pas de l’inscrire dans votre calendrier.<br><br>";
    $html .= "Nous comptons sur vous pour respecter cette échéance.";
    
    $record = new stdClass();
    $record->base = 1;
    $record->type = "courseevent";
    $record->name = "Evènement de cours par défaut";
    $record->subject = "Rappel d'évènement de cours";
    $record->bodyhtml = $html;

    $DB->insert_record('local_mf_notification', $record);

    $html = "{{firstname}} {{lastname}}<br><br>";
    $html .= "Vous êtes inscrit à la formation « \"{{coursename}}\" » dans la plateforme {{lmsname}}.<br>";
    $html .= "L'activité \"{{activityname}}\" est maintenant disponible.";
    $record = new stdClass();
    $record->base = 1;
    $record->type = "moduleaccess";
    $record->name = "Notification levée de restriction";
    $record->subject = "Levée de restriction de l'acivité";
    $record->bodyhtml = $html;

    $DB->insert_record('local_mf_notification', $record);


    // Création des champs personnalisés de cours dans la catégorie 'Notifications'.
    require_login();

    $handler = core_course\customfield\course_handler::create();
    $categoryid = $handler->create_category(get_string('notifications_category', 'local_moofactory_notification'));

    if ($categoryid) {
        $category = \core_customfield\category_controller::create($categoryid);

        // Champ 'Inscriptions à ce cours'
        $type = "checkbox";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);
        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $data = new stdClass();
        $data->name = get_string('courseenrollments', 'local_moofactory_notification');
        $data->shortname = 'courseenrollments';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Champ 'Delai'
        $type = "text";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $data = new stdClass();
        $data->name = get_string('courseenrollmentstime', 'local_moofactory_notification');
        $data->shortname = 'courseenrollmentstime';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "defaultvalue" => "", "displaysize" => 3, "maxlength" => 6, "ispassword" => "0", "link" => "",  "locked" => "0",  "visibility" => "2");
        $data->description_editor = array("text" => get_string('courseenrollmentstime_desc', 'local_moofactory_notification'), "format" => "1", "itemid" => 123);
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Select choix de la notification
        $type = "select";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $array = Array();
        $records = $DB->get_records('local_mf_notification', array('type'=>'courseenroll'));
        foreach($records as $record) {
            $array[] = $record->name;
        }
        $options = implode("\n", $array);
        $record = $DB->get_record('local_mf_notification', array('id'=>get_config('local_moofactory_notification', 'coursesenrollmentsnotification')));
        $defaultvalue = $record->name;
        
        $data = new stdClass();
        $data->name = get_string('usednotification', 'local_moofactory_notification');
        $data->shortname = 'courseenrollmentsnotification';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "options" => $options, "defaultvalue" => $defaultvalue, "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Champ 'Non accès à ce cours'
        $type = "checkbox";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $data = new stdClass();
        $data->name = get_string('courseaccess', 'local_moofactory_notification');
        $data->shortname = 'courseaccess';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Champ 'Depuis'
        $type = "text";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $data = new stdClass();
        $data->name = get_string('courseaccesstime', 'local_moofactory_notification');
        $data->shortname = 'courseaccesstime';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "defaultvalue" => "", "displaysize" => 3, "maxlength" => 6, "ispassword" => "0", "link" => "",  "locked" => "0",  "visibility" => "2");
        $data->description_editor = array("text" => get_string('courseaccesstime_desc', 'local_moofactory_notification'), "format" => "1", "itemid" => 123);
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Select choix de la notification
        $type = "select";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $array = Array();
        $records = $DB->get_records('local_mf_notification', array('type'=>'courseaccess'));
        foreach($records as $record) {
            $array[] = $record->name;
        }
        $options = implode("\n", $array);
        $record = $DB->get_record('local_mf_notification', array('id'=>get_config('local_moofactory_notification', 'coursesaccessnotification')));
        $defaultvalue = $record->name;
        
        $data = new stdClass();
        $data->name = get_string('usednotification', 'local_moofactory_notification');
        $data->shortname = 'courseaccessnotification';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "options" => $options, "defaultvalue" => $defaultvalue, "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Champ 'Evènements liés à ce cours'
        $type = "checkbox";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);
        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $data = new stdClass();
        $data->name = get_string('courseevents', 'local_moofactory_notification');
        $data->shortname = 'courseevents';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Champ 'Tenir compte des restrictions d'accès aux activités'
        $type = "checkbox";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $data = new stdClass();
        $data->name = get_string('courseeventscheckavailability', 'local_moofactory_notification');
        $data->shortname = 'courseeventscheckavailability';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Champ 'Ne pas tenir compte des restrictions de type "date"'
        $type = "checkbox";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $data = new stdClass();
        $data->name = get_string('courseeventscheckdateavailability', 'local_moofactory_notification');
        $data->shortname = 'courseeventscheckdateavailability';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Champ 'Ne pas tenir compte des restrictions de type "groupe"'
        $type = "checkbox";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $data = new stdClass();
        $data->name = get_string('courseeventscheckgroupavailability', 'local_moofactory_notification');
        $data->shortname = 'courseeventscheckgroupavailability';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Select choix de la notification
        $type = "select";
        $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

        $handler = $field->get_handler();
        if (!$handler->can_configure()) {
            print_error('nopermissionconfigure', 'core_customfield');
        }

        $array = Array();
        $records = $DB->get_records('local_mf_notification', array('type'=>'courseevent'));
        foreach($records as $record) {
            $array[] = $record->name;
        }
        $options = implode("\n", $array);
        $record = $DB->get_record('local_mf_notification', array('id'=>get_config('local_moofactory_notification', 'courseseventsnotification')));
        $defaultvalue = $record->name;
        
        $data = new stdClass();
        $data->name = get_string('usednotification', 'local_moofactory_notification');
        $data->shortname = 'courseeventsnotification';
        $data->configdata = array("required" => "0", "uniquevalues" => "0", "options" => $options, "defaultvalue" => $defaultvalue, "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
        $data->mform_isexpanded_id_header_specificsettings = 1;
        $data->mform_isexpanded_id_course_handler_header = 1;
        $data->categoryid = $categoryid;
        $data->type = $type;
        $data->id = 0;

        $handler->save_field_configuration($field, $data);

        // Champs rappels
        $configvars = ['daysbeforeevents1', 'hoursbeforeevents1', 'daysbeforeevents2', 'hoursbeforeevents2', 'daysbeforeevents3', 'hoursbeforeevents3'];
        foreach($configvars as $configvar){
            $name = $configvar;
            $type = "text";
            $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

            $handler = $field->get_handler();
            if (!$handler->can_configure()) {
                print_error('nopermissionconfigure', 'core_customfield');
            }

            $data = new stdClass();
            $data->name = get_string($name, 'local_moofactory_notification');
            $data->shortname = $name;
            $data->configdata = array("required" => "0", "uniquevalues" => "0", "defaultvalue" => "", "displaysize" => 3, "maxlength" => 3, "ispassword" => "0", "link" => "",  "locked" => "0",  "visibility" => "2");
            $data->description_editor = array("text" => get_string($name.'_desc', 'local_moofactory_notification'), "format" => "1", "itemid" => 123);
            $data->mform_isexpanded_id_header_specificsettings = 1;
            $data->mform_isexpanded_id_course_handler_header = 1;
            $data->categoryid = $categoryid;
            $data->type = $type;
            $data->id = 0;
    
            $handler->save_field_configuration($field, $data);
        }
    }

    return true;
}

