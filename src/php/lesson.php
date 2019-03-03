<?php
include_once('../common/user-service.php');
if (!is_authorised()) {
    header('Location: /');
    exit();
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="logo.png"/>
    <title>Forlea</title>
    <script src="js/lib/jquery.js"></script>
    <link rel="stylesheet" href="css/login.css">
    <script src="js/auth.js"></script>
    <link rel="stylesheet" href="css/description.css">
    <link rel="stylesheet" href="css/lesson.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/search.js"></script>
    <script src="js/lesson.js"></script>
    <script src="js/main.js"></script>
</head>
<body>
<div class="main container">
    <div class="header">

        <div class="logo-container">
            <a href="/" class="logo">
                Forlea
            </a>
        </div>

        <div class="select sphere">
            <div class="select-header">Темы</div>
            <div class="sphere-list hidden">
                <?php
                include_once('../common/db-repository.php');
                $categories = get_categories();

                for ($i = 0; $i < sizeof($categories); $i++) {
                    print '<a class="sphere-item" href="/catalog.php?category=' . $categories[$i]->id . '">' . $categories[$i]->name . '</a>';
                }
                ?>
            </div>

        </div>

        <div class="search"><input type="text"><select>
                <option selected disabled>Категория</option>
                <?php
                for ($i = 0; $i < sizeof($categories); $i++) {
                    print '<option value="' . $categories[$i]->id . '">' . $categories[$i]->name . '</option>';
                }
                ?>
            </select>
            <button></button>
        </div>

        <?php
        include_once('../common/user-service.php');
        if (is_authorised()) {
            print '<div class="profile"><a href="profile.php">Профиль</a></div>';
            print '<div class="logout"><a onclick="logout()">Выйти</a></div>';
        } else {
            print '<div class="login"><a href="login.html">Войти</a></div>';
            print '<div class="register"><a href="login.html">Регистрация</a></div>';
        }
        ?>
    </div>
    <div class="content">
        <div class="lesson left list">
            <?php
            include_once('../common/lesson-service.php');
            include_once('../common/file_service.php');

            function print_lesson($lesson)
            {
                print '<input id="lesson_id" type="hidden" value="' . $lesson->id . '"/>';
                print '<div class="lesson-header">' . $lesson->name . '</div>';

                $content = array_values(array_filter($lesson->links, function ($link) {
                    return $link->type === 'content';
                }));

                for ($i = 0; $i < count($content); $i++) {
                    print '<div class="">' .
                        (explode('/', $content[$i]->value)[0] === 'video' ?
                            '<video controls class="video-content"><source src="' . file_prefix() . $content[$i]->value . '"></video>' :
                            '<div class="readable-content">' . file_get_contents(get_file_storage_path() . $content[$i]->value) . '</div>') . '</div>';
                }

                $description = array_values(array_filter($lesson->links, function ($link) {
                    return $link->type === 'description';
                }));

                if (!empty($description)) {
                    if (!empty($description[0]->name)) {
                        print '<div class="lesson-header">' . $description[0]->name . '</div>';
                    }
                    print '<div class="description">' . file_get_contents(get_file_storage_path() . $description[0]->value) . '</div>';
                }


                $additional = array_values(array_filter($lesson->links, function ($link) {
                    return $link->type === 'additional_material';
                }));
                if (!empty($additional)) {
                    print '<div class="lesson-header">Дополнительные ссылки:</div>';
                    for ($i = 0; $i < count($additional); $i++) {
                        print '<div><a href="' . $additional[$i]->value . '">' . $additional[$i]->name . '</a></div>';
                    }
                }

                print '<div><form class="comment-wrapper"><input type="text" placeholder="Оставьте свой комментарий"><button class="comment-send">&gt;</button></form></div>';

                for ($i = 0; $i < count($lesson->comments); $i++) {
                    print '<div class="comment">';
                    print '<div class="user">' . $lesson->comments[$i]->user . '</div>';
                    print '<div class="date">' . $lesson->comments[$i]->date . '</div>';
                    print '<div class="text">' . $lesson->comments[$i]->message . '</div>';
                    print '</div>';
                }
            }

            if (isset($_GET['lesson']) && isset($_GET['course'])) {
                $lesson = get_lesson($_GET['course'], $_GET['lesson']);
            } else if (isset($_GET['lesson_id'])) {
                $lesson = get_lesson_by_id($_GET['lesson_id']);
            }

            $lessons = get_lessons($lesson->course, get_username());

            for ($i = 0; $i < count($lessons); $i++) {
                print '<div class="list-item '
                    . ($lessons[$i]->passed ? 'visited ' : '')
                    . ($lessons[$i]->id === $lesson->id ? 'active ' : '')
                    . '"><a href="/lesson.php?lesson_id=' . $lessons[$i]->id . '">' . $lessons[$i]->name . '</a></div>';
            }
            ?>
        </div>
        <div class="lesson right">
            <?php
            if (!empty($lesson)) {
                print_lesson($lesson);
            }
            ?>
        </div>
    </div>
    <div class="footer">
        <div class="col">
            <div>
                <a href="" class="logo">
                    ForLea
                </a>
            </div>

            <div class="copyright">&copy; Forlea Inc., 2019 Все права защищены.</div>
        </div>
        <div class="col">
            <div class="description">
                Some description for Forlea site, which helps people to gain knowledge in different spheres
            </div>
        </div>
        <div class="col">
            <div class="catalog-item">
                <a href="catalog.php">Каталог</a>
            </div>
        </div>
        <div class="col">
            <?php
            for ($i = 0; $i < sizeof($categories); $i++) {
                print '<div class="catalog-item"><a href="/catalog.php?category=' . $categories[$i]->id . '">' . $categories[$i]->name . '</a></div>';
            }
            ?>
        </div>
        <div class="col">
            <?php
            if (is_authorised()) {
                print '<div class="catalog-item profile"><a href="dashboard.html">Профиль</a></div>';
                print '<div class="catalog-item logout"><a href="logout.html">Выйти</a></div>';
            } else {
                print '<div class="catalog-item login"><a href="login.html">Войти</a></div>';
                print '<div class="catalog-item register"><a href="login.html">Регистрация</a></div>';
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>
