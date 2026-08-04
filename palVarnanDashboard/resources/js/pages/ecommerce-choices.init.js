/*
Template Name: Milk Vandi - Admin & Dashboard Template
Author: MilkVandi
Website: https://MilkVandi.com/
Contact: MilkVandi@gmail.com
File: Ecommerce Choices Js File
*/

// Chocies Select plugin
document.addEventListener("DOMContentLoaded", function () {
    var genericExamples = document.querySelectorAll("[data-trigger]");
    for (i = 0; i < genericExamples.length; ++i) {
        var element = genericExamples[i];
        new Choices(element, {
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "This is a search placeholder",
        });
    }
});
