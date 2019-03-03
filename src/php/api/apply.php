<?php
include_once ('../../common/user-service.php');
include_once ('../../common/course-service.php');

if (is_authorised()) {
    $body = json_decode(file_get_contents('php://input'));
    apply_course(get_username(), $body ->course);
}