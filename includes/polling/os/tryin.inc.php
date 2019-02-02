<?php

$version = preg_replace('/[\r\n\"]+/', ' ', snmp_get($device, "softwareVersion.0", "-OQv", "OAP-NMU"));
$hardware =  preg_replace('/[\r\n\"]+/', ' ', snmp_get($device, "deviceType.0", "-OQv", "OAP-NMU") . ' ' . snmp_get($device, "hardwareVersion.0", "-OQv", "OAP-NMU"));
$serial = snmp_get($device, "serialNumber.0", "-OQv", "OAP-NMU");
