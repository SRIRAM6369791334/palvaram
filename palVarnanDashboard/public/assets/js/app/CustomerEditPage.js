const addValidator = new JustValidate("#editBasicProfileForm", {
    validateBeforeSubmitting: true,
});
const addValidator1 = new JustValidate("#udpatePasswordForm", {
    validateBeforeSubmitting: true,
});
$("#first_name").on("input", function () {
    const firstNameValue = this.value;
    const lastNameValue = $("#last_name").val();
    $("#full_name").val(firstNameValue + " " + lastNameValue);
});

$("#last_name").on("input", function () {
    const lastNameValue = this.value;
    const firstNameValue = $("#first_name").val();
    $("#full_name").val(firstNameValue + " " + lastNameValue);
});

$(".backBtn").on("click", function () {
    history.back();
});

$(".full_name_container").hide();

addValidator
    .addField("#first_name", [
        {
            rule: "required",
            errorMessage: "*First Name Field is required",
        },
        {
            rule: "minLength",
            value: 3,
            errorMessage: "*Should more than 3 characters",
        },
        {
            rule: "maxLength",
            value: 15,
            errorMessage: "*Should not more than 15 characters",
        },
    ])
    .addField("#email", [
        {
            rule: "required",
            errorMessage: "*Email Field is required",
        },
        {
            rule: "email",
            errorMessage: "*Must be valid Email Format(ex:- john@gmail.com)",
        },
    ])
    .addField("#phone_number", [
        {
            rule: "required",
            errorMessage: "*Phone Number Field is required",
        },
        {
            rule: "minLength",
            value: 10,
            errorMessage: "*Invalid Phone number",
        },
        {
            rule: "maxLength",
            value: 10,
            errorMessage: "*Invalid Phone number",
        },
    ])
    // .addField("#gender_select", [
    //     {
    //         rule: "required",
    //         errorMessage: "*Gender Field is required",
    //     },
    // ])
    .onSuccess((event) => {
        $(".basic_profile_submit_btn").attr("disabled", "true");
        editBasicProfileFormSubmit(event);
    });

    addValidator1
    .addField("#edit_password", [
        {
            rule: "required",
            errorMessage: "*Password Field is required",
        },

    ]) .onSuccess((event) => {
        $(".basic_profile_submit_btn").attr("disabled", "true");
        udpatePasswordFormSubmit(event);
    });

function editBasicProfileFormSubmit(event) {
    const formData = new FormData(event.target);
    formData.append("user_id", userId);

    $.ajax({
        type: "post",
        url: apiUrl + "api/updateProfile",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            location.reload();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".basic_profile_submit_btn").removeAttr("disabled");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

// Active address tab
var url = new URL(window.location.href);
if (url.searchParams.get("address") == "true") {
    $(".address_tab").trigger("click");

    // Remove the "address" parameter
    url.searchParams.delete("address");

    // Modify the browser's history state without reloading the page
    window.history.replaceState({}, document.title, url.toString());

    Toastify({
        text: "Address Added Successfully",
        className: "info",
        close: true,
        position: "center",
    }).showToast();
}

if (url.searchParams.get("default") == "true") {
    $(".address_tab").trigger("click");

    // Remove the "address" parameter
    url.searchParams.delete("default");

    // Modify the browser's history state without reloading the page
    window.history.replaceState({}, document.title, url.toString());

    Toastify({
        text: "Default Set Address Successfull",
        className: "info",
        close: true,
        position: "center",
    }).showToast();
}

if (url.searchParams.get("delete") == "true") {
    $(".address_tab").trigger("click");

    // Remove the "address" parameter
    url.searchParams.delete("delete");

    // Modify the browser's history state without reloading the page
    window.history.replaceState({}, document.title, url.toString());

    Toastify({
        text: "Delete Address Successfull",
        className: "info",
        close: true,
        position: "center",
    }).showToast();
}

if (url.searchParams.get("update") == "true") {
    // Remove the "address" parameter
    url.searchParams.delete("update");

    // Modify the browser's history state without reloading the page
    window.history.replaceState({}, document.title, url.toString());

    Toastify({
        text: "Password Updated Successfully",
        className: "info",
        close: true,
        position: "center",
    }).showToast();
}

// add new address submit function
$("#addAddressForm").on("submit", function (e) {
    e.preventDefault();
    $(".add_submit_btn").attr("disabled", "true");
    const formData = new FormData(e.target);
    $(".add_submit_btn").html("Please Wait...");

    formData.append("user_id", userId);

    $.ajax({
        type: "post",
        url: apiUrl + `api/addUserAddress/${userId}`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            var url = new URL(window.location.href);
            url.searchParams.set("address", "true");
            window.location.replace(url.href);
            // location.reload();
        },
    });
});

$(".bx-show").hide();
$(".icon").on("click", function () {
    const parentEl = $(this).closest(".input-group");
    const input = $(this).closest(".input-group").find("input");
    if (input.attr("type") === "password") {
        input.attr("type", "text");
        $(parentEl).find(".bx-hide").hide();
        $(parentEl).find(".bx-show").show();
    } else {
        input.attr("type", "password");
        $(parentEl).find(".bx-show").hide();
        $(parentEl).find(".bx-hide").show();
    }
});

// $("#udpatePasswordForm").on("submit", function (e) {
//     e.preventDefault();
//     const formData = new FormData(e.target);

//     // Manually trigger Parsley.js validation
//     if (!$(this).parsley().isValid()) {
//         return; // Exit if form validation fails
//     }
//     $.ajax({
//         type: "post",
//         url: apiUrl + `api/updatePassword`,
//         data: formData,
//         contentType: false,
//         processData: false,
//         success: function (response) {
//             var url = new URL(window.location.href);
//             url.searchParams.set("update", "true");
//             window.location.replace(url.href);
//         },
//     });
// });


function udpatePasswordFormSubmit(event) {
    const formData = new FormData(event.target);
    formData.append("user_id", userId);

    console.log(userId);

    $.ajax({
        type: "post",
        url: "updatePasss/" + userId,
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            location.reload();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".editpass_submit_btn").removeAttr("disabled");
            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}




$(".others_container").hide();
$("#address_type_select").on("change", function () {
    const value = this.value;

    if (value == 3) {
        $(".others_container").show();
        return;
    }

    $("#other_adress_type").val("");
    $(".others_container").hide();
});

$(".address_radio_input").on("click", function () {
    const selectedValue = $(this).attr("data-addressid");
    Swal.fire({
        title: "Are you sure want to make this address as default address for the user ?",
        text: "All later booking will be delivered Here !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Confirm",
        cancelButtonText: "No",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:
                    apiUrl +
                    `api/makeAddressAsDefault/${userId}/${selectedValue}`,
                method: "post",
                dataType: "json",
                success: function (response) {
                    var url = new URL(window.location.href);
                    url.searchParams.set("default", "true");
                    window.location.replace(url.href);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                },
            });
        }
    });
});

$(".address_delete_btn").on("click", function () {
    const selectedValue = $(this).attr("data-addressid");
    Swal.fire({
        title: "Are you sure want to remove this address?",
        text: "You wont able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Confirm",
        cancelButtonText: "No",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url:
                    apiUrl + `api/deleteUserAddress/${userId}/${selectedValue}`,
                method: "post",
                dataType: "json",
                success: function (response) {
                    var url = new URL(window.location.href);
                    url.searchParams.set("delete", "true");
                    window.location.replace(url.href);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
                },
            });
        }
    });
});
//
