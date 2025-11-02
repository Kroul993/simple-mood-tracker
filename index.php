<?php
require_once __DIR__ . '/db.php';

$message = '';
$error = '';

// Procesar mensaje de sesión (GET)
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

// Obtener todas las entradas (asegúrate que $db->getAllEntries() devuelve array)
$entries = [];
if (isset($db) && method_exists($db, 'getAllEntries')) {
    $entries = $db->getAllEntries();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo defined('APP_NAME') ? htmlspecialchars(APP_NAME) : 'Mi App'; ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1><?php echo defined('APP_NAME') ? htmlspecialchars(APP_NAME) : 'Mi App'; ?></h1>
        <p>Registra tu estado de ánimo diariamente</p>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-error">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <div class="btn-group">
        <a href="create.php" class="btn btn-primary">+ Registrar nuevo ánimo</a>
    </div>

    <?php if (!empty($entries) && count($entries) > 0): ?>
        <?php foreach ($entries as $entry): ?>
            <?php
                // Seguridad y valores por defecto
                $moodIndex = isset($entry['mood']) ? $entry['mood'] : null;
                $mood = ['emoji' => '', 'label' => 'Desconocido'];
                if ($moodIndex !== null && defined('MOODS') && isset(MOODS[$moodIndex])) {
                    $mood = MOODS[$moodIndex];
                }

                // Formateo de fecha seguro
                $formatted_date = '';
                if (!empty($entry['entry_date'])) {
                    try {
                        $date = new DateTime($entry['entry_date']);
                        $formatted_date = $date->format('d/m/Y');
                    } catch (Exception $e) {
                        $formatted_date = htmlspecialchars($entry['entry_date']);
                    }
                }
            ?>
            <div class="entry-card">
                <div class="entry-header">
                    <div class="entry-date"><?php echo $formatted_date; ?></div>
                    <div class="entry-mood">
                        <span class="mood-emoji"><?php echo htmlspecialchars($mood['emoji']); ?></span>
                        <span class="mood-text"><?php echo htmlspecialchars($mood['label']); ?></span>
                    </div>
                </div>

                <?php if (!empty($entry['notes'])): ?>
                <div class="entry-notes">
                    <strong>Notas:</strong> <?php echo nl2br(htmlspecialchars($entry['notes'])); ?>
                </div>
                <?php endif; ?>

                <div class="entry-actions">
                    <a href="edit.php?id=<?php echo urlencode($entry['id']); ?>" class="btn btn-secondary">Editar</a>

                    <form action="delete.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este registro? Esta acción no se puede deshacer.');">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($entry['id']); ?>">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
    <div class="empty">
        No hay registros aún. ¡Comienza a registrar tu ánimo!
    </div>
    <?php endif; ?>
</div>
</body>
</html>