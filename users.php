<?php
require_once 'conexion.php';

$message = '';
$errors = [];

// Traitement de la suppression
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $requete = $connexion->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $requete->execute([':id' => $id]);
        $message = "Utilisateur supprimé avec succès.";
    } catch(PDOException $e) {
        $errors[] = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Récupération de tous les utilisateurs
try {
    $requete = $connexion->query("SELECT * FROM utilisateurs ORDER BY id DESC");
    $utilisateurs = $requete->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $errors[] = "Erreur lors de la récupération des utilisateurs : " . $e->getMessage();
    $utilisateurs = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #a9cce3;
            text-align: left;
        }
        th {
            background-color: #2874a6;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #ebf5fb;
        }
        tr:hover {
            background-color: #d6eaf8;
        }
        .actions {
            display: flex;
            gap: 5px;
        }
        .edit-btn {
            background-color: #2874a6;
        }
        .edit-btn:hover {
            background-color: #1a5276;
        }
        .delete-btn {
            background-color: #e74c3c;
        }
        .delete-btn:hover {
            background-color: #c0392b;
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
            <h1>Liste des Utilisateurs</h1>
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
        
        <?php if (!empty($utilisateurs)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?php echo $utilisateur['id']; ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['email']); ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $utilisateur['id']; ?>">
                                    <button class="edit-btn">Modifier</button>
                                </a>
                                <a href="?delete=<?php echo $utilisateur['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?');">
                                    <button class="delete-btn">Supprimer</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun utilisateur enregistré.</p>
        <?php endif; ?>
    </div>
</body>
</html>