<?php
/*
 * LibreNMS
 *
 * Copyright (c) 2016 Søren Friis Rosiak <sorenrosiak@gmail.com>
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 */

$temp = snmp_get($device, "upsAdvBatteryReplaceIndicator.0", "-Ovqe", "PowerNet-MIB");
$cur_oid = '.1.3.6.1.4.1.318.1.1.1.2.2.4.0';
$index = '0';

if (is_numeric($temp)) {
    //Create State Index
    $state_name = 'upsAdvBatteryReplaceIndicator';
    $states = [
        ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'noBatteryNeedsReplacing'],
        ['value' => 2, 'generic' => 2, 'graph' => 0, 'descr' => 'batteryNeedsReplacing'],
    ];
    create_state_index($state_name, $states);

    $descr = 'UPS Battery Replacement Status';
    //Discover Sensors
    discover_sensor($valid['sensor'], 'state', $device, $cur_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $temp, 'snmp', $index);

    //Create Sensor To State Index
    create_sensor_to_state_index($device, $state_name, $index);
}

$cooling_status = snmpwalk_cache_oid($device, 'coolingUnitStatusDiscreteEntry', [], 'PowerNet-MIB');
foreach ($cooling_status as $index => $data) {
    $cur_oid = '.1.3.6.1.4.1.318.1.1.27.1.4.2.2.1.4.' . $index;
    $state_name = $data['coolingUnitStatusDiscreteDescription'];
    $state_index_id = create_state_index($state_name);

    if ($state_index_id !== null) {
        $tmp_states = explode(',', $data['coolingUnitStatusDiscreteIntegerReferenceKey']);
        $states = [];
        foreach ($tmp_states as $k => $ref) {
            preg_match('/([\w]+)\\(([\d]+)\\)/', $ref, $matches);
            $nagios_state = get_nagios_state($matches[1]);
            $states[] = [$state_index_id, $matches[1], 0, $matches[2], $nagios_state];
        }
        foreach ($states as $value) {
            $insert = [
                'state_index_id' => $value[0],
                'state_descr' => $value[1],
                'state_draw_graph' => $value[2],
                'state_value' => $value[3],
                'state_generic_value' => $value[4]
            ];
            dbInsert($insert, 'state_translations');
        }
    }
    discover_sensor($valid['sensor'], 'state', $device, $cur_oid, $cur_oid, 'apc', $state_name, 1, 1, null, null, null, null, $data['coolingUnitStatusDiscreteValueAsInteger']);
    create_sensor_to_state_index($device, $state_name, $index);
}

unset($cooling_status);

$cooling_unit = snmpwalk_cache_oid($device, 'coolingUnitExtendedDiscreteEntry', [], 'PowerNet-MIB');
foreach ($cooling_unit as $index => $data) {
    $cur_oid = '.1.3.6.1.4.1.318.1.1.27.1.6.2.2.1.4.' . $index;
    $state_name = $data['coolingUnitExtendedDiscreteDescription'];
    $state_index_id = create_state_index($state_name);

    if ($state_index_id !== null) {
        $tmp_states = explode(',', $data['coolingUnitExtendedDiscreteIntegerReferenceKey']);
        $states = [];
        foreach ($tmp_states as $k => $ref) {
            preg_match('/([\w]+)\\(([\d]+)\\)/', $ref, $matches);
            $nagios_state = get_nagios_state($matches[1]);
            $states[] = [$state_index_id, $matches[1], 0, $matches[2], $nagios_state];
        }
        foreach ($states as $value) {
            $insert = [
                'state_index_id' => $value[0],
                'state_descr' => $value[1],
                'state_draw_graph' => $value[2],
                'state_value' => $value[3],
                'state_generic_value' => $value[4]
            ];
            dbInsert($insert, 'state_translations'); // FIXME - Last dbInsert in sensor states
        }
    }
    discover_sensor($valid['sensor'], 'state', $device, $cur_oid, $cur_oid, 'apc', $state_name, 1, 1, null, null, null, null, $data['coolingUnitExtendedDiscreteValueAsInteger']);
    create_sensor_to_state_index($device, $state_name, $index);
}

