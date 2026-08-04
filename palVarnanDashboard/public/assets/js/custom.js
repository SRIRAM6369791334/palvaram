window.addEventListener("load", function () {
    $(".preloader").fadeOut();
    $("#right-bar-toggle").hide();
    $("#right-bar-toggle").remove();
    $(".right-bar").remove();
    // $("#right-bar-toggle").remove();

    function setCsrfProtection() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
    }

    setCsrfProtection();

    $(".password-toggle-icon").on("click", function () {
        console.log("check");
        let input = $("#userpassword");
        console.log(input);
        if (input.attr("type") == "password") {
            input.attr("type", "text");
            $(this).find(".password-toggle-icon-show").hide();
            $(this).find(".password-toggle-icon-hide").show();
        } else {
            input.attr("type", "password");
            $(this).find(".password-toggle-icon-show").show();
            $(this).find(".password-toggle-icon-hide").hide();
        }
    });
});
