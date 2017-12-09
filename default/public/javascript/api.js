(function ($) {

    $.api = {

        url: '',

        lastError: { },

        get: function (source) {
            return $.api._send(source, 'GET');
        },

        post: function (source, data) {
            return $.api._send(source, 'POST', data);
        },

        update: function (source, data) {
            return $.api._send(source, 'PUT', data);
        },

        destroy: function (source) {
            return $.api._send(source, 'DELETE');
        },

        _send: function (source, type, params) {

            var _this = this;

            _this.lastError = {};

            //Url
            var url = $.api.url + source;

            // Config
            var prop = {
                'url': url,
                'async': true,
                'dataType': 'json',
                'type': type.toUpperCase(),
                'data': (type === 'GET') ? undefined : params,
                beforeSend: function (req) {
                    req.setRequestHeader('x-token-auth', window.localStorage.getItem('token') || '');
                }
            };

            //Request
            var request = $.ajax(prop);

            request.fail(function (xhr, text) {

                if (xhr.status === 0) { // Aborted

                    // Don't make anything
                    _this.lastError = {};
                } else if (xhr.statusCode().status === 401) {

                    // Show session error
                    $.flash.error('Tu sesión ha expirado');
                    throw new Error('Invalid token');

                } else if (xhr.statusCode().status === 403) {

                    // Show ACL error
                    $.flash.error('Tu no tienes los permisos para acceder al recurso.');
                    throw new Error('Forbidden');
                } else {

                    // Set error
                    _this._setError(source, xhr, text);
                }

            }).always(function () {

                $('body').css('cursor', 'default');

            });

            return request;

        },

        _setError: function (endpoint, xhr, text) {
            var data = {
                'statusCode': xhr.statusCode().status,
                'error': text,
                'endpoint': endpoint
            };

            var error = (!xhr.responseJSON) ? data : $.extend(data, xhr.responseJSON);
            if (error.error === 'error') {
                error.error = 'Se ha producido un error al conectar con el servidor. Por favor intenta de nuevo';
            }
            this.lastError = error;
            return this.lastError;
        }

    };

    var src = $('script:last').attr('src');
    $.api.url = src.substr(0, src.length - 37) + 'api/';

}(jQuery));

