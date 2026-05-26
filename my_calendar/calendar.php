<?php
include 'application.php';
$app = new Application();

$types = Application::getTypes();
$errors = [];

if ($_POST)
    {
        $app->fill($_POST);

        if($app->validate())
            {
                if($app->save()) {
                    header('Location: calendar.php');
                    exit();
                } else {
                    $errors['form'] = 'Ошибка при сохранении данных!';
                }
            }
        else
            {
                $errors = $app->getErrors();
            }

    }


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой календарь - Новая задача</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            margin: 0 0 20px 0;
            font-size: 20px;
            font-weight: 500;
            color: #333;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
            color: #555;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #999;
        }

        textarea {
            resize: vertical;
            min-height: 60px;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        .row .form-group {
            flex: 1;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 8px;
        }

        button:hover {
            background: #555;
        }
        
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 4px;
        }
        
        .form-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Новая запись</h2>
        
        <?php if(isset($errors['form'])): ?>
            <div class="form-error"><?= htmlspecialchars($errors['form']) ?></div>
        <?php endif; ?>
        
        <form action="" method="post">

            <div class="form-group">
                <label for="theme">Тема</label>
                <input type="text" id="theme" name="theme" value="<?= htmlspecialchars($_POST['theme'] ?? '') ?>">
                <?php if(isset($errors['theme'])): ?>
                    <div class="error-message"><?= $errors['theme'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="type">Тип</label>
                <select id="type" name="type">
                    <option value="">-- Выберите тип --</option> <!-- ДОБАВЛЕНО: пустой option -->
                    <?php foreach ($types as $key => $value): ?>
                        <option value="<?= $key ?>" <?= (($_POST['type'] ?? '') == $key) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?php if(isset($errors['type'])): ?>
                    <div class="error-message"><?= $errors['type'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="location">Место</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                <?php if(isset($errors['location'])): ?>
                    <div class="error-message"><?= $errors['location'] ?></div>
                <?php endif; ?>
            </div>

            <div class="row">
                <div class="form-group">
                    <label for="date">Дата</label>
                    <input type="text" id="date" name="date" placeholder="ДД.ММ.ГГГГ" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">
                    <?php if(isset($errors['date'])): ?>
                        <div class="error-message"><?= $errors['date'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="time">Время</label>
                    <input type="text" id="time" name="time" placeholder="ЧЧ:ММ" value="<?= htmlspecialchars($_POST['time'] ?? '') ?>">
                    <?php if(isset($errors['time'])): ?>
                        <div class="error-message"><?= $errors['time'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="duration">Длительность (минут)</label>
                <input type="number" id="duration" name="duration" min="1" step="1" value="<?= htmlspecialchars($_POST['duration'] ?? '') ?>">
                <?php if(isset($errors['duration'])): ?>
                    <div class="error-message"><?= $errors['duration'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="comment">Комментарий</label>
                <textarea id="comment" name="comment"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
            </div>

            <button type="submit">Сохранить</button>

        </form>
    </div>

</body>
</html>