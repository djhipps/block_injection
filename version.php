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
 * @package    block
 * @subpackage jsinjection
 * @copyright  Moodle
 * @author     Damian Hippisley d.j.hippisley@qmul.ac.uk
 * @date       29/05/19
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @filename   version.php
 * Note: alter the namespace if included in a subfolder for autoloading
 *
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_jsinjection';
$plugin->version = 2019052800;  // YYYYMMDDHH (year, month, day, 24-hr time)
$plugin->requires = 2017111302; // YYYYMMDDHH (This is the release version for Moodle 2.0)
