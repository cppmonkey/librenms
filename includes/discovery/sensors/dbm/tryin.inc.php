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
    $group = sprintf('EDFA Card %d', $card);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
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
    $group = sprintf('OEO Card %d', $card);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        // Descover SPF Optics
        $channel_oid = 11;
        foreach (range('A', 'D') as $channel) {
            foreach (range(1, 2) as $optic) {
                $distance   = snmp_get($device, sprintf('vSFP%s%dModeTrasmissionDistance.0', $channel, $optic), '-Ovq', $mib_file);
                $speed      = snmp_get($device, sprintf('vSFP%s%dModeTrasmissionRate.0', $channel, $optic), '-Ovq', $mib_file);
                $wave       = snmp_get($device, sprintf('vSFP%s%dModeWave.0', $channel, $optic), '-Ovq', $mib_file);

                $current = snmp_get($device, sprintf('vSFP%s%dTxPower.0', $channel, $optic), '-Ovq', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.4.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Tx Power (%dG %dnm %dkm)', $channel, $optic, $speed / 100, $wave / 100 , $distance / 1000 );
                $index = substr($num_oid, 24);

                if (is_numeric($current)) {
                    $current /= $divisor;
                    discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, $divisor, $multiplier, null, null, null, null, $current, 'snmp', null, null, null, $group);
                }
                $current = snmp_get($device, sprintf('vSFP%s%dRxPower.0', $channel, $optic), '-Ovq', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.5.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Rx Power (%dG %dnm %dkm)', $channel, $optic, $speed / 100, $wave / 100 , $distance / 1000 );
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
    $group = sprintf('OLP Card %d', $card);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $current, 'snmp', null, null, null, $group);
    }

    $type = '6';
    $mib_file = sprintf('OAP-C%d-VOA', $card);
    $group = sprintf('VOA Card %d', $card);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $current, 'snmp', null, null, null, $group);
    }

    $type = '7';
    $mib_file = sprintf('OAP-C%d-DEDFA', $card);
    $group = sprintf('DEDFA Card %d', $card);
    $current = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $current, 'snmp', null, null, null, $group);
    }

    $type = '8';
    $mib_file = sprintf('OAP-C%d-OSW', $card);
    $group = sprintf('OSW Card %d', $card);
    $current = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);

    if (is_numeric($current)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'dbm';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $current, 'snmp', null, null, null, $group);
    }
}
