document.addEventListener('DOMContentLoaded', function () {
    let baseUrl = './../';
    let content = baseUrl + 'front-end/view/content-movie-detail.html';
    let idContent = 'main-content-detail';

    const urlParams = new URLSearchParams(window.location.search);
    const movieId = urlParams.get('id');

    let apiUrl = `${globalApiUrl}films/details/`;

    showSpinner(document.body);

    loadContent(`${baseUrl}front-end/view/header.html`, 'header-content');
    loadContent(content, idContent);
    loadContent(`${baseUrl}front-end/view/footer.html`, 'footer-content');

    getApiData(apiUrl, movieId);

    console.info("May the Force be with you!");

    setTimeout(() => {
        hideSpinner(document.body);
        document.getElementById(idContent).style.display = "block";
    }, 3000);

});
