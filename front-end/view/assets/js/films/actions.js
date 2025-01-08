async function getApiData(url, id=null) {
    const apiUrl = url;
    const movieId = id;

    if(movieId !== null){

        const filmsData = await fetchApi(apiUrl + movieId);

        if (filmsData && filmsData.data[0]) {
            displayFilmDetailsInModal(filmsData.data[0]);
        } else {
            console.error('Failed to load the movies.');
        }

    } else {
        const filmsData = await fetchApi(apiUrl);

        if (filmsData && filmsData.data) {
            displayFilmsOnPage(filmsData.data);
        } else {
            console.error('Failed to load the movies.');
        }
    }
}

window.getApiData = getApiData;