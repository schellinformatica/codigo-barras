<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistema de Impressão de Etiquetas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Select2 Bootstrap 5 Theme -->
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />

  <style>
    body {
      background-color: #f8f9fa;
    }

    .form-section {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .etiqueta {
      width: 200px;
      background: #fdfdfd;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 12px;
      font-size: 14px;
    }

    #previewContainer {
      margin-top: 30px;
    }

    .form-title {
      font-weight: 600;
      font-size: 1.5rem;
      margin-bottom: 25px;
      text-align: center;
    }

    .btn-group-custom .btn {
      min-width: 160px;
    }
  </style>
</head>

<body class="bg-light">
  <div class="container py-5">
    <div class="form-section mx-auto" style="max-width: 650px;">
      <div class="form-title">Impressão de Etiquetas</div>
      <form id="impressaoForm">
        <div class="mb-3">
          <label for="produto_id" class="form-label">Produto</label>
          <select id="produto_id" name="produto_id" class="form-select" style="width: 100%"></select>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" id="quantidade" name="quantidade" class="form-control" min="1" required>
          </div>

          <div class="col-md-6 mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" id="data" name="data" class="form-control" required>
          </div>
        </div>

        <div class="mb-3">
          <label for="lote" class="form-label">Lote</label>
          <input type="text" id="lote" name="lote" class="form-control" required>
        </div>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="mostrarCodigo" checked>
          <label class="form-check-label" for="mostrarCodigo">Incluir Código de Barras</label>
        </div>

        <div class="mb-3" id="codigoGroup">
          <label for="codigo" class="form-label">Código de Barras</label>
          <input type="text" id="codigo" name="codigo" class="form-control">
        </div>

        <div class="btn-group-custom d-grid gap-2 d-md-flex justify-content-md-between flex-wrap mb-3">
          <button type="submit" class="btn btn-success">Imprimir PPLB</button>
          <button type="button" class="btn btn-warning" onclick="imprimirPPLA()">Imprimir PPLA</button>
          <button type="button" class="btn btn-outline-primary" onclick="gerarArquivoTxt()">Gerar PPLB GoLabel</button>
          <button type="button" class="btn btn-outline-primary" onclick="gerarArquivoTxtPPLA()">Gerar PPLA GoLabel</button>
          <button type="button" class="btn btn-secondary" onclick="mostrarPreview()">Pré-visualizar</button>
        </div>

        <div id="status" class="alert alert-info d-none text-center">
          <img src="loading.gif" alt="Carregando..." style="width: 20px; height: 20px;">
          <span>Enviando para impressão...</span>
        </div>

        <div id="txtStatus" class="alert alert-success d-none text-center"></div>

        <hr class="my-4" />

        <div>
          <button type="button" class="btn btn-outline-dark w-100" onclick="mostrarFormularioCadastro()">Cadastrar novo produto</button>
          <div id="formularioCadastro" class="mt-3 d-none">
            <input type="password" id="senhaCadastro" class="form-control mb-2" placeholder="Senha de acesso">
            <input type="text" id="novoProduto" class="form-control mb-2" placeholder="Nome do produto" disabled>
            <button type="button" class="btn btn-success w-100" onclick="cadastrarNovoProduto()">Salvar</button>
            <div id="mensagemCadastro" class="mt-2 text-center"></div>
          </div>
        </div>
      </form>
    </div>

    <div id="previewContainer" class="d-flex flex-wrap justify-content-center gap-3"></div>
  </div>

  <script>

    $('#produto_id').select2({
      theme: 'bootstrap4', // aplica estilo compatível com Bootstrap 4/5
      placeholder: 'Selecione um produto',
    });

    document.getElementById('mostrarCodigo').addEventListener('change', function () {
      document.getElementById('codigoGroup').style.display = this.checked ? 'block' : 'none';
    });

    function gerarArquivoTxt() {
      var nome = document.getElementById('nome').value;
      var quantidade = document.getElementById('quantidade').value;
      var codigo = document.getElementById('codigo').value;

      var conteudo = `^XA
^FO100,50^A0N,30,30^FD${nome}^FS
^FO100,100^A0N,30,30^FDQuantidade: ${quantidade}^FS
^FO100,150^BY2,3,100^BCN,100,Y,N,N
^FD${codigo}^FS
^XZ`;

      var link = document.createElement('a');
      link.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(conteudo);
      link.download = 'etiqueta.txt';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      const txtStatus = document.getElementById('txtStatus');
      txtStatus.classList.remove('d-none');
      txtStatus.textContent = 'Arquivo gerado e baixado com sucesso!';
    }

    function gerarArquivoTxtPPLA() {
        var nome = document.getElementById('nome').value;
        var quantidade = document.getElementById('quantidade').value;
        var codigo = document.getElementById('codigo').value;

        var conteudo = `N
        A50,50,0,3,1,1,N,"${nome}"
        A50,100,0,3,1,1,N,"Quantidade: ${quantidade}"
        B50,150,0,1,2,2,100,N,"${codigo}"
        P1
        `;

        var link = document.createElement('a');
        link.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(conteudo);
        link.download = 'etiqueta_ppla.txt';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        const txtStatus = document.getElementById('txtStatus');
        txtStatus.classList.remove('d-none');
        txtStatus.textContent = 'Arquivo PPLA gerado e baixado com sucesso!';
    }

