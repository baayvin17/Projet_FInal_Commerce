<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $connectButtonText = 'Se déconnecter';
    $loginPage = './logout.php';
    $panier_url = "./panier.php";
    $wish_url = "./wish.php";
} else {
    $connectButtonText = 'Se connecter';
    $loginPage = './login.php';
    $panier_url = "./panier.php";
    $wish_url = "./wish.php";
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "dbphp";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

$filter_category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT id_veste, nom, description, prix, image_url, category FROM veste";
if (!empty($filter_category)) {
    $sql .= " WHERE category = '$filter_category'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik+Marker+Hatch&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fascinate+Inline&family=Rubik+Marker+Hatch&family=Sedgwick+Ave+Display&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/product.css">
    <link rel="stylesheet" href="../css/loading.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bungee+Shade&family=Permanent+Marker&family=Whisper&display=swap"
        rel="stylesheet">
    <title>Vestes</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="./catalogue.php">HB Commerce</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto" style="font-size: 20px;">
                    <li class="nav-item active">
                        <a class="nav-link" href="./catalogue.php">Accueil <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./profil.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./basket.php">Basket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./veste.php">Vestes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./pantalon.php">Pantalon</a>
                    </li>
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="./logout.php">Se déconnecter</a></li>';
                    } else {
                        echo '<li class="nav-item"><a class="nav-link" href="./login.php">Se connecter</a></li>';
                    }

                    ?>
                </ul>
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<a href="' . $wish_url . '" class="btn btn-info ml-2">Wishlist <span class="badge badge-light"></span></a>';
                } else {
                    echo '<a href="' . $wish_url . '" class="btn btn-info ml-2">Wishlist</a>';
                }
                ?>
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<a href="' . $panier_url . '" class="btn btn-primary ml-2">Mon Panier <span class="badge badge-light"></span></a>';
                } else {
                    echo '<a href="' . $panier_url . '" class="btn btn-primary ml-2">Mon Panier</a>';
                }
                ?>
                <?php
                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $sql_check_admin = "SELECT est_admin FROM utilisateurs WHERE id_utilisateur = ? AND est_admin = 1";
                    $stmt_check_admin = $conn->prepare($sql_check_admin);
                    $stmt_check_admin->bind_param("i", $user_id);
                    $stmt_check_admin->execute();
                    $result_check_admin = $stmt_check_admin->get_result();

                    if ($result_check_admin->num_rows > 0) {
                        echo '<a href="./admin.php" class="btn btn-success ml-2">Admin</a>';
                    }
                }
                ?>
            </div>
        </div>
    </nav>
    <h2 class="text-center mt-4">Les Vestes</h2>
    <div>

        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="ml-5 input-group">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="category-filter">Filtrer par catégorie :</label>
                </div>
                <div class="col-1">
                        <select class="custom-select" id="category-filter" name="category">
                            <option value="">Toutes les catégories</option>
                            <option value="Enfant">Enfant</option>  
                            <option value="Homme">Homme</option>
                            <option value="Femme">Femme</option>
                        </select>
                </div>
                    <button type="submit" class="btn btn-warning">Filtrer</button>
            </div>
        </form>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card">';
                echo '<img src="' . $row['image_url'] . '" alt="' . $row['nom'] . '" style="width:100%">';
                echo '<div class="container">';
                echo '<h4><b>' . $row['nom'] . '</b></h4>';
                echo '<p class="category">' . $row['category'] . '</p>';
                echo '<p>' . $row['description'] . '</p>';
                echo '<p>Prix : $' . number_format($row['prix'], 2) . '</p>';
                echo '<a href="product_veste.php?id=' . $row['id_veste'] . '" class="btn btn-primary">Voir Détails</a>';


                if (isset($user_id)) {
                    $sql_user = "SELECT statut FROM utilisateurs WHERE id_utilisateur = ?";
                    $stmt_user = $conn->prepare($sql_user);
                    $stmt_user->bind_param("i", $user_id);
                    $stmt_user->execute();
                    $result_user = $stmt_user->get_result();
                    $user = $result_user->fetch_assoc();

                    if ($user['statut'] == 'actif') {
                        echo '<a href="ajouter_panier_veste.php?id=' . $row['id_veste'] . '&user_id=' . $user_id . '" class="mx-4 btn btn-success">Ajouter au Panier</a>';
                        echo '<a href="ajouter_wish_veste.php?id=' . $row['id_veste'] . '&user_id=' . $user_id . '" class="btn btn-info">Ajouter a la wishlist</a>';
                    } else {
                        echo '<a href="#" class="btn btn-success">Votre compte n\'est pas vérifié pour ajouter au panier</a>';
                    }
                } else {
                    echo '<a href="./login.php" class="btn btn-success">Connexion pour Ajouter au Panier</a>';
                }

                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "Aucun résultat trouvé";
        }

        $conn->close();
        ?>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <footer>
            © 2023 PHP Site Web
            <a href="contact.php" class="btn btn-primary">Nous contacter</a>
        </footer>
</body>

</html>