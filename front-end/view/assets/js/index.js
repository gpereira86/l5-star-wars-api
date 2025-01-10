document.addEventListener('DOMContentLoaded', function () {
    let baseUrl = './';
    let content = baseUrl + 'front-end/view/content.html';
    let idContent = 'main-content';
    let apiUrl = 'http://localhost/l5-test/api/films';

    showSpinner(document.body);

    loadContent(`${baseUrl}front-end/view/header.html`, 'header-content');
    loadContent(content, idContent);
    loadContent(`${baseUrl}front-end/view/footer.html`, 'footer-content');
    //loadContent(`${baseUrl}front-end/view/modal.html`, 'modal-content');

    getApiData(apiUrl);

    const myModal = document.getElementById('filmDetailModal');

    console.info("May the Force be with you!");

    setTimeout(() => {
        hideSpinner(document.body);
        document.getElementById(idContent).style.display = "block";
    }, 2000);

});
