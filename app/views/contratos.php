<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Contratos</h1>
    <div class="campo">   
        <label for = 'idProvincia'>Provincia</label>
        <select id = 'idProvincia' name = 'idProvincia'> 
            <option value =""> Seleccione una provincia</option>

            <?php foreach($Provincias as $provincia): ?>
                <option value = " <?= htmlspecialchars($provincia['idProvincia']) ?> ">
                    <?= htmlspecialchars($provincia['NomProvincia']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for = 'idDistrito'>Distrito</label>
        <select id = 'idDistrito' name ='idDistrito'> 
            <option value =""> Seleccione un distrito</option>
            
        </select>
    </div>
    <div>
    </div>
    <footer></footer>
    
    <nav></nav>
    <script src = "js/contratos.js">  </script>
</body>
</html>