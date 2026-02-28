 $(document).ready(function() {
     "use strict";
            var info = $('table tbody tr');
            info.click(function() {
                var email    = $(this).children().first().text(); 
                var password = $(this).children().first().next().text();
                $("input[name=email]").val(email);
                $("input[name=password]").val(password);
            });
        });