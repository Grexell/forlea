<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="logo.png" />
    <title>Forlea</title>
    <script src="js/lib/jquery.js"></script>
    <link rel="stylesheet" href="css/login.css">
    <script src="js/auth.js"></script>
    <link rel="stylesheet" href="css/description.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/search.js"></script>
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
        <?php
        include_once('../common/course-service.php');
        include_once('../common/file_service.php');
        if (isset($_GET['course'])) {
            $course_id = $_GET['course'];
            $category = get_course_by_id($course_id);
            $username = get_username();
            print '<div class="course-header">' . $category->courses[0]->name . '</div>';
            print '<div class="course-subheader"><a href="catalog.php?category=' . $category->id . '">' . $category->name . '</a></div>';
            print '<div class="course-info"><div class="left"><div class="embed">' . file_get_contents(get_file_storage_path() . description_prefix() . $category->courses[0]->id) . '</div>';

            for ($i = 0; $i < count($category->courses[0]->lessons); $i++) {
                print '<div class="bordered">' . $category->courses[0]->lessons[$i] . '</div>';
            }

            print '</div>';
            print '<div class="right">'
                . '<img src="' . file_prefix() . get_course_image_prefix() . $category->courses[0]->id . '.png" alt="" class="course-image"><div class="lesson-count">Содержит '
                . ($category->courses[0]->lesson_count ? $category->courses[0]->lesson_count : 0)
                . ' уроков</div><div class="applied">Записалось '
                . ($category->courses[0]->users_count ? $category->courses[0]->users_count : 0)
                . ' учащихся</div>' .
                (is_authorised() ? (!is_applied_course($username, $course_id) ?
                    '<button class="apply" onclick="engage()">Записаться</button>' : '<div>Вы уже записались</div>') : '<button class="apply" onclick="engage()">Записаться</button>')
                . '</div></div>';
        }

        ?>

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