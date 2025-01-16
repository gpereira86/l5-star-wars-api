async function getApiData(url, id=null) {
    const apiUrl = url;
    const movieId = (id !== null ? id : '');
    const filmsData = await fetchApi(apiUrl + movieId);
    const urlCharacterNames = `${globalApiUrl}characters-names`;

    if(movieId !== ''){

        if (filmsData && filmsData.data[0]) {

            const moviePosterUrl = await fetchApi(`${globalApiUrl}movie/${encodeURIComponent(filmsData.data[0]['name'])}`);

            if(filmsData.responseCode !== 404) {

                let charactersNames = await getCharacterApiData(urlCharacterNames, filmsData.data[0]['characters']);

                filmsData.data[0].charactersnames = charactersNames['charactersnames'];
                filmsData.data[0].moviePoster = moviePosterUrl.data;

                // displayFilmDetailsInModal(filmsData.data[0]); // Call to populate and display the modal (when activated)
                displayFilmDetailsNewRoute(filmsData.data[0]);
            } else {
                console.error('Failed to load the movie.');
                window.location.href = `${globalSiteUrl}error-page`;
            }

        } else {
            console.error('Failed to load the movies.');
            window.location.href = `${globalSiteUrl}error-page`;
        }

    } else {

        if (filmsData && filmsData.data) {

            for (const movie of filmsData.data) {
                try {
                    const moviePosterUrl = await fetchApi(`${globalApiUrl}movie/${encodeURIComponent(movie.name)}`);
                    movie.moviePoster = moviePosterUrl.data;
                } catch (error) {
                    movie.moviePoster = './front-end/view/assets/img/starwarslogo.webp';
                    console.error(`Error fetching poster for ${movie.name}:`, error);
                }
            }

            displayFilmsOnPage(filmsData.data);
        } else {
            console.error('Failed to load the movies.');
            window.location.href = `${globalSiteUrl}error-page`;
        }
    }

}

async function getCharacterApiData(url, ids) {
    const apiUrl = url;
    const characterIds = ids;

    try {
        const response = await fetch(apiUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(characterIds)
        });

        if (!response.ok) {
            throw new Error(`Error: ${response.status}`);
        }

        const data = await response.json();

        return data;

    } catch (error) {
        console.error("Error:", error);
        return null;
    }
}

function goBack() {
    history.back();
}

window.getApiData = getApiData;
window.goBack = goBack;