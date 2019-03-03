<?php
include_once('../../common/user-service.php');
include_once('../../common/lesson-service.php');

$body = json_decode(file_get_contents('php://input'));

if (is_authorised() && isset($body->text) && isset($body->lesson_id)) {
    comment(get_username(), $body->lesson_id, htmlspecialchars($body->text));
    print 'true';
} else {
    print 'false';
}