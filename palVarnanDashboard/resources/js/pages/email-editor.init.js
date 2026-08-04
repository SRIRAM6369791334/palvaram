/*
Template Name: Milk Vandi - Admin & Dashboard Template
Author: MilkVandi
Website: https://MilkVandi.com/
Contact: MilkVandi@gmail.com
File: Email summernote Js File
*/

ClassicEditor.create(document.querySelector("#email-editor"))
    .then(function (editor) {
        editor.ui.view.editable.element.style.height = "200px";
    })
    .catch(function (error) {
        console.error(error);
    });
