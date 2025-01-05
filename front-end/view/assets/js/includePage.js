function loadContent(file, elementId) {

    fetch(file)
        .then(response => {
            if (!response.ok) {
                throw new Error('Falha ao carregar o arquivo: ' + file);
            }
            return response.text();
        })
        .then(data => {
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = data;
            } else {
                console.error(`Elemento com id "${elementId}" não encontrado`);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar o conteúdo:', error);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    loadContent('./front-end/view/header.html', 'header-content');
    loadContent('./front-end/view/menu.html', 'menu-content');
    loadContent('./front-end/view/content.html', 'main-content');
    loadContent('./front-end/view/footer.html', 'footer-content');
    loadContent('./front-end/view/film-detail.html', 'footer-content');
    console.info("May the Force be with you!");
});