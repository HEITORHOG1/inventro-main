
var baseurl=$("#mainsiteurl").val();
var addstring=$("#adddptstring").val();
var updatestring=$("#updatestring").val();
var CSRF_TOKEN = $('#csrf_token').val();
"use strict";
	function addDepartment(){
	    var save_method = 'save_department';
	    
	    $("#dip").attr("action", baseurl+"hrm/department/save_department");
	    $('.modal-title').text('Add Department'); 
	    $('#department_form').modal('show'); 
	    $("#department").val('');
	    $("#description").val('');
	    $('.modal-title').text('Add Department');  
	}
    "use strict";
	function editDepartment(department_id)
	{

			var submit_url = baseurl+"hrm/department/edit_department";
			var dataString = "department_id="+department_id;
            
			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {


					var data = JSON.parse(res);


					$("#department").val(data.department_name);
					$("#description").val(data.department_description);
					$("#department_id").val(data.department_id);

				    $("#dip").attr("action", baseurl+"hrm/department/update_department");
				    $('#department_form').modal('show'); 
				    $('.modal-title').text('Edit Department'); 
				    $('.dbtn').text('Update Department'); 


				},error: function() {

				}
			});


	}

    "use strict";
	function deleteDepartment(department_id)
	{

		if(confirm('Do you want to delete')===false){
			return false;
		}


			var submit_url = baseurl+"hrm/department/delete_department";
			var dataString = "department_id="+department_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString+ "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					$("#departmenttbl").load(" #departmenttbl > *");
				   

				},error: function() {

				}
			});


	}

