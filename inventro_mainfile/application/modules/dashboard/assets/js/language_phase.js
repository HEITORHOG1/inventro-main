var base_url = $('#languagebase_url').val();
"use strict";
var CSRF_TOKEN = $('#csrf_token').val();
function SaveData(id){

    var id = $('#id_'+id).val();

    var submit_url = base_url+"dashboard/language/addlebel";

        $.ajax({
            type: 'POST',
            url: submit_url,
            data: $("#addlebel"+id).serialize() + "&csrf_test_name=" + CSRF_TOKEN,
            success: function(res) {

                if(res==='1'){
                    toastr.success('Add Language Successfull');
                }else{
                    toastr.error('Error! Please try again');
                }

                $("#lislt1").load(location.href+" #lislt1>*","");


            },error: function() {

            }
        });

}