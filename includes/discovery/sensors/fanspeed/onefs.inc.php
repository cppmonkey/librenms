<?php
/**
 * onefs.inc.php
 *
 * LibreNMS fanspeeds module for OneFS
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
 * @copyright  2017 Neil Lathwood
 * @author     Neil Lathwood <gh+n@laf.io>
 */

echo 'OneFS: ';
$oids = snmpwalk_cache_multi_oid($device, 'fanTable', [], 'ISILON-MIB');

foreach ($oids as $index => $entry) {
    if (is_numeric($entry['fanSpeed']) && is_numeric($index)) {
        $descr   = $entry['fanDescription'];
        $oid     = '.1.3.6.1.4.1.12124.2.53.1.4.'.$index;
        $current = $entry['fanSpeed'];
        discover_sensor($valid['sensor'], 'fanspeed', $device, $oid, $index, 'onefs', $descr, '1', '1', 0, 0, 5000, 9000, $current);
    }
}

unset($oids);
