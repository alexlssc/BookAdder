jQuery(document).ready(function($) {

    $('.header_logo').on('input', function(e) {
      var stringUrl = $('.header_logo');
      if(stringUrl.val().length == 0){
        $(".header_logo").css("display", "None");
        console.log('0');
      } else {
        $(".header_logo").css("display", "block");
        console.log('0');
      }
    });


    $('.header_logo_upload').click(function(e) {
        e.preventDefault();

        var custom_uploader = wp.media({
            title: 'Custom Image',
            button: {
                text: 'Upload Image'
            },
            multiple: false  // Set this to true to allow multiple files to be selected
        })
        .on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('.header_logo').attr('src', attachment.url);
            $('.header_logo_url').val(attachment.url);

        })
        .open();
    });
});
