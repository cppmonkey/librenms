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

foreach (range(1, 16) as $card) {
    $state_name = 'vCardState';
    $states = array(
        array('value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'Off'),
        array('value' => 1, 'generic' => 0, 'graph' => 1, 'descr' => 'On')
    );
    create_state_index($state_name, $states);

    $type = '1';
    $group = sprintf('EDFA Card %d', $card);
    $edfa_card_state = snmp_get($device, 'vCardState.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $edfa_card_work_mode = snmp_get($device, 'vWorkMode.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.8.0', $card, $type);
    $descr = 'Work Mode';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_work_mode)) {
        $state_name = 'vWorkMode';
        $states = array(
            array('value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'acc'),
            array('value' => 2, 'generic' => 0, 'graph' => 1, 'descr' => 'apc'),
            array('value' => 3, 'generic' => 0, 'graph' => 2, 'descr' => 'agc')
        );
        create_state_index($state_name, $states);

        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_work_mode, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $edfa_card_pump_switch = snmp_get($device, 'vPUMPSwitch.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.9.0', $card, $type);
    $descr = 'PUMP Switch';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_pump_switch)) {
        $state_name = 'vPUMPSwitch';
        $states = array(
            array('value' => 0, 'generic' => 0, 'graph' => 1, 'descr' => 'On'),
            array('value' => 1, 'generic' => 2, 'graph' => 0, 'descr' => 'Off')
        );
        create_state_index($state_name, $states);

        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_pump_switch, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $state_name = 'EDFA_NormalAlarm';
    $states = array(
        array('value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'Alarm'),
        array('value' => 1, 'generic' => 0, 'graph' => 1, 'descr' => 'Normal')
    );
    create_state_index($state_name, $states);

    $edfa_card_input_power_state = snmp_get($device, 'vInputPowerState.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.16.0', $card, $type);
    $descr = 'Input Power';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_input_power_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_input_power_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $edfa_card_output_power_state = snmp_get($device, 'vOutputPowerState.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.17.0', $card, $type);
    $descr = 'Output Power';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_output_power_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_output_power_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $edfa_card_module_temp_state = snmp_get($device, 'vModuleTemperatureState.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.18.0', $card, $type);
    $descr = 'Module Temp';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_module_temp_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_module_temp_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $edfa_card_pump_temp_state = snmp_get($device, 'vPUMPTemperatureState.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.19.0', $card, $type);
    $descr = 'Pump Temp';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_pump_temp_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_pump_temp_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $edfa_card_pump_current_state = snmp_get($device, 'vPUMPCurrentState.0', '-Ovqe', sprintf('OAP-C%d-EDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.20.0', $card, $type);
    $descr = 'Pump Current';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_pump_current_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_pump_current_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '2';
    $mib_file = sprintf('OAP-C%d-OEO', $card);
    $group = sprintf('OEO Card %d', $card);
    $oeo_card_state = snmp_get($device, 'vCardState.0', '-Ovqe', $mib_file);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($oeo_card_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $oeo_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);

        // Descover SPF Optics
        $channel_oid = 11;
        foreach (range('A', 'D') as $channel) {
            foreach (range(1, 2) as $optic) {
                $oeo_sfp_state = snmp_get($device, sprintf('vSFP%s%dState.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.1.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d State', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($oeo_sfp_state)) {
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $oeo_sfp_state, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }

                $oeo_sfp_tx_power_alarm = snmp_get($device, sprintf('vSFP%s%dTxPowerAlarm.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.10.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Tx Power Alarm', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($oeo_sfp_tx_power_alarm)) {
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $oeo_sfp_tx_power_alarm, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }

                $oeo_sfp_rx_power_alarm = snmp_get($device, sprintf('vSFP%s%dRxPowerAlarm.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.11.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Rx Power Alarm', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($oeo_sfp_rx_power_alarm)) {
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $oeo_sfp_rx_power_alarm, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }

                $oeo_sfp_mode_temperature_alarm = snmp_get($device, sprintf('vSFP%s%dModeTemperatureAlarm.0', $channel, $optic), '-Ovqe', $mib_file);
                $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.%d.11.0', $card, $type, $channel_oid);
                $descr = sprintf('SFP %s%d Mode Temperature Alarm', $channel, $optic);
                $index = substr($num_oid, 24);

                if (is_numeric($oeo_sfp_mode_temperature)) {
                    discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $oeo_sfp_mode_temperature, 'snmp', null, null, null, $group);
                    create_sensor_to_state_index($device, $state_name, $index);
                }
                $channel_oid++;
            }
        }
    }

    $type = '3';
    $mib_file = sprintf('OAP-C%d-OLP', $card);
    $group = sprintf('OLP Card %d', $card);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $olp_card_c1state = snmp_get($device, 'c1State.0', '-Ovqe', $mib_file);

    if (is_numeric($olp_card_c1state)) {
        $descr = 'State';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $olp_card_c1state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.8.0', $card, $type);
    $olp_card_c1workmode = snmp_get($device, 'c1WorkMode.0', '-Ovqe', $mib_file);

    if (is_numeric($olp_card_c1workmode)) {
        $descr = 'Work Mode';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $olp_card_c1workmode, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.9.0', $card, $type);
    $olp_card_c1channel = snmp_get($device, 'c1Channel.0', '-Ovqe', $mib_file);

    if (is_numeric($olp_card_c1channel)) {
        $descr = 'Channel';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $olp_card_c1channel, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.9.0', $card, $type);
    $olp_card_c1workmodesave = snmp_get($device, 'c1Channel.0', '-Ovqe', $mib_file);

    if (is_numeric($olp_card_c1workmodesave)) {
        $descr = 'Work Mode Save';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $olp_card_c1workmodesave, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.22.0', $card, $type);
    $olp_card_c1backmode = snmp_get($device, 'c1BackMode.0', '-Ovqe', $mib_file);

    if (is_numeric($olp_card_c1backmode)) {
        $descr = 'Back Mode';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $olp_card_c1backmode, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '4';
    $group = sprintf('Type 4 Card %d', $card);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $type_4_card_state = snmp_get($device, $num_oid, '-Ovqet', '');

    if (is_numeric($type_4_card_state)) {
        $descr = 'State';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $type_4_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.2.0', $card, $type);
    $type_4_card_state = snmp_get($device, $num_oid, '-Ovqet', '');

    if (is_numeric($type_4_card_state)) {
        $descr = 'Type';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $type_4_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.3.0', $card, $type);
    $type_4_card_state = snmp_get($device, $num_oid, '-Ovqet', '');

    if (is_numeric($type_4_card_state)) {
        $descr = 'Descr';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $type_4_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '5';
    $group = sprintf('Type 5 Card %d', $card);
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $type_5_card_state = snmp_get($device, $num_oid, '-Ovqet', '');

    if (is_numeric($type_5_card_state)) {
        $descr = 'State';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $type_5_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.2.0', $card, $type);
    $type_5_card_state = snmp_get($device, $num_oid, '-Ovqet', '');

    if (is_numeric($type_5_card_state)) {
        $descr = 'Type';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $type_5_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.3.0', $card, $type);
    $type_5_card_state = snmp_get($device, $num_oid, '-Ovqet', '');

    if (is_numeric($type_5_card_state)) {
        $descr = 'Descr';
        $index = substr($num_oid, 24);
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $type_5_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '6';
    $group = sprintf('VOA Card %d', $card);
    $voa_card_state = snmp_get($device, 'vCardState.0', '-Ovqe', sprintf('OAP-C%d-VOA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($voa_card_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $voa_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '7';
    $group = sprintf('DEDFA Card %d', $card);
    $edfa_card_state = snmp_get($device, 'vCardState.0', '-Ovqe', sprintf('OAP-C%d-DEDFA', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($edfa_card_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $edfa_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }

    $type = '8';
    $group = sprintf('OSW Card %d', $card);
    $osw_card_state = snmp_get($device, 'c1State.0', '-Ovqe', sprintf('OAP-C%d-OSW', $card));
    $num_oid = sprintf('.1.3.6.1.4.1.40989.10.16.%d.%d.1.0', $card, $type);
    $descr = 'State';
    $index = substr($num_oid, 24);

    if (is_numeric($osw_card_state)) {
        discover_sensor($valid['sensor'], 'state', $device, $num_oid, $index, $state_name, $descr, '1', '1', null, null, null, null, $osw_card_state, 'snmp', null, null, null, $group);
        create_sensor_to_state_index($device, $state_name, $index);
    }
}
