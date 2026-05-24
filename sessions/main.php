<?php
include 'application.php';

$app = new Application();

$success = $app->getSuccess();

if ($success) {
    echo '<p style="color: green">' . htmlspecialchars($success) . '</p>';
    echo '<p><a href="main.php">Вернуться к форме</a></p>';
    exit;
}

$topics = $app::getTopics();
$payments = $app::getPayments();
$errors = [];

if ($_POST)
    {
        $app->fill($_POST);

        if($app->validate())
            {
                $app->save();
                $app->setSuccess('Регистрация успешна');
                header('Location: main.php');
                exit();
            }
        else
            {
                $errors = $app->getErrors();
            }

    }

?>
<html>
    <head>
        <meta charset="utf-8">
        <title>Регистрация на конференцию</title>
        <style>
            .error { color: red; font-size: 0.9em; }
            .error-summary { color: red; font-weight: bold; margin-bottom: 15px; }
            div { margin-bottom: 10px; }
        </style>
    </head>
    <body>

        <?php  if (!empty($errors)): ?>
            <p style="color: red;"><b>Проверьте правильность заполнения формы!</b></p>
        <?php endif ?>
        <form action="" method="POST">
            <div>
                <label>Имя:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                <?php echo $errors['name'] ?>
            </div>
            <div>
                <label>Фамилия:</label>
                <input type="text" name="lastname" value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">
                <?php echo $errors['lastname'] ?>
            </div>
            <div>
                <label>Почта:</label>
                <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <?php echo $errors['email'] ?? ''; ?> 
            </div>
            <div>
                <label>Телефон:</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                <?php echo $errors['phone'] ?? ''; ?> 
            </div>
            <div>
                <label>Выберите тематику конференции:</label>
                <select name="topic">
                    <option value="">-- Выберите тему --</option>
                    <?php foreach ($topics as $key => $value): ?>
                        <option value="<?= $key ?>" <?= (($_POST['topic'] ?? '') == $key) ? 'selected' : '' ?>>
                            <?= $value ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?php echo $errors['topic'] ?? ''; ?> 
            </div>
                
            <div>
                <label>Выберите метод оплаты:</label>
                <select name="payment">
                    <option value="">-- Выберите способ оплаты --</option>
                    <?php foreach ($payments as $key => $value): ?>
                        <option value="<?= $key ?>" <?= (($_POST['payment'] ?? '') == $key) ? 'selected' : '' ?>>
                            <?= $value ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?php echo $errors['payment'] ?? ''; ?> 
            </div>
            <div>
                <label>Хотите ли вы получать рассылку о конференции:</label>
                <input type="checkbox" name="agreed" value="yes">
            </div>
            <div>
                <button type="submit">Отправить</button>
            </div>
        </form>
    </body>
</html>