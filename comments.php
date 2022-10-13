<?php
// Connect to DB with user1.
try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet_ssi", "user1", "password1");
} catch(Exception $e) {
    var_dump($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Projet SSI - Commentaires</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Ajouter un commentaire</h1>

    <form action="comments.php" method="post">
        <label for="utilisateur_id">Identifiant utilisateur</label>
        <input type="text" name="utilisateur_id" id="utilisateur_id" minlength="1" maxlength="30" pattern="[0-9]+" />
        <label for="description">Description</label>
        <input type="text" name="description" id="description" minlength="1" maxlength="255" />
        <input type="submit" name="commenter" value="Envoyer" />
    </form>

    <?php
    // Add comment if requested.
    if (isset($_POST['commenter'])) {
        $err = false;

        if (empty($_POST['utilisateur_id']) || empty($_POST['description'])) {
            $err = true;
            echo "Erreur : au moins une case est vide !<br />";
        }

        if (!empty($_POST['utilisateur_id']) && !is_numeric($_POST['utilisateur_id'])) {
            $err = true;
            echo "Erreur : l'identifiant doit être numérique !<br />";
        }

        if (!$err) {
            try {
                $q = $pdo->prepare("INSERT INTO commentaire(description, utilisateur_id) VALUES (:description, :utilisateur_id);");
                $q->bindParam('description', $_POST['description'], PDO::PARAM_STR);
                $q->bindParam('utilisateur_id', $_POST['utilisateur_id'], PDO::PARAM_INT);
                $q->execute();
            } catch(Exception $e) {
                echo "Erreur : ";
                var_dump($e->getMessage());
                echo "<br />";
            }
        }
    }
    ?>

    <h1>Liste des commentaires</h1>

    <?php
    // Remove comment if requested.
    if (isset($_POST['supprimer'])) {
        $err = false;

        if (!isset($_POST['id'])) {
            $err = true;
            echo "Erreur : commentaire introuvable !";
        }

        if (!$err) {
            try {
                $q = $pdo->prepare("DELETE FROM commentaire WHERE id = :id;");
                $q->bindParam('id', $_POST['id'], PDO::PARAM_INT);
                $q->execute();
            } catch(Exception $e) {
                echo "Erreur : ";
                var_dump($e->getMessage());
                echo "<br />";
            }
        }
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>Identifiant</th>
                <th>Description</th>
                <th>Identifiant utilisateur</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $q = $pdo->query("SELECT * FROM commentaire;");
        while ($row = $q->fetch()) {
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['utilisateur_id']); ?></td>
                <td>
                    <form action='comments.php' method='post'>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <input type="submit" name="supprimer" value="Supprimer">
                    </form>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
</body>
</html>
