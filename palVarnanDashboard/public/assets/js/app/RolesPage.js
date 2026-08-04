const addValidator = new JustValidate("#addRoleForm", {
    validateBeforeSubmitting: true,
});
const addValidator1 = new JustValidate("#editRoleForm", {
    validateBeforeSubmitting: true,
});

addValidator
    .addField("#add_role_name", [
        {
            rule: "required",
            errorMessage: "*Role Name is required",
        },
        {
            rule: 'minLength',
            value: 3,
            errorMessage: '*Minimum 3 characters required',
        },
    ])
    .onSuccess((event) => {
        $(".addrole_submit_btn").attr("disabled", "true");
        $(".addrole_submit_btn").html("Uploading.....");
        addRoleFormSubmit(event);
    });

addValidator1
    .addField("#edit_role_name", [
        {
            rule: "required",
            errorMessage: "*Role Name is required",
        },
        {
            rule: 'minLength',
            value: 3,
            errorMessage: '*Minimum 3 characters required',
        },
    ])
    .onSuccess((event) => {

        $(".editrole_submit_btn").attr("disabled", "true");
        $(".editrole_submit_btn").html("Uploading.....");
        editRoleFormSubmit(event);
    });


// GRID
const gridNew = new gridjs.Grid({
    columns: [
        "S.NO",
        "Role Name",
        // "Guard Name",   
        {
            name: "Action",
            sort: false,
        },
    ],
    pagination: {
        limit: 10,
    },
    sort: true,
    search: true,

    data: roles.map((role, index) => {
        return [
            index + 1,
            role.name,
            // role.guard_name,

            gridjs.html(`
                <div> 
                    <button data-bs-toggle="modal"
                        data-roleid="${role.id}"
                        data-name="${role.name}"
                        data-permissions='${JSON.stringify(role.permissions)}'
                        data-bs-target="#editRoleModal"
                        class="btn btn-secondary edit_btn2">
                        Edit
                    </button> 

                    <button class="btn btn-danger delete_btn"
                        style="margin-top:3px;margin-bottom:3px"
                        data-roleid="${role.id}">
                        Delete
                    </button>

                    <button 
                        class="btn btn-info permission_btn"
                        data-roleid="${role.id}"
                        data-rolename="${role.name}"
                        data-permissions='${JSON.stringify(role.permissions)}'
                        data-bs-toggle="modal"
                        data-bs-target="#permissionModal">
                        Permissions
                    </button>
                </div>
            `),
        ];
    }),

    style: {
        table: { border: "1px solid #ccc" },
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


// RE-RENDER
function gridjsReRender(roles) {
    if (gridNew) gridNew.config.plugin.remove("pagination");
    if (gridNew) gridNew.config.plugin.remove("search");

    gridNew.updateConfig({
        data: roles.map((role, index) => {
            return [
                index + 1,
                role.name,
                role.guard_name, // ✅ ADD THIS

                gridjs.html(`
                    <div> 
                        <button data-bs-toggle="modal"
                            data-roleid="${role.id}"
                            data-name="${role.name}"
                            data-permissions='${JSON.stringify(role.permissions)}'
                            data-bs-target="#editRoleModal"
                            class="btn btn-secondary edit_btn2">
                            Edit
                        </button> 

                        <button class="btn btn-danger delete_btn"
                            style="margin-top:3px;margin-bottom:3px"
                            data-roleid="${role.id}">
                            Delete
                        </button>

                        <a href="roles/${role.id}/permissions"
                            class="btn btn-info"
                            style="margin-top:3px;">
                            Permissions
                        </a>
                    </div>
                `),
            ];
        }),
    }).forceRender();
}


// ADD ROLE
function addRoleFormSubmit(e) {
    const formdata = new FormData(e.target);

    $.ajax({
        url: "roles/store",
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {

            $(".addrole_submit_btn").removeAttr("disabled");
            $(".addrole_submit_btn").html("Submit");

            const updatedData = response.roles;

            $("#addRoleForm")[0].reset();
            $("#addRoleModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender(updatedData);

            Swal.fire("Added", "Role Added Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {

            $(".addrole_submit_btn").removeAttr("disabled");
            $(".addrole_submit_btn").html("Submit");

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}


// EDIT CLICK
$(document).on("click", ".edit_btn2", function () {

    $("#edit_role_id").val($(this).attr("data-roleid"));
    $("#edit_role_name").val($(this).attr("data-name"));

    let rolePerms = JSON.parse($(this).attr("data-permissions"));

    let html = "";

    permissions.forEach(p => {
        let checked = rolePerms.some(rp => rp.name === p.name) ? "checked" : "";

        html += `
            <div class="col-md-4">
                <input type="checkbox" name="permissions[]" value="${p.name}" ${checked}>
                ${p.name}
            </div>
        `;
    });

    $("#edit_permissions_container").html(html);
});


// UPDATE ROLE
function editRoleFormSubmit(e) {
    const formdata = new FormData(e.target);

    $.ajax({
        url: "roles/update/" + $("#edit_role_id").val(),
        method: "POST",
        dataType: "json",
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {

            const updatedData = response.roles;

            $("#editRoleForm")[0].reset();
            $("#editRoleModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            gridjsReRender(updatedData);

            $(".editrole_submit_btn").removeAttr("disabled");
            $(".editrole_submit_btn").html("Update");

            Swal.fire("Updated", "Role Updated Successfully.", "success");
        },
        error: function (jqXHR, textStatus, errorThrown) {

            $(".editrole_submit_btn").removeAttr("disabled");
            $(".editrole_submit_btn").html("Update");

            Swal.fire(textStatus.toUpperCase(), errorThrown, "warning");
        },
    });
}


// DELETE ROLE
$(document).on("click", ".delete_btn", function () {

    const id = $(this).attr("data-roleid");

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
                url: "roles/delete/" + id,
                method: "post",
                dataType: "json",
                success: function (response) {

                    const updatedData = response.roles;

                    gridjsReRender(updatedData);

                    Swal.fire(
                        "Deleted!",
                        "Role Deleted Successfully.",
                        "success"
                    );
                },
                error: function (jqXHR, textStatus, errorThrown) {

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

$(document).on("click", ".permission_btn", function () {

    let roleId = $(this).data("roleid");
    let roleName = $(this).data("rolename");
    let rolePerms = JSON.parse($(this).attr("data-permissions"));

    $("#permission_role_id").val(roleId);
    $("#permission_role_title").text("Manage Permissions - " + roleName);

    let html = "";

    // GROUP PERMISSIONS (same as backend)
    let grouped = {};

    permissions.forEach(p => {
        let module = p.name.split('.')[0];

        if (!grouped[module]) grouped[module] = [];
        grouped[module].push(p);
    });

    for (let module in grouped) {

        html += `
            <div class="col-md-4 mb-3">
                <div class="border p-3 rounded">

                    <b class="text-primary">${module}</b>

                    <div>
                        <input type="checkbox" class="select_all" data-module="${module}">
                        <small>Select All</small>
                    </div>
                    <hr>
        `;

        grouped[module].forEach(p => {

            let checked = rolePerms.some(rp => rp.name === p.name) ? "checked" : "";

            html += `
                <div>
                    <input type="checkbox"
                        class="perm_${module}"
                        name="permissions[]"
                        value="${p.name}"
                        ${checked}>
                    ${p.name}
                </div>
            `;
        });

        html += `</div></div>`;
    }

    $("#permission_container").html(html);
});

$(document).on("change", ".select_all", function () {

    let module = $(this).data("module");

    $(".perm_" + module).prop("checked", this.checked);
});

$(document).on("submit", "#permissionForm", function (e) {
    e.preventDefault();

    $(".permission_submit_btn").attr("disabled", true).text("Saving...");

    let formData = new FormData(this);
    let roleId = $("#permission_role_id").val();

    $.ajax({
        url: "roles/" + roleId + "/permissions",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {

            $(".permission_submit_btn").removeAttr("disabled").text("Save Permissions");

            $("#permissionModal").hide();
            $(".modal-backdrop").remove();
            document.body.style.overflowY = "scroll";

            Swal.fire("Success", "Permissions Updated", "success");

            // OPTIONAL: reload roles to update permissions in grid
            location.reload();

        },
        error: function () {
            $(".permission_submit_btn").removeAttr("disabled").text("Save Permissions");
            Swal.fire("Error", "Something went wrong", "error");
        }
    });
});