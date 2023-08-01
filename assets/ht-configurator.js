jQuery(document).ready(function($) {


	var configurator = $('.htc-wrapper');
	var form = configurator.find('form.ht-configurator');

	$('.htc-right-column').show();


	htc_resize();
	htc_form_change();



	// Listen for changes in any input field within the form
	form.find('.htc-options-wrapper input:not([type="text"]), .htc-options-wrapper select').on('change select', function() {
		htc_form_change();
	});

	form.find('button.htc-apply-coupon').on('click', function(e) {
		e.preventDefault();
		htc_form_change();
	});

	function htc_form_change() {
		var form_fields = {};
		$.each(form.serializeArray(), function (index, item) {
			// Initialize as an empty array if undefined
			if (!form_fields[item.name]) {
				form_fields[item.name] = [];
			}

			form_fields[item.name].push(item.value);
		});

		configurator.addClass('htc-loading');

		var data = {
			action: 'htc_form_change', 
			form_fields: form_fields,
		};

		$.post(
			ht_configurator.ajaxurl, 
			data, 
			function () {}
		)
		.always(function () {
			// configurator.removeClass('htc-loading');
		})
		.done(function (response) {
			// load image
			if(response.data.image_url) {
				configurator.find('.htc-image').attr('src', response.data.image_url).attr('data-image-id', response.data.image_id);
			} else {
				configurator.removeClass('htc-loading');
			}

			// set price
			if(response.data.price) {
				configurator.find('.htc-total-price').text(response.data.price)
				configurator.find('input[name="price"]').val(response.data.price)
			}

			// set coupon message
			if(response.data.coupon_message) {
				configurator.find('.htc-coupon-message').text(response.data.coupon_message)
			} else {
				configurator.find('.htc-coupon-message').text('')
			}

		});
	}

	$('img.htc-image').on('load', function() {
		configurator.removeClass('htc-loading');
	})

	$(window).on('resize', function() {
		htc_resize();
	})

	$('.htc-fieldset-popup-open').on('click', function(e) {
		e.preventDefault();
		$(this).closest('fieldset').find('.htc-fieldset-popup').show();
	})

	$('.htc-fieldset-popup-ok').on('click', function(e) {
		e.preventDefault();
		$(this).closest('.htc-fieldset-popup').hide();
	})









	function htc_resize() {
		var element_top = configurator.offset().top;
		var viewport_height = $(window).height();
		var distance_to_screen_bottom = viewport_height - element_top;
		configurator.height(distance_to_screen_bottom);
	} 





	// Listen for changes in any input field within the form and trigger 'submit' event
	configurator.find('button.htc-submit').on('click', function(e) {
		e.preventDefault();

		configurator.find('.htc-form-error-message').text('')
		configurator.find('.htc-form-success-message').text('')

		var form_fields = {};
		$.each(form.serializeArray(), function (index, item) {
			// Initialize as an empty array if undefined
			if (!form_fields[item.name]) {
				form_fields[item.name] = [];
			}

			form_fields[item.name].push(item.value);
		});

		configurator.addClass('htc-loading');

		var data = {
			action: 'htc_form_submit', 
			form_fields: form_fields,
			image_id: configurator.find('.htc-image').attr('data-image-id')
		};

		$.post(
			ht_configurator.ajaxurl, 
			data, 
			function () {}
		)
		.always(function () {
			// configurator.removeClass('htc-loading');
		})
		.done(function (response) {
			configurator.removeClass('htc-loading');

			if(response.data.message && !response.success) {
				configurator.find('.htc-form-error-message').text(response.data.message)
			}

			if(response.data.message && response.success) {
				configurator.find('.htc-form-success-message').text(response.data.message)
				configurator.find('.htc-submit').addClass('disabled')
			}

			// Scroll the .htc-right-column to the bottom
			var right_column = $('.htc-right-column');
			right_column.scrollTop(right_column[0].scrollHeight);


		});
	});

});