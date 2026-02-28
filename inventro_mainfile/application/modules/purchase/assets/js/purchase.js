var CSRF_TOKEN = $('#csrf_token').val();
"use strict";
function productList(sl) {
     var base_url    = $('.baseUrl').val();
    var supplier_id = $('#supplier_id').val();

    if ( supplier_id == 0) {
        alert('Please select Supplier !');
        return false;
    }

    // Auto complete
    var options = {
        minLength: 0,
        source: function( request, response ) {
            var product_name = $('#product_name_'+sl).val();
        $.ajax( {
          url: base_url+"purchase/purchase/product_search_by_supplier",
          method: 'post',
          dataType: "json",
          data: {
            term: request.term,
            supplier_id:$('#supplier_id').val(),
            product_name:product_name,
            csrf_test_name: CSRF_TOKEN
          },
          success: function( data ) {
            response( data );
          }
        });
      },
       focus: function( event, ui ) {
           $(this).val(ui.item.label);
           return false;
       },
       select: function( event, ui ) {
            $(this).parent().parent().find(".autocompletevalue").val(ui.item.value); 
            var sl = $(this).parent().parent().find(".sl").val(); 

            var product_id          = ui.item.value;
          
          var  supplier_id=$('#supplier_id').val();
     
           
            var base_url    = $('.baseUrl').val();


            var available_quantity    = 'available_quantity_'+sl;
            var product_rate    = 'product_rate_'+sl;
            var cartoonqty   = 'boxamount_'+sl;
         
            $.ajax({
                type: "POST",
                url: base_url+"purchase/purchase/retrieve_product_data",
                 data: {product_id:product_id,supplier_id:supplier_id,csrf_test_name: CSRF_TOKEN},
                cache: false,
                success: function(data)
                {
                    console.log(data);
                    obj = JSON.parse(data);
                     $('#'+available_quantity).val(obj.total_product);
                     $('#'+product_rate).val(obj.supplier_price);
                     $('#'+cartoonqty).val(obj.cartoonqty);
                     calculate_store(sl);
                  
                } 
            });

            $(this).unbind("change");
            return false;
       }
   }

   $('body').on('keypress.autocomplete', '.product_name', function() {
       $(this).autocomplete(options);
   });

}
 
// bank select part
"use strict";
 function bankPaymets(val){
         
        if(val==2){
           var style = 'block'; 
           document.getElementById('bank_idss').setAttribute("required", true);
        }else{
                var style ='none';
                document.getElementById('bank_idss').removeAttribute("required");
        }
           
    document.getElementById('bank_div').style.display = style;
    }

  

    // Add more item for purchase
    "use strict";
     var count = 2;
    var limits = 500;

    function addPurchaseOrderField1(divName){

  
        if (count == limits)  {
            alert("You have reached the limit of adding " + count + " inputs");
        }
        else{
            var newdiv = document.createElement('tr');
            var tabin="product_name_"+count;
             tabindex = count * 4 ,
           newdiv = document.createElement("tr");
            tab1 = tabindex + 1;
            
            tab2 = tabindex + 2;
            tab3 = tabindex + 3;
            tab4 = tabindex + 4;
            tab5 = tabindex + 5;
            tab6 = tab5 + 1;

            newdiv.innerHTML ='<td class="span3 supplier"><input type="text" name="product_name" required class="form-control product_name productSelection" onkeypress="productList('+ count +');" placeholder="Item Name" id="product_name_'+ count +'" tabindex="'+tab1+'" > <input type="hidden" class="autocompletevalue product_id_'+ count +'" name="product_id[]" id="hiddenid"/>  <input type="hidden" class="sl" value="'+ count +'">  </td>  <td class="wt"> <input type="text" id="available_quantity_'+ count +'" class="form-control text-right stock_ctn_'+ count +'" placeholder="0.00" readonly/> </td><td class="text-right"><input type="text" name="product_quantity[]" value="1" tabindex="'+tab2+'" required  id="uqty'+ count +'" class="form-control text-right store_cal_' + count + '" onkeyup="calculate_store(' + count + ');" onchange="calculate_store(' + count + ');" placeholder="0.00" value="" min="0"/>  </td><td class="text-right"><input type="hidden" name="" id="boxamount_'+ count +'"><input type="text" name="box_qty[]" tabindex="'+tab2+'" required  id="box_qty_'+ count +'" class="form-control text-right box_qty_' + count + '" readonly  placeholder="0.00" value="" min="0"/>  </td><td class="test"><input type="text" name="product_rate[]" onkeyup="calculate_store('+ count +');" onchange="calculate_store('+ count +');" id="product_rate_'+ count +'" class="form-control product_rate_'+ count +' text-right" placeholder="0.00" value="" min="0" tabindex="'+tab3+'"/></td><td class="text-right"><input class="form-control total_price text-right total_price_'+ count +'" type="text" name="total_price[]" id="total_price_'+ count +'" value="0.00" readonly="readonly" /> </td><td> <button style="text-align: right;" class="btn btn-danger red" type="button" value="" onclick="deleteRow(this)" tabindex="8"><i class="fas fa-trash"></i></button></td>';
            document.getElementById(divName).appendChild(newdiv);
            document.getElementById(tabin).focus();
            document.getElementById("add_purchase_item").setAttribute("tabindex", tab5);
            document.getElementById("add_purchase").setAttribute("tabindex", tab6);
           
            count++;

            $("select.form-control:not(.dont-select-me)").select2({
                placeholder: "Select option",
                allowClear: true
            });
        }
    }

    //Calculate store product
    "use strict";
    function calculate_store(sl) {
       
        var gr_tot = 0;
        var grandtotal = 0;
        var discount        = $("#discount").val();
        var item_ctn_qty    = $("#uqty"+sl).val();
        var vendor_rate     = $("#product_rate_"+sl).val();
        var cartoonqty      = $("#boxamount_"+sl).val();
        var cartoon         = item_ctn_qty/cartoonqty;
        var total_price     = item_ctn_qty * vendor_rate;
        $("#box_qty_"+sl).val(cartoon.toFixed(2));
        $("#total_price_"+sl).val(total_price.toFixed(2));

       
        //Total Price
        $(".total_price").each(function() {
            isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
        });
        var grandtotal  = gr_tot - discount;

        $("#grandTotal").val(grandtotal.toFixed(2,2));
    }
"use strict";
     function deleteRow(e) {
        var t = $("#purchaseTable > tbody > tr").length;
        if (1 == t) alert("There only one row you can't delete.");
        else {
            var a = e.parentNode.parentNode;
            a.parentNode.removeChild(a)
        }
        calculate_store()
    }

    