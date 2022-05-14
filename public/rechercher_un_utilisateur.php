<?php
    $title = "Rechercher un utilisateur"; // Titre de la page
    require_once "commons/header.php"; // Inclusion du header
    if(!isset($_SESSION['loggedin'])){ // Si l'utilisateur n'est pas connecté
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }else if($_SESSION['role']<1){ // Si l'utilisateur n'est pas un administrateur
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>'; // Message d'erreur
        require_once "commons/footer.php"; // Inclusion du footer
        die(); // On arrête l'execution du script
    }
?>
<div class="w3-center"> <!-- Début de la div contenant le formulaire -->
    <h2>Rechercher un utilisateur</h2> <!-- Titre du formulaire -->
    <input autofocus type="text" class="w3-input" placeholder="Entrer un nom, prénom ou description..." onkeyup="javascript:load_data_utilisateur(this.value)"/> <!-- Champ de recherche -->
    <br/>   <!-- Saut de ligne -->
    <h1 id="erreur"></h1> <!-- Message d'erreur -->
</div>
<table class="w3-table-all" id="tableau-rechercher-utilisateurs"> <!-- Début de la table contenant les résultats de la recherche -->
    <thead> <!-- Début du tableau contenant les titres des colonnes -->
        <tr class="w3-blue"> <!-- Début de la ligne contenant les titres des colonnes -->
            <th>N° de l'utilisateur</th> <!-- Titre de la colonne N° de l'utilisateur -->
            <th>Type d'utilisateur</th> <!-- Titre de la colonne Type d'utilisateur -->
            <th>Description de l'utilisateur</th> <!-- Titre de la colonne Description de l'utilisateur -->
            <th>Nom</th> <!-- Titre de la colonne Nom -->
            <th>Prénom</th> <!-- Titre de la colonne Prénom -->
            <th>Role</th> <!-- Titre de la colonne Role -->
            <th>Identifiant</th> <!-- Titre de la colonne Identifiant -->
            <th>Email</th> <!-- Titre de la colonne Email -->
            <th>N° de téléphone</th> <!-- Titre de la colonne N° de téléphone -->
            <th>Compte activé</th> <!-- Titre de la colonne Compte activé -->
            <th>Actions</th> <!-- Titre de la colonne Actions -->
        </tr> <!-- Fin de la ligne contenant les titres des colonnes -->
    </thead> <!-- Fin du tableau contenant les titres des colonnes -->
    <tbody> <!-- Début du tableau contenant les résultats de la recherche -->
    </tbody> <!-- Fin du tableau contenant les résultats de la recherche -->
</table> <!-- Fin de la table contenant les résultats de la recherche -->
<div id="supprimer_utilisateur_confirmation" class="w3-modal"> <!-- Début de la div contenant la confirmation de la suppression d'un utilisateur -->
    <div class="w3-modal-content w3-animate-top w3-card-4"> <!-- Début de la div contenant la confirmation de la suppression d'un utilisateur -->
        <header class="w3-container w3-red"> <!-- Début de la div contenant le titre de la confirmation de la suppression d'un utilisateur -->
            <span onclick="document.getElementById('supprimer_utilisateur_confirmation').style.display='none'" class="w3-button w3-display-topright">&times;</span> <!-- Bouton de fermeture de la confirmation de la suppression d'un utilisateur -->
            <h2>Etes-vous sûr de vouloir supprimer cet utilisateur ?</h2> <!-- Titre de la confirmation de la suppression d'un utilisateur -->
        </header> <!-- Fin de la div contenant le titre de la confirmation de la suppression d'un utilisateur -->
        <div class="w3-container"> <!-- Début de la div contenant les boutons de la confirmation de la suppression d'un utilisateur -->
            <p>Cela entrainera aussi la suppression de tous les donnees_capteurs passés et en cours pour cet utilisateur</p> <!-- Message de la confirmation de la suppression d'un utilisateur -->
            <a id="supprimer_utilisateur_button" class="w3-red w3-center w3-button" style="width: 100%;" href="">Confirmer la suppression</a> <!-- Bouton de confirmation de la suppression d'un utilisateur -->
        </div> <!-- Fin de la div contenant les boutons de la confirmation de la suppression d'un utilisateur -->
    </div> <!-- Fin de la div contenant la confirmation de la suppression d'un utilisateur -->
