window.onload = function () {
    const spaceship1 = document.querySelector('.spaceship-1');
    const spaceship2 = document.querySelector('.spaceship-2');
    const explosion = document.querySelector('.explosion');
    const pageError = document.getElementById('pageError');

    pageError.style.visibility = 'hidden';
    pageError.style.opacity = 0;
    pageError.style.transition = 'opacity 3s ease-in-out';

    spaceship1.style.transform = 'translateX(49vw)';
    spaceship2.style.transform = 'translateX(-51vw)';

    setTimeout(() => {
        explosion.style.display = 'block';
        explosion.style.opacity = 1;

        spaceship1.style.opacity = 0;
        spaceship2.style.opacity = 0;

        setTimeout(() => {
            spaceship1.style.display = 'none';
            spaceship2.style.display = 'none';

            pageError.style.visibility  = 'visible';
            pageError.style.opacity = 1;

            explosion.style.opacity = 0;

            setTimeout(() => {
                explosion.style.display = 'none';
            }, 1000);
        }, 1000);

    }, 1950);
};