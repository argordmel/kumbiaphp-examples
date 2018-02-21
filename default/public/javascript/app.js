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
    $.api.post('auth/login', data).done(function (result) {
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
    }).fail(function () {
        $.flash.error($.api.lastError.error);
    });
    return false;
});

//
// USUARIOS
//

$(function () {
    if ($('.view-home').length > 0 ) {
        var container = $('.view-home table tbody');
        container.empty();
        $.api.get('usuarios').done( function (r) {
            var data = r.data || [];
            var counter = 1;
            $('.stats-content .stats-content__counter b').text(data.length);
            data.forEach( function (row) {
                var html = `<tr>
                            <td>${counter++}</td>
                            <td>${row.nombre}</td>
                            <td>${row.email}</td>
                            <td>${row.activo ? 'ACTIVO' : 'ELIMINADO'}</td>
                            <td class="table-tbody__actions">
                                <a href="#" class="button-edit-user" data-user="${row.id}" title="Editar Usuario" data-tooltip=>
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                                <a href="#" class="button-delete-user" data-user="${row.id}" title="Eliminar Usuario" data-tooltip>
                                    <i class="mdi mdi-delete"></i>
                                </a>
                            </td>
                        </tr>`;
                $('.view-home table tbody').append(html);
            });
        }).fail(function () {
            $.flash.error($.api.lastError.error);
        });
    }

    $('body').on('click', '.button--download', function (e) {
       e.preventDefault();

        var anchor = document.createElement("a");
        var file = $.api.url + '/reportes/usuarios/';
        var headers = new Headers();
        headers.append('x-token-auth', window.localStorage.getItem('token') || '');
        fetch(file, {headers})
            .then( (response, xhr) => {
                if (response.status !== 200) {
                    $.flash.error('Lo sentimos, pero el reporte no se ha podido generar');
                    return null;
                }
                return response.blob();
            })
            .then(blobby => {
                if (!blobby) {
                    return;
                }
                var objectUrl = window.URL.createObjectURL(blobby);
                anchor.href = objectUrl;
                anchor.download = 'reporte.xlsx';
                anchor.click();
                window.URL.revokeObjectURL(objectUrl);
            });


    });
});

