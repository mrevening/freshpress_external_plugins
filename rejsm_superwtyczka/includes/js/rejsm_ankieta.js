/**
 * Created by mrevening on 2016-09-05.
 */



jQuery(document).ready(function($) {
    $("#Zatrudnienie").change(function () {
        var control = $(this);
        if (control.val() == "2") {
            $("#row-hide").show();
        } else {

            $("#row-hide").hide();
        }
    });
})(jQuery);

// jQuery(document).ready(function($) {
//     var e = document.getElementById("zatrudnienie");
//     var strUser = e.options[e.selectedIndex].value;
//     if (strUser == "2") {
//         $("#row-hide").show();
//     }
//     else {
//         $("#row-hide").hide();
//     }
//
// })(jQuery);
