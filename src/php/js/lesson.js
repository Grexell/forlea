$(function () {
    function pass() {
        const lesson_id = $('#lesson_id').val();

        fetch('/api/pass.php', {
            method: 'post',
            body: JSON.stringify({
                lesson_id
            }),
        }).then(value => {
        });
    }

    $('.comment-wrapper').on('submit', function (ev) {
        const text = $('.comment-wrapper input').val();
        const lesson_id = $('#lesson_id').val();
        const d = new Date();
        if (text) {
            fetch('/api/comment.php', {
                method: 'post',
                body: JSON.stringify({
                    lesson_id,
                    text
                }),
            })
                .then(value => value.json())
                .then(value => {
                    if (value === true) {
                        const datestring = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2) +
                            " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);
                        $('.content .right .comment').before('<div class="comment"><div class="user">Вы</div>' +
                            '<div class="date">' + datestring + '</div>' +
                            '<div class="text">' + text + '</div>' +
                            '</div>');
                        $('.comment-wrapper input').val('');
                    }
                });
        }
        ev.preventDefault();
    });

    $('.video-content').on('ended', function () {
        pass();
    });

    const textContent = $('.readable-content');
    if (textContent.length) {
        pass();
    }
});