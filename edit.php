<?php
require_once __DIR__ . '/db.php';

$errors = [];
$entry = null;
$form_data = ['entry_date' => '', 'mood' => '', 'notes' => ''];

// Obtener ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header('Location: index.php?error=ID inválido');
    exit;
}

// Obtener entrada
$entry = $db->getEntryById($id);

if (!$entry) {
    header('Location: index.php?error=Entrada no encontrada');
    exit;
}

// Cargar datos iniciales
$form_data = [
    'entry_date' => $entry['entry_date'],
    'mood' => $entry['mood'],
    'notes' => $entry['notes']
];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry_date = trim($_POST['entry_date'] ?? '');
    $mood = trim($_POST['mood'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Validaciones
    if (empty($entry_date)) {
        $errors[] = 'La fecha es requerida';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $entry_date);
        if (!$date || $date->format('Y-m-d') !== $entry_date) {
            $errors[] = 'La fecha debe ser válida';
        }
    }

    if (empty($mood) || !array_key_exists($mood, MOODS)) {
        $errors[] = 'Debes seleccionar un estado de ánimo válido';
    }

    // Si no hay errores, actualizar
    if (empty($errors)) {
        try {
            $db->updateEntry($id, $entry_date, $mood, $notes);
            header('Location: index.php?message=Entrada actualizada correctamente');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Error al actualizar: ' . $e->getMessage();
        }
    }

    // Guardar datos del formulario
    $form_data = ['entry_date' => $entry_date, 'mood' => $mood, 'notes' => $notes];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ánimo - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="form-container">
        <h2>Editar registro de ánimo</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Errores encontrados:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="edit.php?id=<?php echo $id; ?>" method="POST">
            <div class="form-group">
                <label for="entry_date">Fecha</label>
                <input type="date" id="entry_date" name="entry_date" value="<?php echo htmlspecialchars($form_data['entry_date']); ?>" required>
            </div>

            <div class="form-group">
                <label>¿Cómo te sentías?</label>
                <div class="mood-options">
                    <?php foreach (MOODS as $value => $mood): ?>
                        <div class="mood-option">
                            <input type="radio" id="mood_<?php echo $value; ?>" name="mood" value="<?php echo $value; ?>"
                                <?php echo $form_data['mood'] === $value ? 'checked' : ''; ?> required>
                            <label for="mood_<?php echo $value; ?>">
                                <span class="mood-emoji-form"><?php echo $mood['emoji']; ?></span>
                                <span class="mood-text-form"><?php echo $mood['label']; ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notas (opcional)</label>
                <textarea id="notes" name="notes" placeholder="¿Qué pasó hoy? ¿Por qué te sientes así?"><?php echo htmlspecialchars($form_data['notes']); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="index.php" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
</body>

</html>