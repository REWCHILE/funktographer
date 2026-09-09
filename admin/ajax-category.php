<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_verify($csrf)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Token CSRF inválido o sesión expirada.']);
    exit;
}

$action = trim($_POST['action'] ?? 'create');

function getCategoriesList($pdo) {
    return $pdo->query("SELECT id, name, slug, display_order, is_active FROM categories ORDER BY display_order ASC, id ASC")->fetchAll();
}

try {
    // 1. LIST CATEGORIES
    if ($action === 'list') {
        echo json_encode([
            'success' => true,
            'categories' => getCategoriesList($pdo)
        ]);
        exit;
    }

    // 2. DELETE CATEGORY
    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de categoría inválido.']);
            exit;
        }

        // Fetch category to delete
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch();
        if (!$cat) {
            echo json_encode(['success' => false, 'error' => 'La categoría no existe.']);
            exit;
        }

        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);

        echo json_encode([
            'success' => true,
            'message' => "Categoría '{$cat['name']}' eliminada correctamente.",
            'deleted_id' => $id,
            'categories' => getCategoriesList($pdo)
        ]);
        exit;
    }

    // 3. UPDATE CATEGORY
    if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 1;

        if ($id <= 0 || empty($name)) {
            echo json_encode(['success' => false, 'error' => 'El nombre de la categoría es obligatorio.']);
            exit;
        }

        $slug = slugify($name);
        // Ensure slug is unique except current
        $stmtSlug = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
        $stmtSlug->execute([$slug, $id]);
        if ($stmtSlug->fetch()) {
            $slug .= '-' . $id;
        }

        $stmtUp = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, display_order = ? WHERE id = ?");
        $stmtUp->execute([$name, $slug, $order, $id]);

        echo json_encode([
            'success' => true,
            'message' => "Categoría actualizada con éxito.",
            'category' => ['id' => $id, 'name' => $name, 'slug' => $slug, 'display_order' => $order],
            'categories' => getCategoriesList($pdo)
        ]);
        exit;
    }

    // 4. CREATE CATEGORY (DEFAULT)
    $name = trim($_POST['name'] ?? '');
    $slugInput = trim($_POST['slug'] ?? '');
    $order = isset($_POST['display_order']) && $_POST['display_order'] !== '' ? intval($_POST['display_order']) : null;

    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'El nombre de la categoría no puede estar vacío.']);
        exit;
    }

    $slug = !empty($slugInput) ? slugify($slugInput) : slugify($name);

    if ($order === null) {
        $maxOrder = $pdo->query("SELECT MAX(display_order) FROM categories")->fetchColumn();
        $order = ($maxOrder !== false) ? intval($maxOrder) + 1 : 1;
    }

    $stmtCheck = $pdo->prepare("SELECT id, name, slug, display_order FROM categories WHERE slug = ? OR name = ? LIMIT 1");
    $stmtCheck->execute([$slug, $name]);
    $existing = $stmtCheck->fetch();

    if ($existing) {
        echo json_encode([
            'success' => true,
            'is_existing' => true,
            'message' => 'La categoría ya existía y fue seleccionada.',
            'category' => [
                'id' => intval($existing['id']),
                'name' => $existing['name'],
                'slug' => $existing['slug'],
                'display_order' => intval($existing['display_order'])
            ],
            'categories' => getCategoriesList($pdo)
        ]);
        exit;
    }

    $stmtInsert = $pdo->prepare("INSERT INTO categories (name, slug, display_order, is_active) VALUES (?, ?, ?, 1)");
    $stmtInsert->execute([$name, $slug, $order]);
    $newId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Categoría creada con éxito.',
        'category' => [
            'id' => intval($newId),
            'name' => $name,
            'slug' => $slug,
            'display_order' => $order
        ],
        'categories' => getCategoriesList($pdo)
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error en base de datos: ' . $e->getMessage()]);
}
