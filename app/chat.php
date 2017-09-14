<?php
    
    $requestMethod = getenv('REQUEST_METHOD');
    if ($requestMethod !== 'POST') {
        header('Location: /');
    }
    
    $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_SPECIAL_CHARS);
?>

<html>
    <head>
        <title>title</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        
        <script>
            window.sessionStorage.setItem('nickname', '<?= $nickname; ?>');
        </script>
    </head>
    <body>
        <div class="container">
            <h1>Sala</h1>
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <textarea class="form-control" rows="6" name="output"></textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select multiple="true" name="clients" class="form-control"></select>
                    </div>
                </div>
            </div>

            <div class="input-group">
                <input type="text" class="form-control" name="input"/>
                <span class="input-group-btn">
                    <button class="btn btn-default" name="send" type="button">Enviar</button>
                </span>
            </div>
        </div>

        <script src="../client/websocket.js"></script>
    </body>
</html>