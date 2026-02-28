<script type="text/javascript">
	   var CSRF_TOKEN = $('#csrf_token').val();
 'use strict';
	function addDesignation()
	{
	    $("#dip").attr("action", "<?php echo base_url('hrm/designation/save_designation')?>");
	    $('#designation_form').modal('show'); 
	    $('.modal-title').text('Add new designation'); 
	}
	'use strict';
	function editDesignation(designation_id)
	{


			var submit_url = "<?php echo base_url('hrm/designation/edit_designation'); ?>";
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

				    $("#dip").attr("action", "<?php echo base_url('hrm/designation/update_designation')?>");
				    $('#designation_form').modal('show'); 
				    $('.modal-title').text('Edit Designation'); 
				    $('.dbtn').text('Update Designation'); 


				},error: function() {

				}
			});


	}
	'use strict';
	function deleteDesignation(designation_id)
	{

		if(confirm('Do you want to delete')===false){
			return false;
		}


			var submit_url = "<?php echo base_url('hrm/designation/delete_designation'); ?>";
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


</script>