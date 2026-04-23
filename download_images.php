<?php
// Run this file ONCE on the server then delete it
$images = [
    '1_prawns.jpg'      => 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=600&q=80',
    '2_crabsoup.jpg'    => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=600&q=80',
    '3_fishcutlets.jpg' => 'https://images.unsplash.com/photo-1519984388953-d2406bc725e1?w=600&q=80',
    '4_calamari.jpg'    => 'https://images.unsplash.com/photo-1604909052743-94e838986d24?w=600&q=80',
    '5_snapper.jpg'     => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=600&q=80',
    '6_lobster.jpg'     => 'https://images.unsplash.com/photo-1615361200141-f45040f367be?w=600&q=80',
    '7_crab.jpg'        => 'https://images.unsplash.com/photo-1599084993091-1cb5c0721cc6?w=600&q=80',
    '8_prawn.jpg'       => 'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=600&q=80',
    '9_rice.jpg'        => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=600&q=80',
    '10_beef.jpg'       => 'https://images.unsplash.com/photo-1558030006-450675393462?w=600&q=80',
    '11_pannacotta.jpg' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=600&q=80',
    '12_watalappan.jpg' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=600&q=80',
    '13_lavacake.jpg'   => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&q=80',
    '14_cheesecake.jpg' => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=600&q=80',
    '15_coconut.jpg'    => 'https://images.unsplash.com/photo-1550828520-4cb496926fc9?w=600&q=80',
    '16_lassi.jpg'      => 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=600&q=80',
    '17_lime.jpg'       => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=600&q=80',
    '18_tea.jpg'        => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=600&q=80',
];

$dir = __DIR__ . '/admin/uploads/menu/';
if (!is_dir($dir)) mkdir($dir, 0755, true);

echo "<h2>Downloading images...</h2>";
foreach ($images as $filename => $url) {
    $path = $dir . $filename;
    $data = @file_get_contents($url);
    if ($data) {
        file_put_contents($path, $data);
        echo "✅ $filename downloaded<br>";
    } else {
        echo "❌ $filename failed<br>";
    }
}

// Now update the database
include 'config/db.php';
$updates = [
    'Spiced Devilled Prawns'   => '1_prawns.jpg',
    'Crab Soup'                => '2_crabsoup.jpg',
    'Fish Cutlets'             => '3_fishcutlets.jpg',
    'Calamari Rings'           => '4_calamari.jpg',
    'Whole Grilled Red Snapper'=> '5_snapper.jpg',
    'Lobster Thermidor'        => '6_lobster.jpg',
    'Butter Garlic Crab'       => '7_crab.jpg',
    'Prawn Curry'              => '8_prawn.jpg',
    'Rice and Curry'           => '9_rice.jpg',
    'Grilled Beef Tenderloin'  => '10_beef.jpg',
    'Coconut Panna Cotta'      => '11_pannacotta.jpg',
    'Watalappan'               => '12_watalappan.jpg',
    'Chocolate Lava Cake'      => '13_lavacake.jpg',
    'Mango Cheesecake'         => '14_cheesecake.jpg',
    'Fresh King Coconut'       => '15_coconut.jpg',
    'Mango Lassi'              => '16_lassi.jpg',
    'Fresh Lime Soda'          => '17_lime.jpg',
    'Ceylon Tea'               => '18_tea.jpg',
];

echo "<h2>Updating database...</h2>";
foreach ($updates as $name => $file) {
    $name = $conn->real_escape_string($name);
    $conn->query("UPDATE menu_items SET image='$file' WHERE name='$name'");
    echo "✅ $name updated<br>";
}

echo "<h2 style='color:green'>✅ All done! <a href='index.php'>Go to site</a> — then DELETE this file!</h2>";
?>