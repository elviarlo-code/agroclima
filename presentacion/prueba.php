<?php
require_once("../logica/clsPersona.php");
$objPersona = new clsPersona();
$listado = $objPersona->listarUbigeo();

$input = isset($_GET['input']) ? $_GET['input'] : '';
$sufijo = isset($_GET['sufijo']) ? $_GET['sufijo'] : '';

$tablaubigeo = 1;

// Crear contenido para el cuerpo del card
$bodyContent = '<div class="table-responsive">';
$bodyContent .= '<table class="table table-bordered table-hover table-light-primary table-sm" id="tabla_ubigeo">';
$bodyContent .= '<thead>
    <tr>
        <th>Ubigeo</th>
        <th>Departamento</th>
        <th>Provincia</th>
        <th>Distrito</th>
        <th>*</th>
    </tr>
</thead>
<tbody>';
while ($fila = $listado->fetch(PDO::FETCH_NAMED)) {
    $bodyContent .= '<tr>';
    $bodyContent .= '<td>' . $fila['iddistrito'] . '</td>';
    $bodyContent .= '<td>' . $fila['departamento'] . '</td>';
    $bodyContent .= '<td>' . $fila['provincia'] . '</td>';
    $bodyContent .= '<td>' . $fila['distrito'] . '</td>';
    $bodyContent .= '<td class="text-center">
        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold"
        onclick="seleccionaUbigeoPersona' . $sufijo . '(
            \'' . $fila['iddistrito'] . '\',
            \'' . $fila['idprovincia'] . '\',
            \'' . $fila['iddepartamento'] . '\',
            \'' . $fila['distrito'] . '\',
            \'' . $fila['provincia'] . '\',
            \'' . $fila['departamento'] . '\',
            \'' . $input . '\',
            \'' . $sufijo . '\'
        )">
        <i class="fas fa-check-square"></i> Seleccionar</button>
    </td>';
    $bodyContent .= '</tr>';
}
$bodyContent .= '</tbody></table></div>';

// Pasar el contenido a JavaScript
//$bodyContent = addslashes($bodyContent); // Escapar comillas para JavaScript
?>