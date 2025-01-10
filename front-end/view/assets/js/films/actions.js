async function getApiData(url, id=null) {
    const apiUrl = url;
    const movieId = (id !== null ? id : '');
    const filmsData = await fetchApi(apiUrl + movieId);

    if(movieId !== ''){

        if (filmsData && filmsData.data[0]) {
            // displayFilmDetailsInModal(filmsData.data[0]);
            displayFilmDetailsNewRoute(filmsData.data[0]);
        } else {
            console.error('Failed to load the movies.');
        }

    } else {

        if (filmsData && filmsData.data) {
            displayFilmsOnPage(filmsData.data);
        } else {
            console.error('Failed to load the movies.');
        }
    }

}

function goBack() {
    history.back();
}

window.getApiData = getApiData;
window.goBack = goBack;