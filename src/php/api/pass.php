<?php
include_once('../../common/user-service.php');
include_once('../../common/lesson-service.php');

$body = json_decode(file_get_contents('php://input'));
if (is_authorised() && isset($body->lesson_id)) {
    pass($body->lesson_id, get_username());
}