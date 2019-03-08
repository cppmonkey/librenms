<?php
/**
 * tryin.inc.php
 *
 * LibreNMS state sensor discovery module for Tryin EDFA's
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    LibreNMS
 * @link       http://librenms.org
 * @copyright  2019 Paul Parsons
 * @author     Paul Parsons <paul@cppmonkey.net>
 */

$state_name = 'vCardState';
$states = [
    ['value' => 0, 'generic' => 1, 'graph' => 0, 'descr' => 'Off'],
    ['value' => 1, 'generic' => 0, 'graph' => 1, 'descr' => 'On'],
];
create_state_index($state_name, $states);

$state_name = 'vWorkMode';
$states = [
    ['value' => 1, 'generic' => 3, 'graph' => 0, 'descr' => 'ACC'],
    ['value' => 2, 'generic' => 3, 'graph' => 1, 'descr' => 'APC'],
    ['value' => 3, 'generic' => 0, 'graph' => 2, 'descr' => 'AGC (Automatic Gain Control)'],
];
create_state_index($state_name, $states);

$state_name = 'vstatus';
$states = [
    ['value' => 0, 'generic' => 0, 'graph' => 0, 'descr' => 'OK'],
    ['value' => 1, 'generic' => 1, 'graph' => 1, 'descr' => 'MUTE'],
    ['value' => 2, 'generic' => 2, 'graph' => 2, 'descr' => 'ERROR'],
];
create_state_index($state_name, $states);

$state_name = 'vPUMPSwitch';
$states = [
    ['value' => 0, 'generic' => 0, 'graph' => 1, 'descr' => 'On'],
    ['value' => 1, 'generic' => 1, 'graph' => 0, 'descr' => 'Off'],
];
create_state_index($state_name, $states);

$state_name = 'NormalAlarm';
$states = [
    ['value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'Alarm'],
    ['value' => 1, 'generic' => 0, 'graph' => 1, 'descr' => 'Normal'],
];
create_state_index($state_name, $states);

$state_name = 'c1WorkModeSave';
$states = [
    ['value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'noSave'],
    ['value' => 1, 'generic' => 0, 'graph' => 1, 'descr' => 'Save'],
];
create_state_index($state_name, $states);

$state_name = 'c1Channel';
$states = [
    ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'Main'],
    ['value' => 2, 'generic' => 0, 'graph' => 1, 'descr' => 'Sub'],
];
create_state_index($state_name, $states);

$state_name = 'ManualAuto';
$states = [
    ['value' => 0, 'generic' => 0, 'graph' => 0, 'descr' => 'Manual'],
    ['value' => 1, 'generic' => 0, 'graph' => 1, 'descr' => 'Auto'],
];
create_state_index($state_name, $states);

$state_name = 'SfpWorkMode';
$states = [
    ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'Normal'],
    ['value' => 2, 'generic' => 1, 'graph' => 1, 'descr' => 'FC'],
    ['value' => 3, 'generic' => 1, 'graph' => 2, 'descr' => 'Loopback'],
];
create_state_index($state_name, $states);

$state_name = 'SfpPowerControl';
$states = [
    ['value' => 0, 'generic' => 1, 'graph' => 0, 'descr' => 'Off'],
    ['value' => 1, 'generic' => 1, 'graph' => 1, 'descr' => 'On'],
    ['value' => 2, 'generic' => 0, 'graph' => 1, 'descr' => 'Auto'],
];
create_state_index($state_name, $states);

