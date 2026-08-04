const addValidator = new JustValidate("#adduserForm", {
    validateBeforeSubmitting: true,
});
const addValidator1 = new JustValidate("#editUserForm", {
    validateBeforeSubmitting: true,
});
const addValidator2 = new JustValidate("#updateUserForm",{
    validateBeforeSubmitting: true,
})

addValidator

    .addField('#add_country_id', [
        {
            rule: "required",
            errorMessage: "*Country is required",
        },
    ])
    .addField("#add_user_name", [
        {
            rule: "required",
            errorMessage: "*Name Field is required",
        },
        {

            rule: 'minLength',
            value: 3,
            errorMessage: '*Name should be at Minimum 4 character',

        },

    ])
    .addField('#add_email', [
        {
            rule: "required",
            errorMessage: "*Email Field is required",
        },
    ])
    .addField('#add_phone_number', [
        {
            rule: "required",
            errorMessage: "*Phone Number Field is required",
        },
    ])
    .addField('#add_role_select', [
        {
            rule: "required",
            errorMessage: "*Role Field is required",
        },
    ])
    .addField('#add_status_select', [
        {
            rule: "required",
            errorMessage: "*Status Field is required",
        },
    ])
    .addField('#add_password', [
        {
            rule: "required",
            errorMessage: "*Password Field is required",
        },
    ])
    .onSuccess((event) => {
        $(".adduser_submit_btn").attr("disabled", "true");
        $(".adduser_submit_btn").html("Uploading.....");
        adduserFormSubmit(event);
    });

    addValidator1
    .addField("#edit_user_name", [
        {
            rule: "required",
            errorMessage: "*Name Field is required",
        },
        {

            rule: 'minLength',
            value: 3,
            errorMessage: '*Name should be at Minimum 4 character',

        },

    ])
    .addField('#edit_email', [
        {
            rule: "required",
            errorMessage: "*Email Field is required",
        },
    ])
    .addField('#edit_phone_number', [
        {
            rule: "required",
            errorMessage: "*Phone Number Field is required",
        },
    ])
    .addField('#edit_role_select', [
        {
            rule: "required",
            errorMessage: "*Role Field is required",
        },
    ])
    .addField('#edit_status_select', [
        {
            rule: "required",
            errorMessage: "*Status Field is required",
        },
    ])

    .onSuccess((event) => {
        $(".edituser_submit_btn").attr("disabled", "true");
        $(".edituser_submit_btn").html("Uploading.....");
        editUserFormSubmit(event);
    });

    addValidator2
        .addField('#update_user_password',[
            {
                rule: "required",
                errorMessage: "*Password Field is required",
            }
        ])
        .onSuccess((event) => {
            $(".updete_submit_btn").attr("disabled", "true");
            $(".updete_submit_btn").html("Uploading.....");
            updateUserFormSubmit(event);
        });



const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Employee id",
        "Name",
        "Email Id",
        "Phone Number",
        "Country",
        "Role",
        "Status",
        {
            name: "Action",
            sort: false,
        },
    ],
    pagination: {
        limit: 10,
    },
    sort: !0,
    search: !0,
    data: users.map((users, index) => {

    let countryText = '-';
    if (users.country_id && Array.isArray(users.country_id)) {
        countryText = users.country_id.map(id => {
            const c = window.countries.find(c => c.id == id);
            return c ? c.name : id;
        }).join(', ');
    }
    let roleText = users.roles.length > 0 
    ? users.roles[0].name 
    : 'No Role';
    let statusText = '';
    if(users.status == '1'){
        statusText = 'Active';
    } else if(users.status == '2'){
        statusText = 'InActive';
    }
        return [
            index + 1,
            users.empl_num,
            users.name,
            users.email,
            users.phone_number,
            countryText, 
            roleText,
            statusText,
            gridjs.html(
                `<div> <button data-bs-toggle="modal"
                data-userid ="${users.id}"
                data-name = "${users.name}"
                data-email="${users.email}"
                data-phonenumber ="${users.phone_number}"
                data-userrole="${users.roles.length ? users.roles[0].name : ''}"
                data-userstatus="${users.status}" data-country='${JSON.stringify(users.country_id)}'
                data-bs-target="#editProductModal"  class="btn btn-secondary edit_btn2 ">Edit</button> <button class="btn btn-danger delete_btn" style="margin-top: 3px;
                margin-bottom: 3px" data-userid = ${users.id}>Delete</button>
                <button class="btn btn-success update-btn"   data-bs-toggle="modal" data-bs-target="#edit1PasswordModal" data-userid = ${users.id}>Password</button></div>`
            ),

        ];
    }),
    style: {
        table: {
            border: "1px solid #ccc",
        },
        th: {
            "background-color": "rgba(0, 0, 0, 0.1)",
            color: "#000",
            "border-bottom": "3px solid #ccc",
            "text-align": "center",
            "border-right": "0.5px solid #ccc",
        },
        td: {
            "text-align": "center",
            "border-right": "0.5px solid #ccc",
            "border-bottom": "0.5px solid #ccc",
        },
    },
});

gridNew.render(document.getElementById("table-gridjs"));


