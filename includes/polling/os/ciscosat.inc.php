<?php
$ciscosat_data = snmp_get_multi_oid($device, ['.1.3.6.1.4.1.1429.2.2.5.1.1.1.0', '.1.3.6.1.4.1.1429.2.2.5.42.2.1.1.6.1', '.1.3.6.1.4.1.1429.2.2.5.1.1.4.0']);
$version      = $ciscosat_data['.1.3.6.1.4.1.1429.2.2.5.1.1.1.0'];
$serial       = $ciscosat_data['.1.3.6.1.4.1.1429.2.2.5.42.2.1.1.6.1'];
$hardware     = $ciscosat_data['.1.3.6.1.4.1.1429.2.2.5.1.1.4.0'];
