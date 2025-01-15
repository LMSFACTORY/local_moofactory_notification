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
 * Upgrade scripts for the notification local plugin
 *
 * @package    local_moofactory_notification
 * @copyright  2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade script for local_moofactory_notification
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_local_moofactory_notification_upgrade($oldversion) {
    global $CFG, $DB;
    require_login();

    $dbman = $DB->get_manager();

    if ($oldversion < 2021012700) {
        // Define table local_mf_accessnotif to be created.
        $table = new xmldb_table('local_mf_accessnotif');

        // Adding fields to table local_mf_accessnotif.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('notificationtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('notificationnumber', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table local_mf_accessnotif.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_mf_accessnotif.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
    }

    if ($oldversion < 2021020900) {
        // Notifications par défaut.
        // Non accès aux cours.
        $record = $DB->get_record('local_mf_notification', array('type'=>'courseaccess', 'base'=>1));
        if(empty($record)){
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
        }
    }

    if ($oldversion < 2021031500) {
        // Notifications par défaut.
        // Evènement de site.
        $record = $DB->get_record('local_mf_notification', array('type'=>'siteevent', 'base'=>1));
        if(empty($record)){
            $html = "{{firstname}} {{lastname}}<br><br>";
            $html .= "Le {{eventdate}}, {{eventname}} sur la plateforme « <a href=\"{{lmsurl}}\"\>{{lmsname}}</a> », n’oubliez pas d’inscrire cet évènement dans votre calendrier.";
            
            $record = new stdClass();
            $record->base = 1;
            $record->type = "siteevent";
            $record->name = "Evènement de site par défaut";
            $record->subject = "Rappel d'évènement de site";
            $record->bodyhtml = $html;
        
            $DB->insert_record('local_mf_notification', $record);
        }
    }

    if ($oldversion < 2024112700) {
        $table = new xmldb_table('local_mf_modaccessnotif');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('moduleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('notificationtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('notificationnumber', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Créer la table si elle n'existe pas déjà.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024112700, 'local', 'moofactory_notification');
    }
    if ($oldversion < 2024112800) {
        // Notifications par défaut.
        // Notification de levee de restriction.
        $record = $DB->get_record('local_mf_notification', array('type'=>'moduleaccess', 'base'=>1));
        if(empty($record)){
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
        }
    }

    if ($oldversion < 2024122700) {
        $table = new xmldb_table('local_mf_event_notifications');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('eventid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('notificationtime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('notified', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'Indicates if the event has been notified');


        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Créer la table si elle n'existe pas déjà
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Sauvegarder le point de mise à jour pour cette version
        upgrade_plugin_savepoint(true, 2024122700, 'local', 'moofactory_notification');
    }


    // Création des champs personnalisés de cours dans la catégorie 'Notifications'.
    $categoryid = $DB->get_field('customfield_category', 'id', array('name' => get_string('notifications_category', 'local_moofactory_notification')));

    if(empty($categoryid)){
        $handler = core_course\customfield\course_handler::create();
        $categoryid = $handler->create_category(get_string('notifications_category', 'local_moofactory_notification'));
    }

    // ********** Peut être utile ***********
    //$handler->move_field(field_controller $field, int $categoryid, int $beforeid = 0)


    if ($categoryid) {
        $category = \core_customfield\category_controller::create($categoryid);

        // Champ 'Inscriptions à ce cours'
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseenrollments'));
        if(empty($id)){
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
        }

        // Champ 'Delai'
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseenrollmentstime'));
        if(empty($id)){
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
        }

        // Select choix de la notification
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseenrollmentsnotification'));
        if(empty($id)){
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
        }

        // Champ 'Non accès à ce cours'
        $beforeid = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseevents'));

        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseaccess'));
        if(empty($id)){
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
            $handler->move_field($field, $categoryid, $beforeid);
        }

        // Champ 'Depuis'
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseaccesstime'));
        if(empty($id)){
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
            $handler->move_field($field, $categoryid, $beforeid);
        }

        // Select choix de la notification
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseaccessnotification'));
        if(empty($id)){
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
            $handler->move_field($field, $categoryid, $beforeid);
        }

        // Select choix de la notification 2
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseaccessnotification2'));
        if(empty($id)){
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
            $data->name = get_string('usednotification2', 'local_moofactory_notification');
            $data->shortname = 'courseaccessnotification2';
            $data->configdata = array("required" => "0", "uniquevalues" => "0", "options" => $options, "defaultvalue" => $defaultvalue, "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
            $data->mform_isexpanded_id_header_specificsettings = 1;
            $data->mform_isexpanded_id_course_handler_header = 1;
            $data->categoryid = $categoryid;
            $data->type = $type;
            $data->id = 0;
    
            $handler->save_field_configuration($field, $data);
            $handler->move_field($field, $categoryid, $beforeid);
        }

        //Select choix role notif 2
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseaccessrole2'));
        if(empty($id)){
            $type = "select";
            $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

            $handler = $field->get_handler();
            if (!$handler->can_configure()) {
                print_error('nopermissionconfigure', 'core_customfield');
            }

            $roles = $DB->get_records('role', null, '', 'id, shortname');
            $rolenames = role_fix_names($roles);

            $array = [];
            foreach ($roles as $role) {
                $array[] = $rolenames[$role->id]->localname ; 
            }
            $options = implode("\n", $array);
            
            $data = new stdClass();
            $data->name = get_string('selectrole2', 'local_moofactory_notification');
            $data->shortname = 'courseaccessrole2';
            $data->configdata = array("required" => "0", "uniquevalues" => "0", "options" => $options, "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
            $data->mform_isexpanded_id_header_specificsettings = 1;
            $data->mform_isexpanded_id_course_handler_header = 1;
            $data->categoryid = $categoryid;
            $data->type = $type;
            $data->id = 0;
    
            $handler->save_field_configuration($field, $data);
            $handler->move_field($field, $categoryid, $beforeid);
        }


        // Champ 'Copie à'
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseaccesscopie'));
        if(empty($id)){
            $type = "text";
            $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);

            $handler = $field->get_handler();
            if (!$handler->can_configure()) {
                print_error('nopermissionconfigure', 'core_customfield');
            }

            $data = new stdClass();
            $data->name = get_string('copienotif', 'local_moofactory_notification');
            $data->shortname = 'courseaccesscopie';
            $data->configdata = array("required" => "0", "uniquevalues" => "0", "defaultvalue" => "", "maxlength" => 255, "locked" => "0",  "visibility" => "2");
            $data->mform_isexpanded_id_header_specificsettings = 1;
            $data->mform_isexpanded_id_course_handler_header = 1;
            $data->categoryid = $categoryid;
            $data->type = $type;
            $data->id = 0;
            $handler->save_field_configuration($field, $data);
            $handler->move_field($field, $categoryid, $beforeid);
        }



        // Champ 'Evènements liés à ce cours'
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseevents'));
        if(empty($id)){
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
        }

        // Champ 'Tenir compte des restrictions d'accès aux activités'
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseeventscheckavailability'));
        if(empty($id)){
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
        }

        // Champ 'Ne pas tenir compte des restrictions de type "date"'
        $beforeid = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseeventsnotification'));

        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseeventscheckdateavailability'));
        if(empty($id)){
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
            $handler->move_field($field, $categoryid, $beforeid);
        }

        // Champ 'Ne pas tenir compte des restrictions de type "groupe"'
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseeventscheckgroupavailability'));
        if(empty($id)){
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
            $handler->move_field($field, $categoryid, $beforeid);
        }

        // Select choix de la notification
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseeventsnotification'));
        if(empty($id)){
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
        }

        // Champs rappels
        $configvars = ['daysbeforeevents1', 'hoursbeforeevents1', 'daysbeforeevents2', 'hoursbeforeevents2', 'daysbeforeevents3', 'hoursbeforeevents3'];
        foreach($configvars as $configvar){
            $name = $configvar;
            $id = $DB->get_field('customfield_field', 'id', array('shortname' => $name));
            if(empty($id)){
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
    }

    return true;
}
