<?php
function puedeAccederModulo($perfil, $modulo) {

    // 🔧 Normalización de perfil
    $perfil = trim($perfil);
    if ($perfil === 'Operador-a') {
        $perfil = 'Operador';
    }

    // Super Admin entra a todo
    if ($perfil === 'Super Admin') {
        return true;
    }

    global $mysqli;

    $modulo = trim(strtolower($modulo));

    $stmt = $mysqli->prepare("
        SELECT 1
        FROM profile_modules
        WHERE perfil = ?
          AND LOWER(modulo) = ?
          AND puede_acceder = 1
        LIMIT 1
    ");
    $stmt->bind_param("ss", $perfil, $modulo);
    $stmt->execute();
    $stmt->store_result();

    return $stmt->num_rows > 0;
}