</div> <!-- Fin de la div contenant la confirmation de la suppression d'un utilisateur -->
<script> // Début du script
    function load_data_utilisateur(query){ // Fonction permettant de charger les données de la recherche d'un utilisateur
        if(query.length>0){ // Si la longueur de la chaîne de caractères est supérieure à 0
            var form_data = new FormData(); // Création d'un nouvel objet de type FormData
            form_data.append('query',query); // Ajout de la chaîne de caractères à l'objet
            var ajax_request = new XMLHttpRequest(); // Création d'un nouvel objet de type XMLHttpRequest
            ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_utilisateur_by_searchstring.php'); // Ouverture d'une requête AJAX POST vers le fichier ajax_get_utilisateur_by_searchstring.php
            ajax_request.send(form_data); // Envoi de l'objet form_data
            ajax_request.onreadystatechange = function(){ // Début de la fonction permettant de traiter la réponse AJAX
                if(ajax_request.readyState==4 && ajax_request.status==200){ // Si la réponse AJAX est prête
                    var response = JSON.parse(ajax_request.responseText); // Récupération de la réponse AJAX sous forme de tableau JSON
                    if(response.length > 0){ // Si la taille du tableau JSON est supérieure à 0
                        tabBody=document.getElementsByTagName("tbody").item(0); // Récupération du tableau contenant les résultats de la recherche
                        tabBody.innerHTML = ""; // Suppression de tous les éléments du tableau
                        function addRow(id,type_utilisateur_str,description,nom_utilisateur,prenom_utilisateur,role,login,email,telephone,isactive){ // Fonction permettant d'ajouter une ligne dans le tableau contenant les résultats de la recherche
                            if (!document.getElementsByTagName) return; // Si le navigateur ne supporte pas la fonction getElementsByTagName()
                            tabBody=document.getElementsByTagName("tbody").item(0); // Récupération du tableau contenant les résultats de la recherche
                            row=document.createElement("tr"); // Création d'une nouvelle ligne dans le tableau contenant les résultats de la recherche

                            cell_id = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_type_utilisateur_str = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_description = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_nom_utilisateur = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_prenom_utilisateur = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_role = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_identifiant = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_email = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_telephone = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_isactive = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne
                            cell_actions = document.createElement("td"); // Création d'une nouvelle cellule dans la ligne

                            text_id = document.createTextNode(id); // Création d'un nouveau texte contenant l'id de l'utilisateur
                            text_type_utilisateur_str = document.createTextNode(type_utilisateur_str); // Création d'un nouveau texte contenant le type de l'utilisateur
                            text_description = document.createElement("span"); // Création d'un nouveau texte contenant la description de l'utilisateur
                            text_description.innerHTML = description; // Ajout de la description de l'utilisateur dans le texte
                            text_nom_utilisateur = document.createTextNode(nom_utilisateur); // Création d'un nouveau texte contenant le nom de l'utilisateur
                            text_prenom_utilisateur = document.createTextNode(prenom_utilisateur); // Création d'un nouveau texte contenant le prénom de l'utilisateur

                            if(role==1){ // Si le rôle de l'utilisateur est égal à 1
                                text_role = document.createTextNode('Utilisateur Administrateur'); // Création d'un nouveau texte contenant le rôle de l'utilisateur
                            }else if(role==2){ // Si le rôle de l'utilisateur est égal à 2
                                text_role = document.createTextNode('Utilisateur Super Administrateur'); // Création d'un nouveau texte contenant le rôle de l'utilisateur
                            }else{ // Sinon
                                text_role = document.createTextNode('Utilisateur Standard'); // Création d'un nouveau texte contenant le rôle de l'utilisateur
                            }
                            
                            text_identifiant = document.createTextNode(login); // Création d'un nouveau texte contenant l'identifiant de l'utilisateur
                            text_email = document.createTextNode(email); // Création d'un nouveau texte contenant l'email de l'utilisateur
                            text_telephone = document.createTextNode(telephone); // Création d'un nouveau texte contenant le téléphone de l'utilisateur
                            text_isactive = document.createElement("i"); // Création d'un nouveau texte contenant l'état de l'utilisateur
                            if(isactive==1){ // Si l'utilisateur est actif
                                text_isactive.title="Compte activé"; // Ajout d'un titre au texte contenant l'état de l'utilisateur
                                text_isactive.style="color:green;"; // Ajout d'une couleur verte au texte contenant l'état de l'utilisateur
                                text_isactive.className="fa fa-check-square-o fa-2x"; // Ajout d'une icône de validation au texte contenant l'état de l'utilisateur
                            }else{ // Sinon
                                text_isactive.title="Compte inactivé"; // Ajout d'un titre au texte contenant l'état de l'utilisateur
                                text_isactive.style="color:darkred;"; // Ajout d'une couleur rouge au texte contenant l'état de l'utilisateur
                                text_isactive.className="fa fa-window-close-o fa-2x"; // Ajout d'une icône de désactivation au texte contenant l'état de l'utilisateur
                            }

                            link1 = document.createElement('a'); // Création d'un nouveau lien
                            link1_icon = document.createElement('i'); // Création d'un nouvel élément contenant une icône
                            link1_icon.classList.add("fa"); // Ajout d'une classe à l'élément contenant une icône
                            link1_icon.classList.add("fa-pencil"); // Ajout d'une classe à l'élément contenant une icône
                            link1_icon.classList.add("fa-2x"); // Ajout d'une classe à l'élément contenant une icône
                            link1.appendChild(link1_icon); // Ajout de l'élément contenant une icône au lien
                            link1.href='modifier_un_utilisateur.php?id='+id; // Ajout d'un attribut href à l'élément contenant un lien
                    
                            br = document.createElement('br'); // Création d'un nouveau br
                                
                            link2 = document.createElement('a'); // Création d'un nouveau lien
                            link2_icon = document.createElement('i'); // Création d'un nouvel élément contenant une icône
                            link2_icon.style.color = "darkred"; // Ajout d'une couleur rouge au texte contenant l'état de l'utilisateur
                            link2_icon.classList.add("fa"); // Ajout d'une classe à l'élément contenant une icône
                            link2_icon.classList.add("fa-trash"); // Ajout d'une classe à l'élément contenant une icône
                            link2_icon.classList.add("fa-2x"); // Ajout d'une classe à l'élément contenant une icône
                            link2.appendChild(link2_icon); // Ajout de l'élément contenant une icône au lien
                            link2.href="#"; // Ajout d'un attribut href à l'élément contenant un lien
                            link2.onclick=function(){ // Ajout d'un évènement au lien
                                document.getElementById('supprimer_utilisateur_button').href='supprimer_un_utilisateur.php?id='+id; // Ajout d'un attribut href à l'élément contenant un lien
                                document.getElementById('supprimer_utilisateur_confirmation').style.display='block'; // Affichage de la boîte de dialogue de confirmation
                                return false; // Annulation de l'action par défaut du lien
                            };
                                
                            cell_id.appendChild(text_id); // Ajout du texte contenant l'id de l'utilisateur à la cellule contenant l'id de l'utilisateur
                            cell_type_utilisateur_str.appendChild(text_type_utilisateur_str); // Ajout du texte contenant le type de l'utilisateur à la cellule contenant le type de l'utilisateur
                            cell_description.appendChild(text_description); // Ajout du texte contenant la description de l'utilisateur à la cellule contenant la description de l'utilisateur
                            cell_nom_utilisateur.appendChild(text_nom_utilisateur); // Ajout du texte contenant le nom de l'utilisateur à la cellule contenant le nom de l'utilisateur
                            cell_prenom_utilisateur.appendChild(text_prenom_utilisateur); // Ajout du texte contenant le prénom de l'utilisateur à la cellule contenant le prénom de l'utilisateur
                            cell_role.appendChild(text_role); // Ajout du texte contenant le rôle de l'utilisateur à la cellule contenant le rôle de l'utilisateur
                            cell_identifiant.appendChild(text_identifiant); // Ajout du texte contenant l'identifiant de l'utilisateur à la cellule contenant l'identifiant de l'utilisateur
                            cell_email.appendChild(text_email); // Ajout du texte contenant l'email de l'utilisateur à la cellule contenant l'email de l'utilisateur
                            cell_telephone.appendChild(text_telephone); // Ajout du texte contenant le téléphone de l'utilisateur à la cellule contenant le téléphone de l'utilisateur
                            cell_isactive.appendChild(text_isactive); // Ajout du texte contenant l'état de l'utilisateur à la cellule contenant l'état de l'utilisateur
                            cell_actions.appendChild(link1); // Ajout du lien au conteneur de la cellule contenant les actions
                            cell_actions.appendChild(br); // Ajout d'un br au conteneur de la cellule contenant les actions
                            cell_actions.appendChild(link2); // Ajout du lien au conteneur de la cellule contenant les actions
                                
                            row.appendChild(cell_id); // Ajout de la cellule contenant l'id de l'utilisateur à la ligne
                            row.appendChild(cell_type_utilisateur_str); // Ajout de la cellule contenant le type de l'utilisateur à la ligne
                            row.appendChild(cell_description); // Ajout de la cellule contenant la description de l'utilisateur à la ligne
                            row.appendChild(cell_nom_utilisateur); // Ajout de la cellule contenant le nom de l'utilisateur à la ligne
                            row.appendChild(cell_prenom_utilisateur); // Ajout de la cellule contenant le prénom de l'utilisateur à la ligne
                            row.appendChild(cell_role); // Ajout de la cellule contenant le rôle de l'utilisateur à la ligne
                            row.appendChild(cell_identifiant); // Ajout de la cellule contenant l'identifiant de l'utilisateur à la ligne
                            row.appendChild(cell_email); // Ajout de la cellule contenant l'email de l'utilisateur à la ligne
                            row.appendChild(cell_telephone); // Ajout de la cellule contenant le téléphone de l'utilisateur à la ligne
                            row.appendChild(cell_isactive); // Ajout de la cellule contenant l'état de l'utilisateur à la ligne
                            row.appendChild(cell_actions); // Ajout de la cellule contenant les actions à la ligne
                            tabBody.appendChild(row); // Ajout de la ligne à la table
                        }
                        for(var i = 0; i <response.length; i++){ // Pour chaque utilisateur
                            addRow(response[i].id,response[i].type_utilisateur_str,response[i].description,response[i].nom_utilisateur,response[i].prenom_utilisateur,response[i].role,response[i].login,response[i].email,response[i].telephone,response[i].isactive); // Ajout d'une ligne à la table
                            document.getElementById('erreur').innerHTML = ''; // Suppression du message d'erreur
                        }
                    }else{ // Si la réponse de la requête est vide
                        tabBody=document.getElementsByTagName("tbody").item(0); // Récupération du conteneur du tableau
                        tabBody.innerHTML = ""; // Suppression du contenu du tableau
                        document.getElementById('erreur').innerHTML = 'Aucun utilisateur trouvé'; // Affichage du message d'erreur
                    }
                }
            }
        }
    }
    $th = document.getElementsByTagName('th'); // Récupération des th
    for(i=0;i<$th.length;i++){ // Pour chaque th
        $th[i].name=$th[i].innerHTML; // Ajout d'un attribut name à chaque th
        $th[i].innerHTML += ' <i class="fa fa-sort"></i> '; // Ajout d'un icône de tri à chaque th
        $th[i].addEventListener('click',function(){ // Ajout d'un évènement au clic sur chaque th
            sortTable(this.cellIndex); // Appel de la fonction de tri
        }); // Fin de l'évènement
        $th[i].style.cursor = 'pointer'; // Ajout d'un curseur pointer à chaque th
    }
    function sortTable(n){ // Fonction de tri
        var table,rows,switching,i,x,y,shouldSwitch,dir,switchcount=0; // Déclaration des variables
        table=document.getElementsByTagName("table")[0]; // Récupération du tableau
        switching=true; // Initialisation de la variable switching
        dir="asc"; // Initialisation de la variable dir
        while(switching){ // Tant que la variable switching est vraie
            switching=false; // Initialisation de la variable switching
            rows=table.rows; // Récupération des lignes du tableau
            for(i=1;i<(rows.length-1);i++){ // Pour chaque ligne du tableau
                shouldSwitch=false; // Initialisation de la variable shouldSwitch
                x=rows[i].getElementsByTagName("td")[n]; // Récupération de la cellule de la ligne
                y=rows[i+1].getElementsByTagName("td")[n]; // Récupération de la cellule de la ligne suivante
                typeofdata=0; // Initialisation de la variable typeofdata
                if(!isNaN(parseFloat(x.innerHTML))){ // Si la valeur de la cellule de la ligne est un nombre
                    typeofdata = 1; // Initialisation de la variable typeofdata
                }else{ // Sinon
                    typeofdata = 0; // Initialisation de la variable typeofdata
                }
                if(typeofdata==1){ // Si la valeur de la cellule de la ligne est un nombre
                    if(dir=="asc"){ // Si la valeur de la variable dir est asc
                        if(parseFloat(x.innerHTML)>parseFloat(y.innerHTML)){ // Si la valeur de la cellule de la ligne est supérieure à celle de la ligne suivante
                            shouldSwitch=true; // Initialisation de la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-asc"></i>'; // Ajout d'un icône de tri numérique ascendant à la cellule de la ligne
                            break; // Sortie de la boucle
                        }
                    }else if(dir=="desc"){ // Si la valeur de la variable dir est desc
                        if(parseFloat(x.innerHTML)<parseFloat(y.innerHTML)){ // Si la valeur de la cellule de la ligne est inférieure à celle de la ligne suivante
                            shouldSwitch=true; // Initialisation de la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-desc"></i>';
                            break; // Sortie de la boucle
                        }
                    }
                }else if(typeofdata==0){ // Sinon
                    if(dir=="asc"){ // Si la valeur de la variable dir est asc
                        if(x.innerHTML.toLowerCase()>y.innerHTML.toLowerCase()){ // Si la valeur de la cellule de la ligne est supérieure à celle de la ligne suivante
                            shouldSwitch=true; // Initialisation de la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-asc"></i>'; // Ajout d'un icône de tri alphabétique ascendant à la cellule de la ligne
                            break; // Sortie de la boucle
                        }
                    }else if(dir=="desc"){ // Si la valeur de la variable dir est desc
                        if(x.innerHTML.toLowerCase()<y.innerHTML.toLowerCase()){ // Si la valeur de la cellule de la ligne est inférieure à celle de la ligne suivante
                            shouldSwitch=true; // Initialisation de la variable shouldSwitch
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-desc"></i>'; // Ajout d'un icône de tri alphabétique descendant à la cellule de la ligne
                            break; // Sortie de la boucle
                        }
                    }
                }
            }
            if(shouldSwitch){ // Si la variable shouldSwitch est vraie
                rows[i].parentNode.insertBefore(rows[i+1],rows[i]); // Inversion de la ligne
                switching=true; // Initialisation de la variable switching
                switchcount++; // Incrémentation de la variable switchcount
            }else{ // Sinon
                if(switchcount==0&&dir=="asc"){ // Si la variable switchcount est égale à 0 et que la valeur de la variable dir est asc
                    dir="desc"; // Initialisation de la variable dir
                    switching=true; // Initialisation de la variable switching
                }
            }
        }
    }
</script>
<?php require_once "commons/footer.php";?> <!-- Appel du footer -->