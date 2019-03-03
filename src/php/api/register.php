<?php
include_once('../../common/file_service.php');
include_once('../../common/user-service.php');

$file_check = check_file($_FILES['image']);
$user_check = exist_user($_POST['username']);
if (empty($file_check) && empty($user_check)) {
    $user_id = register($_POST['username'], $_POST['password']);
    if (is_int($user_id)) {
        save_file($_FILES['image'], get_user_file_prefix().$user_id);
    }
    print (is_int($user_id)) ? 'true' : 'false';

} else {
    print json_encode(array_merge($user_check, $file_check));
}