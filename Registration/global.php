<?php

    $source = isset($_GET['source']) ? $_GET['source'] : null;

    if($source === 'waiting')
    {
        sleep(3);
        header('Location: global.php?source=true');
        exit;
    }

    $topics = [
        1 => 'Бизнес',
        2 => 'Технологии',
        3 => 'Реклама и Маркетинг'
    ];
    
    $payments = [
        1 => 'WebMoney',
        2 => 'Яндекс.Деньги',
        3 => 'PayPal',
        4 => 'кредитная карта'
    ];

    $flag = false;
    $errors = [];
    $patter_phone = "/^8[0-9]{10}$/";
    $patter_email = "/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/";
       
    if ($_POST) {

        if (empty($_POST['name'])) {
            $errors['name'] = 'Поле с именем обязательно к заполнению! <br>';
        }
        if (empty($_POST['lastname'])) {
            $errors['lastname'] = 'Поле с фамилией обязательно к заполнению! <br>';
        }
        if (empty($_POST['email'])) {
            $errors['email'] = 'Поле с адресом электронной почты обязательно к заполнению! <br>';
        }
        elseif (!preg_match($patter_email, $_POST['email'])) {
            $errors['email'] = 'Ошибка в написании адреса электронной почты! <br>';
        }
        if (empty($_POST['phone'])) {
            $errors['phone'] = 'Поле с телефоном обязательно к заполнению! <br>';
        }
        elseif (!preg_match($patter_phone, $_POST['phone'])) {
            $errors['phone'] = 'Ошибка в написании номера телефона! <br>';
        }
        if (empty($_POST['topic'])) {
            $errors['topic'] = 'Поле с темой обязательно к заполнению! <br>';
        }
        if (empty($_POST['payment'])) {
            $errors['payment'] = 'Поле с методом оплаты обязательно к заполнению! <br>';
        }
        
        if (empty($_POST['agreed'])) {
            $_POST['agreed'] = "no";
        }
        if (!$errors) {
            $date = new DateTime();
            $data = print_r($_POST, true);
            $file = "./data/file_{$date->format('Y-m-d H:i:s')}.txt";
            file_put_contents($file, $data);
            
            header('Location: global.php?source=waiting');
            exit();
        }
    }

    if($source === 'true')
    {
        echo '<p style="color: green">Регистрация успешна!</p>';
        echo '<p><a href="global.php">Вернуться к форме</a></p>';
        $flag = true;
    }
?>
<?php if (!$flag): ?>
    <html>
        <head>
            <meta charset="utf-8">
            <title>Регистрация на конференцию</title>
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
<?php endif; ?>