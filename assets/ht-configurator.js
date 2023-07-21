jQuery(document).ready(function($) {

   var configurator = $('.dtc-wrapper');
   var form = configurator.find('form.ht-configurator');

   htc_form_change();

   // Listen for changes in any input field within the form and trigger 'submit' event
   form.find('input, select').on('change select', function() {
      // form.trigger('submit');
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
            configurator.find('.dtc-image').attr('src', response.data.image_url)
         } else {
            configurator.removeClass('htc-loading');
         }

         // set price
         if(response.data.image_url) {
            configurator.find('.dtc-total-price').text(response.data.price)
         }

      });
   }

   $('img.dtc-image').on('load', function() {
      configurator.removeClass('htc-loading');
   })

});