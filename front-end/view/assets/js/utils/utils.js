function formatDateUS(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const year = date.getFullYear();

    // Array com os nomes dos meses
    const months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    // Obter o nome do mês
    const month = months[date.getMonth()];

    return `${month} ${day}, ${year}`;
}

window.formatDateUS = formatDateUS;