function gridjsReRender(users) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");
    gridNew
        .updateConfig({
            data: users.map((users, index) => {

                // ✅ Resolve country names from window.countries
                let countryText = '-';
                if (users.country_id && Array.isArray(users.country_id)) {
                    countryText = users.country_id.map(id => {
                        const c = window.countries.find(c => c.id == id);
                        return c ? c.name : id;
                    }).join(', ');
                }

                let roleText = users.roles.length > 0 ? users.roles[0].name : 'No Role';
                let statusText = '';
                if (users.status == '1') { statusText = 'Active'; }
                else if (users.status == '2') { statusText = 'InActive'; }

                return [
                    index + 1,
                    users.empl_num,
                    users.name,
                    users.email,
                    users.phone_number,
                    countryText,
                    roleText,
                    statusText,
                    gridjs.html(
                        `<div>
                        <button data-bs-toggle="modal"
                            data-userid="${users.id}"
                            data-name="${users.name}"
                            data-email="${users.email}"
                            data-phonenumber="${users.phone_number}"
                            data-userrole="${users.roles.length ? users.roles[0].name : ''}"
                            data-userstatus="${users.status}"
                            data-country='${JSON.stringify(users.country_id)}'
                            data-bs-target="#editProductModal"
                            class="btn btn-secondary edit_btn2">Edit</button>
                        <button class="btn btn-danger delete_btn" style="margin-top:3px;margin-bottom:3px" data-userid="${users.id}">Delete</button>
                        <button class="btn btn-success update-btn" data-bs-toggle="modal" data-bs-target="#edit1PasswordModal" data-userid="${users.id}">Password</button>
                        </div>`
                    ),
                ];
            }),
        })
        .forceRender();
}

//  addusers

function adduserFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "userss",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            $(".adduser_submit_btn").removeAttr("disabled");
            $(".adduser_submit_btn").html("Submit");

            const updatedProduct = response.users;
            $("#adduserForm")[0].reset();
            $("#addUserModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            console.log(updatedProduct);

            gridjsReRender(updatedProduct);
            Swal.fire("Added", "Records Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edituser_submit_btn").removeAttr("disabled");
            $(".adduser_submit_btn").removeAttr("disabled");
            $(".edituser_submit_btn").html("Update");
            $(".adduser_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}

$(document).on("click", ".edit_btn2", function () {

    $("#edit_user_id").val($(this).data("userid"));
    $("#edit_user_name").val($(this).data("name"));
    $("#edit_email").val($(this).data("email"));
    $("#edit_phone_number").val($(this).data("phonenumber"));

    $("#edit_role_select").val($(this).data("userrole")).trigger('change');
    $("#edit_status_select").val($(this).data("userstatus")).trigger('change');

    let countries = $(this).data("country");

    let countryArray = [];

    if (countries) {
        try {
            // if JSON string
            countryArray = JSON.parse(countries);
        } catch (e) {
            // fallback for comma string
            countryArray = countries.split(',').filter(Boolean);
        }
    }

    setTimeout(() => {
        $("#edit_country_id")
            .val(countryArray)
            .trigger("change");
    }, 200);
});

// update password

$(document).on("click", ".update-btn", function () {

    $("#update_user_id").val($(this).attr("data-userid"));
});
// update password

function updateUserFormSubmit(e){
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updatepass/" + $("#update_user_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedProduct = response.users;
            $("#editUserForm")[0].reset();
            $("#edit1PasswordModal").hide();
            $(".modal-backdrop").remove();
            $(".edit_product_remove_btn ").trigger("click");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedProduct);
            $(".updete_submit_btn").removeAttr("disabled");
            $(".updete_submit_btn").html("Update");
            Swal.fire("Updated", "Records Updated  Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".updete_submit_btn").removeAttr("disabled");
            $(".adduser_submit_btn").removeAttr("disabled");
            $(".updete_submit_btn").html("Update");
            $(".adduser_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    })
}

// edit users
function editUserFormSubmit(e) {
    const formdata = new FormData(e.target);
    $.ajax({
        url: "updateuser/" + $("#edit_user_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            const updatedProduct = response.users;
            $("#editUserForm")[0].reset();
            bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
            $(".modal-backdrop").remove();
            $(".edit_product_remove_btn ").trigger("click");
            document.body.style.overflowY = "scroll";
            gridjsReRender(updatedProduct);
            $(".edituser_submit_btn").removeAttr("disabled");
            $(".edituser_submit_btn").html("Update");
            Swal.fire("Updated", "Records Updated  Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".edituser_submit_btn").removeAttr("disabled");
            $(".adduser_submit_btn").removeAttr("disabled");
            $(".edituser_submit_btn").html("Update");
            $(".adduser_submit_btn").html("Submit");
            console.log(textStatus + ": " + errorThrown);

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}



// delete user

$(document).on("click", ".delete_btn", function () {
    const id = $(this).attr("data-userid");
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "destroyusers/" + id,
                method: "post",
                dataType: "json",
                success: function (response) {
                    const updatedProduct = response.users;
                    gridjsReRender(updatedProduct);
                    Swal.fire(
                        "Deleted!",
                        "Records Deleted Successfully.",
                        "success"
                    );
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $(".edituser_submit_btn").removeAttr("disabled");
                    $(".adduser_submit_btn").removeAttr("disabled");
                    $(".edituser_submit_btn").html("Update");
                    $(".adduser_submit_btn").html("Submit");
                    console.log(textStatus + ": " + errorThrown);

                    Swal.fire(
                        textStatus.toUpperCase(),
                        errorThrown,
                        "warning"
                    );
                },
            });
        }
    });
});


 $('#addUserModal').on('shown.bs.modal', function () {

    if ($('#add_country_id').hasClass("select2-hidden-accessible")) {
        $('#add_country_id').select2('destroy'); // ✅ destroy old instance
    }

    $('#add_country_id').select2({
        placeholder: "Select Countries",
        width: '100%',
        dropdownParent: $('#addUserModal')
    });
});

  $('#editProductModal').on('shown.bs.modal', function () {

    if ($('#edit_country_id').hasClass("select2-hidden-accessible")) {
        $('#edit_country_id').select2('destroy'); // ✅ destroy old instance
    }

    $('#edit_country_id').select2({
        placeholder: "Select Countries",
        width: '100%',
        dropdownParent: $('#editProductModal')
    });
});
