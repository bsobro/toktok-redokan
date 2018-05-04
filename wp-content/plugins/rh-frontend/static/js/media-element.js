(function ($) {
	$.fn.wp_media_lib_element = function (options) {
		var main_class = 'wpfepp-media',
			main_selector = '.' + main_class;

		if (!this.hasClass(main_class)) {
			this.addClass(main_class);
		}

		var Components = function ($context) {
			var $main_element = $context.closest(main_selector);
			this.select_link = $main_element.find('.wpfepp-media-select');
			this.clear_link = $main_element.find('.wpfepp-media-clear');
			this.preview_container = $main_element.find('.wpfepp-media-preview');
			this.input = $main_element.find('input');
		};

		var $components = new Components(this);
		
		if( $components.preview_container.html() != '' )
			$components.clear_link.show();

		$components.clear_link.click(function (e) {
			e.preventDefault();
			var $components = new Components($(this));

			$components.preview_container.html('');
			$components.input.val('');
			$components.input.change();
			$components.clear_link.hide();
		});

		$components.select_link.click(function (e) {
			e.preventDefault();
			var $components = new Components($(this)),
				ids,
				settings = $.extend(
					{
						multiple: false,
						attribute: 'id',
						title: '',
						buttonText: ''
					},
					options
				),
				media_frame = wp.media.frames.file_frame = wp.media({
					title: settings.title,
					button: {
						text: settings.buttonText
					},
					multiple: settings.multiple
				});

			media_frame.on('open', function () {
				if ($components.input.val() == '' || settings.attribute != 'id')
					return;

				ids = (settings.multiple) ? $components.input.val().split(',') : [$components.input.val()];
				ids.forEach(function (id) {
					var attachment = wp.media.attachment(id);
					media_frame.state().get('selection').add(attachment ? [attachment] : []);
				});
			});

			media_frame.on('select', function () {
				var media_objects = media_frame.state().get('selection').toJSON();
				$components.preview_container.html('');
				media_objects.forEach(function (item) {

					if (typeof item.sizes != 'undefined') {
						item = item.sizes.thumbnail;
						item.iconurl = item.url;
					} else {
						item.iconurl = item.icon;
					}

					$('<img>')
						.attr('src', item.iconurl)
						.attr('alt', item.filename)
						.attr('title', item.title)
						.appendTo($components.preview_container);
				});

				var media_details = [];
				for (var i = 0; i < media_objects.length; i++) {
					media_details.push(media_objects[i][settings.attribute]);
				}
				var final_val = media_details.join(',');
				$components.input.val(final_val);
				$components.input.change();
			});
			media_frame.open();
			$components.clear_link.show();
		});

		return this;
	};
}(jQuery));