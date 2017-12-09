(function ($) {

    /**
     * Object Flash
     */
    $.flash = {

		/**
		 * Container
		 */
		target: null,

		/**
		 * Show Error Message
		 *
		 * @param String msg
		 * @param Mixing cb
		 */
		error: function (msg, cb) {
			$.flash.display('alert', msg, cb);
		},

		/**
		 * Show Valid Message
		 *
		 * @param String msg
		 * @param Mixing cb
		 */
		valid: function (msg, cb) {
			$.flash.display('success', msg, cb);
		},

		/**
		 * Show Info Message
		 *
		 * @param String msg
		 * @param Mixing cb
		 */
		info: function (msg, cb) {
			$.flash.display('info', msg, cb);
		},

		/**
		 * Show Warning Message
		 *
		 * @param String msg
		 * @param Mixing cb
		 */
		warning: function (msg, cb) {
			$.flash.display('warning', msg, cb);
		},

		/**
		 *
		 * @param String type
		 * @param String msg
		 * @param Mixing cb
		 */
		display: function (type, msg, cb) {
			var tmp_id = Math.floor(Math.random() * 11);
            var delay = 7000;
            var element = '';
            var script = '';
			if (cb !== undefined) {
				if (typeof cb === 'function') {
					element	= '<div id="alert-id-' + tmp_id + '" data-alert class="alert-box radius ' + type + '"><i class="mdi mdi-alert-box"></i><i class="mdi mdi-checkbox-market"></i><i class="mdi mdi-close-octagon"></i><i class="mdi mdi-bell"></i>' + msg + '<a href="#" class="close">&times;</a></div>';
					script	= '<script type="text/javascript">$("#alert-id-' + tmp_id + '").delay(' + 7000 + ').fadeOut(500);</script>';
					$.cookie('flash_message', element + script, { path: '/' });
					setTimeout(function () { cb(); }, 100);
					return;
				} else {
					delay	= cb;
				}
			}
			$.flash.clear();
			element	= '<div id="alert-id-' + tmp_id + '" data-alert class="alert-box radius ' + type + '"><i class="mdi mdi-alert-box"></i><i class="mdi mdi-checkbox-market"></i><i class="mdi mdi-close-octagon"></i><i class="mdi mdi-bell"></i>' + msg + '<a href="#" class="close">&times;</a></div>';
			script	= '<script type="text/javascript">if(' + delay + ' > 0) { $("#alert-id-' + tmp_id + '").delay(' + delay + ').fadeOut(500); } else { $("#alert-id-' + tmp_id + '").show(); }</script>';
			$($.flash.target + ':first').append(element + script);
		},

		/**
		 * Clear all flash
		 */
		clear: function () {
			$($.flash.target).empty();
		},

		/**
		 * Show input error
		 * @param JQuery Object object
		 */
		input: function (object, timeout) {
			var elem = object;
			setTimeout(function () {
                elem.attr('data-invalid', '').attr('aria-invalid', 'true').parent('div').addClass('error'); elem.parents('form:first').attr('data-invalid', '');
			}, (timeout > 0) ? timeout : 700);
		},

		/**
		 * Remove input error
		 * @param JQuery Object object
		 */
		removeInput: function (object, timeout) {
			var elem = object;
			setTimeout(function () {
                elem.removeAttr('data-invalid').removeAttr('aria-invalid').parent('div').removeClass('error');
			}, (timeout > 0) ? timeout : 700);
		},

		/**
		 * Initialize
		 */
		initialize: function (element) {
			$.flash.target = element;
			if ($.cookie('flash_message')) {
				$($.flash.target + ':first').append($.cookie('flash_message'));
				$.removeCookie('flash_message', { path: '/' });
			}
		}

	};

    // Init pulugin
    $.flash.initialize('#flash-message');

}(jQuery));

/***
 * Close message
 */
$(function () {
    $('body').on('click', '#flash-message .close', function (e) {
        e.preventDefault();
		$(this).parents('.alert-box:first').hide();
    });
});
