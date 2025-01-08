document.addEventListener('DOMContentLoaded', function () {

    showSpinner(document.body);

    setTimeout(() => {
        hideSpinner(document.body);
        document.getElementById("main-content").style.display = "block";
    }, 2000);


    loadContent('./front-end/view/header.html', 'header-content');
    loadContent('./front-end/view/menu.html', 'menu-content');
    loadContent('./front-end/view/content.html', 'main-content');
    loadContent('./front-end/view/film-detail.html', 'modal-content');
    loadContent('./front-end/view/footer.html', 'footer-content');
    console.info("May the Force be with you!");

    getApiData('http://localhost/l5-test/api/films');

    const myModal = document.getElementById('filmDetailModal')

});

