<?php
require_once 'conexion.php';

$message = '';
$errors = [];
$userData = [
    'id' => '',
    'nom' => '',
    'email' => ''
];

// Récupération des données de l'utilisateur
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $requete = $connexion->prepare("SELECT * FROM utilisateurs WHERE id = :id");
        $requete->execute([':id' => $id]);
        $userData = $requete->fetch(PDO::FETCH_ASSOC);
        
        if (!$userData) {
            $errors[] = "Utilisateur non trouvé.";
        }
    } catch(PDOException $e) {
        $errors[] = "Erreur lors de la récupération : " . $e->getMessage();
    }
} else {
    header('Location: users.php');
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation des données
    if (empty($_POST['nom'])) {
        $errors[] = "Le nom est obligatoire.";
    }
    
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }
    
    if (empty($errors)) {
        // Vérifier si l'email existe déjà (sauf pour cet utilisateur)
        $checkEmail = $connexion->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email AND id != :id");
        $checkEmail->execute([
            ':email' => $_POST['email'],
            ':id' => $_POST['id']
        ]);
        $emailExists = $checkEmail->fetchColumn();
        
        if ($emailExists) {
            $errors[] = "Cet email est déjà utilisé par un autre utilisateur.";
        } else {
            // Mise à jour de l'utilisateur
            $requete = $connexion->prepare("UPDATE utilisateurs SET nom = :nom, email = :email WHERE id = :id");
            try {
                $requete->execute([
                    ':id' => $_POST['id'],
                    ':nom' => htmlspecialchars($_POST['nom']),
                    ':email' => $_POST['email']
                ]);
                $message = "Modification réussie !";
            } catch(PDOException $e) {
                $errors[] = "Erreur lors de la modification : " . $e->getMessage();
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
    <title>Modification d'un utilisateur</title>
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
        .btn-cancel {
            background-color: #7f8c8d;
            margin-left: 10px;
        }
        .btn-cancel:hover {
            background-color: #5f6a6b;
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
            <h1>Modification d'un Utilisateur</h1>
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
            <h2>Modifier l'utilisateur</h2>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($userData['id']); ?>">
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($userData['nom']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                </div>
                <button type="submit">Mettre à jour</button>
                <a href="users.php"><button type="button" class="btn-cancel">Annuler</button></a>
            </form>
        </div>
    </div>
</body>
</html>