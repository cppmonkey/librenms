<?php
/**
 * tryin.inc.php
 *
 * LibreNMS DBm sensor discovery module for Tryin EDFA's
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

$dev_type = 'tryin';

$multiplier = 1;
$divisor    = 100;

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
        $current = snmp_get($device, 'vGainOrOutputPower.0', '-Ovq', $mib_file);

        if (is_numeric($current)) {
            $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.21.0', $card, $type);
            $descr = 'Gain';
            $index = substr($num_oid, 24);
            $current /= $divisor;
            discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
        }

        $current = snmp_get($device, 'vInput.0', '-Ovq', $mib_file);

        if (is_numeric($current)) {
            $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.28.0', $card, $type);
            $descr = 'Input Power';
            $index = substr($num_oid, 24);
            $current /=  $divisor;
            discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
        }

        $current = snmp_get($device, 'vOutput.0', '-Ovq', $mib_file);

        if (is_numeric($current)) {
            $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.29.0', $card, $type);
            $descr = 'Output Power';
            $index = substr($num_oid, 24);
            $current /= $divisor;
            discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
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

    if (is_numeric($current)) {
        // Descover SPF Optics
        $channel_oid = 11;
        foreach (range('A', 'D') as $channel) {
            $group = sprintf('Card %d %s SFP Channel %s', $card, $deviceType, $channel);
            foreach (range(1, 2) as $optic) {
                $distance   = snmp_get($device, sprintf('vSFP%s%dModeTransmissionDistance.0', $channel, $optic), '-Ovq', $mib_file);
                $speed      = snmp_get($device, sprintf('vSFP%s%dModeTransmissionRate.0', $channel, $optic), '-Ovq', $mib_file);
                $wave       = snmp_get($device, sprintf('vSFP%s%dModeWave.0', $channel, $optic), '-Ovq', $mib_file);

                $current = snmp_get($device, sprintf('vSFP%s%dTxPower.0', $channel, $optic), '-Ovq', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.4.0', $card, $type, $channel_oid);

                // Older chassis report the wave length to the nearest decimal. Detect and adjust for this
                $waveLength = "";
                if (strpos($pre_cache['tryin_nmu_sv'], 'SV1.02') !== false) {
                    $waveDivisor = 1;
                    $waveLength = sprintf('%.0fnm', $wave);
                } else {
                    $waveDivisor = 100;
                    $waveLength = sprintf('%.2fnm', $wave / $waveDivisor);
                }

                // Detect and shorten 10G speed definition
                $opticDetails = "";
                if ($speed > 10000) {
                    $opticDetails = sprintf('%.1fG %s %dkm', $speed / 1000, $waveLength, $distance / 1000);
                } else {
                    $opticDetails = sprintf('%.2fG %s %dkm', $speed / 1000, $waveLength, $distance / 1000);
                }

                $descr = sprintf('SFP %s%d Tx Power (%s)', $channel, $optic, $opticDetails);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $current /= $divisor;
                    discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
                }
                $current = snmp_get($device, sprintf('vSFP%s%dRxPower.0', $channel, $optic), '-Ovq', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.5.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Rx Power (%s)', $channel, $optic, $opticDetails);
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $current /= $divisor;
                    discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
                }
                $channel_oid++;
            }
        }
    }

    $type = '3';
    $mib_file = sprintf('OAP-C%d-OLP', $card); // glsun-OXC or tryin-OLP
    $deviceType = snmp_get($device, 'c1DeviceType.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'c1DeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'c1SoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'c1HardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $current, 'snmp', null, null, null, $group);
    }

    $type = '6';
    $mib_file = sprintf('OAP-C%d-VOA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'c1DeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'c1SoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'c1HardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $current, 'snmp', null, null, null, $group);
    }

    $type = '7';
    $mib_file = sprintf('OAP-C%d-DEDFA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'vDeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'vSoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'vHardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        $divisor = 10;

        $current = snmp_get($device, 'vGainOrOutputPower.0', '-Ovq', $mib_file);

        if (is_numeric($current)) {
            $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.21.0', $card, $type);
            $descr = 'Gain';
            $index = substr($num_oid, 24);
            $current /= $divisor;
            discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
        }

        $current = snmp_get($device, 'vInputPower.0', '-Ovq', $mib_file);

        if (is_numeric($current)) {
            $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.25.0', $card, $type);
            $descr = 'Input Power';
            $index = substr($num_oid, 24);
            $current /=  $divisor;
            discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
        }

        $current = snmp_get($device, 'vOutputPower.0', '-Ovq', $mib_file);

        if (is_numeric($current)) {
            $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.26.0', $card, $type);
            $descr = 'Output Power';
            $index = substr($num_oid, 24);
            $current /= $divisor;
            discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
        }
        $divisor = 100;
    }

    $type = '8';
    $mib_file = sprintf('OAP-C%d-OSW', $card);
    $deviceType = snmp_get($device, 'c1DeviceType.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'c1DeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'c1SoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'c1HardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'dbm';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $current, 'snmp', null, null, null, $group);
    }

    $type = '9';
    $mib_file = sprintf('OAP-C%d-YEDFA', $card);
    $deviceType = snmp_get($device, 'vDeviceType.0', '-Ovqa', $mib_file);
    $deviceDescription = snmp_get($device, 'vDeviceDescription.0', '-Ovqa', $mib_file);
    $deviceSV = snmp_get($device, 'vSoftwareVerion.0', '-Ovqa', $mib_file);
    $deviceHV = snmp_get($device, 'vHardwareVerion.0', '-Ovqa', $mib_file);
    $group = sprintf('Card %d %s - %s (%s - %s)', $card, $deviceType, $deviceDescription, $deviceHV, $deviceSV);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'dbm';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $current, 'snmp', null, null, null, $group);
    }
}
