<?php
require_once 'conexion.php';

$message = '';
$errors = [];

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation des données
    if (empty($_POST['nom'])) {
        $errors[] = "Le nom est obligatoire.";
    }
    
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }
    
    if (empty($errors)) {
        // Vérifier si l'email existe déjà
        $checkEmail = $connexion->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
        $checkEmail->execute([':email' => $_POST['email']]);
        $emailExists = $checkEmail->fetchColumn();
        
        if ($emailExists) {
            $errors[] = "Cet email est déjà utilisé.";
        } else {
            // Enregistrer l'utilisateur
            $requete = $connexion->prepare("INSERT INTO utilisateurs (nom, email) VALUES (:nom, :email)");
            try {
                $requete->execute([
                    ':nom' => htmlspecialchars($_POST['nom']),
                    ':email' => $_POST['email']
                ]);
                $message = "Inscription réussie ! Vos informations ont été enregistrées.";
            } catch(PDOException $e) {
                $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'enregistrement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f0f8ff;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #1a5276;
        }
        .form-container {
            max-width: 400px;
            margin: 0 auto 30px;
            padding: 20px;
            border: 1px solid #a9cce3;
            border-radius: 5px;
            background-color: #ebf5fb;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2874a6;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #a9cce3;
            border-radius: 4px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4efdf;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }
        .error {
            background-color: #f8d7da;
            color: #c0392b;
            border-left: 4px solid #c0392b;
        }
        .nav {
            margin-bottom: 20px;
        }
        .nav a {
            display: inline-block;
            margin-right: 10px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        .header {
            background-color: #1a5276;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Système d'Enregistrement</h1>
        </div>
        
        <div class="nav">
            <a href="register.php">Inscription</a>
            <a href="users.php">Liste des utilisateurs</a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="message error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <h2>Inscription</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">S'inscrire</button>
            </form>
        </div>
    </div>
</body>
</html>