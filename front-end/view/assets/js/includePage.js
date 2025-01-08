function loadContent(file, elementId) {

    fetch(file)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error loading the file' + file);
            }
            return response.text();
        })
        .then(data => {
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = data;
            } else {
                console.error(`Element with id  "${elementId}" not found`);
            }
        })
        .catch(error => {
            console.error('Error loading the content', error);
        });
}

window.loadContent = loadContent;
