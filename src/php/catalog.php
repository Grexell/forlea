<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="logo.png" />
    <title>Каталог</title>
    <script src="js/lib/jquery.js"></script>
    <link rel="stylesheet" href="css/login.css">
    <script src="js/auth.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/catalog.css">
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
                include('../common/db-repository.php');
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
        include('../common/user-service.php');
        include_once('../common/file_service.php');
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

        function print_courses_with_title($courses)
        {
            for ($j = 0; $j < count($courses); $j++) {
                print '<div class="item">
                    <div>
                        <img src="' . file_prefix() . get_course_image_prefix() . $courses[$j]->id . '.png" alt="" class="course-image">
                    </div>
                    <div class="course-info">
                        <div class="course-name"><a href="/description.php?course=' . $courses[$j]->id . '">' . $courses[$j]->name . '</a></div>
                        <div class="course-creator-list">';
                for ($i = 0; $i < count($courses[$j]->creators); $i++) {
                    print '<div class="course-creator">' . $courses[$j]->creators[$i] . '</div>';
                }
                print '</div>
                        <div class="course-tag-list">
                            <div class="course-tag">#some long tag1</div>
                            <div class="course-tag">#some long tag2</div>
                            <div class="course-tag">#some long tag3</div>
                            <div class="course-tag">#some long tag4</div>
                            <div class="course-tag">#some long tag5</div>
                        </div>
                        <div class="course-lesson-counter">' . ($courses[$j]->lesson_count ? $courses[$j]->lesson_count : 0) . ' занятий</div>
                        <div class="course-applied-counter">' . ($courses[$j]->users_count ? $courses[$j]->users_count : 0) . ' пользователей записалось на курс</div>
                    </div>
                </div>';
            }

        }

        function print_category($category)
        {
            print '<div class="group-header"><a href="/catalog.php?category=' . $category->id . '">' . $category->name . '</a></div>';
            print_courses_with_title($category->courses);
        }

        function print_paginating($page_count, $current_page, $url)
        {
            print '<div class="pagination"> ';
            if ($current_page == 1) {
                print '<span>&laquo;</span> ';
            } else {
                print '<a href="' . $url . 'page=' . ($current_page - 1) . '">&laquo;</a> ';
            }
            if ($page_count <= 6) {
                for ($i = 1; $i <= $page_count; $i++) {
                    print '<a href="' . $url . 'page=' . $i . '"' . ($i == $current_page ? 'class="active"' : '') . '>' . $i . '</a> ';
                }
            } else {
                if ($current_page < 5) {
                    for ($i = 1; $i < 5; $i++) {
                        print '<a href="' . $url . 'page=' . $i . '"' . ($i == $current_page ? 'class="active"' : '') . '>' . $i . '</a> ';
                    }
                    print ' ... ';
                    print '<a href="' . $url . 'page=' . $page_count . '">' . $page_count . '</a> ';
                } else if ($current_page + 5 > $page_count) {
                    print '<a href="' . $url . 'page=1">1</a> ';
                    print ' ... ';
                    for ($i = $page_count - 4; $i <= $page_count; $i++) {
                        print '<a href="' . $url . 'page=' . $i . '"' . ($i == $current_page ? 'class="active"' : '') . '>' . $i . '</a> ';
                    }
                } else {
                    for ($i = $current_page - 4; $i <= $current_page; $i++) {
                        print '<a href="' . $url . 'page=' . $i . '"' . ($i == $current_page ? 'class="active"' : '') . '>' . $i . '</a> ';
                    }
                    print ' ... ';
                    print '<a href="' . $url . 'page=' . $page_count . '">' . $page_count . '</a> ';
                }
            }
            if ($current_page == $page_count) {
                print '<span>&raquo;</span> ';
            } else {
                print '<a href="' . $url . 'page=' . ($current_page + 1) . '">&raquo;</a> ';
            }
            print '</div>';
        }

        $page_size = isset($_GET['page_size']) ? $_GET['page_size'] : 15;
        $page_number = isset($_GET['page']) ? $_GET['page'] : 1;

        if (isset($_GET['text'])) {
            $category_id = isset($_GET['category']) ? $_GET['category'] : null;
            $count = get_course_by_text_category_count($_GET['text'], $category_id);
            $courses = get_all_courses_by_text_and_category_at_page($_GET['text'], $category_id, $page_number, $page_size);
            print '<div class="group-header">Найдено: ' . $count . ' результатов по запросу "' . $_GET['text'] . '"</div>';

            print_courses_with_title($courses);
            $page_count = ceil($count / $page_size);

            if (isset($category_id)) {
                print_paginating($page_count, $page_number, '?text=' . $_GET['text'] . '&category=' . $category_id . '&page_size=' . $page_size . '&');
            } else {
                print_paginating($page_count, $page_number, '?text=' . $_GET['text'] . '&page_size=' . $page_size . '&');
            }
        } else if (isset($_GET['category'])) {
            $category_id = $_GET['category'];
            $category = get_all_courses_by_category_at_page($category_id, $page_number, $page_size);
            print_category($category);
            $page_count = ceil(get_course_by_category_count($category_id) / $page_size);
            print_paginating($page_count, $page_number, '?category=' . $category_id . '&page_size=' . $page_size . '&');
        } else {
            $courses = get_all_courses(5);

            for ($i = 0; $i < count($courses); $i++) {
                print_category($courses[$i]);
            }
        }
        ?>

        <!--                <div class="group-header">-->
        <!--                    Some category-->
        <!--                </div>-->
        <!--                <div class="item">-->
        <!--                    <div>-->
        <!--                        <img src="asset/homepage_first.png" alt="" class="course-image">-->
        <!--                    </div>-->
        <!--                    <div class="course-info">-->
        <!--                        <div class="course-name"><a href="">Long course name</a></div>-->
        <!--                        <div class="course-creator-list">-->
        <!--                            <div class="course-creator">course creator</div>-->
        <!--                            <div class="course-creator">course creator</div>-->
        <!--                            <div class="course-creator">course creator</div>-->
        <!--                            <div class="course-creator">course creator</div>-->
        <!--                        </div>-->
        <!--                        <div class="course-tag-list">-->
        <!--                            <div class="course-tag">#some long tag1</div>-->
        <!--                            <div class="course-tag">#some long tag2</div>-->
        <!--                            <div class="course-tag">#some long tag3</div>-->
        <!--                            <div class="course-tag">#some long tag4</div>-->
        <!--                            <div class="course-tag">#some long tag5</div>-->
        <!--                        </div>-->
        <!--                        <div class="course-lesson-counter">10 lesson</div>-->
        <!--                        <div class="course-applied-counter">100 000</div>-->
        <!---->
        <!--                    </div>-->
        <!--                </div>-->


        <!--        <div class="group-header">-->
        <!--            Some category-->
        <!--        </div>-->
        <!--        <div class="item">-->
        <!--            <div>-->
        <!--                <img src="asset/homepage_first.png" alt="" class="course-image">-->
        <!--            </div>-->
        <!--            <div class="course-info">-->
        <!--                <div class="course-name"><a href="">Long course name</a></div>-->
        <!--                <div class="course-creator-list">-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                </div>-->
        <!--                <div class="course-tag-list">-->
        <!--                    <div class="course-tag">#some long tag1</div>-->
        <!--                    <div class="course-tag">#some long tag2</div>-->
        <!--                    <div class="course-tag">#some long tag3</div>-->
        <!--                    <div class="course-tag">#some long tag4</div>-->
        <!--                    <div class="course-tag">#some long tag5</div>-->
        <!--                </div>-->
        <!--                <div class="course-lesson-counter">10 lesson</div>-->
        <!--                <div class="course-applied-counter">100 000</div>-->
        <!---->
        <!--            </div>-->
        <!--        </div>-->
        <!---->
        <!--        <div class="item">-->
        <!--            <div>-->
        <!--                <img src="asset/homepage_first.png" alt="" class="course-image">-->
        <!--            </div>-->
        <!--            <div class="course-info">-->
        <!--                <div class="course-name"><a href="">Long course name</a></div>-->
        <!--                <div class="course-creator-list">-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                </div>-->
        <!--                <div class="course-tag-list">-->
        <!--                    <div class="course-tag">#some long tag1</div>-->
        <!--                    <div class="course-tag">#some long tag2</div>-->
        <!--                    <div class="course-tag">#some long tag3</div>-->
        <!--                    <div class="course-tag">#some long tag4</div>-->
        <!--                    <div class="course-tag">#some long tag5</div>-->
        <!--                </div>-->
        <!--                <div class="course-lesson-counter">10 lesson</div>-->
        <!--                <div class="course-applied-counter">100 000</div>-->
        <!---->
        <!--            </div>-->
        <!--        </div>-->
        <!---->
        <!--        <div class="item">-->
        <!--            <div>-->
        <!--                <img src="asset/homepage_first.png" alt="" class="course-image">-->
        <!--            </div>-->
        <!--            <div class="course-info">-->
        <!--                <div class="course-name"><a href="">Long course name</a></div>-->
        <!--                <div class="course-creator-list">-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                    <div class="course-creator">course creator</div>-->
        <!--                </div>-->
        <!--                <div class="course-tag-list">-->
        <!--                    <div class="course-tag">#some long tag1</div>-->
        <!--                    <div class="course-tag">#some long tag2</div>-->
        <!--                    <div class="course-tag">#some long tag3</div>-->
        <!--                    <div class="course-tag">#some long tag4</div>-->
        <!--                    <div class="course-tag">#some long tag5</div>-->
        <!--                </div>-->
        <!--                <div class="course-lesson-counter">10 lesson</div>-->
        <!--                <div class="course-applied-counter">100 000</div>-->
        <!---->
        <!--            </div>-->
        <!--        </div>-->
        <!---->
        <!--                <div class="pagination">-->
        <!--                    <a href="#">&laquo;</a>-->
        <!--                    <a href="#">1</a>-->
        <!--                    <a class="active" href="#">2</a>-->
        <!--                    <a href="#">3</a>-->
        <!--                    <a href="#">4</a>-->
        <!--                    ...-->
        <!--                    <a href="#">5</a>-->
        <!--                    <a href="#">6</a>-->
        <!--                    <a href="#">&raquo;</a>-->
        <!--                </div>-->
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