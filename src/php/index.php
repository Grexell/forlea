<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="logo.png" />
    <title>Forlea</title>
    <script src="js/lib/jquery.js"></script>
    <link rel="stylesheet" href="css/login.css">
    <script src="js/auth.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/home.css">
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
        <div class="overload-learning">
            <a href="catalog.php">Overload Learning</a>
        </div>
        <div class="description-line">
            <div>
                <div class="text">Просматривайте интересные и познавательные уроки на самые разнообразные тематики.
                    Более&nbsp;1000&nbsp;квалифицированных преподавателей
                </div>
            </div>
        </div>

        <div class="description-line">
            <div>
                <div class="text">Делитесь мнением о пройденном уроке, общайтесь с создателями и участниками курсов.
                    Количество видеоуроков&nbsp;-&nbsp;7000+
                </div>
            </div>
        </div>
        <div class="description-line">
            <div>
                <div class="text">Проходите испытания для закрепления знаний по пройденным материалам</div>
            </div>
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