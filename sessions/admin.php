<?php

require_once 'application.php';

$app = new Application();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checked_lines'])) {
    $app->deleteLines($_POST['checked_lines']);
    header('Location: admin.php');
    exit;
}

$lines = $app->getAllLines();
?>

<html>
    <body>
        <h1>Admin</h1>
        
        <form method="POST">
            <?php foreach ($lines as $index => $line): ?>
                <div>
                    <input type="checkbox" name="checked_lines[]" value="<?= $index ?>" id="item_<?= $index ?>">
                    <label for="item_<?= $index ?>"><?= htmlspecialchars($line) ?></label>
                </div>
            <?php endforeach; ?>
            <br>
            <button type="submit">Удалить выбранное</button>
        </form>
    </body>
</html>