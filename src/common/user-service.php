<?php
include_once('db-repository.php');

session_start();

function is_authorised()
{
    return !empty($_SESSION['username']) && !empty($_SESSION['password']);
}

function is_authorized_or_redirect()
{
    $authorized = is_authorised();
    if (!$authorized) {
        header('Location: /login.php');
    }
    return $authorized;
}

function authorize($username, $password)
{
    $stmt = get_global_or_new_statement('select_user_statement',
        'SELECT username, password FROM user where LCASE(username) = LCASE(?)');

    $stmt->bind_param('s', $username);
    $stmt->execute();

    $stmt->bind_result($name, $real_pass);

    if ($stmt->fetch() && ($password === $real_pass)) {
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $stmt->close();
        return 'true';
    }
    $stmt->close();
    return 'false';
}

function get_username()
{
    if (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    } else {
        return null;
    }
}

function logout()
{
    unset($_SESSION['username']);
    unset($_SESSION['password']);
}

function exist_user($username)
{
    $check_username = '' . $username;
    $check_usr_stmt = get_global_or_new_statement('select_user_statement',
        'SELECT username, password FROM user where LCASE(username) = LCASE(?)');

    $check_usr_stmt->bind_param('s', $check_username);
    $check_usr_stmt->execute();

    $check_usr_stmt->store_result();

    $user_check = "";
    $check_usr_stmt->bind_result($user_check, $password_check);
    $check_usr_stmt->fetch();

    $result = [];
    if ($check_usr_stmt->num_rows == 1) {
        $result [] = 'Пользователь с таким именем существует';
    }

    $check_usr_stmt->close();
    return $result;
}

function register($username, $password)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'insert into user(username, password) values(?, ?)');

    $stmt->bind_param('ss', $username, $password);

    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        $stmt->close();
        authorize($username, $password);
        return $id;
    }
    $stmt->close();
    return 'false';
}

function get_user($username)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'select id from user where username = ?');

    $stmt->bind_param('s', $username);

    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();
    return $id;
}

function get_next_lesson_id($course, $username)
{
    $course_id = $course->id;
    $stmt = get_global_or_new_statement('create_user_statement',
        'select l.id from lesson as l where id_course = ? and not exists(select id_lesson from passed_lesson where l.id = passed_lesson.id_lesson and id_user = (select id from user where username = ?)) limit 1;');
    $stmt->bind_param('is', $course_id, $username);

    $stmt->execute();

    $stmt->bind_result($lesson_id);
    if ($stmt->fetch()) {
        $course->next_lesson = $lesson_id;
    }
    $stmt->close();
}

function get_applied_courses($username)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'select course.id, course.name, count(lesson.id) from course inner join lesson on id_course=course.id inner join passed_lesson on lesson.id=id_lesson where id_user=(select id from user where username = ?) group by id_course');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $courses = [];

    $stmt->bind_result($course_id, $course_name, $lesson_count);
    while ($stmt->fetch()) {
        $course = new stdClass();
        $course->id = $course_id;
        $course->name = $course_name;
        $course->count = $lesson_count;
        $courses[] = $course;
    }
    $stmt->close();

    for ($i = 0; $i < count($courses); $i++) {
        get_next_lesson_id($courses[$i], $username);
    }

    return $courses;
}

?>