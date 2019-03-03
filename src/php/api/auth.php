<?php
include('../../common/user-service.php');
$auth_params = json_decode(file_get_contents('php://input'));
print authorize($auth_params->username, $auth_params->password);