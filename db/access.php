<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 03/09/18
 * Time: 11:41
 */

$capabilities = array(

    /**
     * Note - not using clone permissions moodle/my:manageblocks as this
     * would add Teachers to the list. Don't want that. Course Admins will have
     * to be added manually.
     */
    'block/jsinjection:addinstance' => array(
        'riskbitmask' => RISK_DATALOSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
    ),
    'block/jsinjection:editsettings' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
    ),

);