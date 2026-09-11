<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

echo "Seeding database...\n";

// 1. Admin user
$adminUser = 'admin';
$adminEmail = 'contacto@funktographer.cl';
$adminPass = password_hash('admin1234', PASSWORD_BCRYPT);

$stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
$stmt->execute([$adminUser]);
if (!$stmt->fetch()) {
    $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$adminUser, $adminEmail, $adminPass]);
    echo "Default admin created: user 'admin', pass 'admin1234'\n";
}

// 2. Site settings
$settings = [
    'site_name' => 'Funktographer',
    'site_tagline' => 'Fotografía y Video Profesional en Santiago de Chile',
    'phone' => '+56 9 4757 3794',
    'whatsapp' => '56947573794',
    'email' => 'contacto@funktographer.cl',
    'instagram' => 'https://www.instagram.com/funktographer/',
    'coverage' => 'Santiago de Chile / Cobertura Nacional e Internacional',
    'about_short' => 'Capturamos mucho más que momentos. Contamos historias a través de imágenes, preservando las emociones, los lugares y las experiencias que hacen que cada recuerdo sea inolvidable.'
];

foreach ($settings as $key => $val) {
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$key, $val, $val]);
}
echo "Site settings initialized.\n";

// 3. Home images
$jsonFile = "C:/Users/abak_/.gemini/antigravity-ide/brain/af129efb-b666-4a51-ae67-a5bb0c4cf17d/scratch/downloaded_photos.json";
if (file_exists($jsonFile)) {
    $photos = json_decode(file_get_contents($jsonFile), true);
    
    // Helper to determine category
    function detectCategory($name) {
        $n = strtolower($name);
        if (str_contains($n, 'orsan') || str_contains($n, 'evento') || str_contains($n, 'cisco') || str_contains($n, 'ponencia')) {
            return 'Eventos';
        }
        if (str_contains($n, 'croissant') || str_contains($n, 'ramen') || str_contains($n, 'tartaleta') || str_contains($n, 'postre') || str_contains($n, 'torta') || str_contains($n, 'profiterol') || str_contains($n, 'cocteles') || str_contains($n, 'surf')) {
            return 'Gastronomia';
        }
        if (str_contains($n, 'fujifilm') || str_contains($n, 'camara')) {
            return 'Equipo';
        }
        return 'Retratos';
    }

    $pdo->exec("DELETE FROM home_images");
    $stmt = $pdo->prepare("INSERT INTO home_images (title, image_url, category, display_order, is_active) VALUES (?, ?, ?, ?, 1)");
    
    $order = 1;
    foreach ($photos as $p) {
        $title = ucwords(str_replace(['-', '_', 'scaled', '1'], ' ', pathinfo($p['filename'], PATHINFO_FILENAME)));
        $cat = detectCategory($p['filename']);
        $imgUrl = 'uploads/home/' . $p['filename'];
        $stmt->execute([$title, $imgUrl, $cat, $order++]);
    }
    echo "Seeded " . count($photos) . " home images into database.\n";
}

// 4. Projects and Project Images
$pdo->exec("DELETE FROM project_images");
$pdo->exec("DELETE FROM projects");

