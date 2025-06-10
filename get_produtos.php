<?php
// Conexão com o banco (ajuste conforme seu ambiente)
$pdo = new PDO("mysql:host=localhost;dbname=codigobarras;charset=utf8", "root", "");

// Termo de busca enviado pelo Select2
$search = $_GET['search'] ?? '';

// Consulta segura com LIKE e parâmetro
$stmt = $pdo->prepare("SELECT id, nome FROM produto WHERE nome LIKE :search");
$stmt->execute(['search' => "%$search%"]);

$produtos = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $produtos[] = [
    'id' => $row['id'],
    'nome' => $row['nome']
  ];
}

// Retorna JSON
header('Content-Type: application/json');
echo json_encode($produtos);
?>
