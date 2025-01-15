<?php

namespace local_moofactory_notification\task;

defined('MOODLE_INTERNAL') || die();

class send_modulesaccess_notification extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('sendmoduleaccessnotifications', 'local_moofactory_notification');
    }
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/local/moofactory_notification/lib.php');

        local_moofactory_notification_send_modulesaccess_notification();
    }
}