$projectsData = [
    [
        'title' => 'Reportaje Corporativo WEG: ¡Conectados para Impulsar!',
        'slug' => 'reportaje-corporativo-weg',
        'category' => 'Eventos',
        'client' => 'WEG Chile',
        'event_date' => 'Marzo 2024',
        'description' => 'Cobertura integral fotográfica y audiovisual del evento anual corporativo de WEG Chile. Captura de ponencias, ambiente networking, interacción entre líderes y momentos clave con iluminación controlada en salón.',
        'cover_image' => 'uploads/home/Fotogo-Ponencia-cisco-mining-summit-2024-Funktographer.jpg',
        'is_featured' => 1,
        'images' => [
            'uploads/home/Fotogo-Ponencia-cisco-mining-summit-2024-Funktographer.jpg',
            'uploads/home/Foto-evento-Algo-electrico-con-quimica-Funktographer-24.jpg',
            'uploads/home/Evento-Orsan-Funktographer-5-scaled.jpg'
        ]
    ],
    [
        'title' => 'Convención Anual Orsan 2024',
        'slug' => 'convencion-anual-orsan-2024',
        'category' => 'Eventos',
        'client' => 'Orsan',
        'event_date' => 'Enero 2024',
        'description' => 'Registro dinámico del congreso corporativo y celebración empresarial para Orsan. Retratos protocolares en photocall, momentos de premiación, espectáculos en vivo y cierre festivo.',
        'cover_image' => 'uploads/home/Evento-Orsan_2024_b-scaled.jpg',
        'is_featured' => 1,
        'images' => [
            'uploads/home/Evento-Orsan_2024_b-scaled.jpg',
            'uploads/home/Evento-Orsan-Funktographer-2.jpg',
            'uploads/home/Evento-Orsan-Funktographer-3.jpg',
            'uploads/home/Evento-Orsan-Funktographer-7-scaled.jpg',
            'uploads/home/Orsan-1-scaled.jpg'
        ]
    ],
    [
        'title' => 'Carta Visual Gourmet: D’Gang y Pine Cone',
        'slug' => 'carta-visual-dgang-pinecone',
        'category' => 'Gastronomia',
        'client' => 'D’Gang / Pastelería Pine Cone',
        'event_date' => 'Febrero 2024',
        'description' => 'Fotografía publicitaria y gastronómica de alta definición para menú impreso y plataformas digitales. Trabajo de texturas, iluminación lateral para realzar frescura y volumen de pastelería fina, platos calientes y coctelería de autor.',
        'cover_image' => 'uploads/home/Foto-Surf-and-Turf-Dgang-Funktographer.jpg',
        'is_featured' => 1,
        'images' => [
            'uploads/home/Foto-Surf-and-Turf-Dgang-Funktographer.jpg',
            'uploads/home/Tartaleta-de-frutas_Pine-Cone_1.jpg',
            'uploads/home/Postre_Pine-Cone.jpg',
            'uploads/home/Ramen-One-Fideos-Web4466.jpg',
            'uploads/home/Croissant.jpg',
            'uploads/home/Cocteles-Dgang-27-Dic8887.jpg'
        ]
    ],
    [
        'title' => 'Editorial de Retrato y Moda Urbana',
        'slug' => 'editorial-retrato-moda-urbana',
        'category' => 'Retratos',
        'client' => 'Editorial Independiente / Modelos',
        'event_date' => '2024',
        'description' => 'Sesiones de retrato en locación urbana y estudio con énfasis en expresión, textura y paleta de color cinematográfica. Uso de ópticas fijas con gran apertura para aislar sujetos con desenfoque suave (bokeh).',
        'cover_image' => 'uploads/home/Retrato-modelo-genesis-6-Funktographer.jpg',
        'is_featured' => 1,
        'images' => [
            'uploads/home/Retrato-modelo-genesis-6-Funktographer.jpg',
            'uploads/home/giselle_bog_park.jpg',
            'uploads/home/Diana_ec_arbol_2.jpg',
            'uploads/home/ari_bokeh_1.jpg',
            'uploads/home/Pryscila_workshop_gaston.jpg',
            'uploads/home/Genesis_2.jpg'
        ]
    ]
];

$stmtProj = $pdo->prepare("INSERT INTO projects (title, slug, category, client, event_date, description, cover_image, is_featured, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmtImg = $pdo->prepare("INSERT INTO project_images (project_id, image_url, caption, display_order) VALUES (?, ?, ?, ?)");

$pOrder = 1;
foreach ($projectsData as $p) {
    $stmtProj->execute([
        $p['title'],
        $p['slug'],
        $p['category'],
        $p['client'],
        $p['event_date'],
        $p['description'],
        $p['cover_image'],
        $p['is_featured'],
        $pOrder++
    ]);
    $projId = $pdo->lastInsertId();
    
    $iOrder = 1;
    foreach ($p['images'] as $img) {
        $stmtImg->execute([$projId, $img, $p['title'], $iOrder++]);
    }
}

echo "Seeded " . count($projectsData) . " projects with gallery photos successfully!\n";
