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

require_once("classes/info_moofactory_notification.php");

function test(){
    $number = rand(1, 999);
    set_config('test', $number, 'local_moofactory_notification');
}

function local_moofactory_notification_extend_navigation_course($navigation, $course, $context) {
    global $OUTPUT, $PAGE, $DB;

    if(has_capability('moodle/course:configurecustomfields', $context)){
        // Actualisation de la valeur par défaut du champs notification des inscriptions aux cours en fonction de la valeur de définie au niveau du plugin
        $array = Array();
        $record = $DB->get_record('local_mf_notification', array('id'=>get_config('local_moofactory_notification', 'coursesenrollmentsnotification')));
        $notifdefaultvalue = $record->name;
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseenrollmentsnotification'));
        if ($id) {
            $field = \core_customfield\field_controller::create($id);
            $handler = $field->get_handler();
            require_login();
            if (!$handler->can_configure()) {
                print_error('nopermissionconfigure', 'core_customfield');
            }

            $records = $DB->get_records('local_mf_notification', array('type'=>'courseenroll'), 'base DESC, name ASC');
            foreach($records as $record) {
                $array[] = $record->name;
            }
            $options = implode("\n", $array);
            
            $data = new stdClass();
            $data->configdata = array("required" => "0", "uniquevalues" => "0", "options" => $options, "defaultvalue" => $notifdefaultvalue, "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
            
            $handler->save_field_configuration($field, $data);
        }

        // Actualisation de la valeur par défaut du champs notification non accès aux cours en fonction de la valeur de définie au niveau du plugin
        $array = Array();
        $record = $DB->get_record('local_mf_notification', array('id'=>get_config('local_moofactory_notification', 'coursesaccessnotification')));
        $notifdefaultvalue = $record->name;
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseaccessnotification'));
        if ($id) {
            $field = \core_customfield\field_controller::create($id);
            $handler = $field->get_handler();
            require_login();
            if (!$handler->can_configure()) {
                print_error('nopermissionconfigure', 'core_customfield');
            }

            $records = $DB->get_records('local_mf_notification', array('type'=>'courseaccess'), 'base DESC, name ASC');
            foreach($records as $record) {
                $array[] = $record->name;
            }
            $options = implode("\n", $array);
            
            $data = new stdClass();
            $data->configdata = array("required" => "0", "uniquevalues" => "0", "options" => $options, "defaultvalue" => $notifdefaultvalue, "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
            
            $handler->save_field_configuration($field, $data);
        }

        // Actualisation de la valeur par défaut du champs notification des évènements de cours en fonction de la valeur de définie au niveau du plugin
        $array = Array();
        $record = $DB->get_record('local_mf_notification', array('id'=>get_config('local_moofactory_notification', 'courseseventsnotification')));
        $notifdefaultvalue = $record->name;
        $id = $DB->get_field('customfield_field', 'id', array('shortname' => 'courseeventsnotification'));
        if ($id) {
            $field = \core_customfield\field_controller::create($id);
            $handler = $field->get_handler();
            require_login();
            if (!$handler->can_configure()) {
                print_error('nopermissionconfigure', 'core_customfield');
            }

            $records = $DB->get_records('local_mf_notification', array('type'=>'courseevent'), 'base DESC, name ASC');
            foreach($records as $record) {
                $array[] = $record->name;
            }
            $options = implode("\n", $array);
            
            $data = new stdClass();
            $data->configdata = array("required" => "0", "uniquevalues" => "0", "options" => $options, "defaultvalue" => $notifdefaultvalue, "checkbydefault" => "0",  "locked" => "0",  "visibility" => "2");
            
            $handler->save_field_configuration($field, $data);
        }
    }

    if(has_capability('local/moofactory_notification:setnotifications', $context)){
        $path = $PAGE->url->get_path();
        $js = "";
        $enabled = get_config('local_moofactory_notification', 'enabled');
        $coursesenrollments = get_config('local_moofactory_notification', 'coursesenrollments');
        $coursesenrollmentstime = get_config('local_moofactory_notification', 'coursesenrollmentstime');
        $coursesaccess = get_config('local_moofactory_notification', 'coursesaccess');
        $coursesaccesstime = get_config('local_moofactory_notification', 'coursesaccesstime');
        $coursesevents = get_config('local_moofactory_notification', 'coursesevents');

        if($path == "/course/edit.php"){
            $courseid = $PAGE->url->get_param("id");
            $courseenrollmentstime = local_moofactory_notification_getCustomfield($courseid, 'courseenrollmentstime', 'text');
            $courseaccesstime = local_moofactory_notification_getCustomfield($courseid, 'courseaccesstime', 'text');

            $js .= "$('<hr>').insertBefore($('#id_customfield_courseaccess').closest('.form-group.row.fitem'));";
            $js .= "$('<hr>').insertBefore($('#id_customfield_courseevents').closest('.form-group.row.fitem'));";

            $js .= "$('#id_customfield_courseenrollmentsnotification option[value=\"0\"]').remove();";
            $js .= "$('#id_customfield_courseaccessnotification option[value=\"0\"]').remove();";
            $js .= "$('#id_customfield_courseeventsnotification option[value=\"0\"]').remove();";
            $configvars = ['daysbeforeevents1', 'hoursbeforeevents1', 'daysbeforeevents2', 'hoursbeforeevents2', 'daysbeforeevents3', 'hoursbeforeevents3'];
            if(!$enabled){
                $js .= "$('#id_customfield_courseenrollments').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseenrollmentstime').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseenrollmentsnotification').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseaccess').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseaccesstime').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseaccessnotification').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseevents').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseeventscheckavailability').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseeventscheckdateavailability').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseeventscheckgroupavailability').attr('disabled', 'disabled');";
                $js .= "$('#id_customfield_courseeventsnotification').attr('disabled', 'disabled');";
                foreach($configvars as $configvar){
                    $name = 'id_customfield_'.$configvar;
                    $js .= "$('#$name').attr('disabled', 'disabled');";
                }
            }
            else{
                if(!$coursesenrollments){
                    $js .= "$('#id_customfield_courseenrollments').attr('disabled', 'disabled');";
                    $js .= "$('#id_customfield_courseenrollmentstime').attr('disabled', 'disabled');";
                    $js .= "$('#id_customfield_courseenrollmentsnotification').attr('disabled', 'disabled');";
                }
                if(!$coursesaccess){
                    $js .= "$('#id_customfield_courseaccess').attr('disabled', 'disabled');";
                    $js .= "$('#id_customfield_courseaccesstime').attr('disabled', 'disabled');";
                    $js .= "$('#id_customfield_courseaccessnotification').attr('disabled', 'disabled');";
                }
                if(!$coursesevents){
                    $js .= "$('#id_customfield_courseevents').attr('disabled', 'disabled');";
                    $js .= "$('#id_customfield_courseeventscheckavailability').attr('disabled', 'disabled');";
                    $js .= "$('#id_customfield_courseeventscheckdateavailability').attr('disabled', 'disabled');";
                    $js .= "$('#id_customfield_courseeventscheckgroupavailability').attr('disabled', 'disabled');";
                    $js .= "$('#id_customfield_courseeventsnotification').attr('disabled', 'disabled');";
                    foreach($configvars as $configvar){
                        $name = 'id_customfield_'.$configvar;
                        $js .= "$('#$name').attr('disabled', 'disabled');";
                    }
                }
            }

            if(is_null($courseenrollmentstime)){
                $js .= "$('#id_customfield_courseenrollmentstime').val($coursesenrollmentstime);";
            }

            if(is_null($courseaccesstime)){
                $js .= "$('#id_customfield_courseaccesstime').val($coursesaccesstime);";
            }

            $configvars[] = 'courseenrollmentstime';
            foreach($configvars as $configvar){
                $name = 'id_customfield_'.$configvar;
                $msg = get_string('notanumber', 'local_moofactory_notification');
                $js .= "$('#$name').blur(function(){";
                $js .= "    if(isNaN($('#$name').val()) || parseInt($('#$name').val()) < 0){";
                $js .= "        $('#id_saveanddisplay').attr('disabled', 'disabled');";
                $js .= "        $('#$name').addClass('is-invalid');";
                $js .= "        $('#id_error_customfield_$configvar').html('$msg');";
                $js .= "    }";
                $js .= "    else{";
                $js .= "        $('#$name').removeClass('is-invalid');";
                $js .= "        $('#id_error_customfield_$configvar').html('');";
                $js .= "        ret = checkValues();";
                $js .= "        if(ret){";
                $js .= "            $('#id_saveanddisplay').removeAttr('disabled');";
                $js .= "        }";
                $js .= "    }";
                $js .= "});";
            }

            $configvar = 'courseaccesstime';
            $name = 'id_customfield_'.$configvar;
            $msg = get_string('notanullnumber', 'local_moofactory_notification');
            $js .= "$('#$name').blur(function(){";
            $js .= "    if(isNaN($('#$name').val()) || Number($('#$name').val()) <= 0){";
            $js .= "        $('#id_saveanddisplay').attr('disabled', 'disabled');";
            $js .= "        $('#$name').addClass('is-invalid');";
            $js .= "        $('#id_error_customfield_$configvar').html('$msg');";
            $js .= "    }";
            $js .= "    else{";
            $js .= "        $('#$name').removeClass('is-invalid');";
            $js .= "        $('#id_error_customfield_$configvar').html('');";
            $js .= "        ret = checkValues();";
            $js .= "        if(ret){";
            $js .= "            $('#id_saveanddisplay').removeAttr('disabled');";
            $js .= "        }";
            $js .= "    }";
            $js .= "});";

            $js .= "function checkValues(){";
            $js .= "    var configvars = ['courseenrollmentstime', 'courseaccesstime', 'daysbeforeevents1', 'hoursbeforeevents1', 'daysbeforeevents2', 'hoursbeforeevents2', 'daysbeforeevents3', 'hoursbeforeevents3'];";
            $js .= "    var ret = true;";
            $js .= "    for(i=0;i<configvars.length;i++){";
            $js .= "        if(isNaN($('#id_customfield_'+configvars[i]).val())){";
            $js .= "            ret &= false;";
            $js .= "        }";
            $js .= "    }";
            $js .= "    return ret;";
            $js .= "}";

            $js .= "initCheckAvailability($('#id_customfield_courseeventscheckavailability').is(':checked'));";
        }

        // Ajout de l'item "Activation des notifications" dans le menu "Modifier"" des activités.
        if($path == "/course/view.php"){
            if($enabled && $coursesevents){
                $courseid = $PAGE->url->get_param("id");
                $activities = local_moofactory_notification_get_all_activities($courseid);
                $courseevents = local_moofactory_notification_getCustomfield($courseid, 'courseevents', 'checkbox');

                // Affichage d'un picto pour indiquer que les notification sont activées.
                foreach($activities as $activity){
                    $moduleid = $activity["id"];
                    $moduleevents = get_config('local_moofactory_notification', 'moduleevents_'.$courseid.'_'.$moduleid.'');
                    if(!empty($moduleevents)){
                        $js .= "$(\"[data-owner='#module-" . $moduleid . "']  .dropdown a.dropdown-toggle\").prepend('<span class=\"icon fa fa-envelope-o fa-fw\"></span>');";
                    }
                }

                if(empty($courseevents) && $coursesevents){
                    $menuitem = '<a class="dropdown-item editing_mfnotification menu-action cm-edit-action"';
                    $menuitem .= ' data-action="mfnotification" role="menuitem"';
                    $menuitem .= ' href="' . new moodle_url('/local/moofactory_notification/module.php', array('courseid' => $courseid, 'id' => 'moduleid')) . '"';
                    $menuitem .= ' title="' . get_string('menuitem', 'local_moofactory_notification') . '">';
                    $menuitem .= $OUTPUT->pix_icon('t/email', get_string('menuitem', 'local_moofactory_notification'));
                    $menuitem .= '<span class="menu-action-text">' . get_string('menuitem', 'local_moofactory_notification') . '</span>';
                    $menuitem .= '</a>';

                    $js .= "gmenuitem = '$menuitem';";
                    $js .= "$('.activity .dropdown-menu.dropdown-menu-right').each(function(index, element ){";
                    $js .= "    $(element).parents().each(function(index, element){";
                    $js .= "        if($(element).attr('data-owner') != undefined){";
                    $js .= "            owner = $(element).attr('data-owner');";
                    $js .= "            owner = owner.replace('#module-', '');";
                    $js .= "            menuitem = gmenuitem.replace('moduleid', owner);";
                    $js .= "        }";
                    $js .= "    });";
                    $js .= "    $(element).append(menuitem);";
                    $js .= "});";
                }
            }
        }
        $PAGE->requires->jquery();
        $PAGE->requires->js('/local/moofactory_notification/util.js');
        $PAGE->requires->js_init_code($js, true);
    }
}

// Events callback
function local_moofactory_notification_user_enrolment_created($event) : bool {
    global $DB;

    if(!empty($event)){
        $courseid = $event->courseid;
        $userid = $event->relateduserid;
    }
    else{
        $courseid = get_config('local_moofactory_notification', 'enrollcourseid');
        $userid = get_config('local_moofactory_notification', 'enrolluserid');
    }
    $user = $DB->get_record('user', array('id'=>$userid));


    // Le user a-t-il un statut actif ?
    $context = context_course::instance($courseid, IGNORE_MISSING);
    $is_enrolled = is_enrolled($context, $userid, '', true);

    if(!$is_enrolled){
        // Sinon, mise en liste d'attente dans la table 'local_mf_enrollnotif'
        $notificationtime = 0;
        $record = new stdclass();
        $record->userid = $userid;
        $record->courseid = $courseid;
        $record->notificationtime = $notificationtime + time();

        $id = $DB->insert_record('local_mf_enrollnotif', $record);
        return false;
    }


    // Activation des notifications
    $enabled = get_config('local_moofactory_notification', 'enabled');
    // Activation des inscriptions aux cours
    $coursesenrollments = get_config('local_moofactory_notification', 'coursesenrollments');
    // Tableau des notification de type courseenroll
    $courseenrollmentsnotifications = $DB->get_records('local_mf_notification', array('type'=>'courseenroll'), 'base DESC, name ASC');

    // si les notifications sont activées
    if(!empty($enabled)){
        // si les notifications d'inscriptions aux cours sont activées
        if(!empty($coursesenrollments)){
            $courseenrollments = local_moofactory_notification_getCustomfield($courseid, 'courseenrollments', 'checkbox');
            // si les notifications d'inscriptions au cours courant sont activées
            if(!empty($courseenrollments)){
                $courseenrollmentstime = local_moofactory_notification_getCustomfield($courseid, 'courseenrollmentstime', 'text');
                // S'il n'y a pas de délai programmé, envoi immédiat
                if(empty($courseenrollmentstime)){
                    // message
                    local_moofactory_notification_prepare_enrollments_email($user, $courseid, $courseenrollmentsnotifications);
                }
                // Sinon, mise en liste d'attente dans la table 'local_mf_enrollnotif'
                else{
                    $notificationtime = $courseenrollmentstime * 60;
                    $record = new stdclass();
                    $record->userid = $userid;
                    $record->courseid = $courseid;
                    $record->notificationtime = $notificationtime + time();

                    $id = $DB->insert_record('local_mf_enrollnotif', $record);
                }
            }
        }
    }
    return true;
}

function local_moofactory_notification_user_enrolment_updated($event) : bool {
    global $DB;
    
    $courseid = $event->courseid;
    $userid = $event->relateduserid;

    $coursecontext = \context_course::instance($courseid);
    $is_enrolled = is_enrolled($coursecontext, $userid, 'moodle/course:isincompletionreports', true);
    if(empty($is_enrolled)){
        // L'inscription de l'utilisateur n'est plus active...
        // Il faut supprimer la ligne correspondante dans la table local_mf_accessnotif
        $DB->delete_records('local_mf_accessnotif', array('userid'=>$userid, 'courseid'=>$courseid));
    }
    return true;
}

function local_moofactory_notification_user_enrolment_deleted($event) : bool {
    global $DB;
    
    $courseid = $event->courseid;
    $userid = $event->relateduserid;

    // L'utilisateur désinscrit n'est plus dans le cours...
    // Il faut supprimer la ligne correspondante dans la table local_mf_accessnotif et dans local_mf_enrollnotif
    $DB->delete_records('local_mf_accessnotif', array('userid'=>$userid, 'courseid'=>$courseid));
    $DB->delete_records('local_mf_enrollnotif', array('userid'=>$userid, 'courseid'=>$courseid));
    return true;
}

function local_moofactory_notification_course_viewed($event) : bool {
    global $DB;
    
    $courseid = $event->courseid;
    $userid = $event->userid;

    // Il faut supprimer la ligne correspondante dans la table local_mf_accessnotif pour réinitialiser l'envoi des notif
    $DB->delete_records('local_mf_accessnotif', array('userid'=>$userid, 'courseid'=>$courseid));
    return true;
}

function local_moofactory_notification_course_updated($event) : bool {
    global $DB;
    
    $courseid = $event->courseid;

    // Activation des non accès à ce cours
    $courseaccess = local_moofactory_notification_getCustomfield($courseid, 'courseaccess', 'checkbox');

    if(empty($courseaccess)){
        // Plus d'envoi de notifications pour ce cours
        // Il faut supprimer les lignes correspondantes dans la table local_mf_accessnotif
        $DB->delete_records('local_mf_accessnotif', array('courseid'=>$courseid));
    }
    return true;
}

// Evènements de type site
function local_moofactory_notification_send_siteevents_notification(){
    global $CFG, $DB, $SITE;
    
    require_once($CFG->dirroot.'/calendar/lib.php');

    // Nombre de notifications envoyées
    $nbnotif = 0;
    // Activation des notifications
    $enabled = get_config('local_moofactory_notification', 'enabled');
    // Activation évènements de type cours 
    $siteevents = get_config('local_moofactory_notification', 'siteevents');
    // Tableau des notification de type courseevent
    $siteeventsnotifications = $DB->get_records('local_mf_notification', array('type'=>'siteevent'), 'base DESC, name ASC');

    // Maintenant
    $time = time();

    // La dernière fois que la tâche s'est exécutée
    $previoussiteeventstasktime = get_config('local_moofactory_notification', 'previoussiteeventstasktime');
    if(empty($previoussiteeventstasktime)){
        set_config('previoussiteeventstasktime', $time, 'local_moofactory_notification');
        return;
    }
    else{
        set_config('previoussiteeventstasktime', $time, 'local_moofactory_notification');
    }


    // Si les notifications sont activées
    if(!empty($enabled)){
        // Si les évènements de type site sont activées
        if(!empty($siteevents)){
            // Délai pour l'envoi des notifications
            $daysvalue = get_config('local_moofactory_notification', 'daysbeforesiteevent');
            $hoursvalue = get_config('local_moofactory_notification', 'hoursbeforesiteevent');
            $delay = (int)$daysvalue * 60 * 60 * 24 + (int)$hoursvalue * 60 * 60;
            if(!empty($delay)){
                // Tous les évènements à venir
                $events = calendar_get_legacy_events($previoussiteeventstasktime + $delay, $time + $delay, false, false, true, true, false);

                foreach($events as $event) {
                    if($event->eventtype == "site"){
                        //$targetedevents = calendar_get_legacy_events($previoussiteeventstasktime + $delay, $time + $delay, false, false, true, true, false);
                        //foreach($targetedevents as $targetedevent) {
                            //if($targetedevent->id == $event->id){
                                // message
                                $notifvalue = get_config('local_moofactory_notification', 'siteeventsnotification');
                                $notif = $siteeventsnotifications[$notifvalue];
                                $bodyhtml = urldecode($notif->bodyhtml);
                                $find = $CFG->wwwroot."/";
                                $bodyhtml = str_replace($find,"",$bodyhtml);

                                $variables = local_moofactory_notification_fetch_variables($bodyhtml);

                                $users = get_users_listing('id');

                                if(!empty($notif)){
                                    foreach ($users as $user) {
                                        if(!is_siteadmin($user)){
                                            if(!$user->suspended){
                                                $data = new stdClass();
                                                $data->firstname = $user->firstname;
                                                $data->lastname = $user->lastname;
                                                $data->username = $user->username;
                                                $data->usergroup = "";
                                                $data->eventdate = date("d/m/Y à H:i", $event->timestart);
                                                $data->eventname = $event->name;
                                                $data->coursename = "";
                                                $data->coursestartdate = "";
                                                $data->courseenddate = "";
                                                $data->courseenrolstartdate = "";
                                                $data->courseenrolenddate = "";
                                                $data->courseurl = "";
                                                $data->activityname = "";
                                                $data->lmsurl = $CFG->wwwroot;
                                                $data->lmsname = $SITE->fullname;
                                                $data->interval = "";

                                                $msgbodyhtml = local_moofactory_notification_replace_variables($variables, $bodyhtml, $data);

                                                $msg = new stdClass();
                                                $msg->subject = $notif->subject;
                                                $msg->from = "moofactory";
                                                $msg->bodytext = "";
                                                $msg->bodyhtml = $msgbodyhtml;

                                                $ret = local_moofactory_notification_send_email($user, $msg, $courseid, 'siteevents_notification');

                                                $dateEnvoi = date("d/m/Y H:i:s", time());
                                                mtrace("\n" . 'Envoyé le : ' . $dateEnvoi . ' à ' . $data->firstname . ' ' . $data->lastname . ' (Evènement le ' . $data->eventdate .")");
                                                mtrace('Objet du mail : ' . $msg->subject);
                                                $nbnotif++;
                                            }
                                        }
                                    }
                                }
                                else{
                                    mtrace("Pas d'envoi : la notification est inexistante.");
                                }
                            //}
                        //}
                    }
                }
            }
        }
    }
    mtrace("\n" . $nbnotif . ' notification(s) envoyée(s).' ."\n");
}

// Inscriptions aux cours
function local_moofactory_notification_send_coursesenroll_notification(){
    global $DB;

    // Nombre de notifications envoyées
    $nbnotif = 0;
    // Maintenant
    $time = time();
    
    // La dernière fois que la tâche s'est exécutée
    $previouscourseenrolltasktime = get_config('local_moofactory_notification', 'previouscourseenrolltasktime');
    if(empty($previouscourseenrolltasktime)){
        set_config('previouscourseenrolltasktime', $time, 'local_moofactory_notification');
        return;
    }
    else{
        set_config('previouscourseenrolltasktime', $time, 'local_moofactory_notification');
    }

    $sql = "SELECT * FROM {local_mf_enrollnotif} ";
    $sql .= "WHERE notificationtime > ? AND notificationtime <= ?";
    $records = $DB->get_records_sql(
        $sql,
        array($previouscourseenrolltasktime, $time));

    if(!empty($records)){
        foreach($records as $record){
            $courseid = $record->courseid;
            $userid = $record->userid;
            $enrolled = is_enrolled(context_course::instance($courseid), $userid);
            $active = is_enrolled(context_course::instance($courseid), $userid, '', true);

            // Si le user courant est bien inscrit au cours correspondant
            if($enrolled){
                // S'il est actif
                if($active){
                    $user = $DB->get_record('user', array('id'=>$userid));
                    // Activation des notifications
                    $enabled = get_config('local_moofactory_notification', 'enabled');
                    // Activation des inscriptions aux cours
                    $coursesenrollments = get_config('local_moofactory_notification', 'coursesenrollments');
                    // Tableau des notification de type courseenroll
                    $courseenrollmentsnotifications = $DB->get_records('local_mf_notification', array('type'=>'courseenroll'), 'base DESC, name ASC');

                    // Si les notifications sont activées
                    if(!empty($enabled)){
                        // si les notifications d'inscriptions aux cours sont activées
                        if(!empty($coursesenrollments)){
                            $courseenrollments = local_moofactory_notification_getCustomfield($courseid, 'courseenrollments', 'checkbox');
                            // si les notifications d'inscriptions au cours courant sont activées
                            if(!empty($courseenrollments)){
                                // message
                                local_moofactory_notification_prepare_enrollments_email($user, $courseid, $courseenrollmentsnotifications);
                                $nbnotif++;
                            }
                        }
                    }
                    // Suppression de la ligne correspondant à l'envoi qui vient d'être effectué dans la table 'local_mf_enrollnotif'
                    $DB->delete_records('local_mf_enrollnotif', array('userid'=>$userid, 'courseid'=>$courseid, 'notificationtime'=>$record->notificationtime));
                }
                // Update de notificationtime dans la table 'local_mf_enrollnotif' avec l'heure courante
                else{
                    $data = new stdclass();
                    $data->id = $record->id;
                    $data->notificationtime = $time + 1;
                    $res = $DB->update_record('local_mf_enrollnotif', $data);
                }
            }
        }
    }
    mtrace("\n" . $nbnotif . ' notification(s) envoyée(s).' ."\n");
}

// Non accès aux cours
function local_moofactory_notification_send_coursesaccess_notification(){
    global $DB;

    // Nombre de notifications envoyées
    $nbnotif = 0;
    // Activation des notifications
    $enabled = get_config('local_moofactory_notification', 'enabled');
    // Activation des non accès aux cours 
    $coursesaccess = get_config('local_moofactory_notification', 'coursesaccess');
    // Tableau des notification de type courseaccess
    $courseaccessnotifications = $DB->get_records('local_mf_notification', array('type'=>'courseaccess'), 'base DESC, name ASC');
    // Nombre max de notifications à envoyer entre deux accès 
    $coursesaccessnotifnumber = get_config('local_moofactory_notification', 'coursesaccessnotifnumber');
    // Valeur par défaut
    $coursesaccessnotifdefaultnumber = get_config('local_moofactory_notification', 'coursesaccessnotifdefaultnumber');
    if(empty($coursesaccessnotifnumber)){
        // Si 0, on force la valeur par défaut
        set_config('coursesaccessnotifnumber', $coursesaccessnotifdefaultnumber, 'local_moofactory_notification'); // Valeur par défaut
        $coursesaccessnotifnumber = $coursesaccessnotifdefaultnumber;
    }

    // Maintenant
    $time = time();

    // Si l'envoi des notifications est désactivé au niveau plugin, on vide la table local_mf_accessnotif
    if(empty($enabled) || empty($coursesaccess)){
        $DB->delete_records('local_mf_accessnotif');
    }

    // si les notifications sont activées
    if(!empty($enabled)){
        // si les notifications des non accès aux cours sont activées
        if(!empty($coursesaccess)){
            // Recherche de tous les cours visibles dont la date de début est antérieure à la date courante
            // et la date de fin est inactive ou postérieure à la date courante.
            $sql = "SELECT id, fullname FROM {course} WHERE id <> 1 AND (startdate <= ?) AND (enddate = 0 OR enddate > ?) AND visible = 1";
            $params = array ($time, $time);
            $courses = $DB->get_records_sql($sql, $params);

            foreach ($courses as $course) {
                $courseid = $course->id;
                $courseaccess = local_moofactory_notification_getCustomfield($courseid, 'courseaccess', 'checkbox');
                // si les notifications des non accès au cours courant sont activées
                if(!empty($courseaccess)){
                    $coursecontext = \context_course::instance($courseid);
                    // 'moodle/course:isincompletionreports' - this capability is allowed to only students
                    // Seulement les inscriptions actives
                    $enrolledusers = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports', 0, 'u.*', null, 0, 0, true);

                    foreach ($enrolledusers as $user) {
                        if(!is_siteadmin($user)){
                            $userid = $user->id;

                            $progress = local_moofactory_notification_get_progress($courseid, $userid);
                            // On envoie un notification uniquement si le cours n'est pas achevé
                            if($progress < 100){
                                // Calcul de l'intervalle
                                $courseaccesstime = local_moofactory_notification_getCustomfield($courseid, 'courseaccesstime', 'text');
                                $interval = (int)$courseaccesstime * 60 * 60 * 24; // jours
                                // $interval = (int)$courseaccesstime * 60 * 60; // heures pour tests
                                // $interval = (int)$courseaccesstime * 60; // minutes pour tests

                                // On envoie une notification si le délai de non accès a été atteint depuis la dernière fois que la tâche s'est exécutée

                                // On regarde si une notif a déja été envoyée et depuis combien de temps
                                $recordaccessnotif = $DB->get_record('local_mf_accessnotif', array('userid' => $userid, 'courseid' => $courseid), 'id, notificationtime, notificationnumber');

                                if(!empty($recordaccessnotif)){
                                    $notificationtime = $recordaccessnotif->notificationtime;
                                    $idaccessnotif = $recordaccessnotif->id;
                                    $notificationnumber = $recordaccessnotif->notificationnumber;

                                    if($notificationtime + $interval <= $time){
                                        // Est-ce qu'on est en dessous du nombre max de notif à envoyer ?
                                        if($notificationnumber < $coursesaccessnotifnumber){
                                            // update de local_mf_accessnotif
                                            $data = new stdclass();
                                            $data->userid = $userid;
                                            $data->courseid = $courseid;
                                            // Offset d'une minute pour rattraper le décalage entre l'heure de l'exécution théorique de la tâche et l'heure réelle
                                            $data->notificationtime = $time - 60; 
                                            $data->id = $idaccessnotif;
                                            $data->notificationnumber = $notificationnumber + 1;
                                            $res = $DB->update_record('local_mf_accessnotif', $data);

                                            // Envoi de la notification
                                            local_moofactory_notification_prepare_access_email($user, $courseid, $courseaccesstime, $courseaccessnotifications);
                                            $nbnotif++;
                                        }
                                    }
                                }

                                // Si aucune notif n'a été envoyée, on regarde depuis combien de temps il n'y a pas eu d'accès
                                else{
                                    $recordlastaccess = $DB->get_field('user_lastaccess', 'timeaccess', array('userid' => $userid, 'courseid' => $courseid), IGNORE_MISSING);
                                    // SI le user est déja allé sur le cours, on récupère la date de son dernier accès
                                    if(!empty($recordlastaccess)){
                                        $lastaccesstime = $recordlastaccess;
                                    }
                                    // Sinon on récupère la date de son inscription la plus ancienne
                                    else{
                                        $enrolments = local_moofactory_notification_get_user_enrolments($courseid, $userid);
                                        $times = array();
                                        foreach ($enrolments as $enrolment) {
                                            $times[] = $enrolment->timestart;
                                        }
                                        $timestart = min($times);
                                        $lastaccesstime = $timestart;
                                    }

                                    if($lastaccesstime + $interval <= $time){
                                        // Insert dans la table accessnotif de la ligne correspondant au cours et au user
                                        $record = new stdclass();
                                        $record->userid = $userid;
                                        $record->courseid = $courseid;
                                        // Offset d'une minute pour rattraper le décalage entre l'heure de l'exécution théorique de la tâche et l'heure réelle
                                        $record->notificationtime = $time - 60;
                                        $record->notificationnumber = 1;
                                        $DB->insert_record('local_mf_accessnotif', $record);

                                        // Envoi de la notification
                                        local_moofactory_notification_prepare_access_email($user, $courseid, $courseaccesstime, $courseaccessnotifications);
                                        $nbnotif++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    mtrace("\n" . $nbnotif . ' notification(s) envoyée(s).' ."\n");
}

function local_moofactory_notification_get_progress($courseid, $userid) {
    global $DB;
    
    $params = array('id' => $courseid);
    $course = $DB->get_record('course', $params, '*', MUST_EXIST);

    $activities = local_moofactory_notification_get_activities($courseid);
    $exclusions = local_moofactory_notification_exclusions($courseid);
    $useractivities = local_moofactory_notification_filter_visibility($activities, $userid, $courseid, $exclusions);
    if (!empty($useractivities)) {
        $completions = local_moofactory_notification_completions($useractivities, $userid, $course, null);
        $progressvalue = local_moofactory_notification_percentage($useractivities, $completions, $course, $userid);
    } else {
        $progressvalue = 0;
    }
    return $progressvalue;
}

/**
 * Returns the activities with completion set in current course
 *
 * @param int    courseid   ID of the course
 * @param int    config     The block instance configuration
 * @param string forceorder An override for the course order setting
 * @return array Activities with completion settings in the course
 */
function local_moofactory_notification_get_activities($courseid, $config = null, $forceorder = null) {
    $modinfo = get_fast_modinfo($courseid, -1);

    $sections = $modinfo->get_sections();
    $activities = array();
    foreach ($modinfo->instances as $module => $instances) {
        $modulename = get_string('pluginname', $module);
        foreach ($instances as $index => $cm) {
            if (
                $cm->completion != COMPLETION_TRACKING_NONE && (
                    $config == null || (
                        !isset($config->activitiesincluded) || (
                            $config->activitiesincluded != 'selectedactivities' ||
                                !empty($config->selectactivities) &&
                                in_array($module.'-'.$cm->instance, $config->selectactivities))))
            ) {
                $activities[] = array (
                    'type'       => $module,
                    'modulename' => $modulename,
                    'id'         => $cm->id,
                    'instance'   => $cm->instance,
                    'name'       => format_string($cm->name),
                    'expected'   => $cm->completionexpected,
                    'section'    => $cm->sectionnum,
                    'position'   => array_search($cm->id, $sections[$cm->sectionnum]),
                    'url'        => method_exists($cm->url, 'out') ? $cm->url->out() : '',
                    'context'    => $cm->context,
                    'icon'       => $cm->get_icon_url(),
                    'available'  => $cm->available,
                    'deletioninprogress' => $cm->deletioninprogress,
                );
            }
        }
    }

    // Sort by first value in each element, which is time due.
    if ($forceorder == 'orderbycourse' || ($config && $config->orderby == 'orderbycourse')) {
        usort($activities, 'format_moofactory_compare_events');
    } else {
        usort($activities, 'format_moofactory_compare_times');
    }

    return $activities;
}

function local_moofactory_notification_get_all_activities($courseid, $config = null, $forceorder = null) {
    $modinfo = get_fast_modinfo($courseid, -1);

    $sections = $modinfo->get_sections();
    $activities = array();
    foreach ($modinfo->instances as $module => $instances) {
        $modulename = get_string('pluginname', $module);
        foreach ($instances as $index => $cm) {
            $activities[] = array (
                'type'       => $module,
                'modulename' => $modulename,
                'id'         => $cm->id,
                'instance'   => $cm->instance,
                'name'       => format_string($cm->name),
                'expected'   => $cm->completionexpected,
                'section'    => $cm->sectionnum,
                'position'   => array_search($cm->id, $sections[$cm->sectionnum]),
                'url'        => method_exists($cm->url, 'out') ? $cm->url->out() : '',
                'context'    => $cm->context,
                'icon'       => $cm->get_icon_url(),
                'available'  => $cm->available,
                'deletioninprogress' => $cm->deletioninprogress,
            );
        }
    }

    return $activities;
}

/**
 * Finds gradebook exclusions for students in a course
 *
 * @param int $courseid The ID of the course containing grade items
 * @ array of exclusions as activity-user pairs
 */
function local_moofactory_notification_exclusions($courseid) {
    global $DB;

    $query = "SELECT g.id, ". $DB->sql_concat('i.itemmodule', "'-'", 'i.iteminstance', "'-'", 'g.userid') ." as exclusion
               FROM {grade_grades} g, {grade_items} i
              WHERE i.courseid = :courseid
                AND i.id = g.itemid
                AND g.excluded <> 0";
    $params = array ('courseid' => $courseid);
    $results = $DB->get_records_sql($query, $params);
    $exclusions = array();
    foreach ($results as $key => $value) {
        $exclusions[] = $value->exclusion;
    }
    return $exclusions;
}

/**
 * Filters activities that a user cannot see due to grouping constraints
 *
 * @param array  $activities The possible activities that can occur for modules
 * @param array  $userid The user's id
 * @param string $courseid the course for filtering visibility
 * @param array  $exclusions Assignment exemptions for students in the course
 * @ array The array with restricted activities removed
 */
function local_moofactory_notification_filter_visibility($activities, $userid, $courseid, $exclusions) {
    global $CFG;
    $filteredactivities = array();
    $modinfo = get_fast_modinfo($courseid, $userid);
    $coursecontext = CONTEXT_COURSE::instance($courseid);

    // Keep only activities that are visible.
    foreach ($activities as $index => $activity) {

        $coursemodule = $modinfo->cms[$activity['id']];

        // Check visibility in course.
        if (!$coursemodule->visible && !has_capability('moodle/course:viewhiddenactivities', $coursecontext, $userid)) {
            continue;
        }

        // Check availability, allowing for visible, but not accessible items.
        if (!empty($CFG->enableavailability)) {
            if (has_capability('moodle/course:viewhiddenactivities', $coursecontext, $userid)) {
                $activity['available'] = true;
            } else {
                if (isset($coursemodule->available) && !$coursemodule->available && empty($coursemodule->availableinfo)) {
                    continue;
                }
                $activity['available'] = $coursemodule->available;
            }
        }

        // Check visibility by grouping constraints (includes capability check).
        if (!empty($CFG->enablegroupmembersonly)) {
            if (isset($coursemodule->uservisible)) {
                if ($coursemodule->uservisible != 1 && empty($coursemodule->availableinfo)) {
                    continue;
                }
            } else if (!groups_course_module_visible($coursemodule, $userid)) {
                continue;
            }
        }

        // Check for exclusions.
        if (in_array($activity['type'].'-'.$activity['instance'].'-'.$userid, $exclusions)) {
            continue;
        }

        // Save the visible event.
        $filteredactivities[] = $activity;
    }
    return $filteredactivities;
}

/**
 * Checked if a user has completed an activity/resource
 *
 * @param array $activities  The activities with completion in the course
 * @param int   $userid      The user's id
 * @param int   $course      The course instance
 * @param array $submissions Submissions by the user
 * @ array   an describing the user's attempts based on module+instance identifiers
 */
function local_moofactory_notification_completions($activities, $userid, $course, $submissions) {
    $completions = array();
    $completion = new completion_info($course);
    $cm = new stdClass();

    foreach ($activities as $activity) {
        $cm->id = $activity['id'];
        $activitycompletion = $completion->get_data($cm, true, $userid);
        $completions[$activity['id']] = $activitycompletion->completionstate;
    }

    return $completions;
}

/**
 * Calculates an overall percentage of progress
 *
 * @param array $activities   The possible events that can occur for modules
 * @param array $completions The user's attempts on course activities
 * @ int  Progress value as a percentage
 */
function local_moofactory_notification_percentage($activities, $completions, $course, $userid) {
    global $DB;

    $completecount = 0;
    $numactivities = 0;

    $notavailablecount = 0;

    $sql = "SELECT cd.intvalue FROM {customfield_data} cd ";
    $sql .= "LEFT JOIN {customfield_field} cf ON cf.id = cd.fieldid ";
    $sql .= "WHERE cd.instanceid = ? AND cf.shortname = ?";
    $record = $DB->get_record_sql(
        $sql,
        array($course->id, 'checkavailability'));
    $checkavailability = $record->intvalue;

    $modinfo = get_fast_modinfo($course, $userid);

    foreach ($activities as $activity) {
        if(!empty($checkavailability)){
            $mod = $modinfo->cms[$activity['id']];
            if($mod->available){
                if (
                    $completions[$activity['id']] == COMPLETION_COMPLETE ||
                    $completions[$activity['id']] == COMPLETION_COMPLETE_PASS
                ) {
                    $completecount++;
                }
                if(empty($activity['deletioninprogress'])){
                    $numactivities++;
                }
            }
        }
        else{
            if (
                $completions[$activity['id']] == COMPLETION_COMPLETE ||
                $completions[$activity['id']] == COMPLETION_COMPLETE_PASS
            ) {
                $completecount++;
            }
            if(empty($activity['deletioninprogress'])){
                $numactivities++;
            }
        }
    }

    $progressvalue = $completecount == 0 ? 0 : $completecount / $numactivities;

    return (int)round($progressvalue * 100);
}

function local_moofactory_notification_get_user_enrolments($courseid, $userid) {
    global $DB;

    $sql = "SELECT ue.*
              FROM {user_enrolments} ue
              JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid)
              JOIN {user} u ON u.id = ue.userid
             WHERE ue.userid = :userid AND ue.status = :active AND e.status = :enabled AND u.deleted = 0";
    $params = array('enabled'=>ENROL_INSTANCE_ENABLED, 'active'=>ENROL_USER_ACTIVE, 'userid'=>$userid, 'courseid'=>$courseid);

    if (!$enrolments = $DB->get_records_sql($sql, $params)) {
         false;
    }

    return $enrolments;
}

function local_moofactory_notification_get_user_enrolment_dates($courseid, $userid) {
    $enrolments = local_moofactory_notification_get_user_enrolments($courseid, $userid);
    $starttimes = array();
    $endtimes = array();
    foreach ($enrolments as $enrolment) {
        $starttimes[] = $enrolment->timestart;
        $endtimes[] = $enrolment->timeend;
    }
    // On récupère la date de début d'inscription la plus ancienne et la date de fin la plus éloignée.
    $timestart = min($starttimes);
    $timeend = max($endtimes);
    return [$timestart, $timeend];
}

// Evènements de type cours.
function local_moofactory_notification_send_coursesevents_notification(){
    global $CFG, $DB, $SITE;
    
    require_once($CFG->dirroot.'/calendar/lib.php');

    // Nombre de notifications envoyées
    $nbnotif = 0;
    // Activation des notifications.
    $enabled = get_config('local_moofactory_notification', 'enabled');
    // Activation évènements de type cours.
    $coursesevents = get_config('local_moofactory_notification', 'coursesevents');
    // Tableau des notification de type courseevent.
    $courseeventsnotifications = $DB->get_records('local_mf_notification', array('type'=>'courseevent'), 'base DESC, name ASC');

    // Maintenant.
    $time = time();

    // La dernière fois que la tâche s'est exécutée.
    $previouscourseeventstasktime = get_config('local_moofactory_notification', 'previouscourseeventstasktime');
    if(empty($previouscourseeventstasktime)){
        set_config('previouscourseeventstasktime', $time, 'local_moofactory_notification');
        return;
    }
    else{
        set_config('previouscourseeventstasktime', $time, 'local_moofactory_notification');
    }

    // Si les notifications sont activées.
    if(!empty($enabled)){
        // Si les évènements de type cours sont activées.
        if(!empty($coursesevents)){
            // Tous les évènements à venir.
            $events = calendar_get_legacy_events($previouscourseeventstasktime, 0, false, false, true, false, false);

            // Pour les rendez-vous.
            $events2 = calendar_get_legacy_events($previouscourseeventstasktime, 0, false, false, 0, false, false);

            $events = array_merge($events, $events2);

            foreach($events as $event) {
                if (!empty($event->courseid) && !empty($event->modulename )){
                    $courseid = $event->courseid;
                    $coursecontext = \context_course::instance($courseid);
                    // 'moodle/course:isincompletionreports' - this capability is allowed to only students.
                    // Seulement les inscriptions actives.
                    $enrolledusers = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports', 0, 'u.*', null, 0, 0, true);

                    $instances = get_fast_modinfo($event->courseid, $event->userid)->get_instances_of($event->modulename);
                    if (array_key_exists($event->instance, $instances)) {
                        $module = $instances[$event->instance];

                        $moduleid = $module->id;
                        $modulename = $module->name;

                        // Activation évènements au niveau du cours.
                        $courseevents = local_moofactory_notification_getCustomfield($courseid, 'courseevents', 'checkbox');

                        // Si les évènements sont activés au niveau du cours.
                        if(!empty($courseevents)){
                            // Restriction
                            $modulecheckavailabilityvalue = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckavailability', 'checkbox');
                            // Date
                            $modulecheckdateavailabilityvalue = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckdateavailability', 'checkbox');
                            // Groupe
                            $modulecheckgroupavailabilityvalue = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckgroupavailability', 'checkbox');
                        }
                        // Sinon on prend le paramétrage de l'activité.
                        else{
                            // Restriction
                            $modulecheckavailabilityname = 'modulecheckavailability_'.$courseid.'_'.$moduleid;
                            $modulecheckavailabilityvalue = get_config('local_moofactory_notification', $modulecheckavailabilityname);
                            // Date
                            $modulecheckdateavailabilityname = 'modulecheckdateavailability_'.$courseid.'_'.$moduleid;
                            $modulecheckdateavailabilityvalue = get_config('local_moofactory_notification', $modulecheckdateavailabilityname);
                            // Groupe
                            $modulecheckgroupavailabilityname = 'modulecheckgroupavailability_'.$courseid.'_'.$moduleid;
                            $modulecheckgroupavailabilityvalue = get_config('local_moofactory_notification', $modulecheckgroupavailabilityname);

                            // Si les notifications de l'activité n'ont jamais été paramétrées et enregistrées on prend les réglages du cours
                            if($modulecheckavailabilityvalue === false){
                                $modulecheckavailabilityvalue = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckavailability', 'checkbox');
                            }
                            // Date
                            if($modulecheckdateavailabilityvalue === false){
                                $modulecheckdateavailabilityvalue = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckdateavailability', 'checkbox');
                            }
                            // Groupe
                            if($modulecheckgroupavailabilityvalue === false){
                                $modulecheckgroupavailabilityvalue = local_moofactory_notification_getCustomfield($courseid, 'courseeventscheckgroupavailability', 'checkbox');
                            }
                        }
                    }
                }

                // Pour l'évènement courant, on cherche les delais des trois rappels (cours ou activité)
                // Evènements liés au cours $courseid  
                if(!empty($courseevents)){
                    // Valeurs des rappels liés au cours $courseid
                    $delays = local_moofactory_notification_get_delays('course', $courseid);
                }
                else{
                    // Evènements liés à l'activité $moduleid  
                    $moduleevents = get_config('local_moofactory_notification', 'moduleevents_'.$courseid.'_'.$moduleid.'');
                    if(!empty($moduleevents)){
                        // Valeurs des rappels liés à l'activité $moduleid
                        $delays = local_moofactory_notification_get_delays('module', $courseid, $moduleid);
                    }
                }

                // Envoi des notifications si l'évènement est prévu à l'issue d'un des délais, au moment de la tâche courante.
                if(!empty($delays)){
                    foreach($delays as $delay) {
                        $targetedevents = calendar_get_legacy_events($previouscourseeventstasktime + $delay, $time + $delay, false, false, $courseid, false, false);

                        // Pour les rendez-vous
                        $targetedevents2 = calendar_get_legacy_events($previouscourseeventstasktime + $delay, $time + $delay, false, false, 0, false, false);

                        $targetedevents = array_merge($targetedevents, $targetedevents2);

                        foreach($targetedevents as $targetedevent) {
                            if($targetedevent->id == $event->id){
                                // message
                                if(!empty($courseevents)){
                                    // Notification du cours $courseid
                                    $notifvalue = (int)local_moofactory_notification_getCustomfield($courseid, 'courseeventsnotification', 'select');
                                    if(!empty($notifvalue)){
                                        $courseeventsnotifications = array_values($courseeventsnotifications);
                                        $notifvalue--;
                                    }
                                    else{
                                        $notifvalue = get_config('local_moofactory_notification', 'courseseventsnotification');
                                    }
                                }
                                else{
                                    // Notification de l'activité $moduleid
                                    $moduleevents = get_config('local_moofactory_notification', 'moduleevents_'.$courseid.'_'.$moduleid.'');
                                    if(!empty($moduleevents)){
                                        $configvarid = 'modulenotification'.'_'.$courseid.'_'.$moduleid;
                                        $notifvalue = get_config('local_moofactory_notification', $configvarid);
                                    }
                                }
                
                                $notif = $courseeventsnotifications[$notifvalue];
                                $bodyhtml = urldecode($notif->bodyhtml);
                                $find = $CFG->wwwroot."/";
                                $bodyhtml = str_replace($find,"",$bodyhtml);

                                $variables = local_moofactory_notification_fetch_variables($bodyhtml);

                                if(!empty($notif)){
                                    foreach ($enrolledusers as $user) {
                                        $instances = get_fast_modinfo($event->courseid, $user->id)->get_instances_of($event->modulename);

                                        // echo($user->firstname." ".$user->lastname."<br>");
                            
                                        if (array_key_exists($event->instance, $instances)) {
                                            $module = $instances[$event->instance];
                                        }

                                        $moduleavailable = true;

                                        if(!empty($modulecheckavailabilityvalue) && !empty($module->availability)){
                                            // On tient compte des restrictions s'il y en a.

                                            // Get availability information.
                                            $ci = new \core_availability\info_moofactory_notification($module);
                                            $ci->set_modinfo($event->courseid, $user->id);

                                            $tree = $ci->get_availability_tree();
                                            list($innernot, $andoperator) = $tree->get_logic_flags(false);
                                            $children = $tree->get_all_children('core_availability\condition');

                                            // Il faut traiter chaque restriction pour en déduire la disponibilité du module.
                                            foreach ($children as $index => $child) {
                                                $childresult = $child->is_available($innernot, $ci, true, $user->id);
                                                $type = preg_replace('~^availability_(.*?)\\\\condition$~', '$1', get_class($child));

                                                if($type != "date" && $type != "group"){
                                                    $moduleavailable &= $childresult;
                                                }
                                                elseif($type == "date"){
                                                    if(!empty($modulecheckdateavailabilityvalue)){
                                                        $moduleavailable &= true;
                                                    }
                                                    else{
                                                        $moduleavailable &= $childresult;
                                                    }
                                                }
                                                elseif($type == "group"){
                                                    if(!empty($modulecheckgroupavailabilityvalue)){
                                                        $moduleavailable &= true;
                                                    }
                                                    else{
                                                        $moduleavailable &= $childresult;
                                                    }
                                                }
                                            }
                                        }

                                        // Pas de notification si l'activité est achevée.
                                        $result = $DB->get_record('course_modules_completion', array('coursemoduleid' => $moduleid, 'userid' => $user->id), 'completionstate');
                                        $completionstate = $result->completionstate;

                                        if(!is_siteadmin($user) && $moduleavailable && !$completionstate){
                                            // Pour les activités rendez-vous, on tient compte du user concerné
                                            if($targetedevent->modulename != "scheduler" || $targetedevent->userid == $user->id){
                                                $data = new stdClass();
                                                $data->firstname = $user->firstname;
                                                $data->lastname = $user->lastname;
                                                $data->username = $user->username;
                                                $data->usergroup = local_moofactory_notification_get_group($courseid, $user->id);
                                                $data->eventdate = date("d/m/Y à H:i", $targetedevent->timestart);
                                                $data->eventname = $targetedevent->name;
                                                $course = $DB->get_record('course', array('id' => $courseid), 'fullname,startdate,enddate');
                                                $data->coursename = $course->fullname;
                                                $data->coursestartdate = $course->startdate == "0" ? "" : date("d/m/Y à H:i", $course->startdate);
                                                $data->courseenddate = $course->enddate == "0" ? "" : date("d/m/Y à H:i", $course->enddate);
                                                list($courseenrolstartdate, $courseenrolenddate) = local_moofactory_notification_get_user_enrolment_dates($courseid, $user->id);
                                                $data->courseenrolstartdate = $courseenrolstartdate == "0" ? "" : date("d/m/Y à H:i", $courseenrolstartdate);
                                                $data->courseenrolenddate = $courseenrolenddate == "0" ? "" : date("d/m/Y à H:i", $courseenrolenddate);
                                                $data->courseurl = $CFG->wwwroot . '/course/view.php?id=' . $courseid;
                                                $data->activityname = $modulename;
                                                $data->lmsurl = $CFG->wwwroot;
                                                $data->lmsname = $SITE->fullname;
                                                $data->interval = "";

                                                $msgbodyhtml = local_moofactory_notification_replace_variables($variables, $bodyhtml, $data);

                                                $msg = new stdClass();
                                                $msg->subject = $notif->subject;
                                                $msg->from = "moofactory";
                                                $msg->bodytext = "";
                                                $msg->bodyhtml = $msgbodyhtml;

                                                $ret = local_moofactory_notification_send_email($user, $msg, $courseid, 'coursesevents_notification');
                                                
                                                $dateEnvoi = date("d/m/Y H:i:s", time());
                                                mtrace("\n" . 'Envoyé le : ' . $dateEnvoi . ' à ' . $data->firstname . ' ' . $data->lastname . ' - Cours : ' . $data->coursename . ' - Activité : ' . $data->activityname . ' (Evènement le ' . $data->eventdate .")");
                                                mtrace('Objet du mail : ' . $msg->subject);
                                                $nbnotif++;
                                            }
                                        }
                                    }
                                }
                                else{
                                    mtrace("Pas d'envoi : la notification est inexistante.");
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    mtrace("\n" . $nbnotif . ' notification(s) envoyée(s).' ."\n");
}

function local_moofactory_notification_getCustomfield($courseid, $name, $type){
    global $DB;

    switch($type){
        case "select" :
        case "checkbox" :
            $fieldvalue = "intvalue";
            break;
        case "text" :
            $fieldvalue = "charvalue";
            break;
        }
    $sql = "SELECT cd.$fieldvalue FROM {customfield_data} cd ";
    $sql .= "LEFT JOIN {customfield_field} cf ON cf.id = cd.fieldid ";
    $sql .= "WHERE cd.instanceid = ? AND cf.shortname = ?";
    $record = $DB->get_record_sql(
        $sql,
        array($courseid, $name));
    $value = $record->$fieldvalue;
    return $value;
}

function local_moofactory_notification_setCustomfield($courseid, $name, $type, $value){
    global $DB;

    switch($type){
        case "select" :
        case "checkbox" :
            $fieldvalue = "intvalue";
            break;
        case "text" :
            $fieldvalue = "charvalue";
            break;
        }
    $sql = "UPDATE {customfield_data} cd ";
    $sql .= "LEFT JOIN {customfield_field} cf ON cf.id = cd.fieldid ";
    $sql .= "SET cd.$fieldvalue = $value, cd.value = $value ";
    $sql .= "WHERE cd.instanceid = ? AND cf.shortname = ?";
    $DB->execute($sql, array($courseid, $name));
}

function local_moofactory_notification_get_group($courseid, $userid){
    global $DB;

    $sql = "SELECT g.name FROM {groups} g ";
    $sql .= "LEFT JOIN {groups_members} gm ON g.id = gm.groupid ";
    $sql .= "WHERE g.courseid = ? AND gm.userid = ?";

    $records = $DB->get_records_sql(
        $sql,
        array($courseid, $userid));

    $groups = implode(", ", array_keys($records));

    if(!empty($groups)){
        $return = $groups;
    }
    else{
        $return = get_string('nogroup', 'local_moofactory_notification');
    }
    return $return;
}

function local_moofactory_notification_replace_variables($variables, $html, $data) {
    
    foreach($variables as $variable){
        $find = "{{".$variable."}}";
        switch($variable){
            case "firstname" :
                $html = str_replace($find,$data->firstname,$html);
                break;
            case "lastname" :
                $html = str_replace($find,$data->lastname,$html);
                break;
            case "username" :
                $html = str_replace($find,$data->username,$html);
                break;
            case "usergroup" :
                $html = str_replace($find,$data->usergroup,$html);
                break;
            case "eventdate" :
                $html = str_replace($find,$data->eventdate,$html);
                break;
            case "eventname" :
                $html = str_replace($find,$data->eventname,$html);
                break;
            case "coursename" :
                $html = str_replace($find,$data->coursename,$html);
                break;
            case "coursestartdate" :
                $html = str_replace($find,$data->coursestartdate,$html);
                break;
            case "courseenddate" :
                $html = str_replace($find,$data->courseenddate,$html);
                break;
            case "courseenrolstartdate" :
                $html = str_replace($find,$data->courseenrolstartdate,$html);
                break;
            case "courseenrolenddate" :
                $html = str_replace($find,$data->courseenrolenddate,$html);
                break;
            case "courseurl" :
                $html = str_replace($find,$data->courseurl,$html);
                break;
            case "activityname" :
                $html = str_replace($find,$data->activityname,$html);
                break;
            case "lmsurl" :
                $html = str_replace($find,$data->lmsurl,$html);
                break;
            case "lmsname" :
                $html = str_replace($find,$data->lmsname,$html);
                break;
            case "interval" :
                $html = str_replace($find,$data->interval,$html);
                break;
            }
    }
    return $html;
}

function local_moofactory_notification_prepare_access_email($user, $courseid, $courseaccesstime, $courseaccessnotifications) {
    global $DB, $CFG, $SITE;

    $notifvalue = (int)local_moofactory_notification_getCustomfield($courseid, 'courseaccessnotification', 'select');
    if(!empty($notifvalue)){
        $courseaccessnotifications = array_values($courseaccessnotifications);
        $notifvalue--;
    }
    else{
        $notifvalue = get_config('local_moofactory_notification', 'coursesaccessnotification');
    }

    $notif = $courseaccessnotifications[$notifvalue];
    $bodyhtml = urldecode($notif->bodyhtml);
    $find = $CFG->wwwroot."/";
    $bodyhtml = str_replace($find,"",$bodyhtml);
    
    $variables = local_moofactory_notification_fetch_variables($bodyhtml);
    
    if(!empty($notif)){
        $data = new stdClass();
        $data->firstname = $user->firstname;
        $data->lastname = $user->lastname;
        $data->username = $user->username;
        $data->usergroup = local_moofactory_notification_get_group($courseid, $user->id);
        $data->eventdate = "";
        $data->eventname = "";
        $course = $DB->get_record('course', array('id' => $courseid), 'fullname,startdate,enddate');
        $data->coursename = $course->fullname;
        $data->coursestartdate = $course->startdate == "0" ? "" : date("d/m/Y à H:i", $course->startdate);
        $data->courseenddate = $course->enddate == "0" ? "" : date("d/m/Y à H:i", $course->enddate);
        list($courseenrolstartdate, $courseenrolenddate) = local_moofactory_notification_get_user_enrolment_dates($courseid, $user->id);
        $data->courseenrolstartdate = $courseenrolstartdate == "0" ? "" : date("d/m/Y à H:i", $courseenrolstartdate);
        $data->courseenrolenddate = $courseenrolenddate == "0" ? "" : date("d/m/Y à H:i", $courseenrolenddate);
        $data->courseurl = $CFG->wwwroot . '/course/view.php?id=' . $courseid;
        $data->activityname = "";
        $data->lmsurl = $CFG->wwwroot;
        $data->lmsname = $SITE->fullname;
        if((int)$courseaccesstime < 2){
            $data->interval = $courseaccesstime . " jour";
        }
        else{
            $data->interval = $courseaccesstime . " jours";
        }

        $msgbodyhtml = local_moofactory_notification_replace_variables($variables, $bodyhtml, $data);
        
        $msg = new stdClass();
        $msg->subject = $notif->subject;
        $msg->from = "moofactory";
        $msg->bodytext = "";
        $msg->bodyhtml = $msgbodyhtml;
        $ret = local_moofactory_notification_send_email($user, $msg, $courseid, 'coursesaccess_notification');
    }
    else{
        mtrace("Pas d'envoi : la notification est inexistante.");
    }
}

function local_moofactory_notification_prepare_enrollments_email($user, $courseid, $courseenrollmentsnotifications) {
    global $DB, $CFG, $SITE;

    $notifvalue = (int)local_moofactory_notification_getCustomfield($courseid, 'courseenrollmentsnotification', 'select');
    if(!empty($notifvalue)){
        $courseenrollmentsnotifications = array_values($courseenrollmentsnotifications);
        $notifvalue--;
    }
    else{
        $notifvalue = get_config('local_moofactory_notification', 'coursesenrollmentsnotification');
    }
    $notif = $courseenrollmentsnotifications[$notifvalue];
    $bodyhtml = urldecode($notif->bodyhtml);
    $find = $CFG->wwwroot."/";
    $bodyhtml = str_replace($find,"",$bodyhtml);
    
    $variables = local_moofactory_notification_fetch_variables($bodyhtml);
    
    if(!empty($notif)){
        $data = new stdClass();
        $data->firstname = $user->firstname;
        $data->lastname = $user->lastname;
        $data->username = $user->username;
        $data->usergroup = local_moofactory_notification_get_group($courseid, $user->id);
        $data->eventdate = "";
        $data->eventname = "";
        $course = $DB->get_record('course', array('id' => $courseid), 'fullname,startdate,enddate');
        $data->coursename = $course->fullname;
        $data->coursestartdate = $course->startdate == "0" ? "" : date("d/m/Y à H:i", $course->startdate);
        $data->courseenddate = $course->enddate == "0" ? "" : date("d/m/Y à H:i", $course->enddate);
        list($courseenrolstartdate, $courseenrolenddate) = local_moofactory_notification_get_user_enrolment_dates($courseid, $user->id);
        $data->courseenrolstartdate = $courseenrolstartdate == "0" ? "" : date("d/m/Y à H:i", $courseenrolstartdate);
        $data->courseenrolenddate = $courseenrolenddate == "0" ? "" : date("d/m/Y à H:i", $courseenrolenddate);
        $data->courseurl = $CFG->wwwroot . '/course/view.php?id=' . $courseid;
        $data->activityname = "";
        $data->lmsurl = $CFG->wwwroot;
        $data->lmsname = $SITE->fullname;
        $data->interval = "";
        
        $msgbodyhtml = local_moofactory_notification_replace_variables($variables, $bodyhtml, $data);
        
        $msg = new stdClass();
        $msg->subject = $notif->subject;
        $msg->from = "moofactory";
        $msg->bodytext = "";
        $msg->bodyhtml = $msgbodyhtml;
        $ret = local_moofactory_notification_send_email($user, $msg, $courseid, 'coursesenrollments_notification');
        
        $dateEnvoi = date("d/m/Y H:i:s", time());
        mtrace("\n" . 'Envoyé le : ' . $dateEnvoi . ' à ' . $data->firstname . ' ' . $data->lastname . ' - Cours : ' . $data->coursename . ' - Date d\'inscription : ' . $data->courseenrolstartdate);
        mtrace('Objet du mail : ' . $msg->subject);
}
    else{
        mtrace("Pas d'envoi : la notification est inexistante.");
        throw new moodle_exception("Pas d'envoi : la notification est inexistante.");
    }
}

function local_moofactory_notification_send_email($user, $msg, $courseid, $name) {
    if (!isset($user->email) && empty($user->email)) {
        return false;
    }

    $message = new \core\message\message();
    $message->courseid  = empty($courseid) ? SITEID : $courseid; // This is required in recent versions, use it from 3.2 on https://tracker.moodle.org/browse/MDL-47162
    $message->component = 'local_moofactory_notification';
    $message->name = $name;
    $message->userfrom = core_user::get_noreply_user();
    $message->userto = $user;
    $message->subject = $msg->subject;
    $message->fullmessage = $msg->bodytext;
    $message->fullmessagehtml = $msg->bodyhtml;
    // With FORMAT_HTML, most outputs will use fullmessagehtml, and convert it to plain text if necessary.
    // but some output plugins will behave differently (airnotifier only uses fullmessage)
    $message->fullmessageformat = FORMAT_HTML;
    // If smallmessage is not empty,
    // it will have priority over the 2 other fields, with a hard coded FORMAT_PLAIN.
    // But some output plugins may need it, as jabber currently does.
    $message->smallmessage = '';
    $message->notification = '1';
    if(!empty($courseid)){
        $message->contexturl = new moodle_url('/course/view.php', array('id' => $courseid));
        $message->contexturlname = 'Your course';
    }

    $messageid = message_send($message);
    return $messageid;
}

function local_moofactory_notification_get_delays($type, $courseid, $moduleid = 0){
    $configvars = ['daysbeforeevents1', 'hoursbeforeevents1', 'daysbeforeevents2', 'hoursbeforeevents2', 'daysbeforeevents3', 'hoursbeforeevents3'];

    foreach($configvars as $configvar){
        if($type == 'course'){
            $value = local_moofactory_notification_getCustomfield($courseid, $configvar, 'text');
        }
        else{
            $configvarid = 'module'.$configvar.'_'.$courseid.'_'.$moduleid;
            $value = get_config('local_moofactory_notification', $configvarid);

        }

        $idrappel = (int)substr($configvar, -1);
        switch($idrappel){
            case 1:
                if(strpos($configvar, 'days')!==false){
                    $daysvalue1 = $value;
                }
                if(strpos($configvar, 'hours')!==false){
                    $hoursvalue1 = $value;
                }
                break;
            case 2:
                if(strpos($configvar, 'days')!==false){
                    $daysvalue2 = $value;
                }
                if(strpos($configvar, 'hours')!==false){
                    $hoursvalue2 = $value;
                }
                break;
            case 3:
                if(strpos($configvar, 'days')!==false){
                    $daysvalue3 = $value;
                }
                if(strpos($configvar, 'hours')!==false){
                    $hoursvalue3 = $value;
                }
                break;
        }
    }

    $delays = array();
    if(!($daysvalue1 == "") || !($hoursvalue1 == "")){
        $delay1 = (int)$daysvalue1 * 60 * 60 * 24 + (int)$hoursvalue1 * 60 * 60;
        $delays[] = $delay1;
    }
    if(!($daysvalue2 == "") || !($hoursvalue2 == "")){
        $delay2 = (int)$daysvalue2 * 60 * 60 * 24 + (int)$hoursvalue2 * 60 * 60;
        $delays[] = $delay2;
    }
    if(!($daysvalue3 == "") || !($hoursvalue3 == "")){
        $delay3 = (int)$daysvalue3 * 60 * 60 * 24 + (int)$hoursvalue3 * 60 * 60;
        $delays[] = $delay3;
    }
    return $delays;
}

/**
 *  an array of variable names
 * @param string template containing {{variable}} variables 
 * @ array of variable names parsed from template string
 */
function local_moofactory_notification_fetch_variables($html){
	$matches = array();
	$t = preg_match_all('/{{(.*?)}}/', $html, $matches);
	if(count($matches)>1){
        $uniquearray= array_unique($matches[1]);
		return array_values($uniquearray);
	}else{
		return array();
	}
}
