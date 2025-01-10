// Em fetchApi.js
async function fetchApi(endpoint, options = {}) {
    try {
        const response = await fetch(endpoint, options);
        if (!response.ok) {
            fetchLogResponse(
                options.method || 'GET',
                endpoint,
                response.status
            );
            throw new Error(`Request error: ${response.status}`);

        }
        const data = await response.json();

        fetchLogResponse(
            data.method,
            data.endpoint,
            data.responseCode);

        return data;
    } catch (error) {
        console.error('Error loading data: ', error);
        return null;
    }
}

async function fetchFilmDetails(id, modal = true) {
    const apiUrl = `http://localhost/l5-test/api/films/details/`;
    let elementId = 'filmDetailModal';

    if(modal){
        showSpinner(document.getElementById(elementId));

        setTimeout(() => {
            hideSpinner(document.getElementById(elementId));
            document.getElementById(elementId).style.display = "block";
        }, 3000);

        getApiData(apiUrl, modal, id);

    } else{
        let data = [apiUrl, modal, id]
        sessionStorage.setItem('movieId', data);
        window.location.href = window.location.pathname + "movie/teste";

    }
}

function fetchLogResponse(request_method, endpoint, response_code) {
    const apiUrl = "http://localhost/l5-test/api/log-register";

    const data = {
        request_method: request_method,
        endpoint: endpoint,
        response_code: response_code
    };

    fetch(apiUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error: ${response.status}`);
            }
            return response.json();
        })
        // .then(data => {
        //     console.log("Resposta do Backend:", data);
        // })
        .catch(error => {
            console.error("Error:", error);
        });
}

window.fetchApi = fetchApi;
window.fetchFilmDetails = fetchFilmDetails;