<?php
/**
 * zynos.inc.php
 *
 * LibreNMS sensors pre-cache discovery module for ZyXEL ZyNOS
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
 * @author     Paul Parsons <p.parsons@b4rn.org.uk>
 */
echo 'zyxelTransceiverDdmiTable ';
$pre_cache['zynos_zyxelTransceiverDdmiTable'] = snmpwalk_cache_oid($device, 'zyxelTransceiverDdmiTable', array(), 'ZYXEL-TRANSCEIVER-MIB', null, '-OeQUs');
