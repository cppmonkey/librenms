<?php

echo 'JUNIPER-VLAN-MIB VLANs: ';

$vlanversion = snmp_get($device, 'dot1qVlanVersionNumber.0', '-Oqv', 'IEEE8021-Q-BRIDGE-MIB');

if ($vlanversion == 'version1' || $vlanversion == '2') {
    echo "ver $vlanversion ";
    $vtpdomain_id = '1';
    $vlans        = snmpwalk_cache_oid($device, 'jnxExVlanName', array(), 'JUNIPER-VLAN-MIB');
    $tagoruntag   = snmpwalk_cache_oid($device, 'jnxExVlanTag', array(), 'JUNIPER-VLAN-MIB', null, '-OQUs --hexOutputLength=0');
    $untag        = snmpwalk_cache_oid($device, 'jnxExVlanPortTagness', array(), 'JUNIPER-VLAN-MIB', null, '-OQeUs --hexOutputLength=0');

    foreach ($vlans as $vlan_id => $vlan) {
        $vlan_id = $tagoruntag[$vlan_id]['jnxExVlanTag'];
        d_echo(" $vlan_id");
        if (is_array($vlans_db[$vtpdomain_id][$vlan_id])) {
            echo '.';
        } else {
            dbInsert(array(
                'device_id' => $device['device_id'],
                'vlan_domain' => $vtpdomain_id,
                'vlan_vlan' => $vlan_id,
                'vlan_name' => $vlan['jnxExVlanName'],
                'vlan_type' => array('NULL')
            ), 'vlans');
            echo '+';
        }

        $device['vlans'][$vtpdomain_id][$vlan_id] = $vlan_id;
        $egress_ids = $tagoruntag[$vlan_id]['jnxExVlanTag'];

        foreach ($egress_ids as $port_id) {
            $ifIndex = $base_to_index[$port_id];
            $per_vlan_data[$vlan_id][$ifIndex]['untagged'] = $untagged_ids;
        }
    }
}