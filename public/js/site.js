// ==========================================
// SLIDESHOW HERO OTOMATIS
// ==========================================
let currentSlideIndex = 0;
let slideAutoTimer;

function initHeroSlideshow() {
    if (!document.querySelector('.hero-slideshow')) return;
    showSlides(currentSlideIndex);
    startAutoSlide();
}

function showSlides(index) {
    const slides = document.querySelectorAll('.hero-slideshow .slide');
    const indicators = document.querySelectorAll('.slider-indicators .indicator-line');
    if (slides.length === 0) return;

    if (index >= slides.length) currentSlideIndex = 0;
    if (index < 0) currentSlideIndex = slides.length - 1;

    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(ind => ind.classList.remove('active'));

    slides[currentSlideIndex].classList.add('active');
    if (indicators[currentSlideIndex]) indicators[currentSlideIndex].classList.add('active');
}

function changeSlide(direction) {
    resetAutoSlide();
    currentSlideIndex += direction;
    showSlides(currentSlideIndex);
}

function goToSlide(index) {
    resetAutoSlide();
    currentSlideIndex = index;
    showSlides(currentSlideIndex);
}

function startAutoSlide() {
    slideAutoTimer = setInterval(() => {
        currentSlideIndex++;
        showSlides(currentSlideIndex);
    }, 5000);
}

function resetAutoSlide() {
    clearInterval(slideAutoTimer);
    startAutoSlide();
}

// ==========================================
// ANIMASI SCROLL REVEAL
// ==========================================
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.scroll-reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });

    revealElements.forEach(el => observer.observe(el));
}

// ==========================================
// TOGGLE CART SIDEBAR (isi diambil dari halaman /cart Laravel via fetch, opsional)
// Untuk versi sederhana, sidebar ini hanya toggle buka/tutup;
// isinya kita render langsung dari Blade saat halaman dimuat (lihat komponen cart-sidebar.blade.php)
// ==========================================
document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
    initHeroSlideshow();

    const cartBtn = document.getElementById('cart-btn');
    const closeCartBtn = document.getElementById('close-cart');
    const cartSidebar = document.getElementById('cart-sidebar');

    if (cartBtn && cartSidebar) {
        cartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            cartSidebar.classList.add('open');
        });
    }

    if (closeCartBtn && cartSidebar) {
        closeCartBtn.addEventListener('click', () => {
            cartSidebar.classList.remove('open');
        });
    }
});

// Hitung mundur waktu lelang (dipanggil dari halaman yang punya elemen [data-countdown])
function initCountdowns() {
    const timers = document.querySelectorAll('[data-countdown]');
    if (timers.length === 0) return;

    setInterval(() => {
        timers.forEach(el => {
            const endTime = parseInt(el.dataset.countdown, 10);
            const selisih = endTime - Date.now();

            if (selisih <= 0) {
                el.innerText = 'LELANG SELESAI';
                el.style.color = '#FF5555';
                return;
            }

            const jam = Math.floor(selisih / (1000 * 60 * 60));
            const menit = Math.floor((selisih % (1000 * 60 * 60)) / (1000 * 60));
            const detik = Math.floor((selisih % (1000 * 60)) / 1000);
            el.innerText = `${jam.toString().padStart(2, '0')}:${menit.toString().padStart(2, '0')}:${detik.toString().padStart(2, '0')}`;
        });
    }, 1000);
}

document.addEventListener('DOMContentLoaded', initCountdowns);
