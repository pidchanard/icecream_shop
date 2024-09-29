let profile = document.querySelector('.header .flex .profile-detail');
let searchForm = document.querySelector('.header .flex .search-form');
let navbar = document.querySelector('.navbar');

document.querySelector('#user-btn').onclick = () => {
    if (profile.style.display === 'block') {
        profile.style.display = 'none';
    } else {
        profile.style.display = 'block';
    }
    searchForm.style.display = 'none';
};

document.querySelector('#search-btn').onclick = () => {
    if (searchForm.style.display === 'block') {
        searchForm.style.display = 'none';
    } else {
        searchForm.style.display = 'block';
    }
    profile.style.display = 'none';
};

document.querySelector('#menu-btn').onclick = () => {
    // สลับการแสดงผลของ navbar
    if (navbar.style.display === 'block' || navbar.style.display === '') {
        navbar.style.display = 'none';
    } else {
        navbar.style.display = 'block';
    }
};
//////home slider//////////////
const imgBox =document.querySelector('.slider-container');
const slides =document.getElementsByClassName('slideBox');
var i =0;

function nextSlide(){
    slides[i].classList.remove('active');
    i = (i+1) % slides.length;
    slides[i].classList.add('active')
}
function prevSlide(){
    slides[i].classList.remove('active');
    i = (i -1+ slides.length) % slides.length;
    slides[i].classList.add('active');
}