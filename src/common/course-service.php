<?php
include_once('db-repository.php');

function get_course_count()
{
    $count = 0;
    $connection = get_global_or_new_connection();
    $sql = "SELECT count (*) as course_count FROM course";
    if (!$result = $connection->query($sql)) {
        echo $connection->error;
    }
    if ($result->num_rows > 0) {
        if ($row = $result->fetch_assoc()) {
            $count = $row['course_count'];
        }
    }
    $connection->close();

    return $count;
}

function get_course_by_category_count($category_id)
{
    $count = 0;

    $stmt = get_global_or_new_statement('select_user_statement',
        'SELECT count(*) FROM course where id_category=?');

    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    $stmt->close();
    return $count;
}

function get_course_by_text_category_count($text, $category_id)
{
    $count = 0;

    if (!empty($category_id)) {
        $stmt = get_global_or_new_statement('select_user_statement',
            'SELECT count(*) FROM course where id_category=? and REGEXP_LIKE(name,?)');
        $stmt->bind_param('is', $category_id, $text);
    } else {
        $stmt = get_global_or_new_statement('select_user_statement',
            'SELECT count(*) FROM course where REGEXP_LIKE(name,?)');
        $stmt->bind_param('s', $text);
    }

    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();

    $stmt->close();
    return $count;
}

function get_page_count($number, $size)
{
    return $number / $size;
}

function get_creator_course_list($courses)
{
    $stmt = get_global_or_new_statement('select_user_statement',
        'select name from creator inner join creator_course on creator.id=id_creator where id_course=?');

    for ($i = 0; $i < count($courses); $i++) {
        $stmt->bind_param('i', $courses[$i]->id);
        $stmt->execute();
        $stmt->bind_result($creator_name);
        while ($stmt->fetch()) {
            $courses[$i]->creators[] = $creator_name;
        }
    }
    $stmt->close();
}

function get_all_courses($size)
{
    $conn = get_global_or_new_connection();
    $sql = 'select course.id, course.name, course.id_category, category.name as category_name, lesson_count, signed_users from course left join category on course.id_category = category.id left join (select count(*) as lesson_count, id_course, (select count(*) from passed_lesson where id_lesson = min(t1.id)) as signed_users from lesson as t1 group by id_course) as l on l.id_course = course.id order by category.id, course.id desc';

    if (!$result = $conn->query($sql)) {
        echo $conn->error;
    }

    $res = [];

    if ($result->num_rows > 0) {
        $category = new stdClass();
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            $course = new stdClass();
            $course->id = $row['id'];
            $course->name = $row['name'];
            $course->lesson_count = $row['lesson_count'];
            $course->users_count = $row['signed_users'];
            $course->creators = [];

            if (empty($category->id) || $row['id_category'] !== $category->id) {
                $category = new stdClass();
                $category->id = $row['id_category'];
                $category->name = $row['category_name'];
                $category->courses = [];
                $res[] = $category;
                $count = 0;
            }
            if ($count === $size) {
                continue;
            }
            $category->courses[] = $course;
            $count++;
        }
    }
    for ($i = 0; $i < count($res); $i++) {
        get_creator_course_list($res[$i]->courses);
    }
    return $res;
}

function get_all_courses_by_category_at_page($category_id, $number, $size)
{
    $stmt = get_global_or_new_statement('select_user_statement',
        'select course.id, course.name, category.name as category_name, lesson_count, signed_users from course left join category on course.id_category = category.id left join (select count(*) as lesson_count, id_course, (select count(*) from passed_lesson where id_lesson = min(t1.id)) as signed_users from lesson as t1 group by id_course) as l on l.id_course = course.id where id_category=? order by course.id desc limit ?,?');

    $offset = ($number - 1) * $size;
    $stmt->bind_param('iii', $category_id, $offset, $size);
    $stmt->execute();

    $stmt->bind_result($id, $name, $category_name, $lesson_count, $users_count);

    $category = new stdClass();
    $category->id = $category_id;
    $category->courses = [];

    while ($stmt->fetch()) {
        $course = new stdClass();
        $course->id = $id;
        $course->name = $name;
        $category->name = $category_name;
        $course->lesson_count = $lesson_count;
        $course->users_count = $users_count;
        $course->creators = [];
        $category->courses[] = $course;
    }
    $stmt->close();
    get_creator_course_list($category->courses);
    return $category;
}

