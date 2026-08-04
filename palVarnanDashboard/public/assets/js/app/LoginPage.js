const addValidator = new JustValidate("#loginForm");

function convertToLowercase(event) {
    const inputValue = event.target.value;
    event.target.value = inputValue.toLowerCase();
}


addValidator
    .addField("#useremail", [
        {
            rule: "required",
            errorMessage: "*Email Field is required",
        },
    ])
    .addField("#userpassword", [
        {
            rule: "required",
            errorMessage: "*Password Field is required",
        },
    ])
    .onSuccess((event) => {
        event.target.submit();
    });
    document.querySelector("#useremail").addEventListener("input", convertToLowercase);
    
    $(document).ready(function () {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Regex for validating email

    $("#useremail").on("input", function (event) {
        const inputValue = $(this).val();
        if (!emailRegex.test(inputValue)) {
            $(this).addClass("is-invalid"); // Add a class for invalid input
        } else {
            $(this).removeClass("is-invalid"); // Remove the class for valid input
        }
    });
});