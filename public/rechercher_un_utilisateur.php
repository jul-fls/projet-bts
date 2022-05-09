<?php
    $title = "Rechercher un utilisateur";
    require_once "commons/header.php";
    if(!isset($_SESSION['loggedin'])){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'etes pas connecté, merci de vous connecter à votre compte !</h1>';
        require_once "commons/footer.php";
        die();
    }else if($_SESSION['role']<1){
        echo '<h1><img src="resources/danger.png" width="5%" height="auto" />Vous n\'avez pas l\'autorisation d\'accéder à cette page !</h1>';
        require_once "commons/footer.php";
        die();
    }
?>
<div class="w3-center">
    <h2>Rechercher un utilisateur</h2>
    <input autofocus type="text" class="w3-input" placeholder="Entrer un nom, prénom ou description..." onkeyup="javascript:load_data_utilisateur(this.value)"/>
    <br/>
    <h1 id="erreur"></h1>
</div>
<table class="w3-table-all" id="tableau-rechercher-utilisateurs">
    <thead>
        <tr class="w3-blue">
            <th>N° de l'utilisateur</th>
            <th>Type d'utilisateur</th>
            <th>Description de l'utilisateur</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Role</th>
            <th>Identifiant</th>
            <th>Email</th>
            <th>N° de téléphone</th>
            <th>Compte activé</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div id="supprimer_utilisateur_confirmation" class="w3-modal">
    <div class="w3-modal-content w3-animate-top w3-card-4">
        <header class="w3-container w3-red"> 
            <span onclick="document.getElementById('supprimer_utilisateur_confirmation').style.display='none'" class="w3-button w3-display-topright">&times;</span>
            <h2>Etes-vous sûr de vouloir supprimer cet utilisateur ?</h2>
        </header>
        <div class="w3-container">
            <p>Cela entrainera aussi la suppression de tous les donnees_capteurs passés et en cours pour cet utilisateur</p>
            <a id="supprimer_utilisateur_button" class="w3-red w3-center w3-button" style="width: 100%;" href="">Confirmer la suppression</a>
        </div>
    </div>
