var base_url = $('#languagebase_url').val();
var CSRF_TOKEN = $('#csrf_token').val();
"use strict";

$("#languageForm").on('submit',function(e){
    e.preventDefault();
    var submit_url = base_url+"dashboard/language/addlanguage";
    $.ajax({
        type: 'POST',
        url: submit_url,
        data: $(this).serialize() + "&csrf_test_name=" + CSRF_TOKEN,
        success: function(res) {

            if(res==='1'){
                toastr.success('Add Language Successfull');
            }else{
                toastr.error('Error! Please try again');
            }

            $("#langlist").load(location.href+" #langlist>*","");

        },error: function() {

        }
    });
});


// submit form and add data
"use strict";
$("#phraseForm").on('submit',function(e){
    e.preventDefault();

    var submit_url = base_url+"dashboard/language/addPhrase";

    $.ajax({
        type: 'POST',
        url: submit_url,
        data: $(this).serialize() + "&csrf_test_name=" + CSRF_TOKEN,
        success: function(res) {

            if(res==='1'){
                toastr.success('Add Phrase Successfull');
            }else if(res==='2'){
                toastr.error('Error! Phrase already exists');
            }else{
                toastr.error('Error! Please try again');
            }
            $("#langlist").load(location.href+" #langlist>*","");
            
        },error: function() {

        }
    });

});


// Counts and limit for invoice
var count = 2;
var limits = 500;
var wrapper = $('.add_input');

//Add Invoice Field
"use strict";
function addInputFieldPhrash(){
     var html = 
                '<div class="input-group mb-3">'+
                '<label for="country_name" class="col-form-label">Add new phrase *</label>'+
                
                    '<div class="input-group">'+
                      '<input name="phrase[]" type="text" class="form-control rounded-0" id="addphrase" placeholder="Add phrase name" required="">'+
                      ' <button class="btn btn-info btn-flat remove_button"><i class="fas fa-trash"></i></button>'+
                    
                '</div>';
    $(wrapper).append(html);
    count++;
}

//Delete a row 
"use strict";
$(wrapper).on('click', '.remove_button', function(e){
    e.preventDefault();
    $(this).parent().parent('div').remove();
    x--; 
});