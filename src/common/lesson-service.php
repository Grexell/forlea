<?php

function get_lessons($course_id, $username) {
    $stmt = get_global_or_new_statement('create_user_statement',
        'select exists(select id_user from passed_lesson where id_user = (select id from user where LCASE(username) = LCASE(?)) and passed_lesson.id_lesson = lesson.id), lesson.id, lesson.name from lesson where id_course=? limit 1, 20000');
    $stmt->bind_param('si', $username, $course_id);
    $stmt->execute();

    $lessons = [];

    $stmt->bind_result($passed, $lesson_id, $lesson_name);
    while ($stmt->fetch()) {
        $lesson = new stdClass();
        $lesson->id = $lesson_id;
        $lesson->name = $lesson_name;
        $lesson->passed = $passed == 1;
        $lessons[] = $lesson;
    }
    $stmt->close();
    return $lessons;
}

function get_lesson($course_id, $lesson_number)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'select * from lesson left join link on link.id_lesson = lesson.id where lesson.id = (select id from lesson where id_course=? limit ?, 1);');
    $stmt->bind_param('ii', $course_id, $lesson_number);

    $stmt->execute();

    $lesson = new stdClass();

    $stmt->bind_result($lesson_id, $lesson_name, $course_id, $link_id, $link_value, $link_name, $link_type, $id_lesson);
    while ($stmt->fetch()) {
        $lesson->id = $lesson_id;
        $lesson->name = $lesson_name;
        $lesson->course = $course_id;

        $link = new stdClass();
        $link->id = $link_id;
        $link->name = $link_name;
        $link->type = $link_type;
        $link->value = $link_value;

        $lesson->links[] = $link;
    }
    $stmt->close();
    get_comments($lesson->id, $lesson);
    return $lesson;
}

function get_lesson_by_id($target_id)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'select * from lesson left join link on link.id_lesson = lesson.id where lesson.id = ?;');
    $stmt->bind_param('i', $target_id);

    $stmt->execute();

    $lesson = new stdClass();

    $stmt->bind_result($lesson_id, $lesson_name, $course_id, $link_id, $link_value, $link_name, $link_type, $id_lesson);
    while ($stmt->fetch()) {
        $lesson->id = $lesson_id;
        $lesson->name = $lesson_name;
        $lesson->course = $course_id;

        $link = new stdClass();
        $link->id = $link_id;
        $link->name = $link_name;
        $link->type = $link_type;
        $link->value = $link_value;

        $lesson->links[] = $link;
    }
    $stmt->close();
    get_comments($lesson->id, $lesson);
    return $lesson;
}

function get_comments($lesson_id, $lesson)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'select  comment.id, creation_date, message, username from comment join user on id_user=user.id where id_lesson=? order by creation_date desc');
    $stmt->bind_param('i', $lesson_id);

    $stmt->execute();

    $comments = [];

    $stmt->bind_result($comment_id, $post_date, $message, $username);
    while ($stmt->fetch()) {
        $comment = new stdClass();
        $comment->id = $comment_id;
        $comment->date = $post_date;
        $comment->message = $message;
        $comment->user = $username;
        $comments[] = $comment;
    }
    $stmt->close();
    $lesson->comments = $comments;
}

function comment($username, $lesson_id, $message)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'insert into comment(id_lesson, id_user, creation_date, message) values (?, (select id from user where LCASE(username) = LCASE(?)), now(), ?);');

    $stmt->bind_param('iss', $lesson_id, $username, $message);
    $stmt->execute();

    $stmt->close();
}

function pass($lesson_id, $username)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'insert into passed_lesson(id_lesson, id_user, pass_date) values (?, (select id from user where LCASE(username) = LCASE(?)), now());');

    $stmt->bind_param('is', $lesson_id, $username);
    $stmt->execute();

    $stmt->fetch();
    $stmt->close();
}