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

class info_module_moofactory_notification extends info_module {

    public function set_modinfo($courseid, $userid) {
        if (!$this->modinfo) {
            $modinfo = get_fast_modinfo($courseid, $userid);
        }
        $this->modinfo = $modinfo;
    }

    /**
     * Decodes availability data from JSON format.
     *
     * This function also validates the retrieved data as follows:
     * 1. Data that does not meet the API-defined structure causes a
     *    coding_exception (this should be impossible unless there is
     *    a system bug or somebody manually hacks the database).
     * 2. Data that meets the structure but cannot be implemented (e.g.
     *    reference to missing plugin or to module that doesn't exist) is
     *    either silently discarded (if $lax is true) or causes a
     *    coding_exception (if $lax is false).
     *
     * @param string $availability Availability string in JSON format
     * @param boolean $lax If true, throw exceptions only for invalid structure
     * @return tree Availability tree
     * @throws \coding_exception If data is not valid JSON format
     */
    protected function decode_availability($availability, $lax) {
        // Decode JSON data.
        $structure = json_decode($availability);
        if (is_null($structure)) {
            throw new \coding_exception('Invalid availability text', $availability);
        }

        // Recursively decode tree.
        return new tree_moofactory_notification($structure, $lax);
    }
}
