$(function () {
    const sphereList = $('.sphere-list');
    $('.select-header').click(function () {
        sphereList.toggleClass('hidden');
    });
});

function engage() {
    const authDialog = $('.dialog-background');
    const isLogged = !$('.header .login').length;

    if (isLogged) {
        console.log('applied');
        const url_string = window.location.href;
        const url = new URL(url_string);
        const course = url.searchParams.get("course");
        fetch('/api/apply.php', {
            method: 'POST',
            body: JSON.stringify({course})
        })
            .then(() => {
                window.location.href ='/lesson.php?course=' + course + '&lesson=1';
            });
    } else {
        authDialog.show();
        $(document).on('keydown.login', function (ev) {
            if (ev.key === 'Escape') {
                authDialog.hide();
            }
            $(document).off('keydown.login');
        });
        ev.preventDefault();
    }
}