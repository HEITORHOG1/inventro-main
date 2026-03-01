
(function ($) {
    "use strict";
    $("#weekSW-0").click();
    $(document).change(function () {
        $("body").on("change", '#payment_type', function () {
            var base_url = $("#base_url").val();
            var CSRF_TOKEN = $('#csrf_token').val();
            var payment_mode = $(this).val();
            if (payment_mode == '2') {
                $(".bank_name_lbl").html("Bank Name <i class='text-danger'>*</i>");
                $(".bank_name_input").html("<select name='cheque_bank_name' class='form-control select2' id='cheque_bank_name'>\n\
                            <option value=''>-- select one -- </option>\n\
                        </select> ");
                $.ajax({
                    url: base_url + "invoice/invoice/get_banks",
                    type: "POST",
                    dataType: "json",
                    data: {'csrf_test_name': CSRF_TOKEN},
                    success: function (data) {
                        var opts = "<option value=''>-- select one --</option>";
                        $.each(data, function(i, item) {
                            opts += "<option value='" + $('<span>').text(item.bank_id).html() + "'>" + $('<span>').text(item.bank_name).html() + "</option>";
                        });
                        $("#cheque_bank_name").html(opts);
                    }
                });
            }
            if (payment_mode == '1') {
                $(".bank_name_lbl").html("");
                $(".bank_name_input").html("");
            }
        });

        $('body').on('click', '.pay_receipt_btn', function () {
            if ($("#payment_type").val() == '') {
                $("#payment_type").css({'border': '1px solid red'}).focus();
                return false;
            }
            var payment_type = $("#payment_type").val();
            if (payment_type == 2) {
                if ($("#cheque_bank_name").val() == '') {
                    $("#cheque_bank_name").css({'border': '1px solid red'}).focus();
                    return false;
                }
            }
            var transaction_type = $(".transaction_type:checked").val();
            if (transaction_type == 1) {
                if ($("#payment").val() == '') {
                    $("#payment").css({'border': '1px solid red'}).focus();
                    return false;
                }
            }
            if (transaction_type == 2) {
                if ($("#receipt_amount").val() == '') {
                    $("#receipt_amount").css({'border': '1px solid red'}).focus();
                    return false;
                }
            }
        });
    });
})(jQuery);





"use strict";
function transactionCategory1(id) {
    if (id == 1) {
        $(".loaded").html("<select class='form-control select2 customers' name='relation_id' data-placeholder='-- select one --'><option value=''></option></select>");
        get_allcustomer()
    } else if (id == 2) {
        $(".loaded").html("<select class='form-control select2 suppliers' name='relation_id' data-placeholder='-- select one --'><option value=''></option></select>");
        get_allsuppliers()
    }
}
"use strict";
function get_allcustomer() {
    var base_url = $("#base_url").val(); 
    var CSRF_TOKEN = $('#csrf_token').val();

    $.ajax({
        url: base_url + "accounts/account/get_customers",
        type: "POST",
        dataType: "json",
        data: {'csrf_test_name': CSRF_TOKEN},
        success: function (data) {
            var opts = "<option value=''>-- select one --</option>";
            $.each(data, function(i, item) {
                opts += "<option value='" + $('<span>').text(item.customerid).html() + "'>" + $('<span>').text(item.name).html() + "</option>";
            });
            $(".customers").html(opts);
        }
    });
}
"use strict";
function get_allsuppliers() {
    var base_url = $("#base_url").val();
    var CSRF_TOKEN = $('#csrf_token').val();
    $.ajax({
        url: base_url + "accounts/account/get_allsuppliers",
        type: "POST",
        dataType: "json",
        data: {'csrf_test_name': CSRF_TOKEN},
        success: function (data) {
            var opts = "<option value=''>-- select one --</option>";
            $.each(data, function(i, item) {
                opts += "<option value='" + $('<span>').text(item.supplier_id).html() + "'>" + $('<span>').text(item.name).html() + "</option>";
            });
            $(".suppliers").html(opts);
        }
    });
}


"use strict";
var number = "",
  total = 0,
  regexp = /[0-9]/,
  mainScreen = document.getElementById("mainScreen");

function InputSymbol(num) {
  var cur = document.getElementById(num).value;
  var prev = number.slice(-1);

  if (!regexp.test(prev) && !regexp.test(cur)) {
    console.log("Two math operators not allowed after each other ;)");
    return;
  }
  number = number.concat(cur);
  mainScreen.innerHTML = number;
}

"use strict";
function CalculateTotal() {
  total = (Math.round(eval(number) * 100) / 100);
  mainScreen.innerHTML = total;
}

"use strict";
function cancelprint(){
   location.reload();
}

"use strict";
function DeleteLastSymbol() {
  if (number) {
    number = number.slice(0, -1);
    mainScreen.innerHTML = number;
  }
  if (number.length === 0) {
    mainScreen.innerHTML = "0";
  }
}

"use strict";
function ClearScreen() {
  number = "";
  mainScreen.innerHTML = 0;
}


$(document).ready(function(){
"use strict";
    $('#full_paid_tab').keydown(function(event) {
        if(event.keyCode == 13) {
         $('#add_invoice').trigger('click');
        }
    });
});
