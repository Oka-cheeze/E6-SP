let currentIndex = 0;
function moveSlide(direction) {
    const slide = document.querySelector('.carousel-slide');
    const cards = document.querySelectorAll('.card');
    const cardWidth = cards[0].offsetWidth + 20; 

    currentIndex += direction;
    if (currentIndex < 0) currentIndex = cards.length - 1;
    if (currentIndex >= cards.length) currentIndex = 0;

    slide.style.transform = `translateX(${-currentIndex * cardWidth}px)`;
}