<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $quantidade = intval($_POST['quantidade']);
    $codigo = htmlspecialchars($_POST['codigo']);
    $data = htmlspecialchars($_POST['data']);
    $lote = htmlspecialchars($_POST['lote']);
    $exibirCodigo = isset($_POST['exibirCodigo']) && $_POST['exibirCodigo'] == '1';
    $modo = isset($_POST['modo']) && $_POST['modo'] === 'ppla' ? 'ppla' : 'pplb';

    $etiquetas = '';
    for ($i = 1; $i <= $quantidade; $i++) {
       if ($modo === 'ppla') {
            // Comandos PPLA reais
            $etiqueta = "N\n"; // Início da etiqueta (normal mode)
            
            $etiqueta .= "A50,50,0,3,1,1,N,\"$nome\"\n";
            $etiqueta .= "A50,100,0,3,1,1,N,\"Qtd: $i de $quantidade\"\n";
            $etiqueta .= "A50,150,0,3,1,1,N,\"Lote: $lote\"\n";
            $etiqueta .= "A50,200,0,3,1,1,N,\"Data: $data\"\n";

            if ($exibirCodigo && !empty($codigo)) {
                // B = código de barras (posição x, y, rotação, tipo, largura1, largura2, altura, HRI, dados)
                $etiqueta .= "B50,250,0,1,2,2,100,N,\"$codigo\"\n";
            }

            $etiqueta .= "P1\n"; // Imprimir uma etiqueta
        } else {
            // Comandos ZPL
            $etiqueta = "^XA";
            $etiqueta .= "^FO50,30^A0N,30,30^FD" . $nome . "^FS";
            $etiqueta .= "^FO50,70^A0N,25,25^FDQuantidade: " . $i . " de " . $quantidade . "^FS";
            $etiqueta .= "^FO50,110^A0N,25,25^FDLote: " . $lote . "^FS";
            $etiqueta .= "^FO50,150^A0N,25,25^FDData: " . $data . "^FS";
            if ($exibirCodigo && !empty($codigo)) {
                $etiqueta .= "^FO50,190^BY2^BCN,100,Y,N,N^FD" . $codigo . "^FS";
            }
            $etiqueta .= "^XZ";
        }

        $etiquetas .= $etiqueta;
    }

    // Impressora
    $printer_name = "Argox OS-214 PLUS";

    // Arquivo temporário
    $temp_file = tempnam(sys_get_temp_dir(), $modo . '_');
    file_put_contents($temp_file, $etiquetas);

    // Comando para Windows
    $command = "COPY /B \"$temp_file\" \"\\\\$printer_name\"";
    exec($command, $output, $return_var);

    unlink($temp_file);

    echo json_encode(['success' => true, 'message' => 'Impressão enviada com sucesso']);
    exit;
}
?>
