<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['checkedItem'])) {
        foreach ($_POST['checkedItem'] as $id) {
            $filename = 'data/' . basename($id) . '.txt';
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }
    header('Location: admin.php');
    exit;
}
$items = [];
$files = glob("data/file_*.txt");
foreach ($files as $file) {
    $id = basename($file, '.txt');
    $items[$id] = file_get_contents($file);
}
?>

<html>
    <body>
        <h1>Admin</h1>

        <form action="" method="POST">
            <ul>
                <?php foreach ($items as $id => $item): ?>
                    <li>
                        <label>
                            <input type="checkbox" name="checkedItem[]" value="<?= htmlspecialchars($id) ?>">
                            <b><?= htmlspecialchars($id) ?></b>
                        </label>
                        <br>
                        <?= nl2br(htmlspecialchars($item)) ?>
                    </li>
                <?php endforeach ?>
            </ul>
            <button type="submit">Удалить выбранные</button>
        </form>
    </body>
</html>