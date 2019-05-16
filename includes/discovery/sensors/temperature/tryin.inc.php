<?php
/**
 * tryin.inc.php
 *
 * LibreNMS sensors temp discovery module for Tryin EDFA's
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

foreach (range(1, 16) as $card) {
    $type = '1';
    $mib_file = sprintf('OAP-C%d-EDFA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'vDeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'vSoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'vHardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $current = snmp_get($device, 'vModuleTemperature.0', '-Ovqe', $mib_file);
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.22.0', $card, $type);
        $descr = 'Module Temperature';
        $index = substr($num_oid, 24);

        if (is_numeric($current)) {
            $state_name = "tryin_module_temp";
            $low_limit = snmp_get($device, 'vModuleTemperatureLowerLimit.0', '-Ovqe', $mib_file);
            $high_Limit = snmp_get($device, 'vModuleTemperatureUpperLimit.0', '-Ovqe', $mib_file);

            discover_sensor($valid['sensor'], 'temperature', $device, $num_oid, $index, $state_name, $descr, 100, 1, $low_limit/100, null, null, $high_limit/100, $current/100, 'snmp', null, null, null, $group);
        }

        $current = snmp_get($device, 'vPUMPTemperature.0', '-Ovqe', $mib_file);
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.25.0', $card, $type);
        $descr = 'Pump Temperature';
        $index = substr($num_oid, 24);

        if (is_numeric($current)) {
            $state_name = "tryin_pump_temp";
            $low_limit = snmp_get($device, 'vPUMPTemperatureLowerLimit.0', '-Ovqe', $mib_file);
            $high_Limit = snmp_get($device, 'vPUMPTemperatureUpperLimit.0', '-Ovqe', $mib_file);

            discover_sensor($valid['sensor'], 'temperature', $device, $num_oid, $index, $state_name, $descr, 100, 1, $low_limit/100, null, null, $high_limit/100, $current/100, 'snmp', null, null, null, $group);
        }
    }

    $type = '2';
    $mib_file = sprintf('OAP-C%d-OEO', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'vDeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'vSoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'vHardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);

    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        // Descover SPF Optics
        $channel_oid = 11;
        foreach (range('A', 'D') as $channel) {
            foreach (range(1, 2) as $optic) {
                $group = sprintf('Card %d %s SFP Channel %s', $card, $deviceType, $channel);
                $current = snmp_get($device, sprintf('vSFP%s%dModeTemperature.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.9.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Temperature', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $state_name = sprintf('vSFP%s%dModeTemperature.0', $channel, $optic);
                    discover_sensor($valid['sensor'], 'temperature', $device, $num_oid, $index, $state_name, $descr, 100, 1, null, null, null, null, $current/100, 'snmp', null, null, null, $group);
                }
                $channel_oid++;
            }
        }
    }

    $type = '3';
    $mib_file = sprintf('OAP-C%d-OLP', $card); // glsun-OXC or tryin-OLP
    $deviceType = snmp_get($device, 'c1DeviceType.0', '-Ovq', $mib_file);
    $deviceDescription = snmp_get($device, 'c1DeviceDescription.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'c1DeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'c1SoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'c1HardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        
    }

    $type = '4';
    /*
    $mib_file = sprintf('OAP-C%d-EDFA', $card); //FIXME Card Type?
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    */
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.2.0', $card, $type);
    $deviceType = snmp_get($device, $num_oid, '-Ovqa');
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.3.0', $card, $type);
    $deviceDescription = snmp_get($device, $num_oid, '-Ovqa', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.4.0', $card, $type);
    $deviceSV = snmp_get($device, $num_oid, '-Ovqa', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.5.0', $card, $type);
    $deviceHV = snmp_get($device, $num_oid, '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $current = snmp_get($device, $num_oid, '-Ovqet');

    if (is_numeric($current)) {
        
    }

    $type = '5';
    /*
    $mib_file = sprintf('OAP-C%d-EDFA', $card); //FIXME Card Type?
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    */
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.2.0', $card, $type);
    $deviceType = snmp_get($device, $num_oid, '-OQva');
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.3.0', $card, $type);
    $deviceDescription = snmp_get($device, 'c1DeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'c1SoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'c1HardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $current = snmp_get($device, $num_oid, '-Ovqet');

    if (is_numeric($current)) {
        
    }

    $type = '6';
    $mib_file = sprintf('OAP-C%d-VOA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-OQva', $mib_file);
    $deviceDescription = snmp_get($device, 'vDeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'vSoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'vHardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        #no temp sensors
    }

    $type = '7';
    $mib_file = sprintf('OAP-C%d-DEDFA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-OQva', $mib_file);
    $deviceDescription = snmp_get($device, 'vDeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'vSoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'vHardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    
    $current = snmp_get($device, 'vPump1Temp.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.11.0', $card, $type);
    $descr = 'Pump 1 Temperature';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vPump1Temp';
        discover_sensor($valid['sensor'], 'temperture', $device, $num_oid, $index, $state_name, $descr, 100, 1, null, null, null, null, $current/100, 'snmp', null, null, null, $group);
    }

    $current = snmp_get($device, 'vPump2Temperature.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.18.0', $card, $type);
    $descr = 'Pump 2 Temperature';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        $state_name = 'vPump2Temperature';
        discover_sensor($valid['sensor'], 'temperture', $device, $num_oid, $index, $state_name, $descr, 100, 1, null, null, null, null, $current/100, 'snmp', null, null, null, $group);
    }

    $type = '8';
    $mib_file = sprintf('OAP-C%d-OSW', $card);
    $deviceType = snmp_get($device, 'c1DeviceType.0', '-Ovq', $mib_file);
    $deviceDescription = snmp_get($device, 'c1DeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'c1SoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'c1HardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        # No temp sensors
    }

    $type = '9';
    $mib_file = sprintf('OAP-C%d-YEDFA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'vDeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'vSoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'vHardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($current)) {
        # No temp sensors
    }
}
