<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id === 0) {
        header('Location: index.php?error=ID inválido');
        exit;
    }

    try {
        $db->deleteEntry($id);
        header('Location: index.php?message=Entrada eliminada correctamente');
        exit;
    } catch (Exception $e) {
        header('Location: index.php?error=Error al eliminar: ' . urlencode($e->getMessage()));
        exit;
    }
}

// Si no es POST, redirigir
header('Location: index.php');
exit;
