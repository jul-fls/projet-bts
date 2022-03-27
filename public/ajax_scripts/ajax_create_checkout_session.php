<?php
    require "../commons/global.php";
    require "../commons/dbconfig.php";
    require "../vendor/autoload.php";
    \Stripe\Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);

    $response = array(
        'status' => 0,
        'error' => array(
            'message' => 'Requete Invalide !'
        )
    );

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $input = file_get_contents('php://input');
        $request = json_decode($input);
    }
    if(json_last_error() !== JSON_ERROR_NONE){
        http_response_code(400);
        echo json_encode($response);
        die();
    }
    $reservations_unfiltered = [];
    for($i = 0; $i < count($request->reservations); $i++){
        $arr_temp = array();
        foreach($request->reservations[$i] as $key => $value){
            if($key == "lieu_id"){
                $arr_temp['lieu_id'] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            }elseif($key == "date"){
                $arr_temp['date'] = filter_var($value, FILTER_SANITIZE_STRING);
            }elseif($key == "menu_id"){
                $arr_temp['menu_id'] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            }else{
                break;
            }
        }
        array_push($reservations_unfiltered, $arr_temp);
    }
    $code_reduction = $request->code_reduction?$request->code_reduction:null;
    $id_utilisateur = $request->id_utilisateur;
    $sql = 'SELECT utilisateurs.id, utilisateurs.nom_utilisateur, utilisateurs.prenom_utilisateur, utilisateurs.email FROM utilisateurs WHERE id = ? LIMIT 1';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'i',$id_utilisateur);
        $status = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_assoc($result)){
                $id_utilisateur = $row['id'];
                $nom_utilisateur = $row['nom_utilisateur'];
                $prenom_utilisateur = $row['prenom_utilisateur'];
                $email_utilisateur = $row['email'];
            }
        }else{
            http_response_code(400);
            $response['error']['message'] = 'Utilisateur non trouvé !';
            echo json_encode($response);
            die();
        }
    $reservations_clean = [];
    foreach($reservations_unfiltered as $reservationitem){
        $sql = 'SELECT lieux_de_consommation.id, lieux_de_consommation.difference_prix, lieux_de_consommation.designation FROM lieux_de_consommation LEFT JOIN donnees_capteurs ON lieux_de_consommation.id = donnees_capteurs.id_lieu_consommation WHERE lieux_de_consommation.disponible = 1 AND lieux_de_consommation.id = ? AND lieux_de_consommation.jauge > (SELECT COUNT(*) FROM donnees_capteurs WHERE donnees_capteurs.valide = 1 AND donnees_capteurs.jour = ?) LIMIT 1';
        $stmt = mysqli_prepare($conn,$sql);
        $lieu_id = $reservationitem['lieu_id'];
        $date_obj = Datetime::createFromFormat('d/m/Y', $reservationitem['date']);
        $date_formated = $date_obj->format('Y-m-d');
        $date_number = $date_obj->format('N')-1;
        $date_search_jours = '%'.$date_number.'%';
        mysqli_stmt_bind_param($stmt, 'is',$lieu_id,$date_formated);
        $status = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_assoc($result)){
                $arr_temp['lieu_id'] = $row['id'];
                $arr_temp['designation_lieu'] = html_entity_decode($row['designation']);
                $arr_temp['difference_prix'] = $row['difference_prix'];
            }
        }else{
            http_response_code(400);
            $response['error']['message'] = 'Lieu de consommation non trouvé !';
            echo json_encode($response);
            die();
        }
        $sql = 'SELECT menus.id, menus.prix, menus.designation FROM menus LEFT JOIN donnees_capteurs ON menus.id = donnees_capteurs.id_menu WHERE menus.disponible = 1 AND menus.id = ? AND menus.jours_dispo LIKE ? AND menus.quantite_max > (SELECT COUNT(*) FROM donnees_capteurs WHERE donnees_capteurs.valide = 1 AND donnees_capteurs.jour = ?)+1 LIMIT 1';
        $stmt = mysqli_prepare($conn,$sql);
        $menu_id = $reservationitem['menu_id'];
        mysqli_stmt_bind_param($stmt, 'iss',$menu_id,$date_search_jours,$date_formated);
        $status = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_assoc($result)){
                $arr_temp['menu_id'] = $row['id'];
                $arr_temp['prix_menu'] = $row['prix'];
                $arr_temp['designation_menu'] = html_entity_decode($row['designation']);
            }
        }else{
            http_response_code(400);
            $response['error']['message'] = 'Menu non trouvé !';
            echo json_encode($response);
            die();
        }

        $days_before_order = $_SERVER['DAYS_BEFORE_ORDER']-1;
        $date_now_obj = new DateTime();
        $date_obj = Datetime::createFromFormat('d/m/Y', $reservationitem['date']);
        $date_min_obj = new DateTime();
        $date_min_obj->add(new DateInterval('P'.$days_before_order.'D'));
        $date_min_obj->setTime(0,0,0);
        if($date_now_obj->format('H') >= 12){
            $date_min_obj->add(new DateInterval('P1D'));
        }
        $date_max_obj = new DateTime();
        $date_max_obj->add(new DateInterval('P7D'));
        $date_max_obj->add(new DateInterval('P'.$days_before_order.'D'));

        if($date_obj->format('w') == 0 || $date_obj->format('w') == 6){
            http_response_code(400);
            $response['error']['message'] = 'Vous ne pouvez pas réserver un menu le samedi ou le dimanche !';
            echo json_encode($response);
            die();
        }
        if(($date_obj>$date_min_obj) && ($date_obj<=$date_max_obj)){
            $arr_temp['date'] = $reservationitem['date'];
        }else{
            http_response_code(400);
            $response['error']['message'] = 'Date non valide ('.$date_obj->format('d/m/Y').')!, date valide ='.$date_min_obj->format('d/m/Y').' à '.$date_max_obj->format('d/m/Y');
            echo json_encode($response);
            die();
        }
        array_push($reservations_clean, $arr_temp);
    }   
    $line_items = [];
    foreach($reservations_clean as $reservation2){
        $prix_menu = $reservation2['prix_menu'] + $reservation2['difference_prix'];
        $prix_menu = $prix_menu * 100;
        $line_item = array(
            'name' => $reservation2['designation_menu'],
            'description' => $reservation2['designation_lieu']. ' - '.$reservation2['date'],
            'amount' => $prix_menu,
            'currency' => 'eur',
            'quantity' => 1
        );
        array_push($line_items, $line_item);
    }
    if(!is_null($code_reduction)){
        $sql0 = "SELECT codes_reductions.id FROM codes_reductions WHERE codes_reductions.code_reduction = ? LIMIT 1";
        $stmt0 = mysqli_prepare($conn,$sql0);
        mysqli_stmt_bind_param($stmt0,'s',$code_reduction);
        $status0 = mysqli_stmt_execute($stmt0);
        $result0 = mysqli_stmt_get_result($stmt0);
        if(mysqli_num_rows($result0)>0){
            while($row0 = mysqli_fetch_assoc($result0)){
                $id_code_reduction = $row0['id'];
            }
        }
        $sql = 'SELECT codes_reductions.id, codes_reductions.montant_reduction FROM codes_reductions LEFT JOIN transactions ON codes_reductions.id = transactions.id_code_reduction WHERE codes_reductions.utilisations_max > (SELECT COUNT(transactions.id) FROM transactions WHERE transactions.status = "succeeded" AND codes_reductions.id = transactions.id_code_reduction AND transactions.id_code_reduction = ?) AND codes_reductions.code_reduction = ? LIMIT 1';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'is',$id_code_reduction,$code_reduction);
        $status = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result)>0){
            while($row = mysqli_fetch_assoc($result)){
                $montant_reduction = $row['montant_reduction'];
            }
            $coupon = \Stripe\Coupon::create(array(
                "amount_off" => $montant_reduction*100,
                "currency" => "eur",
                "duration" => "once"
            ));
        }else{
            http_response_code(400);
            $response['error']['message'] = 'Code de réduction non valide !';
            echo json_encode($response);
            die();
        }
    }
    
    if(!empty($request->createCheckoutSession)){
        try{
            $success_url = $__WEB_ROOT__.'payment_success.php?session_id={CHECKOUT_SESSION_ID}';
            $cancel_url = $__WEB_ROOT__.'payment_cancel.php?session_id={CHECKOUT_SESSION_ID}';
            $id_donnees_capteurs = [];
            foreach($reservations_clean as $reservation){
                $sql = 'INSERT INTO donnees_capteurs (id_utilisateur, id_menu, jour, id_lieu_consommation, code_commande, montant, valide) VALUES (?,?,?,?,?,?,0)';
                $stmt = mysqli_prepare($conn,$sql);
                $date_insert = Datetime::createFromFormat('d/m/Y', $reservation['date'])->format('Y-m-d');
                $montant_commande = $reservation['prix_menu'] + $reservation['difference_prix'];
                $code_commande = '#'.substr($prenom_utilisateur,0,1).substr($nom_utilisateur,0,1).$id_utilisateur.'-'.rand(000,999);
                mysqli_stmt_bind_param($stmt,'iisisd',$id_utilisateur,$reservation['menu_id'],$date_insert,$reservation['lieu_id'],$code_commande,$montant_commande);
                $status = mysqli_stmt_execute($stmt);
                $id_commande = mysqli_insert_id($conn);
                array_push($id_donnees_capteurs, $id_commande);
                if(!$status){
                    http_response_code(400);
                    $response['error']['message'] = 'Erreur lors de la création de la commande ! =>'.mysqli_error($conn);
                    echo json_encode($response);
                    die();
                }
            }
            
            $checkout_session = \Stripe\Checkout\Session::create([
                'discounts' => [[
                    'coupon' => $coupon->id
                ]],
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
                'customer_email' => $email_utilisateur,
                'payment_intent_data' => [
                    'metadata' => [
                        'id_donnees_capteurs' => json_encode($id_donnees_capteurs),
                        'id_code_reduction' => $id_code_reduction
                    ]
                ]
            ]);
        }catch(Exception $e){
            $api_error = $e->getMessage();
        }

        if(empty($api_error) && $checkout_session){
            $response = array(
                'status' => 1,
                'message' => 'La session de paiement a été créée avec succès !',
                'sessionId' => $checkout_session->id
            );
        }else{
            $response = array(
                'status' => 0,
                'error' => array(
                    'message' => 'Échec de la création de la session de paiement : '.$api_error
                )
            );
        }
    }
    echo json_encode($response);
?>