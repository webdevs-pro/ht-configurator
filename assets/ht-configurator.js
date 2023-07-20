jQuery(document).ready(function($) {

   var form = $('form.ht-configurator');

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

      form.addClass('htc-loading');

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
         form.removeClass('htc-loading');
      })
      .done(function (response) {
         console.log('response', response);

         if(response.data.image_url) {
            $(document).find('.dtc-image').attr('src', response.data.image_url)
         }

      });
   }

});