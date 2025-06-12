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
 * Class handles conditional availability information for a section.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Class handles conditional availability information for a section.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class info_section_moofactory_notification extends info_section {

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