function mostrarPreview() {
    //const nome = document.getElementById('nome').value;
    const nome = $('#produto_id').find(':selected').text();
    const quantidade = parseInt(document.getElementById('quantidade').value);
    const data = document.getElementById('data').value;
    const lote = document.getElementById('lote').value;
    const codigo = document.getElementById('codigo').value;
    const exibirCodigo = document.getElementById('mostrarCodigo').checked;

    const previewContainer = document.getElementById('previewContainer');
    previewContainer.innerHTML = '';
    previewContainer.className = 'd-flex flex-wrap justify-content-center gap-3 mt-4 p-3 border border-2 rounded bg-white';

    // Converte data para o formato dd/mm/aaaa
    let dataFormatada = '';
    if (data) {
        const partesData = data.split('-');
        if (partesData.length === 3) {
            dataFormatada = `${partesData[2]}/${partesData[1]}/${partesData[0]}`;
        }
    }

    for (let i = 1; i <= quantidade; i++) {
        const etiquetaDiv = document.createElement('div');
        etiquetaDiv.className = 'etiqueta border rounded p-3 bg-light text-start';
        etiquetaDiv.style.width = '200px';

        etiquetaDiv.innerHTML = `
            <img src="logo-campo-fino.png" alt="Logo" style="max-width: 100%; max-height: 50px; display: block; margin: 0 auto 8px;">
            Nome: ${nome}<br>
            Quantidade: ${i} de ${quantidade}<br>
            Lote: ${lote}<br>
            Data: ${dataFormatada}<br>
            ${exibirCodigo && codigo.trim() !== '' ? `Código de Barras: ${codigo}<br>` : ''}
        `;

        previewContainer.appendChild(etiquetaDiv);
    }
}

    document.getElementById('impressaoForm').addEventListener('submit', function (event) {
      event.preventDefault();

      //var nome = document.getElementById('nome').value;
      var nome = $('#produto_id').find(':selected').text();
      var quantidade = document.getElementById('quantidade').value;
      var data = document.getElementById('data').value;
      var lote = document.getElementById('lote').value;
      var codigo = document.getElementById('codigo').value;
      var exibirCodigo = document.getElementById('mostrarCodigo').checked;

      const statusDiv = document.getElementById('status');
      statusDiv.classList.remove('d-none');
      statusDiv.innerHTML = `<img src="loading.gif" style="width: 20px; height: 20px;"> Enviando para impressão...`;

      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'processar.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.send(
        'nome=' + encodeURIComponent(nome) +
        '&quantidade=' + encodeURIComponent(quantidade) +
        '&codigo=' + encodeURIComponent(codigo) +
        '&data=' + encodeURIComponent(data) +
        '&lote=' + encodeURIComponent(lote) +
        '&exibirCodigo=' + (exibirCodigo ? '1' : '0')
      );
      xhr.onload = function () {
        if (xhr.status === 200) {
          statusDiv.innerHTML = 'Impressão enviada com sucesso!';
        } else {
          statusDiv.innerHTML = 'Erro ao enviar impressão.';
        }
      };
    });

    function imprimirPPLA() {
        var nome = $('#produto_id').find(':selected').text();
        var quantidade = document.getElementById('quantidade').value;
        var data = document.getElementById('data').value;
        var lote = document.getElementById('lote').value;
        var codigo = document.getElementById('codigo').value;
        var exibirCodigo = document.getElementById('mostrarCodigo').checked;

        const statusDiv = document.getElementById('status');
        statusDiv.classList.remove('d-none');
        statusDiv.innerHTML = '<img src="loading.gif" style="width: 20px; height: 20px;"> Enviando para impressão...';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'processar.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(
            'nome=' + encodeURIComponent(nome) +
            '&quantidade=' + encodeURIComponent(quantidade) +
            '&codigo=' + encodeURIComponent(codigo) +
            '&data=' + encodeURIComponent(data) +
            '&lote=' + encodeURIComponent(lote) +
            '&exibirCodigo=' + (exibirCodigo ? '1' : '0') +
            '&modo=ppla'
        );
        xhr.onload = function () {
            if (xhr.status === 200) {
            statusDiv.innerHTML = 'Impressão (PPLA) enviada com sucesso!';
            } else {
            statusDiv.innerHTML = 'Erro ao enviar impressão (PPLA).';
            }
        };
    }


    function mostrarFormularioCadastro() {
      document.getElementById('formularioCadastro').classList.remove('d-none');
      document.getElementById('mensagemCadastro').innerText = '';
      document.getElementById('novoProduto').disabled = true;

      document.getElementById('senhaCadastro').addEventListener('input', function () {
        const senha = this.value;
        document.getElementById('novoProduto').disabled = (senha !== '1234'); // Defina sua senha aqui
      });
    }

    function cadastrarNovoProduto() {
      const nomeProduto = document.getElementById('novoProduto').value;

      if (nomeProduto.trim() === '') {
        document.getElementById('mensagemCadastro').innerHTML = '<div class="alert alert-warning">Informe o nome do produto.</div>';
        return;
      }

      fetch('cadastrar_produto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'nome=' + encodeURIComponent(nomeProduto)
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('mensagemCadastro').innerHTML = '<div class="alert alert-success">Produto cadastrado com sucesso!</div>';
        document.getElementById('novoProduto').value = '';
        document.getElementById('senhaCadastro').value = '';
        document.getElementById('novoProduto').disabled = true;
      })
      .catch(() => {
        document.getElementById('mensagemCadastro').innerHTML = '<div class="alert alert-danger">Erro ao cadastrar produto.</div>';
      });
    }



    $(document).ready(function () {
      $('#produto_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Selecione um produto',
        ajax: {
          url: 'get_produtos.php',
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              search: params.term || '' // suporta vazio
            };
          },
          processResults: function (data) {
            return {
              results: data.map(function (produto) {
                return {
                  id: produto.id,
                  text: produto.nome
                };
              })
            };
          },
          cache: true
        },
        minimumInputLength: 0 // permite buscar com string vazia
      });

      $('#produto_id').on('select2:open', function () {
        $('.select2-search__field').trigger('keyup'); // força a busca com ''
      });
    });


  </script>
</body>
</html>
