<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forlea</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/home.css">
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
            <div class="sphere-list">
                <?php
                include('../common/db-repository.php');
                $categories = get_categories();

                for ($i = 0; $i < sizeof($categories); $i++) {
                    print '<a class="sphere-item">' . $categories[$i] . '</a>';
                }
                ?>
            </div>

        </div>

        <div class="search"><input type="text">
            <button></button>
        </div>

        <?php
        include('../common/user-service.php');
        if (is_authorised()) {
            print '<div class="profile"><a href="dashboard.html">Профиль</a></div>';
            print '<div class="logout"><a href="logout.html">Выйти</a></div>';
        } else {
            print '<div class="login"><a href="login.html">Войти</a></div>';
            print '<div class="register"><a href="login.html">Регистрация</a></div>';
        }
        ?>

    </div>
    <div class="content">
        <div class="overload-learning">
            <a href="">Overload Learning</a>
        </div>
        <!--a big big description-->
        <!--....................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................-->
        <!--todo: add photo with something high intelligence and link to catalog-->
        <!--todo: add some screen shots and description -->

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
                print '<div class="catalog-item"><a href="">' . $categories[$i] . '</a></div>';
            }
            ?>
        </div>
        <div class="col">
            <?php
            if (is_authorised()) {
                print '<div class="catalog-item"><a href="dashboard.html">Профиль</a></div>';
                print '<div class="catalog-item"><a href="logout.html">Выйти</a></div>';
            } else {
                print '<div class="catalog-item"><a href="login.html">Войти</a></div>';
                print '<div class="catalog-item"><a href="login.html">Регистрация</a></div>';
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>