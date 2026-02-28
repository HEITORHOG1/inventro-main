
"use strict";
var CSRF_TOKEN = $('#csrf_token').val();
function returnBank_paymet(val) {
    var base_url = $("#base_url").val();
    if (val == 2) {
        $(".bank_area").html("<div class='form-group row'>\n\
        <label for='bank' class='col-sm-4 col-form-label'>Bank Name <i class='text-danger'>*</i></label>\n\
        <select class='form-control select2 col-sm-8' name='bank_id' id='bank_id' data-placeholder='-- select one --'>\n\
        <option value=''>-- select one --</option>\n\
        </select>\n\
        </div>");
        $.ajax({
            url: base_url + "invoice/invoice/get_banks",
            type: "POST",
            data: {bank_id: val,'csrf_test_name': CSRF_TOKEN},
            success: function (r) {
                $("#bank_id").html(r);
            }
        });
    } else {
        $(".bank_area").html("");
    }
}



'use strict';
function deleteItem(t) {
    var a = $("#returntable > tbody > tr").length;
    if (1 == a) {
        alert("There only one row you can't delete it.");
    } else {
        var e = t.parentNode.parentNode;
        e.parentNode.removeChild(e);
    }

    Calculation()
}
'use strict';
function Return_calculate(item) {
    var soldqty = $("#soldqty_" + item).val();
    var qnty = $("#quantity_" + item).val();
    var rate = $("#product_rate_" + item).val();
    var deduction = $("#deduction").val();
   
    var boxqty_hide = $("#boxqty_hide_" + item).val();
    var cartoon = qnty / boxqty_hide;
    $("#box_quantity_" + item).val(cartoon.toFixed(2));

    if (parseInt(qnty) > parseInt(soldqty)) {
        var message = "You can not return more than sold QTy ";
        alert(message);
        $("#quantity_" + item).val('');
        $("#total_price_" + item).val('');
        $('input[type=submit]').prop('disabled', true);
    } else {
        $('input[type=submit]').prop('disabled', false);
    }


    Calculation();
}
'use strict';
function Calculation() {
    var t = 0,
            a = 0;
    $(".total_price").each(function () {
        isNaN(this.value) || 0 == this.value.length || (a += parseFloat(this.value))
    }),
          
    $("#grandTotal").val(a.toFixed(2));
    var gt = $("#grandTotal").val();
    var invoiceDiscount = $("#deduction").val();
    var grandTotals = gt - invoiceDiscount;
    $("#grandTotal").val(grandTotals);
    return_paidAmount();
}

