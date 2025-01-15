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

namespace local_moofactory_notification\task;

defined('MOODLE_INTERNAL') || die();

class send_coursesenroll_notification extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('sendcourseenrollmentsnotifications', 'local_moofactory_notification');
    }
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/local/moofactory_notification/lib.php');

        local_moofactory_notification_send_coursesenroll_notification();
    }
}