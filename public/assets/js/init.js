$(document).ready(function () {
    $(".form--select2").each(function () {
        setupValue = $(this).attr("value");
        setupClear = $(this).attr("data-clear") ? true : false;

        if (setupValue == undefined) {
            $(this).select2({
                allowClear: setupClear,
                placeholder: "",
            });
        } else {
            $(this)
                .select2({
                    allowClear: setupClear,
                    placeholder: "",
                })
                .val(setupValue)
                .trigger("change");
        } // end else
    }); // end loop
}); // end function
