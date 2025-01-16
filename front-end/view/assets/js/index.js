document.addEventListener('DOMContentLoaded', function () {
    let baseUrl = './';
    let content = baseUrl + 'front-end/view/content.html';
    let idContent = 'main-content';
    let apiUrl = `${globalApiUrl}films`;

    showSpinner(document.body);

    loadContent(`${baseUrl}front-end/view/header.html`, 'header-content');
    loadContent(content, idContent);
    loadContent(`${baseUrl}front-end/view/footer.html`, 'footer-content');

    // There is the possibility to change the movie details display to a Modal.
    // The mechanism below enables the modal's content to be populated on the page.
    // loadContent(`${baseUrl}front-end/view/modal.html`, 'modal-content');

    getApiData(apiUrl);

    console.info("May the Force be with you!");

    setTimeout(() => {
        hideSpinner(document.body);
        document.getElementById(idContent).style.display = "block";
    }, 3000);

});
