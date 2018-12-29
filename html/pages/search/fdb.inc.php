<div class="panel panel-default panel-condensed">
    <div class="panel-heading">
        <strong>FDB Entries</strong>
    </div>
    <table id="fdb-search" class="table table-hover table-condensed table-striped">
        <thead>
            <tr>
                <th data-column-id="device">Device</th>
                <th data-column-id="mac_address">MAC Address</th>
                <th data-column-id="ipv4_address">IPv4 Address</th>
                <th data-column-id="interface">Port</th>
                <th data-column-id="vlan">Vlan</th>
                <th data-column-id="description">Description</th>
                <th data-column-id="dnsname">DNS Name</th>
            </tr>
        </thead>
    </table>
</div>

<script>

var grid = $("#fdb-search").bootgrid({
    ajax: true,
    rowCount: [50, 100, 250, -1],
    templates: {
        header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\">"+
                "<div class=\"col-sm-9 actionBar\"><span class=\"pull-left\">"+
                "<form method=\"post\" action=\"\" class=\"form-inline\" role=\"form\">"+
                "<div class=\"form-group\">"+
                "<select name=\"device_id\" id=\"device_id\" class=\"form-control input-sm\">"+
                "<option value=\"\">All Devices</option>"+
<?php

use LibreNMS\Authentication\LegacyAuth;

// Select the devices only with FDB tables
$sql = 'SELECT D.device_id AS device_id, `hostname` FROM `ports_fdb` AS F, `ports` AS P, `devices` AS D';

$param = array();
if (!LegacyAuth::user()->hasGlobalRead()) {
    $sql    .= ' LEFT JOIN `devices_perms` AS `DP` ON `D`.`device_id` = `DP`.`device_id`';
    $where  .= ' AND `DP`.`user_id`=?';
    $param[] = LegacyAuth::id();
}

$sql .= " WHERE F.port_id = P.port_id AND P.device_id = D.device_id $where GROUP BY `D`.`device_id`, `D`.`hostname` ORDER BY `hostname`";
foreach (dbFetchRows($sql, $param) as $data) {
    echo '"<option value=\"'.$data['device_id'].'\""+';
    if ($data['device_id'] == $vars['device_id']) {
        echo '" selected "+';
    }

    echo '">'.format_hostname($data, $data['hostname']).'</option>"+';
}
?>
                "</select>"+
                "</div>"+
                "<div class=\"form-group\">"+
                "<select name=\"searchby\" id=\"searchby\" class=\"form-control input-sm\">"+
                "<option value=\"mac\" "+
<?php
if ($vars['searchby'] == 'mac') {
    echo '" selected "+';
}
?>

                ">MAC Address</option>"+
                "<option value=\"ip\" "+
<?php
if ($vars['searchby'] == 'ip') {
    echo '" selected "+';
}
?>

                ">IP Address</option>"+
                "<option value=\"dnsname\" "+
<?php
if ($vars['searchby'] == 'dnsname') {
    echo '" selected "+';
}
?>

                ">DNS Name</option>"+
                "<option value=\"description\" "+
<?php
if ($vars['searchby'] == 'description') {
    echo '" selected "+';
}
?>

                ">Description</option>"+
                "<option value=\"vlan\" "+
<?php
if ($vars['searchby'] == 'vlan') {
    echo '" selected "+';
}
?>

                ">Vlan</option>"+
                "</select>"+
                "</div>"+
                "<div class=\"form-group\">"+
                "<input type=\"text\" name=\"searchPhrase\" id=\"address\" value=\""+
<?php
echo '"'.$vars['searchPhrase'].'"+';
?>

                "\" class=\"form-control input-sm\" placeholder=\"Value\" />"+
                "</div>"+
                "<button type=\"submit\" class=\"btn btn-default input-sm\">Search</button>"+
                "</form></span></div>"+
               "<div class=\"col-sm-3 actionBar\"><p class=\"{{css.actions}}\"></p></div></div></div>"
    },
    post: function ()
    {
        return {
            id: "fdb-search",
            device_id: '<?php echo $vars['device_id']; ?>',
            searchby: '<?php echo $vars['searchby']; ?>',
            searchPhrase: '<?php echo $vars['searchPhrase']; ?>'
        };
    },
    url: "ajax_table.php"
});

</script>
