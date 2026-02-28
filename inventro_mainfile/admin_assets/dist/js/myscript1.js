$(function () {
    "use strict";
    $("#datagrid").DataTable();
	$('.select2').select2();
	$( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });

  });
  $(document).ready(function () {
        "use strict";
         var segment_1 = $("sigmment1").val;
        var segment_2 = $("sigmment2").val;
        var segment_3 = $("sigmment3").val;

        if (segment_3 === 'form') {
            $('.setting').addClass('menu-open');
            $('.user_form').addClass('active');
            $('.setting_active').addClass('active');
        }

        if ( segment_3 === 'index') {
            $('.setting').addClass('menu-open');
            $('.user_list').addClass('active');
            $('.setting_active').addClass('active');
        }

        if ( segment_2 === 'setting') {
            $('.setting').addClass('menu-open');
            $('.appsetting').addClass('active');
            $('.setting_active').addClass('active');
        } 

        if ( segment_2 === 'language') {
            $('.setting').addClass('menu-open');
            $('.appsetting').addClass('active');
            $('.setting_active').addClass('active');
        }

        else if (segment_3 === 'Print_pattern_controller') {
            $('.print_pattern').addClass('active');
        } 
    });


"use strict";
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });
   $("body").on("click", '.swalDefaultSuccess', function () {
        Toast.fire({
        type: 'success',
        title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
      })
   });
    /*$('.swalDefaultSuccess').click(function() {
      Toast.fire({
        type: 'success',
        title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
      })
    });*/
    
    $("body").on("click", '.swalDefaultError', function () {
       Toast.fire({
        type: 'error',
        title: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'
      })
   });

 

    $("body").on("click", '.toastrDefaultInfo', function () {
  
      toastr.info('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
    });
     $("body").on("click", '.toastrDefaultError', function () {
 
      toastr.error('Lorem ipsum dolor sit amet, consetetur sadipscing elitr.')
    });

function editinfo(id){
	   var geturl=$("#url_"+id).val();
	   var myurl =geturl+'/'+id;
	    var dataString = "id="+id;
	 
		 $.ajax({
		 type: "GET",
		 url: myurl,
		 data: dataString,
		 success: function(data) {
			 $('.editinfo').html(data);
			 $('#edit').modal('show');
			 $('.select2').select2();
		 } 
	});
	}