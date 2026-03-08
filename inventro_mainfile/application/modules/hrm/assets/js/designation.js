
var baseurl=$("#mainsiteurl").val();
var CSRF_TOKEN = $('#csrf_token').val();
"use strict";
	function addDesignation()
	{
	    $("#dip").attr("action", baseurl+"hrm/designation/save_designation");
	    $('#designation_form').modal('show'); 
	    $('.modal-title').text('Novo cargo');
	}
    "use strict";
	function editDesignation(designation_id)
	{


			var submit_url = baseurl+"hrm/designation/edit_designation";
			var dataString = "designation_id="+designation_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {
					var data = JSON.parse(res);


					$("#designation").val(data.designation_name);
					$("#description").val(data.designation_description);
					$("#designation_id").val(data.designation_id);

				    $("#dip").attr("action", baseurl+"hrm/designation/update_designation");
				    $('#designation_form').modal('show'); 
				    $('.modal-title').text('Editar Cargo');
				    $('.dbtn').text('Atualizar Cargo');


				},error: function() {

				}
			});


	}
    "use strict";
	function deleteDesignation(designation_id)
	{

		if(confirm('Deseja excluir este registro?')===false){
			return false;
		}


			var submit_url = baseurl+"hrm/designation/delete_designation";
			var dataString = "designation_id="+designation_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					$("#designationtbl").load(" #designationtbl > *");
				   

				},error: function() {

				}
			});


	}

