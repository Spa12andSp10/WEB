<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checked_lines'])) {
    $file = "./data/data.txt";
    
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $new_lines = [];
        
        foreach ($lines as $index => $line) {

            if (!in_array($index, $_POST['checked_lines'])) 
                {
                    $new_lines[] = $line;
                }
            else
                {
                    $new_lines[] = $line . "|deleted";
                }
        }
        
        file_put_contents($file, implode("\n", $new_lines));
    }
    
    header('Location: admin.php');
    exit;
}

$file = "./data/data.txt";
?>

<html>
    <body>
        <h1>Admin</h1>
        
        <form method="POST">
            <?php
            if (file_exists($file)) {
                $lines = file($file);
                
                foreach ($lines as $index => $line) {
                    $line = htmlspecialchars(trim($line));
                    $sub_str = substr($line, -7);
                    if($sub_str == "deleted")
                        {
                            continue;
                        }
                    if (!empty($line)) {
                        echo '<div>';
                        echo '<input type="checkbox" name="checked_lines[]" value="' . $index . '" id="item_' . $index . '">';
                        echo '<label for="item_' . $index . '">' . $line . '</label>';
                        echo '</div>';
                    }
                }
            }
            ?>
            <br>
            <button type="submit">Удалить выбранное</button>
        </form>
    </body>
</html>