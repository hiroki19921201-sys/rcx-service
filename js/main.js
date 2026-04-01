const fvSwiper = new Swiper('.fv-swiper', {
  loop: true,
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
  },
  speed: 800,
  effect: 'fade',
  fadeEffect: {
    crossFade: true,
  },
  pagination: {
    el: '.fv-swiper .swiper-pagination',
    clickable: true,
  },
});

// ハンバーガーメニュー
const hamburger = document.getElementById('hamburger');
const headerNav = document.getElementById('headerNav');

hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('active');
  headerNav.classList.toggle('open');
});

// ナビリンクをクリックしたらメニューを閉じる
headerNav.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    hamburger.classList.remove('active');
    headerNav.classList.remove('open');
  });
});

let productsSwiper = null;

function initProductsSwiper() {
  if (window.innerWidth > 768 && !productsSwiper) {
    productsSwiper = new Swiper('.products-swiper', {
      loop: true,
      speed: 600,
      spaceBetween: 30,
      slidesPerView: 2.5,
      pagination: {
        el: '.products-pagination',
        clickable: true,
      },
    });
  } else if (window.innerWidth <= 768 && productsSwiper) {
    productsSwiper.destroy(true, true);
    productsSwiper = null;
  }
}

initProductsSwiper();
window.addEventListener('resize', initProductsSwiper);
