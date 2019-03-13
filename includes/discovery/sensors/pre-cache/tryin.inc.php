<?php
/**
 * tryin.inc.php
 *
 * LibreNMS Pre-Cache for sensor discovery module for Tryin EDFA's
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

$pre_cache['tryin_nmu'] = snmpwalk_cache_multi_oid($device, '1.3.6.1.4.1.40989.10.16.20', $pre_cache['tryin_nmu'], 'OAP-NMU', null, '-OQUbs');
$pre_cache['tryin_nmu_sv'] = snmp_get($device, 'softwareVersion.0', '-OQv', 'OAP-NMU');
