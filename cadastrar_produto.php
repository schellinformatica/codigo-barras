<?php
$host = 'localhost';        // Altere se necessário
$db   = 'codigobarras';        // Nome do banco
$user = 'root';      // Usuário do banco
$pass = '';        // Senha do banco
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'] ?? '';
        if ($nome !== '') {
            $stmt = $pdo->prepare("INSERT INTO produto (nome) VALUES (:nome)");
            $stmt->execute(['nome' => $nome]);
            echo "Produto cadastrado com sucesso!";
        } else {
            echo "Nome do produto é obrigatório.";
        }
    }
} catch (PDOException $e) {
    echo "Erro ao conectar: " . $e->getMessage();
}
