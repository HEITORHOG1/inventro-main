(function($){
 "use strict";   

     $(document).ready(function () {
        $('body').on('click', '#select_deselect', function () {
            $(".sameChecked").prop('checked', $(this).prop('checked'));
        });
        $('body').on('click', '.can_create_all', function () {
            var create_value = $(this).val();
            $("." + create_value + "_can_create").prop('checked', $(this).prop('checked'));
        });
        $('body').on('click', '.can_read_all', function () {
            var read_value = $(this).val();
            $("." + read_value + "_can_read").prop('checked', $(this).prop('checked'));
        });
        $('body').on('click', '.can_edit_all', function () {
            var edit_value = $(this).val();
            $("." + edit_value + "_can_edit").prop('checked', $(this).prop('checked'));
        });
        $('body').on('click', '.can_delete_all', function () {
            var delete_value = $(this).val();
            $("." + delete_value + "_can_delete").prop('checked', $(this).prop('checked'));
         });
    });

})(jQuery);


 "use strict"; 
var base_url = $('#base_url').val();
  $('#existrole').hide();
  var CSRF_TOKEN = $('#csrf_token').val();
  function userRole(id) {
        $.ajax({
            url:base_url+'menu/crole/user_role_check',
            type: 'post',
            data: {user_id: id ,'csrf_test_name': CSRF_TOKEN},

            success: function (r) {
              $('#existrole').show();
                r = JSON.parse(r);
                $("#existrole ul").empty();
                $.each(r, function (ar, typeval) {
                    if (typeval.role_name == 'Not Found') {
                        $("#existrole ul").html("Not Found!");
                        $("#exitrole ul").css({'color': 'red'});
                    } else {
                        $("#existrole ul").append('<li>' + typeval.role_name + '</li>');
                    }
                });
            }
        });
    }

