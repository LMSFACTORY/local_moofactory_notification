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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class managenotif_form extends moodleform {
    public function definition() {
        global $CFG, $DB, $OUTPUT;

        $mform = $this->_form;

        // Select des notifications.
        $records = $DB->get_records('local_mf_notification', null, 'base DESC, name ASC');
        $options[0] = get_string('choose', 'local_moofactory_notification');
        foreach($records as $record) {
            $options[$record->id] = $record->name;
        }
        $select = $mform->addElement('select', 'selectnotifications', get_string('notifications', 'local_moofactory_notification'), $options, array('onchange' => 'javascript:document.getElementById(\'notificationsform\').submit();'));

        $addurl = new moodle_url($CFG->wwwroot . '/local/moofactory_notification/addnotif.php');
        $html = '<div class="form-group row"><div class="col-md-3">&nbsp;</div>';

        if(!empty($this->_customdata)){
            $select->setSelected($this->_customdata['id']);

            $record = $DB->get_record('local_mf_notification', array('id' => $this->_customdata['id']), 'base, type, name, subject, bodyhtml');

            $duplicateurl = new moodle_url($CFG->wwwroot . '/local/moofactory_notification/duplicatenotif.php', array('id' => $this->_customdata['id']));
            $deleteurl = new moodle_url($CFG->wwwroot . '/local/moofactory_notification/deletenotif.php', array('id' => $this->_customdata['id']));
            if(empty($record->base)){
                $html .= '<div class="col-md-2"><a href="'.$duplicateurl.'">';
                $html .= $OUTPUT->pix_icon('t/copy', '');
                $html .= get_string('duplicate', 'local_moofactory_notification').'</a></div>';
                $html .= '<div class="col-md-2"><a href="'.$deleteurl.'">';
                $html .= $OUTPUT->pix_icon('t/delete', '');
                $html .= get_string('delete', 'local_moofactory_notification').'</a></div>';
                $html .= '<div class="col-md-5"><a href="'.$addurl.'">';
                $html .= $OUTPUT->pix_icon('i/addblock', '');
                $html .= get_string('add', 'local_moofactory_notification').'</a></div>';
            }
            else{
                $html .= '<div class="col-md-2"><a href="'.$duplicateurl.'">';
                $html .= $OUTPUT->pix_icon('t/copy', '');
                $html .= get_string('duplicate', 'local_moofactory_notification').'</a></div>';
                $html .= '<div class="col-md-7"><a href="'.$addurl.'">';
                $html .= $OUTPUT->pix_icon('i/addblock', '');
                $html .= get_string('add', 'local_moofactory_notification').'</a></div>';
            }
        }
        else{
            $html .= '<div class="col-md-7"><a href="'.$addurl.'">';
            $html .= $OUTPUT->pix_icon('i/addblock', '');
            $html .= get_string('add', 'local_moofactory_notification').'</a></div>';
        }
        $html .= '</div>';
        
        $mform->addElement('html', $html);

        $mform->addElement('text', 'notificationname', get_string('name', 'local_moofactory_notification'), 'size="50"');
        $mform->setType('notificationname', PARAM_RAW);
        $mform->addRule('notificationname', get_string('required', 'local_moofactory_notification'), 'required');
        
        if(!is_null($record->base) && $record->base != 1){
            $typeoptions['siteevent'] = get_string('siteevents', 'local_moofactory_notification');
            $typeoptions['courseevent'] = get_string('coursesevents', 'local_moofactory_notification');
            $typeoptions['courseenroll'] = get_string('coursesenrollments', 'local_moofactory_notification');
            $typeoptions['courseaccess'] = get_string('coursesaccess', 'local_moofactory_notification');
            $select = $mform->addElement('select', 'notificationtype', get_string('type', 'local_moofactory_notification'), $typeoptions);
        }
        else{
            $mform->addElement('hidden', 'notificationtype');
            $mform->setType('notificationtype', PARAM_ALPHA);
            $mform->setDefault('notificationtype', $record->type);    
        }
        
        $mform->addElement('text', 'notificationsubject', get_string('subject', 'local_moofactory_notification'), 'size="50"');
        $mform->setType('notificationsubject', PARAM_RAW);
        $mform->addRule('notificationsubject', get_string('required', 'local_moofactory_notification'), 'required');

        $mform->addElement('editor', 'notificationbodyhtml', get_string('bodyhtml', 'local_moofactory_notification'), 'wrap="virtual" rows="20" cols="80"');
        $mform->setType('notificationbodyhtml', PARAM_CLEANHTML);

        $mform->addElement('hidden', 'typeinitial');
        $mform->setType('typeinitial', PARAM_ALPHA);
        $mform->setDefault('typeinitial', $record->type);

        $html = '<div class="form-group row"><div class="col-md-3" style="margin-bottom: .5rem;">'.get_string('params', 'local_moofactory_notification').'</div>';
        $html .= '<div class="col-md-9">';
        $html .= '<table class="table table-sm merge-fields">';
        $html .= '  <tr>';
        $html .= '    <td width="250">{{firstname}}</td>';
        $html .= '    <td>'.get_string('params_firstname', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{lastname}}</td>';
        $html .= '    <td>'.get_string('params_lastname', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{username}}</td>';
        $html .= '    <td>'.get_string('params_username', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{usergroup}}</td>';
        $html .= '    <td>'.get_string('params_usergroup', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{eventdate}}</td>';
        $html .= '    <td>'.get_string('params_eventdate', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{eventname}}</td>';
        $html .= '    <td>'.get_string('params_eventname', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{coursename}}</td>';
        $html .= '    <td>'.get_string('params_coursename', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{coursestartdate}}</td>';
        $html .= '    <td>'.get_string('params_coursestartdate', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{courseenddate}}</td>';
        $html .= '    <td>'.get_string('params_courseenddate', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{courseenrolstartdate}}</td>';
        $html .= '    <td>'.get_string('params_courseenrolstartdate', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{courseenrolenddate}}</td>';
        $html .= '    <td>'.get_string('params_courseenrolenddate', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{courseurl}}</td>';
        $html .= '    <td>'.get_string('params_courseurl', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{activityname}}</td>';
        $html .= '    <td>'.get_string('params_activityname', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{lmsurl}}</td>';
        $html .= '    <td>'.get_string('params_lmsurl', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{lmsname}}</td>';
        $html .= '    <td>'.get_string('params_lmsname', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '  <tr>';
        $html .= '    <td>{{interval}}</td>';
        $html .= '    <td>'.get_string('params_interval', 'local_moofactory_notification').'</td>';
        $html .= '  </tr>';
        $html .= '</table>';
        $html .= '</div></div>';


    
        $mform->addElement('html', $html);

        if(!empty($this->_customdata)){
            $mform->setDefault('notificationname', $record->name);
            $select->setSelected($record->type);
            $mform->setDefault('notificationsubject', $record->subject);
            //$mform->setDefault('notificationbodyhtml', $record->bodyhtml);
            $mform->setDefault('notificationbodyhtml', array('text' => $record->bodyhtml,'format' => FORMAT_HTML));
        }
        else{
            $mform->setDefault('notificationname', ' ');
            $mform->setDefault('notificationsubject', ' ');
        }

        $mform->disable_form_change_checker();
        
        $this->add_action_buttons();
    }


}