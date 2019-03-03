$(function () {
   $('.search button').on('click', function () {
       const text = $('.search input').val();
       const category = $('.search select').val();
       const query = '?' + (text ? 'text=' + text + '&' : '') + (category ? 'category=' + category + '' : '');
       window.location.href = '/catalog.php' + query;
   })
});