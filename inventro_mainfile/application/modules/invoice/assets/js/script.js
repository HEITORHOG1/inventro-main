
"use strict";
var CSRF_TOKEN = $('#csrf_token').val();
function bank_paymet(val) {
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
            dataType: "json",
            data: {bank_id: val,'csrf_test_name': CSRF_TOKEN},
            success: function (data) {
                var opts = "<option value=''>-- select one --</option>";
                $.each(data, function(i, item) {
                    opts += "<option value='" + $('<span>').text(item.bank_id).html() + "'>" + $('<span>').text(item.bank_name).html() + "</option>";
                });
                $("#bank_id").html(opts);
            }
        });
    } else {
        $(".bank_area").html("");
    }
}

"use strict";
function get_allproducts(count) {
    var base_url = $("#base_url").val();
    $.ajax({
        url: base_url + "invoice/invoice/get_products",
        type: "POST",
        dataType: "json",
        data: {'csrf_test_name': CSRF_TOKEN},
        success: function (data) {
            var opts = "<option value=''>-- selecione --</option>";
            $.each(data, function(i, item) {
                opts += "<option value='" + $('<span>').text(item.product_id).html() + "'>" + $('<span>').text(item.name).html() + "</option>";
            });
            $("#product_id_" + count).html(opts);
        }
    });
}
"use strict";
function addInputField(t) {
    var row = $("#normalinvoice tbody tr").length;
    var count = row + 1;
    var limits = 500;
    get_allproducts(count);
    if (count == limits) {
        alert("You have reached the limit of adding" + count + "inputs");
    } else {
        var a = "product_id_" + count, e = document.createElement("tr");
        e.innerHTML = "<td>\n\
<select class='form-control placeholder-single common_product' id='" + a + "' name='product_id[]' onchange='service_cals(" + count + ")' data-placeholder='-- select one --'><option value=''></option></select></td>\n\
<td><input type='text' class='form-control common_available_qtn' name='available_qnt[]' id='available_qnt_" + count + "' onkeyup='' onchange='' value='' readonly style='text-align:right'></td>\n\
<td><input type='text' class='form-control common_qnt' name='product_quantity[]' id='quantity_" + count + "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' value='' style='text-align:right'></td>\n\
<td><input type='text' class='form-control common_boxqnt' name='box_quantity[]' id='box_quantity_" + count + "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' value='' style='text-align:right' readonly>\n\
<input type='hidden' name='' id='boxqty_hide_" + count + "' class='form-control'></td>\n\
<td><input type='text' class='form-control common_rate' name='product_rate[]' id='product_rate_" + count + "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' style='text-align:right'></td>\n\
<td><input type='text' class='form-control common_discount' name='product_discount[]' id='product_discount_" + count + "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' value='' style='text-align:right'></td>\n\
<td><input type='text' class='form-control common_totalprice total_price' name='total_price[]' id='total_price_" + count + "' onkeyup='quantity_calculate(" + count + ")' onchange='quantity_calculate(" + count + ")' placeholder='0.00' style='text-align:right' readonly></td> \n\
<td class='text-center'>\n\
<input type='hidden' id='all_discount_" + count + "' class='all_discount' name='discount_amount[]' />\n\
<button style='text-align: right;' class='btn btn-danger btn-xs' type='button' onclick='deleteRow(this)'><i class='fa fa-trash'></i></button></td>\n\
",
                document.getElementById(t).appendChild(e), document.getElementById(a).focus(), count++
    }
    $(".placeholder-single").select2();
}
"use strict";
function deleteRow(t) {
    var a = $("#normalinvoice > tbody > tr").length;
    if (1 == a) {
        alert("There only one row you can't delete it.");
    } else {
        var e = t.parentNode.parentNode;
        e.parentNode.removeChild(e);

        var common_product = 1;
        $("#normalinvoice > tbody > tr td select.common_product").each(function () {
            $(this).attr('id', 'product_id_' + common_product);
            $(this).attr('onchange', 'service_cals(' + common_product + ')');
            common_product++;
        });
        var common_available_qtn = 0;
        $("#normalinvoice > tbody > tr td input.common_available_qtn").each(function () {
            common_available_qtn++;
            $(this).attr('id', 'available_qnt_' + common_available_qtn);
            $(this).attr('class', 'form-control text-right common_available_qtn available_quantity_' + common_available_qtn);
        });
        var common_qnt = 1;
        $("#normalinvoice > tbody > tr td input.common_qnt").each(function () {
            $(this).attr('id', 'quantity_' + common_qnt);
            $(this).attr('onkeyup', 'quantity_calculate(' + common_qnt + ')');
            $(this).attr('onchange', 'quantity_calculate(' + common_qnt + ')');
            common_qnt++;
        });
        var common_boxqnt = 1;
        $("#normalinvoice > tbody > tr td input.common_qnt").each(function () {
            $(this).attr('id', 'boxqty_hide_' + common_boxqnt);
            $(this).attr('onkeyup', 'quantity_calculate(' + common_boxqnt + ')');
            $(this).attr('onchange', 'quantity_calculate(' + common_boxqnt + ')');
            common_boxqnt++;
        });
        var common_rate = 0;
        $("#normalinvoice > tbody > tr td input.common_rate").each(function () {
            common_rate++;
            $(this).attr('id', 'product_rate_' + common_rate);
            $(this).attr('class', 'common_rate form-control text-right price_item' + common_rate);
            $(this).attr('onkeyup', 'quantity_calculate(' + common_rate + ');');
            $(this).attr('onchange', 'quantity_calculate(' + common_rate + ');');
        });
        var common_discount = 0;
        $("#normalinvoice > tbody > tr td input.common_discount").each(function () {
            common_discount++;
            $(this).attr('id', 'product_discount_' + common_discount);
            $(this).attr('onkeyup', 'quantity_calculate(' + common_discount + ');');
        });
        var common_totalprice = 0;
        $("#normalinvoice > tbody > tr td input.common_totalprice").each(function () {
            common_totalprice++;
            $(this).attr('id', 'total_price_' + common_totalprice);
        });
    }
    calculateSum()
}
"use strict";
function quantity_calculate(item) {
    var available_qnt = $("#available_qnt_" + item).val();
    var qnty = $("#quantity_" + item).val();
    var rate = $("#product_rate_" + item).val();
    var discount = $("#product_discount_" + item).val();
    var invoice_discount = $("#invoice_discount").val();
    var total_discount = $("#total_discount_" + item).val();

    var boxqty_hide = $("#boxqty_hide_" + item).val();
    var cartoon = qnty / boxqty_hide;
    $("#box_quantity_" + item).val(cartoon.toFixed(2));

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
"use strict";
function calculateSum() {
    var t = 0,
            a = 0,
            e = 0,
            o = 0,
            p = 0;
    $(".total_price").each(function () {
        isNaN(this.value) || 0 == this.value.length || (e += parseFloat(this.value))
    }),
            $(".all_discount").each(function () {
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
"use strict";
function invoice_paidamount() {
    var t = $("#grandTotal").val(),
            a = $("#paidAmount").val(),
            e = t - a;
    $("#dueAmmount").val(e.toFixed(2, 2))
}
"use strict";
function service_cals(item) {
    var base_url = $("#base_url").val();
    var invoice_form = $("#invoice_frm").serializeArray();
    var product_id = $("#product_id_" + item).val();
    $.ajax({
        type: "post",
        url: base_url + "invoice/invoice/get_only_service_info/",
        data: {product_id: product_id,'csrf_test_name': CSRF_TOKEN},
        success: function (s) {
            var obj = jQuery.parseJSON(s);
            $('#available_qnt_' + item).val(obj.total_product);
            $('#product_rate_' + item).val(obj.price);
            $('#boxqty_hide_' + item).val(obj.cartoon_qty);

        },
    });
}



// pos_invoice 



 
'use strict';

function CategorySearch(id) {
    var base_url = $("#base_url").val();
    var category = id;
    var searchurl = base_url + 'invoice/invoice/getsearchitem';
    $.ajax({
        type: "post",
        async: false,
        url: searchurl,
        data: {
            category_id: category,
            'csrf_test_name': CSRF_TOKEN
        },
        success: function(data) {
            if (data == '420') {
                $(".all-products").html(data);
            } else {
                $(".all-products").html(data);
            }
        },
        error: function() {
            alert('Request Failed, Please check your code and try again!');
        }
    });
}
'use strict';
$('body').on('keyup', '#searchitem', function() {
    var items = $('#searchitem').val();
    var base_url = $("#base_url").val();
    var searchUrl = base_url + 'invoice/invoice/searchitem_byname';
    $.ajax({
        type: "post",
        async: false,
        url: searchUrl,
        data: {
            item: items,
            'csrf_test_name': CSRF_TOKEN
        },
        success: function(data) {
            if (data == '420') {
                $(".all-products").html(data);
            } else {
                $(".all-products").html(data);
            }
        },
        error: function() {
            alert('Request Failed, Please check your code and try again!');
        }
    });
});

'use strict';

function bankPaymet(val) {
    if (val == 2) {
        var style = 'flex';
        document.getElementById('bank_id').setAttribute("required", true);
    } else {
        var style = 'none';
        document.getElementById('bank_id').removeAttribute("required");
    }

    document.getElementById('bank_div').style.display = style;
}

'use strict';

function onselectimage(id) {
    var product_id = id;
    var exist = $("#product_id_" + product_id).val();
    var qty = $("#quantity_" + product_id).val();
    var base_url = $("#base_url").val();
    var add_qty = parseInt(qty) + 1;
    var thead =
        '<tr><th class="cth">Item</th><th class="cth">QTY</th><th class="cth">Price</th><th class="cth">Dis</th><th class="cth">Total</th></tr>';
    var tn = $("#addinvoice > thead > tr").length;
    if (tn < 1) {
        $('#addinvoice thead').append(thead);
    }
    if (product_id == exist) {
        $("#quantity_" + product_id).val(add_qty);
        QtyCal(id);
        TotalCalculation();
    } else {
        $.ajax({
            type: "post",
            async: false,
            url: base_url + 'invoice/invoice/pos_product_data',
            data: {
                product_id: product_id,
                'csrf_test_name': CSRF_TOKEN
            },
            success: function(data) {
                if (data == false) {
                    alert('This Product Not Found !');

                } else {
                    $("#hidden_tr").css("display", "none");
                    $('#addinvoice tbody').append(data);
                    QtyCal(product_id);
                    TotalCalculation();
                }
            },
            error: function() {
                alert('Request Failed, Please check your code and try again!');
            }
        });
    }

}


'use strict';

function QtyCal(item) {
    var available_qnt = $("#available_qnt_" + item).val();
    var qnty = $("#quantity_" + item).val();
    var rate = $("#product_rate_" + item).val();
    var discount = $("#product_discount_" + item).val();
    var invoice_discount = $("#invoice_discount").val();
    var total_discount = $("#total_discount_" + item).val();
    if (parseInt(qnty) > parseInt(available_qnt)) {
        var message = "Item is out of stock please another";
        alert(message);
        $("#quantity_" + item).val('');
        $("#total_price_" + item).val('');
        $('input[type=submit]').prop('disabled', true);
    } else {
        $('input[type=submit]').prop('disabled', false);
    }

    var total_amount = qnty * rate;
    var dis = total_amount * discount / 100;
    $("#total_price_" + item).val(total_amount - dis);
    $("#all_discount_" + item).val(dis);

    TotalCalculation();
}
'use strict';

function TotalCalculation() {
    var t = 0,
        a = 0,
        e = 0,
        o = 0,
        p = 0;
    $(".totalprice").each(function() {
            isNaN(this.value) || 0 == this.value.length || (e += parseFloat(this.value))
        }),
        $(".all_discount").each(function() {
            isNaN(this.value) || 0 == this.value.length || (p += parseFloat(this.value))
        }),
        $("#total_discount").val(p.toFixed(2, 2)),
        $("#item_total").val(e.toFixed(2));
    var gt = $("#item_total").val();
    var invoiceDiscount = $("#invoice_discount").val();
    var total_discount = $("#total_discount").val();
    var ttl_discount = +invoiceDiscount;
    $("#total_discount").val(ttl_discount);
    var grand_totals = gt - ttl_discount;
    $("#grand_total").val(grand_totals);
}
'use strict';

function PaidAmount() {
    var e = 0;
    var t = $("#grand_total").val(),
        a = $("#paid_amount").val(),
        e = t - a;
    $("#due_amount").val(e.toFixed(2, 2))
}
'use strict';

function deleteRow(e) {
    var t = $("#addinvoice > tbody > tr").length;
    if (1 == t) alert("There only one row you can't delete.");
    else {
        var a = e.parentNode.parentNode;
        a.parentNode.removeChild(a);

    }

}


'use strict';
var barcodeScannerTimer;
var barcodeString = '';
// BarCode Qrcode/part
$('#barcode').on('keypress', function(e) {
    barcodeString = barcodeString + String.fromCharCode(e.charCode);
    clearTimeout(barcodeScannerTimer);
    barcodeScannerTimer = setTimeout(function() {
        BarcodeProcess();
    }, 300);
});
'use strict';

function BarcodeProcess() {
    var base_url = $("#base_url").val();
    if (barcodeString != '') {
        var product_id = barcodeString;
        var exist = $("#product_id_" + product_id).val();
        var qty = $("#quantity_" + product_id).val();
        var add_qty = parseInt(qty) + 1;
        var thead =
            '<tr><th class="cth">Item</th><th class="cth">QTY</th><th class="cth">Price</th><th class="cth">Dis</th><th class="cth">Total</th></tr>';
        var tn = $("#addinvoice > thead > tr").length;
        if (tn < 1) {
            $('#addinvoice thead').append(thead);
        }

        if (product_id == exist) {
            $("#quantity_" + product_id).val(add_qty);
            QtyCal(id);
            TotalCalculation();
        } else {
            $.ajax({
                type: "post",
                async: false,
                url: base_url + 'invoice/invoice/pos_product_data',
                data: {
                    product_id: product_id,
                    'csrf_test_name': CSRF_TOKEN
                },
                success: function(data) {
                    if (data == false) {
                        alert('This Product Not Found !');

                    } else {
                        $("#hidden_tr").css("display", "none");
                        $('#addinvoice tbody').append(data);
                        QtyCal(product_id);
                        TotalCalculation();
                    }
                },
                error: function() {
                    alert('Request Failed, Please check your code and try again!');
                }
            });
        }
    } else {
        alert('barcode is invalid: ' + barcodeString);
    }

    barcodeString = ''; // reset
}

'use strict';
$("#paymentform").submit(function() {
    var base_url = $("#base_url").val();
    var searchUrl = base_url + 'invoice/invoice/add_payment';
    $.ajax({
        type: "POST",
        url: searchUrl,
        dataType: 'json',
        data: $("#paymentform").serialize() + "&_token=" + CSRF_TOKEN,
        success: function(data) {

            obj = JSON.parse(data);
            var style = 'none';
            $('#possubmit').style.display = 'none';
            document.getElementById('possubmit').style.display = style;

        },
        error: function() {
            alert('Request Failed, Please check your code and try again!');
        }
    });
});
'use strict';
function Savepayment() {
    var payenttype = $("#paytype").val();
    var bankid = $("#bank").val();
    if (payenttype == '') {
        alert("Please select Payment Type!!");
        return false;
    }
    if (payenttype == 2) {
        if (bankid == '') {
            alert("Please select Bank!!");
            return false;
        }

    }
    $("#paymenttype").modal('hide');
    $("#payment_type").val(payenttype);
    $("#bank_id").val(bankid);
    $("#ptypebutton").hide();
    $("#possubmit2").show();

}
