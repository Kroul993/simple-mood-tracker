<?php
require_once __DIR__ . '/config.php';

class Database
{
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO('sqlite:' . DB_PATH);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTable();
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS mood_entries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            entry_date DATE NOT NULL,
            mood TEXT NOT NULL CHECK(mood IN ('very_bad', 'bad', 'neutral', 'good', 'very_good')),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->pdo->exec($sql);
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    // Obtener todas las entradas ordenadas por fecha descendente
    public function getAllEntries()
    {
        $sql = "SELECT * FROM mood_entries ORDER BY entry_date DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una entrada por ID
    public function getEntryById($id)
    {
        $sql = "SELECT * FROM mood_entries WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nueva entrada
    public function createEntry($entry_date, $mood, $notes)
    {
        $sql = "INSERT INTO mood_entries (entry_date, mood, notes) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$entry_date, $mood, $notes]);
    }

    // Actualizar entrada
    public function updateEntry($id, $entry_date, $mood, $notes)
    {
        $sql = "UPDATE mood_entries SET entry_date = ?, mood = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$entry_date, $mood, $notes, $id]);
    }

    // Eliminar entrada
    public function deleteEntry($id)
    {
        $sql = "DELETE FROM mood_entries WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Obtener entrada por fecha (para validar duplicados)
    public function getEntryByDate($date, $excludeId = null)
    {
        $sql = "SELECT * FROM mood_entries WHERE entry_date = ?";
        if ($excludeId) {
            $sql .= " AND id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$date, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$date]);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Crear instancia global
$db = new Database();
