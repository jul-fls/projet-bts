function toISOString(date) { // Fonction qui convertit une date en format ISO
    var tzo = -date.getTimezoneOffset(), // On récupère le nombre de minutes de décalage
        dif = tzo >= 0 ? '+' : '-', // On détermine si le décalage est positif ou négatif
        pad = function(num) { // Fonction qui permet d'ajouter un 0 devant un nombre
            return (num < 10 ? '0' : '') + num; // Si le nombre est inférieur à 10, on ajoute un 0 devant
        };

    return date.getFullYear() + 
        '-' + pad(date.getMonth() + 1) +
        '-' + pad(date.getDate()) +
        'T' + pad(date.getHours()) +
        ':' + pad(date.getMinutes()); // On retourne la date au format ISO
}