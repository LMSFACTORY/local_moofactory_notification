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
 * Class that holds a tree of availability conditions.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that holds a tree of availability conditions.
 *
 * The structure of this tree in JSON input data is:
 *
 * { op:'&', c:[] }
 *
 * where 'op' is one of the OP_xx constants and 'c' is an array of children.
 *
 * At the root level one of the following additional values must be included:
 *
 * op '|' or '!&'
 *   show:true
 *   Boolean value controlling whether a failed match causes the item to
 *   display to students with information, or be completely hidden.
 * op '&' or '!|'
 *   showc:[]
 *   Array of same length as c with booleans corresponding to each child; you
 *   can make it be hidden or shown depending on which one they fail. (Anything
 *   with false takes precedence.)
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tree_moofactory_notification extends tree {
    /**
     * Decodes availability structure.
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
     * @see decode_availability
     * @param \stdClass $structure Structure (decoded from JSON)
     * @param boolean $lax If true, throw exceptions only for invalid structure
     * @param boolean $root If true, this is the root tree
     * @return tree Availability tree
     * @throws \coding_exception If data is not valid structure
     */
    public function __construct($structure, $lax = false, $root = true) {
        $this->root = $root;

        // Check object.
        if (!is_object($structure)) {
            throw new \coding_exception('Invalid availability structure (not object)');
        }

        // Extract operator.
        if (!isset($structure->op)) {
            throw new \coding_exception('Invalid availability structure (missing ->op)');
        }
        $this->op = $structure->op;
        if (!in_array($this->op, array(self::OP_AND, self::OP_OR,
                self::OP_NOT_AND, self::OP_NOT_OR), true)) {
            throw new \coding_exception('Invalid availability structure (unknown ->op)');
        }

        // For root tree, get show options.
        $this->show = true;
        $this->showchildren = null;
        if ($root) {
            if ($this->op === self::OP_AND || $this->op === self::OP_NOT_OR) {
                // Per-child show options.
                if (!isset($structure->showc)) {
                    throw new \coding_exception(
                            'Invalid availability structure (missing ->showc)');
                }
                if (!is_array($structure->showc)) {
                    throw new \coding_exception(
                            'Invalid availability structure (->showc not array)');
                }
                foreach ($structure->showc as $value) {
                    if (!is_bool($value)) {
                        throw new \coding_exception(
                                'Invalid availability structure (->showc value not bool)');
                    }
                }
                // Set it empty now - add corresponding ones later.
                $this->showchildren = array();
            } else {
                // Entire tree show option. (Note: This is because when you use
                // OR mode, say you have A OR B, the user does not meet conditions
                // for either A or B. A is set to 'show' and B is set to 'hide'.
                // But they don't have either, so how do we know which one to do?
                // There might as well be only one value.)
                if (!isset($structure->show)) {
                    throw new \coding_exception(
                            'Invalid availability structure (missing ->show)');
                }
                if (!is_bool($structure->show)) {
                    throw new \coding_exception(
                            'Invalid availability structure (->show not bool)');
                }
                $this->show = $structure->show;
            }
        }

        // Get list of enabled plugins.
        $pluginmanager = \core_plugin_manager::instance();
        $enabled = $pluginmanager->get_enabled_plugins('availability');

        // For unit tests, also allow the mock plugin type (even though it
        // isn't configured in the code as a proper plugin).
        if (PHPUNIT_TEST) {
            $enabled['mock'] = true;
        }

        // Get children.
        if (!isset($structure->c)) {
            throw new \coding_exception('Invalid availability structure (missing ->c)');
        }
        if (!is_array($structure->c)) {
            throw new \coding_exception('Invalid availability structure (->c not array)');
        }
        if (is_array($this->showchildren) && count($structure->showc) != count($structure->c)) {
            throw new \coding_exception('Invalid availability structure (->c, ->showc mismatch)');
        }
        $this->children = array();
        foreach ($structure->c as $index => $child) {
            if (!is_object($child)) {
                throw new \coding_exception('Invalid availability structure (child not object)');
            }

            // First see if it's a condition. These have a defined type.
            if (isset($child->type)) {
                // Look for a plugin of this type.
                $classname = '\availability_' . $child->type . '\condition';
                if (!array_key_exists($child->type, $enabled)) {
                    if ($lax) {
                        // On load of existing settings, ignore if class
                        // doesn't exist.
                        continue;
                    } else {
                        throw new \coding_exception('Unknown condition type: ' . $child->type);
                    }
                }
                $this->children[] = new $classname($child);
            } else {
                // Not a condition. Must be a subtree.
                $this->children[] = new tree_moofactory_notification($child, $lax, false);
            }
            if (!is_null($this->showchildren)) {
                $this->showchildren[] = $structure->showc[$index];
            }
        }
    }

    public function check_isavailable($not, info $info, $grabthelot, $userid, $modulecheckdateavailabilityvalue, $modulecheckgroupavailabilityvalue) {
        // We drop date and group elements from children.
        $todrop = array();
        foreach ($this->children as $index => $child) {
            $type = preg_replace('~^availability_(.*?)\\\\condition$~', '$1', get_class($child));
            if($type == "date" && !empty($modulecheckdateavailabilityvalue)){
                $todrop[] = $this->children[$index];
            }
            elseif($type == "group" && !empty($modulecheckgroupavailabilityvalue)){
                $todrop[] = $this->children[$index];
            }
        }
        $this->children = array_diff($this->children, $todrop);
        
        // If there are no children in this group, we just treat it as available.
        if (!$this->children) {
            return new result(true);
        }

        // Get logic flags from operator.
        list($innernot, $andoperator) = $this->get_logic_flags($not);

        if ($andoperator) {
            $allow = true;
        } else {
            $allow = false;
        }
        $failedchildren = array();
        $totallyhide = !$this->show;
        foreach ($this->children as $index => $child) {
            // Check available and get info.
            $childclass = get_class($child);
            if($childclass == "core_availability\\tree_moofactory_notification"){
                $childresult = $child->check_isavailable(
                        $innernot, $info, $grabthelot, $userid, $modulecheckdateavailabilityvalue, $modulecheckgroupavailabilityvalue);
            }
            else{
                $childresult = $child->check_available(
                        $innernot, $info, $grabthelot, $userid);
            }
            $childyes = $childresult->is_available();
            if (!$childyes) {
                $failedchildren[] = $childresult;
                if (!is_null($this->showchildren) && !$this->showchildren[$index]) {
                    $totallyhide = true;
                }
            }

            if ($andoperator && !$childyes) {
                $allow = false;
                // Do not exit loop at this point, as we will still include other info.
            } else if (!$andoperator && $childyes) {
                // Exit loop since we are going to allow access (from this tree at least).
                $allow = true;
                break;
            }
        }

        if ($allow) {
            return new result(true);
        } else if ($totallyhide) {
            return new result(false);
        } else {
            return new result(false, $this, $failedchildren);
        }
    }
}
