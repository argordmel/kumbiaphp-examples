$(document).foundation();

$(function () {
    $('body').removeClass('is-loading');
});

//
// LOGIN
//
$('body').on('submit', '.form-login', function (e) {
    e.preventDefault();
    var data = $(this).serializeJSON();
    $.api.post('auth/login', data).done( function (result) {
        var usuario = result.data || {};
        if (!usuario.token) {
            $.flash.error('No se ha podido establecer la información de la autenticación.');
            return false;
        }
        window.localStorage.setItem('email', usuario.email);
        window.localStorage.setItem('nombre', usuario.nombre);
        window.localStorage.setItem('fotografia', usuario.fotografia);
        window.localStorage.setItem('token', usuario.token);
        window.location.href = 'home';
        return false;
    }).fail( function () {
        $.flash.error($.api.lastError.error);
    });
    return false;
});
