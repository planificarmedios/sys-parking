<?php

include 'conexion.php';

$categoria_id = (int)$_POST['categoria_id'];

$query = mysqli_query($mysqli, "
    SELECT
        id,
        descripcion,
        es_default
    FROM tarifas
    WHERE categoria_id = $categoria_id
    AND estado = 1
    ORDER BY descripcion ASC
");

echo '
<option value="">
    Seleccionar tarifa
</option>
';

while ($t = mysqli_fetch_assoc($query)) {

    echo "
    <option
        value='{$t['id']}'
        data-default='".(int)$t['es_default']."'
    >
        {$t['descripcion']}
    </option>";
}