</div>
<script>
    function load_data_utilisateur(query){
        if(query.length>0){
            var form_data = new FormData();
            form_data.append('query',query);
            var ajax_request = new XMLHttpRequest();
            ajax_request.open('POST','<?=$__WEB_ROOT__?>ajax_scripts/ajax_get_utilisateur_by_searchstring.php');
            ajax_request.send(form_data);
            ajax_request.onreadystatechange = function(){
                if(ajax_request.readyState==4 && ajax_request.status==200){
                    var response = JSON.parse(ajax_request.responseText);
                    if(response.length > 0){
                        tabBody=document.getElementsByTagName("tbody").item(0);
                        tabBody.innerHTML = "";
                        function addRow(id,type_utilisateur_str,description,nom_utilisateur,prenom_utilisateur,role,login,email,telephone,isactive){
                            if (!document.getElementsByTagName) return;
                            tabBody=document.getElementsByTagName("tbody").item(0);
                            row=document.createElement("tr");

                            cell_id = document.createElement("td");
                            cell_type_utilisateur_str = document.createElement("td");
                            cell_description = document.createElement("td");
                            cell_nom_utilisateur = document.createElement("td");
                            cell_prenom_utilisateur = document.createElement("td");
                            cell_role = document.createElement("td");
                            cell_identifiant = document.createElement("td");
                            cell_email = document.createElement("td");
                            cell_telephone = document.createElement("td");
                            cell_isactive = document.createElement("td");
                            cell_actions = document.createElement("td");

                            text_id = document.createTextNode(id);
                            text_type_utilisateur_str = document.createTextNode(type_utilisateur_str);
                            text_description = document.createElement("span");
                            text_description.innerHTML = description;
                            text_nom_utilisateur = document.createTextNode(nom_utilisateur);
                            text_prenom_utilisateur = document.createTextNode(prenom_utilisateur);

                            if(role==1){
                                text_role = document.createTextNode('Utilisateur Administrateur');
                            }else if(role==2){
                                text_role = document.createTextNode('Utilisateur Super Administrateur');
                            }else{
                                text_role = document.createTextNode('Utilisateur Standard');
                            }
                            
                            text_identifiant = document.createTextNode(login);
                            text_email = document.createTextNode(email);
                            text_telephone = document.createTextNode(telephone);
                            text_isactive = document.createElement("i");
                            if(isactive==1){
                                text_isactive.title="Compte activé";
                                text_isactive.style="color:green;";
                                text_isactive.className="fa fa-check-square-o fa-2x";
                            }else{
                                text_isactive.title="Compte inactivé";
                                text_isactive.style="color:darkred;";
                                text_isactive.className="fa fa-window-close-o fa-2x";
                            }

                            link1 = document.createElement('a');
                            link1_icon = document.createElement('i');
                            link1_icon.classList.add("fa");
                            link1_icon.classList.add("fa-pencil");
                            link1_icon.classList.add("fa-2x");
                            link1.appendChild(link1_icon);
                            link1.href='modifier_un_utilisateur.php?id='+id;
                                
                            br = document.createElement('br');
                                
                            link2 = document.createElement('a');
                            link2_icon = document.createElement('i');
                            link2_icon.style.color = "darkred";
                            link2_icon.classList.add("fa");
                            link2_icon.classList.add("fa-trash");
                            link2_icon.classList.add("fa-2x");
                            link2.appendChild(link2_icon);
                            link2.href="#";
                            link2.onclick=function(){
                                document.getElementById('supprimer_utilisateur_button').href='supprimer_un_utilisateur.php?id='+id;
                                document.getElementById('supprimer_utilisateur_confirmation').style.display='block';
                                return false;
                            };
                                
                            cell_id.appendChild(text_id);
                            cell_type_utilisateur_str.appendChild(text_type_utilisateur_str);
                            cell_description.appendChild(text_description);
                            cell_nom_utilisateur.appendChild(text_nom_utilisateur);
                            cell_prenom_utilisateur.appendChild(text_prenom_utilisateur);
                            cell_role.appendChild(text_role);
                            cell_identifiant.appendChild(text_identifiant);
                            cell_email.appendChild(text_email);
                            cell_telephone.appendChild(text_telephone);
                            cell_isactive.appendChild(text_isactive);
                            cell_actions.appendChild(link1);
                            cell_actions.appendChild(br);
                            cell_actions.appendChild(link2);
                                
                            row.appendChild(cell_id);
                            row.appendChild(cell_type_utilisateur_str);
                            row.appendChild(cell_description);
                            row.appendChild(cell_nom_utilisateur);
                            row.appendChild(cell_prenom_utilisateur);
                            row.appendChild(cell_role);
                            row.appendChild(cell_identifiant);
                            row.appendChild(cell_email);
                            row.appendChild(cell_telephone);
                            row.appendChild(cell_isactive);
                            row.appendChild(cell_actions);
                            tabBody.appendChild(row);
                        }
                        for(var i = 0; i <response.length; i++){
                            addRow(response[i].id,response[i].type_utilisateur_str,response[i].description,response[i].nom_utilisateur,response[i].prenom_utilisateur,response[i].role,response[i].login,response[i].email,response[i].telephone,response[i].isactive); 
                            document.getElementById('erreur').innerHTML = '';
                        }
                    }else{
                        tabBody=document.getElementsByTagName("tbody").item(0);
                        tabBody.innerHTML = "";
                        document.getElementById('erreur').innerHTML = 'Aucun utilisateur trouvé';
                    }
                }
            }
        }
    }
    $th = document.getElementsByTagName('th');
    for(i=0;i<$th.length;i++){
        $th[i].name=$th[i].innerHTML;
        $th[i].innerHTML += ' <i class="fa fa-sort"></i> ';
        $th[i].addEventListener('click',function(){
            sortTable(this.cellIndex);
        });
        $th[i].style.cursor = 'pointer';
    }
    function sortTable(n){
        var table,rows,switching,i,x,y,shouldSwitch,dir,switchcount=0;
        table=document.getElementsByTagName("table")[0];
        switching=true;
        dir="asc"; 
        while(switching){
            switching=false;
            rows=table.rows;
            for(i=1;i<(rows.length-1);i++){
                shouldSwitch=false;
                x=rows[i].getElementsByTagName("td")[n];
                y=rows[i+1].getElementsByTagName("td")[n];
                typeofdata=0;
                if(!isNaN(parseFloat(x.innerHTML))){
                    typeofdata = 1;
                }else{
                    typeofdata = 0;
                }
                if(typeofdata==1){
                    if(dir=="asc"){
                        if(parseFloat(x.innerHTML)>parseFloat(y.innerHTML)){
                            shouldSwitch=true;
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-asc"></i>';
                            break;
                        }
                    }else if(dir=="desc"){
                        if(parseFloat(x.innerHTML)<parseFloat(y.innerHTML)){
                            shouldSwitch=true;
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-numeric-desc"></i>';
                            break;
                        }
                    }
                }else if(typeofdata==0){
                    if(dir=="asc"){
                        if(x.innerHTML.toLowerCase()>y.innerHTML.toLowerCase()){
                            shouldSwitch=true;
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-asc"></i>';
                            break;
                        }
                    }else if(dir=="desc"){
                        if(x.innerHTML.toLowerCase()<y.innerHTML.toLowerCase()){
                            shouldSwitch=true;
                            rows[0].getElementsByTagName("th")[n].innerHTML = rows[0].getElementsByTagName("th")[n].name + ' <i class="fa fa-sort-alpha-desc"></i>';
                            break;
                        }
                    }
                }
            }
            if(shouldSwitch){
                rows[i].parentNode.insertBefore(rows[i+1],rows[i]);
                switching=true;
                switchcount++;      
            }else{
                if(switchcount==0&&dir=="asc"){
                    dir="desc";
                    switching=true;
                }
            }
        }
    }
</script>
<?php require_once "commons/footer.php";?>