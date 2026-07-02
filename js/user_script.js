let profile = document.querySelector('.header .flex .profile-detail');
let searchForm = document.querySelector('.header .flex .search-form');

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

//////home slider//////////////
const imgBox = document.querySelector('.slider-container');
const slides = document.getElementsByClassName('slideBox');
const dotsContainer = document.querySelector('.slider-dots');
var i = 0;
var slideTimer;

// Build the dot indicators based on how many slides there are
function renderDots() {
    if (!dotsContainer) return;
    dotsContainer.innerHTML = '';
    for (let d = 0; d < slides.length; d++) {
        const dot = document.createElement('span');
        dot.className = 'dot' + (d === i ? ' active' : '');
        dot.addEventListener('click', function () { goToSlide(d); });
        dotsContainer.appendChild(dot);
    }
}
function updateDots() {
    if (!dotsContainer) return;
    dotsContainer.querySelectorAll('.dot').forEach(function (dot, idx) {
        dot.classList.toggle('active', idx === i);
    });
}
function showSlide(n) {
    if (slides.length === 0) return;
    slides[i].classList.remove('active');
    i = (n + slides.length) % slides.length;
    slides[i].classList.add('active');
    updateDots();
}

// Called from the arrow buttons (inline onclick) — also reset the auto-play timer
function nextSlide() { showSlide(i + 1); resetSlideTimer(); }
function prevSlide() { showSlide(i - 1); resetSlideTimer(); }
function goToSlide(n) { showSlide(n); resetSlideTimer(); }

function startSlideTimer() {
    if (slides.length > 1) {
        slideTimer = setInterval(function () { showSlide(i + 1); }, 4000);
    }
}
function resetSlideTimer() {
    clearInterval(slideTimer);
    startSlideTimer();
}

if (slides.length > 0) {
    renderDots();
    startSlideTimer();

    if (imgBox) {
        imgBox.addEventListener('mouseenter', function () { clearInterval(slideTimer); });
        imgBox.addEventListener('mouseleave', resetSlideTimer);
    }
}

// Live search: update the full Search Result cards as you type (no Enter needed)
const searchInput = document.querySelector('#search-input');
const resultBox = document.querySelector('#box-container'); // exists only on the search page

if (searchInput && resultBox) {
    // We are on the search page: live-update the result cards as the user types.
    let searchTimer;

    // Keep typing seamlessly after arriving here from another page (cursor at end).
    if (searchInput.value) {
        searchInput.focus();
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.value = val;
    }

    searchInput.addEventListener('input', function () {
        const q = searchInput.value.trim();
        clearTimeout(searchTimer);

        // Cleared the box completely -> go back to the home page
        if (q === '') {
            window.location.href = 'home.php';
            return;
        }

        searchTimer = setTimeout(function () {
            fetch('search_results.php?q=' + encodeURIComponent(q))
                .then(function (res) { return res.text(); })
                .then(function (html) { resultBox.innerHTML = html; });
        }, 250);
    });
} else if (searchInput) {
    // Any other page (e.g. home): the first keystroke jumps to the search page,
    // where live search takes over. Clearing the box keeps you where you are.
    let navTimer;

    searchInput.addEventListener('input', function () {
        const q = searchInput.value.trim();
        clearTimeout(navTimer);

        if (q === '') {
            return;
        }

        navTimer = setTimeout(function () {
            window.location.href = 'search_product.php?q=' + encodeURIComponent(q);
        }, 400);
    });
}