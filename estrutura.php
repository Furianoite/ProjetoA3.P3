<!DOCTYPE html>
<html>
<head>
    <title>Análise Léxica de Código</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to bottom right, #333333, #666666);
        }
        .container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            padding: 20px;
            background-color: #fff; 
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
        }
        .code-label {
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 25px; 
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px; 
            background: linear-gradient(to right, #333333, #999999);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        textarea {
            margin-top: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 2px solid #ccc;
            font-size: 16px;
            font-family: 'Arial';
        }
        .custom-select {
            margin-top: 10px;
        }
        input[type="submit"] {
            margin-top: 10px;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }
        .resultado-analise {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 5px;
            text-align: left;
        }
        h2 {
            margin-bottom: 10px;
        }
        strong {
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="code-label">ANALISADOR LÉXICO</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="codigo" class="code-label">INSIRA O CÓDIGO:</label><br>
            <textarea id="codigo" name="codigo" rows="10" cols="50"></textarea><br>
            <label for="linguagem" class="select-label">Selecione a linguagem:</label>
            <div class="custom-select">
                <select name="linguagem" id="linguagem">
                    <option value="php">PHP</option>
                    <option value="python">PYTHON</option>
                    <option value="java">JAVA</option>
                </select>
            </div>
            <input type="submit" value="Analisar">
        </form>
<?php
function realizarAnaliseLexica($codigo, $linguagem) {

    $padraoPalavrasChave = '';
    $padraoIdentificadores = '';
    $padraoOperadores = '';
    $padraoDelimitadores = '';

    switch ($linguagem) {
        case 'php':
            $padraoPalavrasChave = "/\b(if|else|for|while|do|break|class|public|private|function|return|try|catch|finally)\b/";
            $padraoIdentificadores = '/(?<![\w\->])\$\w+\b/';
            $padraoOperadores = "/[\+\-\*\/=<>]|&&|\|\|/";
            $padraoDelimitadores = "/[\(\)\{\}\[\]\.,;]/";
            break;
        case 'python':
            $padraoPalavrasChave = "/\b(if|else|for|while|def|class|import|elif|try|except|finally)\b/";
            $padraoIdentificadores = '/(?<![a-zA-Z0-9_])\b(?!(if|else|for|while|print|def|class|import|elif|try|except|finally|in|switch|case|condi|o|__name__|__main__|and|or|not)\b)[a-zA-Z_][a-zA-Z0-9_]*(?=\s|\(|\b)/';
            $padraoOperadores = "/[\+\-\*\/=<>]|and|or|not/";
            $padraoDelimitadores = "/[\(\)\{\}\[\]\.,;]/";
            break;
        case 'java':
            $padraoPalavrasChave = "/\b(if|else|for|while|do|break|class|public|private|static|void|interface|extends|implements|this|super)\b/";
            $padraoIdentificadores = "/\b(?!(if|else|for|while|do|break|class|public|private|static|void|interface|extends|implements|this|super|System|out|println|printStream|import|new|int|String|Scanner|switch|case|default|main|args|print|in|nextInt|case|java|util))\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\b/";
            $padraoOperadores = "/[\+\-\*\/=<>]|&&|\|\|/";
            $padraoDelimitadores = "/[\(\)\{\}\[\]\.,;]/";
            break;
        default:
            echo "Linguagem não suportada.";
            return;
    }
   
    $codigoSemStrings = preg_replace('/".*?"/', '', $codigo);
   
    $codigoSemStringsOuComentarios = preg_replace(array('/".*?"/', "/'.*?'/", "/#.*?\n/"), '', $codigoSemStrings);
    
    preg_match_all($padraoIdentificadores, $codigoSemStringsOuComentarios, $identificadoresEncontrados);
    preg_match_all($padraoPalavrasChave, $codigo, $palavrasChaveEncontradas);
    preg_match_all($padraoOperadores, $codigo, $operadoresEncontrados);
    preg_match_all($padraoDelimitadores, $codigo, $delimitadoresEncontrados);

    $palavrasChaveUnicas = array_unique($palavrasChaveEncontradas[0]);
    $identificadoresUnicos = array_unique($identificadoresEncontrados[0]);
    $operadoresUnicos = array_unique($operadoresEncontrados[0]);
    $delimitadoresUnicos = array_unique($delimitadoresEncontrados[0]);

    echo '<div class="resultado-analise" id="resultado-analise">';
    echo "<h2>Resultado da Análise Léxica:</h2>";
    echo "<strong>Palavras-chave encontradas:</strong> " . implode(", ", $palavrasChaveUnicas) . "<br>";
    echo "<strong>Operadores encontrados:</strong> " . implode(", ", $operadoresUnicos) . "<br>";
    echo "<strong>Delimitadores encontrados:</strong> " . implode(", ", $delimitadoresUnicos) . "<br>";
    echo "<strong>Identificadores (variáveis) encontrados:</strong> " . implode(", ", $identificadoresUnicos) . "<br>";
    echo '</div>';

    echo '<script>
        setTimeout(function() {
            var resultado = document.getElementById("resultado-analise");
            resultado.style.display = "block";
        }, 1000);
    </script>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["codigo"]) && isset($_POST["linguagem"])) {
    $codigoInserido = $_POST["codigo"];
    $linguagem = $_POST["linguagem"];
    realizarAnaliseLexica($codigoInserido, $linguagem);
}
?>
