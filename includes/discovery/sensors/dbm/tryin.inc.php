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

foreach (range(1, 16) as $card) {
    $type = '1';
    $group = sprintf('EDFA Card %d', $card);
    $edfa_card_state = snmp_get($device, 'vCardState.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));

    if (is_numeric($edfa_card_state)) {
        $edfa_card_input = snmp_get($device, 'vInput.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));

        if (is_numeric($edfa_card_input)) {
            $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.28.0', $card, $type);
            $descr = 'Input Power';
            $index = substr($num_oid, 24);
            discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, 100, 1, null, null, null, null, $edfa_card_input, 'snmp', null, null, null, $group);
        }

        $edfa_card_output = snmp_get($device, 'vOutput.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));

        if (is_numeric($edfa_card_output)) {
            $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.29.0', $card, $type);
            $descr = 'Output Power';
            $index = substr($num_oid, 24);
            discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, 100, 1, null, null, null, null, $edfa_card_output, 'snmp', null, null, null, $group);
        }
    }

    $type = '2';
    $mib_file = sprintf('OAP-C%d-OEO', $card);
    $group = sprintf('OEO Card %d', $card);
    $oeo_card_state = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);

    if (is_numeric($oeo_card_state)) {
        // Descover SPF Optics
        $channel_oid = 11;
        foreach (range('A', 'D') as $channel) {
            foreach (range(1, 2) as $optic) {
                $oeo_sfp_power = snmp_get($device, sprintf('vSFP%s%dTxPower.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.4.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Tx Power', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($oeo_sfp_power)) {
                    discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, 100, '1', null, null, null, null, $oeo_sfp_power, 'snmp', null, null, null, $group);
                }
                $oeo_sfp_power = snmp_get($device, sprintf('vSFP%s%dRxPower.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.5.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Rx Power', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($oeo_sfp_power)) {
                    discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, 100, '1', null, null, null, null, $oeo_sfp_power, 'snmp', null, null, null, $group);
                }
                $channel_oid++;
            }
        }
    }

    $type = '3';
    $group = sprintf('OLP Card %d', $card);
    $olp_card_state = snmp_get($device, 'c1State.0', '-Ovqe', sprintf('OAP-C%d-OLP', $card));

    if (is_numeric($olp_card_state)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $olp_card_state, 'snmp', null, null, null, $group);
    }

    $type = '6';
    $group = sprintf('VOA Card %d', $card);
    $voa_card_state = snmp_get($device, 'vCardState.0', '-Ovqe', sprintf('OAP-C%d-VOA', $card));

    if (is_numeric($voa_card_state)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $voa_card_state, 'snmp', null, null, null, $group);
    }

    $type = '7';
    $group = sprintf('DEDFA Card %d', $card);
    $edfa_card_state = snmp_get($device, 'vCardState.0', '-Ovqe', sprintf('OAP-C%d-DEDFA', $card));

    if (is_numeric($edfa_card_state)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'state';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $edfa_card_state, 'snmp', null, null, null, $group);
    }

    $type = '8';
    $group = sprintf('OSW Card %d', $card);
    $osw_card_state = snmp_get($device, 'c1State.0', '-Ovqe', sprintf('OAP-C%d-OSW', $card));

    if (is_numeric($osw_card_state)) {
        $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
        $descr = 'dbm';
        $index = substr($num_oid, 24);
        //discover_sensor($valid['sensor'], 'dbm', $device, $num_oid, $index, $dev_type, $descr, '1', '1', null, null, null, null, $osw_card_state, 'snmp', null, null, null, $group);
    }
}
