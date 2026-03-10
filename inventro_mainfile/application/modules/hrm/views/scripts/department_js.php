<script type="text/javascript">
	   var CSRF_TOKEN = $('#csrf_token').val();
 'use strict';
	function addDepartment()
	{
	    var save_method = 'save_department';
	    $("#dip").attr("action", "<?php echo base_url('hrm/department/save_department')?>");
	    $('#department_form').modal('show'); 
	    $('.modal-title').text('<?php echo makeString(['add','department'])?>'); 
	}
	'use strict';
	function editDepartment(department_id)
	{

			var submit_url = "<?php echo base_url('hrm/department/edit_department'); ?>";
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

				    $("#dip").attr("action", "<?php echo base_url('hrm/department/update_department')?>");
				    $('#department_form').modal('show'); 
				    $('.modal-title').text('Edit Department'); 
				    $('.dbtn').text('<?php echo makeString(['update','department'])?>'); 


				},error: function() {

				}
			});


	}

	'use strict';
	function deleteDepartment(department_id)
	{

		if(confirm('Deseja excluir este registro?')===false){
			return false;
		}


			var submit_url = "<?php echo base_url('hrm/department/delete_department'); ?>";
			var dataString = "department_id="+department_id;

			$.ajax({

				type: 'POST',
				url: submit_url,
				data: dataString + "&csrf_test_name=" + CSRF_TOKEN,
				success: function(res) {

					$("#departmenttbl").load(" #departmenttbl > *");
				   

				},error: function() {

				}
			});


	}




</script>