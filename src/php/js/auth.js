const AUTH_TEMPLATE = '<div class="dialog-background">\n' +
    '    <div class="dialog">\n' +
    '        <div class="left login-form">\n' +
    '            <form action="login">\n' +
    '                <div class="input-wrapper title">\n' +
    '                    Вход\n' +
    '                </div>\n' +
    '                <div class="input-wrapper error">\n' +
    '                </div>\n' +
    '                <div class="input-wrapper">\n' +
    '                    <input type="text" name="username" placeholder="Username">\n' +
    '                </div>\n' +
    '                <div class="input-wrapper">\n' +
    '                    <input type="password" name="password" placeholder="password">\n' +
    '                </div>\n' +
    '                <div class="input-wrapper">\n' +
    '                <button>Войти</button>\n' +
    '                </div>\n' +
    '            </form>\n' +
    '        </div>\n' +
    '        <div class="right register-form">\n' +
    '            <form action="login">\n' +
    '                <div class="input-wrapper title">\n' +
    '                    Регистрация\n' +
    '                </div>\n' +
    '                <div class="input-wrapper error">\n' +
    '                </div>\n' +
    '                <div class="input-wrapper">\n' +
    '                    <input type="text" name="username" placeholder="Username">\n' +
    '                </div>\n' +
    '                <div class="input-wrapper">\n' +
    '                    <label class="file-input">Выберите изображение<input type="file" name="image"></label>\n' +
    '                </div>\n' +
    '                <div class="input-wrapper">\n' +
    '                    <input type="password" name="password" placeholder="Password">\n' +
    '                </div>\n' +
    '                <div class="input-wrapper">\n' +
    '                    <input type="password" placeholder="Repeat password">\n' +
    '                </div>\n' +
    '                <div class="input-wrapper">\n' +
    '                <button>Зарегистрироваться</button>\n' +
    '                </div>\n' +
    '            </form>\n' +
    '        </div>\n' +
    '    </div>\n' +
    '</div>';


$(initLogin);

function initLogin() {
    document.body.innerHTML += AUTH_TEMPLATE;
    const authDialog = $('.dialog-background');

    authDialog.hide();

    $('.login, .register').on('click', function (ev) {
        authDialog.show();
        $(document).on('keydown.login', function (ev) {
            if (ev.key === 'Escape') {
                authDialog.hide();
            }
            $(document).off('keydown.login');
        });
        ev.preventDefault();
    });

    authDialog.on('click', function (ev) {
        if (ev.target === this) {
            authDialog.hide();
        }
    });

    function login(username, password) {
        fetch('/api/auth.php', {
            method: 'POST',
            body: JSON.stringify({username, password})
        })
            .then(value => value.json())
            .then(value => {
                if (value) {
                    onAuthorize();
                } else {
                    $('.login-form .error').text('Логин или пароль неверный');
                }
            })
    }

    function onAuthorize() {
        authDialog.remove();
        $('.header .login')[0].outerHTML = '<div class="profile"><a href="profile.php">Профиль</a></div>';
        $('.header .register')[0].outerHTML = '<div class="logout"><a onclick="logout()">Выйти</a></div>';
        $('.footer .login')[0].outerHTML = '<div class="catalog-item profile"><a href="profile.php">Профиль</a></div>';
        $('.footer .register')[0].outerHTML = '<div class="catalog-item logout"><a onclick="logout()">Выйти</a></div>';
    }

    $('.login-form form').on('submit', function (ev) {
        login(this.username.value, this.password.value);
        ev.preventDefault();
    });
    
    $('.register-form form').on('submit', function (ev) {
        var formData = new FormData();

        formData.append("username", this.username.value);
        formData.append("password", this.password.value);

        formData.append("image", this.image.files[0]);

        var request = new XMLHttpRequest();
        request.open('POST', '/api/register.php');
        request.onload = () => {
            const response = JSON.parse(request.response);
            if (Array.isArray(response)) {
                $('.register-form .error').html(response.join('<br/>'));
            } else if (response) {
                onAuthorize();
            }
        };
        request.send(formData);
        ev.preventDefault();
    })
}

function logout() {
    fetch('/api/logout.php', {
        method: 'POST'
    }).then(value => {
        $('.header .profile')[0].outerHTML = '<div class="login"><a href="login.html">Войти</a></div>';
        $('.header .logout')[0].outerHTML = '<div class="register"><a href="login.html">Регистрация</a></div>';
        $('.footer .profile')[0].outerHTML = '<div class="catalog-item login"><a href="login.html">Войти</a></div>';
        $('.footer .logout')[0].outerHTML = '<div class="catalog-item register"><a href="login.html">Регистрация</a></div>';

        initLogin();

        if (window.location.pathname === '/profile.php' || window.location.pathname === '/lesson.php') {
            window.location.href ='/';
        }
    });
    return false;
}