/*
Template Name: Milk Vandi - Admin & Dashboard Template
Author: MilkVandi
Website: https://MilkVandi.com/
Contact: MilkVandi@gmail.com
File: Timeline Init Js File
*/

var swiper = new Swiper(".slider", {
    slidesPerView: 1,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 4,
        },
    },
});

var swiper = new Swiper("#timeline-card-slider", {
    slidesPerView: 1,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        768: {
            slidesPerView: 3,
        },
        1024: {
            slidesPerView: 5,
        },
    },
});
