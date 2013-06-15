<?php

include('directvps.php');

$directvps = new directvps('./thijmen.crt','./thijmen.key','./ca.crt');
$vpslists = $directvps->go('get_vpslist');
print_r($vpslists)


?>