foreach (range(1, 16) as $card) {
    $type = '1';
    $mib_file = sprintf('OAP-C%d-EDFA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $group = sprintf('%s Card %d', $deviceType, $card);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vCardState';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $current = snmp_get($device, 'vWorkMode.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.8.0', $card, $type);
    $descr = 'Work Mode';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vWorkMode';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $current = snmp_get($device, 'vPUMPSwitch.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.9.0', $card, $type);
    $descr = 'PUMP Switch';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vPUMPSwitch';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $state_name = 'NormalAlarm';
    $current = snmp_get($device, 'vInputPowerState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.16.0', $card, $type);
    $descr = 'Input Power';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $current = snmp_get($device, 'vOutputPowerState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.17.0', $card, $type);
    $descr = 'Output Power';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $current = snmp_get($device, 'vModuleTemperatureState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.18.0', $card, $type);
    $descr = 'Module Temp';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $current = snmp_get($device, 'vPUMPTemperatureState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.19.0', $card, $type);
    $descr = 'Pump Temp';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $current = snmp_get($device, 'vPUMPCurrentState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.20.0', $card, $type);
    $descr = 'Pump Current';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '2';
    $mib_file = sprintf('OAP-C%d-OEO', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $group = sprintf('%s Card %d', $deviceType, $card);

    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);

        // Descover SPF Optics
        $channel_oid = 11;
        foreach (range('A', 'D') as $channel) {
            foreach (range(1, 2) as $optic) {
                $group = sprintf('%s Card %d - SFP %s%d', $deviceType, $card, $channel, $optic);
                $current = snmp_get($device, sprintf('vSFP%s%dState.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.1.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d State', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $state_name = 'vCardState';
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }

                $current = snmp_get($device, sprintf('vSFP%s%dWorkMode.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.2.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Work Mode', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $state_name = 'SfpWorkMode';
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }

                $current = snmp_get($device, sprintf('vSFP%s%dTxPowerControl.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.3.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Tx Power Control', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $state_name = 'SfpPowerControl';
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }

                $current = snmp_get($device, sprintf('vSFP%s%dTxPowerAlarm.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.10.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Tx Power Alarm', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $state_name = 'NormalAlarm';
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }

                $current = snmp_get($device, sprintf('vSFP%s%dRxPowerAlarm.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.11.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Rx Power Alarm', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $state_name = 'NormalAlarm';
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }

                $current = snmp_get($device, sprintf('vSFP%s%dModeTemperatureAlarm.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.11.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Mode Temperature Alarm', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $state_name = 'NormalAlarm';
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }
                $channel_oid++;
            }
        }
    }

    $type = '3';
    $mib_file = sprintf('OAP-C%d-OLP', $card); // glsun-OXC or tryin-OLP
    $deviceType = snmp_get($device, 'c1DeviceType.0', '-Ovq', $mib_file);
    $group = sprintf('%s Card %d', $deviceType, $card);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $descr = 'State';
        $index = substr($num_oid, 24);
        $state_name = 'vCardState';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.8.0', $card, $type);
    $current = snmp_get($device, 'c1WorkMode.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $descr = 'Work Mode';
        $index = substr($num_oid, 24);
        $state_name = 'ManualAuto';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.9.0', $card, $type);
    $current = snmp_get($device, 'c1Channel.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $descr = 'Channel';
        $index = substr($num_oid, 24);
        $state_name = 'c1Channel';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.10.0', $card, $type);
    $current = snmp_get($device, 'c1workmodesave.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $descr = 'Work Mode Save';
        $index = substr($num_oid, 24);
        $state_name = 'ManualAuto';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.22.0', $card, $type);
    $current = snmp_get($device, 'c1BackMode.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $descr = 'Back Mode';
        $index = substr($num_oid, 24);
        $state_name = 'ManualAuto';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '4';
    /*
    $mib_file = sprintf('OAP-C%d-EDFA', $card); //FIXME Card Type?
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    */
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.2.0', $card, $type);
    $deviceType = snmp_get($device, $num_oid, '-OQv');

    $group = sprintf('%s Card %d', $deviceType, $card);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $current = snmp_get($device, $num_oid, '-Ovqet');

    if (is_numeric($current)) {
        $descr = 'State';
        $index = substr($num_oid, 24);
        $state_name = 'vCardState';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.2.0', $card, $type);
    $current = snmp_get($device, $num_oid, '-Ovqet');

    if (is_numeric($current)) {
        $descr = 'Type';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.3.0', $card, $type);
    $current = snmp_get($device, $num_oid, '-Ovqet');

    if (is_numeric($current)) {
        $descr = 'Descr';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '5';
    /*
    $mib_file = sprintf('OAP-C%d-EDFA', $card); //FIXME Card Type?
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    */
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.2.0', $card, $type);
    $deviceType = snmp_get($device, $num_oid, '-OQv');
    $group = sprintf('%s Card %d', $deviceType, $card);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $current = snmp_get($device, $num_oid, '-Ovqet');

    if (is_numeric($current)) {
        $descr = 'State';
        $index = substr($num_oid, 24);
        $state_name = 'vCardState';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.2.0', $card, $type);
    $current = snmp_get($device, $num_oid, '-Ovqet');

    if (is_numeric($current)) {
        $descr = 'Type';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.3.0', $card, $type);
    $current = snmp_get($device, $num_oid, '-Ovqet');

    if (is_numeric($current)) {
        $descr = 'Descr';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '6';
    $mib_file = sprintf('OAP-C%d-VOA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-OQv', $mib_file);
    $group = sprintf('%s Card %d', $deviceType, $card);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vCardState';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '7';
    $mib_file = sprintf('OAP-C%d-DEDFA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-OQv', $mib_file);
    $group = sprintf('%s Card %d', $deviceType, $card);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vCardState';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '8';
    $mib_file = sprintf('OAP-C%d-OSW', $card);
    $deviceType = snmp_get($device, 'c1DeviceType.0', '-Ovq', $mib_file);
    $group = sprintf('%s Card %d', $deviceType, $card);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vCardState';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '9';
    $mib_file = sprintf('OAP-C%d-YEDFA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $group = sprintf('%s Card %d', $deviceType, $card);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vCardState';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $current = snmp_get($device, 'vWorkMode.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.8.0', $card, $type);
    $descr = 'Status';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vstatus';
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, 1, 1, null, null, null, null, $current, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
}
