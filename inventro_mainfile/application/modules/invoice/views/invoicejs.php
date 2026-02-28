<script type="text/javascript">
'use strict';
// ========== its for row add dynamically =============
function addInputField(t) {
    var row = $("#normalinvoice tbody tr").length;
    var count = row + 1;
    limits = 500;
    if (count == limits) {
        alert("You have reached the limit of adding" + count + "inputs");
    } else {
        var a = "product_id_" + count,
            e = document.createElement("tr");
        e.innerHTML = "<td>\n\
<select class='form-control placeholder-single common_product' id='" + a +
            "' name='product_id[]' onchange='service_cals(" + count + ")' data-placeholder='-- select one --'><option value=''></option><?php foreach ($get_products as $product) { ?><option value='<?php echo $product->product_id; ?>'><?php echo $product->name; ?></option><?php } ?></select></td>\n\
<td><input type='text' class='form-control common_available_qtn' name='available_qnt[]' id='available_qnt_" + count + "' onkeyup='' onchange='' value='' readonly style='text-align:right'></td>\n\
<td><input type='text' class='form-control common_qnt' name='product_quantity[]' id='quantity_" + count +
            "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' value='' style='text-align:right'></td>\n\
<td><input type='text' class='form-control common_boxqnt' name='box_quantity[]' id='box_quantity_" + count +
            "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' value='' style='text-align:right' readonly>\n\
<input type='hidden' name='' id='boxqty_hide_" + count + "' class='form-control'></td>\n\
<td><input type='text' class='form-control common_rate' name='product_rate[]' id='product_rate_" + count +
            "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' style='text-align:right'></td>\n\
<td><input type='text' class='form-control common_discount' name='product_discount[]' id='product_discount_" + count +
            "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' value='' style='text-align:right'></td>\n\
<td><input type='text' class='form-control common_totalprice total_price' name='total_price[]' id='total_price_" +
            count + "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' style='text-align:right' readonly></td> \n\
<td class='text-center'>\n\
<input type='hidden' id='all_discount_" + count + "' class='all_discount' name='discount_amount[]' />\n\
<button style='text-align: right;' class='btn btn-danger btn-xs' type='button' onclick='deleteRow(this)'><i class='fa fa-trash'></i></button></td>\n\
",
            document.getElementById(t).appendChild(e), document.getElementById(a).focus(), count++
    }
    $(".placeholder-single").select2();
}
// ============= its for row delete dynamically =========
'use strict';
function deleteRow(t) {
    var a = $("#normalinvoice > tbody > tr").length;
    if (1 == a) {
        alert("There only one row you can't delete it.");
    } else {
        var e = t.parentNode.parentNode;
        e.parentNode.removeChild(e);

        var common_product = 1;
        $("#normalinvoice > tbody > tr td select.common_product").each(function() {
            $(this).attr('id', 'product_id_' + common_product);
            $(this).attr('onchange', 'service_cals(' + common_product + ')');
            common_product++;
        });
        var common_available_qtn = 0; ///////// today 04-2-2019 ei section
        $("#normalinvoice > tbody > tr td input.common_available_qtn").each(function() {
            common_available_qtn++;
            $(this).attr('id', 'available_qnt_' + common_available_qtn);
            $(this).attr('class', 'form-control text-right common_available_qtn available_quantity_' +
                common_available_qtn);
        });
        var common_qnt = 1;
        $("#normalinvoice > tbody > tr td input.common_qnt").each(function() {
            $(this).attr('id', 'quantity_' + common_qnt);
            $(this).attr('onkeyup', 'quantity_calculate(' + common_qnt + ')');
            $(this).attr('onchange', 'quantity_calculate(' + common_qnt + ')');
            common_qnt++;
        });
        var common_boxqnt = 1;
        $("#normalinvoice > tbody > tr td input.common_qnt").each(function() {
            $(this).attr('id', 'boxqty_hide_' + common_boxqnt);
            $(this).attr('onkeyup', 'quantity_calculate(' + common_boxqnt + ')');
            $(this).attr('onchange', 'quantity_calculate(' + common_boxqnt + ')');
            common_boxqnt++;
        });
        var common_rate = 0;
        $("#normalinvoice > tbody > tr td input.common_rate").each(function() {
            common_rate++;
            $(this).attr('id', 'product_rate_' + common_rate);
            $(this).attr('class', 'common_rate form-control text-right price_item' +
            common_rate); ///////// today 04-2-2019
            $(this).attr('onkeyup', 'quantity_calculate(' + common_rate + ');');
            $(this).attr('onchange', 'quantity_calculate(' + common_rate + ');');
        });
        var common_discount = 0;
        $("#normalinvoice > tbody > tr td input.common_discount").each(function() {
            common_discount++;
            $(this).attr('id', 'product_discount_' + common_discount);
            $(this).attr('onkeyup', 'quantity_calculate(' + common_discount + ');');
        });
        var common_totalprice = 0;
        $("#normalinvoice > tbody > tr td input.common_totalprice").each(function() {
            common_totalprice++;
            $(this).attr('id', 'total_price_' + common_totalprice);
        });
    }
    calculateSum()
}
//    ================= its for service_cal ============



