<?php
/**
 * local_moofactory_notification plugin
 *
 * @package     local_moofactory_notification
 * @copyright   2020 Patrick ROCHET <patrick.r@lmsfactory.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

class info_moofactory_notification extends info_module {

    public function set_modinfo($courseid, $userid) {
        if (!$this->modinfo) {
            $modinfo = get_fast_modinfo($courseid, $userid);
        }
        $this->modinfo = $modinfo;
    }
}
