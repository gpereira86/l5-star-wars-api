// window.onload = function () {
//     const spaceship1 = document.querySelector('.spaceship-1');
//     const spaceship2 = document.querySelector('.spaceship-2');
//     const explosion = document.querySelector('.explosion');
//
//     // Move spaceships towards each other
//     spaceship1.style.transform = 'translateX(50vw)';
//     spaceship2.style.transform = 'translateX(-50vw)';
//
//     // Trigger explosion after 2 seconds
//     setTimeout(() => {
//         // Calculate explosion position
//         const explosionX = window.innerWidth / 2 - 75; // Explosion centered
//         const explosionY = window.innerHeight / 2 - 75;
//
//         explosion.style.left = `${explosionX}px`;
//         explosion.style.top = `${explosionY}px`;
//         explosion.style.display = 'block';
//
//         // Hide spaceships after explosion
//         spaceship1.style.display = 'none';
//         spaceship2.style.display = 'none';
//
//         // Remove explosion after 1 second
//         setTimeout(() => {
//             explosion.style.display = 'none';
//         }, 1000);
//
//     }, 2000);
// };


// window.onload = function () {
//     const spaceship1 = document.querySelector('.spaceship-1');
//     const spaceship2 = document.querySelector('.spaceship-2');
//     const explosion = document.querySelector('.explosion');
//     const pageError = document.getElementById('pageError'); // A nova div com id="pageError"
//
//     // Inicialmente a div pageError deve estar oculta
//     pageError.style.display = 'none';
//
//     // Move spaceships towards each other
//     spaceship1.style.transform = 'translateX(50vw)';
//     spaceship2.style.transform = 'translateX(-50vw)';
//
//     // Trigger explosion after 2 seconds
//     setTimeout(() => {
//         // Calculate explosion position
//         const explosionX = window.innerWidth / 2 - 75; // Explosion centered
//         const explosionY = window.innerHeight / 2 - 75;
//
//         explosion.style.left = `${explosionX}px`;
//         explosion.style.top = `${explosionY}px`;
//         explosion.style.display = 'block';
//
//         // Hide spaceships after explosion
//         spaceship1.style.display = 'none';
//         spaceship2.style.display = 'none';
//
//         // Show pageError div after explosion
//         setTimeout(() => {
//             pageError.style.display = 'block'; // Mostra a div pageError
//
//             // Remove explosion after 1 second
//             setTimeout(() => {
//                 explosion.style.display = 'none';
//             }, 1000);
//         }, 1000);
//
//     }, 2000);
// };


// window.onload = function () {
//     const spaceship1 = document.querySelector('.spaceship-1');
//     const spaceship2 = document.querySelector('.spaceship-2');
//     const explosion = document.querySelector('.explosion');
//     const pageError = document.getElementById('pageError'); // A nova div com id="pageError"
//
//     // Inicialmente a div pageError deve estar oculta
//     pageError.style.display = 'none';
//
//     // Move spaceships towards each other (aplica o movimento no início)
//     spaceship1.style.transform = 'translateX(50vw)';
//     spaceship2.style.transform = 'translateX(-50vw)';
//
//     // Trigger explosion after 2 seconds
//     setTimeout(() => {
//         // Calculate explosion position
//         const explosionX = window.innerWidth / 2 - 75; // Explosion centered
//         const explosionY = window.innerHeight / 2 - 75;
//
//         explosion.style.left = `${explosionX}px`;
//         explosion.style.top = `${explosionY}px`;
//         explosion.style.display = 'block';
//
//         // Make spaceships fade out before disappearing (ao mesmo tempo que se movem)
//         spaceship1.style.opacity = 0;
//         spaceship2.style.opacity = 0;
//
//         // Deixar as naves visíveis por mais um tempo antes de desaparecerem completamente
//         setTimeout(() => {
//             spaceship1.style.display = 'none';
//             spaceship2.style.display = 'none';
//
//             // Show pageError div after explosion
//             pageError.style.display = 'block';
//
//             // Remove explosion after 1 second
//             setTimeout(() => {
//                 explosion.style.display = 'none';
//             }, 1000);
//         }, 1000); // Espera 1 segundo para esconder as naves após a transição de opacidade
//
//     }, 1950); // Naves se movem durante 2 segundos antes da explosão
// };


window.onload = function () {
    const spaceship1 = document.querySelector('.spaceship-1');
    const spaceship2 = document.querySelector('.spaceship-2');
    const explosion = document.querySelector('.explosion');
    const pageError = document.getElementById('pageError'); // A nova div com id="pageError"

    // Inicialmente a div pageError deve estar oculta
    pageError.style.visibility = 'hidden';
    pageError.style.opacity = 0;  // Inicialmente invisível

    // Adiciona uma transição de opacidade no pageError
    pageError.style.transition = 'opacity 3s ease-in-out'; // Transição suave

    // Move spaceships towards each other (aplica o movimento no início)
    spaceship1.style.transform = 'translateX(50vw)';
    spaceship2.style.transform = 'translateX(-50vw)';

    // Trigger explosion after 2 seconds
    setTimeout(() => {
        // Calculate explosion position
        const explosionX = window.innerWidth / 2 - 75; // Explosion centered
        const explosionY = window.innerHeight / 2 - 75;

        // Mostrar a explosão
        explosion.style.left = `${explosionX}px`;
        explosion.style.top = `${explosionY}px`;
        explosion.style.display = 'block';  // Torna a explosão visível
        explosion.style.opacity = 1;  // Define a opacidade para 1, tornando-a visível

        // Make spaceships fade out before disappearing (ao mesmo tempo que se movem)
        spaceship1.style.opacity = 0;
        spaceship2.style.opacity = 0;

        // Deixar as naves visíveis por mais um tempo antes de desaparecerem completamente
        setTimeout(() => {
            spaceship1.style.display = 'none';
            spaceship2.style.display = 'none';

            // Show pageError div after explosion
            pageError.style.visibility  = 'visible';
            pageError.style.opacity = 1;  // Tornar visível com efeito de fade-in

            // Fade out the explosion (esmaecer a explosão)
            explosion.style.opacity = 0;

            // Remove explosion from view after the opacity transition
            setTimeout(() => {
                explosion.style.display = 'none';
            }, 1000); // Tempo para desaparecer suavemente
        }, 1000); // Espera 1 segundo para esconder as naves após a transição de opacidade

    }, 1950); // Naves se movem durante 2 segundos antes da explosão
};



