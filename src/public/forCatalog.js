
    const cards = document.querySelectorAll('.card');


    cards.forEach(function(card) {

    card.addEventListener('mouseenter', function() {

        this.style.transition = 'transform 0.3s, box-shadow 0.3s';
        this.style.transform = 'scale(1.05)';
        this.style.boxShadow = '0 4px 20px rgba(0,0,0,0.2)';
    });

    card.addEventListener('mouseleave', function() {

    this.style.transform = 'scale(1)';
    this.style.boxShadow = '';
});
});
