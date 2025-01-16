async function fetchApi(endpoint, options = {}) {

    try {
        const response = await fetch(endpoint, options);
        if (!response.ok) {
            throw new Error(`Request error: ${response.status}`);
        }
        const data = await response.json();

        return data;
    } catch (error) {
        console.error('Error loading data: ', error);
        return null;
    }
}


// The function below is only used when the modal is activated.
async function fetchFilmDetails(id, modal = true) {
    const apiUrl = `${globalApiUrl}films/details/`;
    let elementId = 'filmDetailModal';

    if(modal){

        showSpinner(document.getElementById(elementId));

        setTimeout(() => {
            hideSpinner(document.getElementById(elementId));
            document.getElementById(elementId).style.display = "block";
        }, 3000);

        getApiData(apiUrl, id);

    } else{
        let data = [apiUrl, modal, id]
        sessionStorage.setItem('movieId', data);
        window.location.href = window.location.pathname + "page-error";

    }
}


window.fetchApi = fetchApi;