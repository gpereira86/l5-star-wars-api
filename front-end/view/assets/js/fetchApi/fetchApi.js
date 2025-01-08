// Em fetchApi.js
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

async function fetchFilmDetails(id) {
    const apiUrl = `http://localhost/l5-test/api/films/details/`;


    showSpinner(document.getElementById("filmDetailModal"));

    setTimeout(() => {
        hideSpinner(document.getElementById("filmDetailModal"));
        document.getElementById("filmDetailModal").style.display = "block";
    }, 3000);

    getApiData(apiUrl, id);
}

window.fetchApi = fetchApi;
window.fetchFilmDetails = fetchFilmDetails;