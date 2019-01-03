<?php
$multipler     = 1;
$divisor       = 100;
$divisor_alarm = 100;
foreach ($pre_cache['zynos_zyxelTransceiverDdmiTable'] as $index => $entry) {
    if ( $entry['zyTransceiverDdmiType'] == 4 && is_numeric($entry['zyTransceiverDdmiCurrent']) && $entry['zyTransceiverDdmiCurrent'] != 0) {
        $index_actual = substr( $index, 0, 3);
        $oid                       = '.1.3.6.1.4.1.890.1.15.3.84.1.2.1.6.' . $index;
        $dbquery                   = dbFetchRows("SELECT `ifIndex`, `ifDescr` FROM `ports` WHERE `ifIndex`= ? AND `device_id` = ? AND `ifAdminStatus` = 'up'", array(
            $index_actual,
            $device['device_id']
        ));
        $limit_low                 = $entry['zyTransceiverDdmiAlarmMin'] / $divisor_alarm;
        $warn_limit_low            = $entry['zyTransceiverDdmiWarnMin'] / $divisor_alarm;
        $limit                     = $entry['zyTransceiverDdmiAlarmMax'] / $divisor_alarm;
        $warn_limit                = $entry['zyTransceiverDdmiWarnMax'] / $divisor_alarm;
        $current                   = $entry['zyTransceiverDdmiCurrent'] / $divisor;
        $entPhysicalIndex          = $index_actual;
        $entPhysicalIndex_measured = 'ports';
        foreach ($dbquery as $dbindex => $dbresult) {
            $descr = makeshortif($dbresult['ifIndex']) . ' Port Tx Power';
            discover_sensor($valid['sensor'], 'dbm', $device, $oid, 'zyTransceiverDdmiCurrent.' . $index, 'zynos', $descr, $divisor, $multiplier, $limit_low, $warn_limit_low, $warn_limit, $limit, $current, 'snmp', $entPhysicalIndex, $entPhysicalIndex_measured);
        }
    }
    if ( $entry['zyTransceiverDdmiType'] == 5 && is_numeric($entry['zyTransceiverDdmiCurrent']) && $entry['zyTransceiverDdmiCurrent'] != 0) {
        $index_actual = substr( $index, 0, 3);
        $oid                       = '.1.3.6.1.4.1.890.1.15.3.84.1.2.1.6.' . $index;
        $dbquery                   = dbFetchRows("SELECT `ifIndex`, `ifDescr` FROM `ports` WHERE `ifIndex`= ? AND `device_id` = ? AND `ifAdminStatus` = 'up'", array(
            $index_actual,
            $device['device_id']
        ));
        $limit_low                 = $entry['zyTransceiverDdmiAlarmMin'] / $divisor_alarm;
        $warn_limit_low            = $entry['zyTransceiverDdmiWarnMin'] / $divisor_alarm;
        $limit                     = $entry['zyTransceiverDdmiAlarmMax'] / $divisor_alarm;
        $warn_limit                = $entry['zyTransceiverDdmiWarnMax'] / $divisor_alarm;
        $current                   = $entry['zyTransceiverDdmiCurrent'] / $divisor;
        $entPhysicalIndex          = $index_actual;
        $entPhysicalIndex_measured = 'ports';
        foreach ($dbquery as $dbindex => $dbresult) {
            $descr = makeshortif($dbresult['ifIndex']) . ' Port Rx Power';
            discover_sensor($valid['sensor'], 'dbm', $device, $oid, 'zyTransceiverDdmiCurrent.' . $index, 'zynos', $descr, $divisor, $multiplier, $limit_low, $warn_limit_low, $warn_limit, $limit, $current, 'snmp', $entPhysicalIndex, $entPhysicalIndex_measured);
        }
    }
}