function get_all_courses_by_text_and_category_at_page($text, $category_id, $number, $size)
{
    $offset = ($number - 1) * $size;

    if (isset($category_id)) {
        $stmt = get_global_or_new_statement('select_user_statement',
            'select course.id, course.name, category.name as category_name, lesson_count, signed_users from course left join category on course.id_category = category.id left join (select count(*) as lesson_count, id_course, (select count(*) from passed_lesson where id_lesson = min(t1.id)) as signed_users from lesson as t1 group by id_course) as l on l.id_course = course.id where id_category=? and  REGEXP_LIKE(COURSE.name,?) order by course.id desc limit ?,?');
        $stmt->bind_param('isii', $category_id, $text, $offset, $size);
    } else {
        $stmt = get_global_or_new_statement('select_user_statement',
            'select course.id, course.name, category.name as category_name, lesson_count, signed_users from course left join category on course.id_category = category.id left join (select count(*) as lesson_count, id_course, (select count(*) from passed_lesson where id_lesson = min(t1.id)) as signed_users from lesson as t1 group by id_course) as l on l.id_course = course.id where REGEXP_LIKE(COURSE.name,?) order by course.id desc limit ?,?');
        $stmt->bind_param('sii', $text, $offset, $size);
    }

    $stmt->execute();

    $stmt->bind_result($id, $name, $category_name, $lesson_count, $users_count);

    $courses = [];

    while ($stmt->fetch()) {
        $course = new stdClass();
        $course->id = $id;
        $course->name = $name;
        $course->lesson_count = $lesson_count;
        $course->creators = [];
        $course->users_count = $users_count;
        $courses[] = $course;
    }
    $stmt->close();
    get_creator_course_list($courses);
    return $courses;
}

function get_course_subjects($course)
{
    $conn = get_global_or_new_connection();
    $sql = 'select name from lesson  where id_course=' . $course->id.' limit 1,2000';

    if (!$result = $conn->query($sql)) {
        echo $conn->error;
    }

    $course->lessons = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $course->lessons[] = $row['name'];
        }
    }
}

function get_course_by_id($course_id)
{
    $stmt = get_global_or_new_statement('select_user_statement',
        'select course.id, course.name, course.id_category, category.name as category_name, lesson_count, signed_users from course left join category on course.id_category = category.id left join (select count(*) as lesson_count, id_course, (select count(*) from passed_lesson where id_lesson = min(t1.id)) as signed_users from lesson as t1 group by id_course) as l on l.id_course = course.id where course.id=?');

    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $stmt->bind_result($id, $name, $id_category, $category_name, $lesson_count, $users_count);

    $category = new stdClass();
    $category->courses = [];

    $stmt->fetch();
    $course = new stdClass();
    $course->id = $id;
    $course->name = $name;
    $category->id = $id_category;
    $category->name = $category_name;
    $course->lesson_count = $lesson_count - 1;
    $course->users_count = $users_count;
    $course->creators = [];
    $category->courses[] = $course;

    $stmt->close();
    get_creator_course_list($category->courses);
    get_course_subjects($category->courses[0]);
    return $category;
}

function apply_course($username, $course_id)
{
    if (!is_applied_course($username, $course_id)) {
        $stmt = get_global_or_new_statement('create_user_statement',
            'insert into passed_lesson(id_user, id_lesson, pass_date) values((select id from user where username=?), (select id from lesson where id_course=? order by id limit 1), now())');

        $stmt->bind_param('si', $username, $course_id);
        $stmt->execute();
        $stmt->close();
    }
    return null;
}

function is_applied_course($username, $course_id)
{
    $stmt = get_global_or_new_statement('create_user_statement',
        'select * from passed_lesson where id_user = (select id from user where LCASE(username)=LCASE(?)) and id_lesson = (select id from lesson where id_course=? order by id limit 1)');
    $stmt->bind_param('si', $username, $course_id);
    $stmt->execute();
    $stmt->store_result();

    $stmt->bind_result($u, $l, $passdate);
    $stmt->fetch();
    $applied = $stmt->num_rows > 0;
    $stmt->close();
    return $applied;
}