//Calcucate Invoice Add Items
'use strict';

function quantity_calculate(item) {
    var available_qnt = $("#available_qnt_" + item).val();
    var qnty = $("#quantity_" + item).val();
    var rate = $("#product_rate_" + item).val();
    var discount = $("#product_discount_" + item).val();
    var invoice_discount = $("#invoice_discount").val();
    var total_discount = $("#total_discount_" + item).val();
    console.log(discount);

    var boxqty_hide = $("#boxqty_hide_" + item).val();
    var cartoon = qnty / boxqty_hide;
    $("#box_quantity_" + item).val(cartoon.toFixed(2));

    //============= its for purchase qnt and sales qnt calculation ==============
    if (parseInt(qnty) > parseInt(available_qnt)) {
        var message = "You can purchase maximum " + available_qnt + " Items";
        alert(message);
        $("#quantity_" + item).val('');
        $("#total_price_" + item).val('');
        $('input[type=submit]').prop('disabled', true);
    } else {
        $('input[type=submit]').prop('disabled', false);
    }

    var total_amount = qnty * rate;
    var dis = total_amount * discount / 100;
    $("#total_price_" + item).val(total_amount);
    $("#all_discount_" + item).val(dis);

    calculateSum();
}
//    ======== its for calculateSum ===========
'use strict';

function calculateSum() {
    var t = 0,
        a = 0,
        e = 0,
        o = 0,
        p = 0;
    $(".total_price").each(function() {
            isNaN(this.value) || 0 == this.value.length || (e += parseFloat(this.value))
        }),
        $(".all_discount").each(function() {
            isNaN(this.value) || 0 == this.value.length || (p += parseFloat(this.value))
        }),
        $("#total_discount").val(p.toFixed(2, 2)),
        $("#grandTotal").val(e.toFixed(2))
    var gt = $("#grandTotal").val();
    var invoiceDiscount = $("#invoice_discount").val();
    var total_discount = $("#total_discount").val();
    var ttl_discount = +invoiceDiscount + +total_discount;
    $("#total_discount").val(ttl_discount);
    var grandTotals = gt - ttl_discount;
    $("#grandTotal").val(grandTotals);
    invoice_paidamount();
}
//Invoice Paid Amount
'use strict';

function invoice_paidamount() {
    var t = $("#grandTotal").val(),
        a = $("#paidAmount").val(),
        e = t - a;
    $("#dueAmmount").val(e.toFixed(2, 2))
}
'use strict';

function service_cals(item) {
    var invoice_form = $("#invoice_frm").serializeArray();
    var product_id = $("#product_id_" + item).val();

    $.ajax({
        type: "POST",
        url: "<?php echo base_url('invoice/CInvoice/get_only_service_info/'); ?>" + product_id,
        success: function(s) {
            var obj = jQuery.parseJSON(s);

            $('#available_qnt_' + item).val(obj.total_product);
            $('#product_rate_' + item).val(obj.price);
            $('#boxqty_hide_' + item).val(obj.cartoon_qty);

        },
    });
}
</script>