<?php

function get_file_storage_path() {
    return $_SERVER['DOCUMENT_ROOT'].file_prefix();
}

function file_prefix() {
    return '\files\\';
}

function description_prefix() {
    return '\description\\';
}

function get_user_file_prefix() {
    return 'users\\';
}

function get_course_image_prefix() {
    return 'course_images\\';
}

function text_content_prefix() {
    return '\text\\';
}

function video_content_prefix() {
    return '\video\\';
}

function check_file($file) {
    $errors = [];

    if (isset($file)) {
        $file_name = $file['name'];
        $file_size = $file['size'];
        $file_type = $file['type'];
        $file_name_parts = explode('.', $file_name);
        $file_ext = strtolower(end($file_name_parts));

        $extensions = array("jpeg", "jpg", "png");

        if (in_array($file_ext, $extensions) === false) {
            $errors[] = "Тип файла не разрешен, выберите JPEG or PNG.";
        }

        if ($file_size > 2097152) {
            $errors[] = 'Размер файла должен быть до 2 МБ';
        }
    }

    return $errors;
}

function save_file($file, $target_filename) {
    $file_tmp = $file['tmp_name'];
    imagepng(imagecreatefromstring(file_get_contents($file_tmp)), get_file_storage_path() . $target_filename.'.png');
}

?>