unset($cooling_unit);

$relays = snmpwalk_cache_oid($device, 'emsOutputRelayControlEntry', [], 'PowerNet-MIB');
foreach ($relays as $index => $data) {
    $cur_oid = '.1.3.6.1.4.1.318.1.1.10.3.2.1.1.3.' . $index;
    $state_name = $data['emsOutputRelayControlOutputRelayName'];
    $states = [
        ['value' => 1, 'generic' => 2, 'graph' => 0, 'descr' => 'immediateCloseEMS'],
        ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'immediateOpenEMS'],
    ];
    create_state_index($state_name, $states);

    $current = apc_relay_state($data['emsOutputRelayControlOutputRelayCommand']);
    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $cur_oid, $cur_oid, $state_name, $state_name, 1, 1, null, null, null, null, $current);
        create_sensor_to_state_index($device, $state_name, $cur_oid);
    }
}
unset(
    $relays,
    $index,
    $data
);

$switched = snmpwalk_cache_oid($device, 'emsOutletControlEntry', [], 'PowerNet-MIB');
foreach ($switched as $index => $data) {
    $cur_oid = '.1.3.6.1.4.1.318.1.1.10.3.3.1.1.3.' . $index;
    $state_name = $data['emsOutletControlOutletName'];
    $states = [
        ['value' => 1, 'generic' => 2, 'graph' => 0, 'descr' => 'immediateOnEMS'],
        ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'immediateOffEMS'],
    ];
    create_state_index($state_name, $states);

    $current = apc_relay_state($data['emsOutletControlOutletCommand']);
    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $cur_oid, $cur_oid, $state_name, $state_name, 1, 1, null, null, null, null, $current);
        create_sensor_to_state_index($device, $state_name, $cur_oid);
    }
}
unset(
    $switched,
    $index,
    $data
);

foreach ($pre_cache['mem_sensors_status'] as $index => $data) {
    if ($data['memSensorsCommStatus']) {
        $cur_oid        = '.1.3.6.1.4.1.318.1.1.10.4.2.3.1.7.' . $index;
        $state_name     = 'memSensorsCommStatus';
        $states = [
            ['value' => 1, 'generic' => 1, 'graph' => 0, 'descr' => 'notInstalled'],
            ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'commsOK'],
            ['value' => 3, 'generic' => 2, 'graph' => 0, 'descr' => 'commsLost'],
        ];
        create_state_index($state_name, $states);

        $current = $data['memSensorsCommStatus'];
    }
    $descr      = $data['memSensorsStatusSensorName'] . ' - ' . $data['memSensorsStatusSensorLocation'];
    $divisor    = 1;
    $multiplier = 1;
    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $cur_oid, $state_name . '.' . $index, $state_name, $state_name, 1, 1, null, null, null, null, $current);
        create_sensor_to_state_index($device, $state_name, $state_name . '.' . $index);
    }

    if ($data['memSensorsAlarmStatus']) {
        $cur_oid        = '.1.3.6.1.4.1.318.1.1.10.4.2.3.1.8.' . $index;
        $state_name     = 'memSensorsAlarmStatus';
        $states = [
            ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'memNormal'],
            ['value' => 2, 'generic' => 1, 'graph' => 0, 'descr' => 'memWarning'],
            ['value' => 3, 'generic' => 2, 'graph' => 0, 'descr' => 'memCritical'],
        ];
        create_state_index($state_name, $states);

        $current = $data['memSensorsAlarmStatus'];
    }
    $descr      = $data['memSensorsStatusSensorName'] . ' - ' . $data['memSensorsStatusSensorLocation'];
    $divisor    = 1;
    $multiplier = 1;
    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $cur_oid, $state_name . '.' . $index, $state_name, $state_name, 1, 1, null, null, null, null, $current);
        create_sensor_to_state_index($device, $state_name, $state_name . '.' . $index);
    }
}
