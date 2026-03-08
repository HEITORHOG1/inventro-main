
// bank select part
"use strict";
 function bankPaymetreturn(val){
        if(val==2){
           var style = 'block'; 
           document.getElementById('bank_idss').setAttribute("required", true);
        }else{
   var style ='none';
    document.getElementById('bank_idss').removeAttribute("required");
        }
           
    document.getElementById('bank_div').style.display = style;
    }

  


    //Calculate store product
    "use strict";
    function calculate_store(sl) {
      
        var gr_tot = 0;
        var grandtotal = 0;
        var purchase_qty    = $("#purchase_qty_"+sl).val();
        var item_ctn_qty    = $("#uqty"+sl).val();
        var vendor_rate     = $("#product_rate_"+sl).val();
        var deduction       = $("#deduction").val();
        var cartoonqty      = $("#boxamount_"+sl).val();
        var cartoon         = item_ctn_qty/cartoonqty;
        var total_price     = item_ctn_qty * vendor_rate;
        $("#box_qty_"+sl).val(cartoon.toFixed(2));
        $("#total_price_"+sl).val(total_price.toFixed(2));


       if (parseInt(item_ctn_qty) > parseInt(purchase_qty)) {
        var message = "Não é possível devolver mais que " + purchase_qty + " unidades";
        alert(message);
        $("#uqty" + sl).val('');
           calculate_store(sl);
    }
       
        //Total Price
        $(".total_price").each(function() {
            isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
        });
        var grandtotal  = gr_tot + - deduction;

        $("#grandTotal").val(grandtotal.toFixed(2,2));
    }
    "use strict";
     function deleteRow(e) {
        var t = $("#purchaseTable > tbody > tr").length;
        if (1 == t) alert("Só há uma linha, não é possível excluir.");
        else {
            var a = e.parentNode.parentNode;
            a.parentNode.removeChild(a)
        }
        calculate_store()
    }

    