<?php
include 'application.php';

$app = new Application();

$filter_status = $_GET['filter_status'] ?? '';
$specific_date = $_GET['specific_date'] ?? '';
$quick_date = $_GET['quick_date'] ?? '';

// Удаление задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task'])) {
    $taskId = (int)$_POST['task_id'];
    $result = $app->deleteTask($taskId);
    if ($result) {
        header('Location: task.php?deleted=1');
    } else {
        header('Location: task.php?error=1');
    }
    exit;
}

// Обновление и сохранение задачи в карточке
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_updated_task'])) {
    $taskId = (int)$_POST['task_id'];
    
    $updateData = [
        'theme' => $_POST['theme'],
        'type' => (int)$_POST['type'],
        'location' => $_POST['location'],
        'duration' => (int)$_POST['duration'],
        'comment' => $_POST['comment'] ?? '',
    ];
    
    $result = $app->updateTask($taskId, $updateData);
    if ($result) {
        header('Location: task.php?view_task=' . $taskId . '&updated=1');
    } else {
        header('Location: task.php?view_task=' . $taskId . '&error=1');
    }
    exit;
}

// работоспособность кнопок
if ($quick_date == 'allTask') {
    $stmt = $app->loadAllTasks();
} elseif ($quick_date == 'today') {
    $stmt = $app->findTodayTask();
} elseif ($quick_date == 'tomorrow') {
    $stmt = $app->findTomorrowTask();
} elseif ($quick_date == 'this_week') {
    $stmt = $app->findWeek();
} elseif ($quick_date == 'next_week') {
    $stmt = $app->findNextWeek();
} 
elseif ($quick_date == 'pull') {
    if ($filter_status == 'overdue') {
        $stmt = $app->findOverdueTask();
    } elseif ($filter_status == 'completed') {
        $stmt = $app->findCompletedTask();
    } elseif ($filter_status == 'by_date' && !empty($specific_date)) {
        $stmt = $app->findDateTask($specific_date);
    } elseif ($filter_status == 'current') {
        $stmt = $app->findCurrentTasks();
    } else {
        $stmt = $app->loadAllTasks();
    }
} else {
    $stmt = $app->loadAllTasks();
}

// Нормальное отображение таблицы
$demoTasks = $stmt->fetchAll(PDO::FETCH_ASSOC); // возвращает строки в виде ассоциативного массива, где ключи - это названия колонок таблицы

if (!$demoTasks) {
    $demoTasks = [];
}

// Завершение (отметка) выбранных задач как выполненных
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_selected'])) {
    if (!empty($_POST['completed_ids'])) {
        $result = $app->completeTasks($_POST['completed_ids']);
        if ($result) {
            $params = [];
            if (!empty($quick_date)) {
                $params['quick_date'] = $quick_date;
            }
            if (!empty($filter_status)) {
                $params['filter_status'] = $filter_status;
            }
            if (!empty($specific_date)) {
                $params['specific_date'] = $specific_date;
            }
            $params['completed'] = 1;
            header('Location: task.php?' . http_build_query($params)); // преобразует массив (или объект) в URL-кодированную строку запроса
        } else {
            header('Location: task.php?error=1');
        }
        exit;
    } else {
        $params = [];
        if (!empty($quick_date)) {
            $params['quick_date'] = $quick_date;
        }
        if (!empty($filter_status)) {
            $params['filter_status'] = $filter_status;
        }
        if (!empty($specific_date)) {
            $params['specific_date'] = $specific_date;
        }
        $params['no_selection'] = 1;
        header('Location: task.php?' . http_build_query($params));
        exit;
    }
}

// открытие карточки задачи
$selectedTask = null;
$editMode = isset($_GET['edit_task']);

if (isset($_GET['view_task']) && !isset($_GET['close'])) {
    $taskId = (int)$_GET['view_task'];
    foreach ($demoTasks as $task) {
        if ($task['id'] === $taskId) {
            $selectedTask = $task;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой календарь - Список задач</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 40px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .filters-section {
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 20px;
        }

        .filter-group {
            flex: 1;
            min-width: 180px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            color: #555;
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: white;
            font-family: inherit;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #999;
        }

        .date-field {
            max-width: 200px;
        }

        .date-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .date-btn {
            padding: 8px 16px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            color: #333;
            text-decoration: none;
            display: inline-block;
        }

        .date-btn:hover {
            background: #e0e0e0;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .warning-message {
            background: #fff3cd;
            color: #856404;
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .tasks-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        th {
            text-align: left;
            padding: 15px 15px;
            background: #fafafa;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #efefef;
            font-size: 14px;
            color: #333;
        }

        tr:hover {
            background: #fafafa;
        }

        .task-link {
            display: block;
            text-decoration: none;
            color: #333;
        }

        .task-link:hover {
            color: #0066cc;
            text-decoration: underline;
        }

        .completed-task {
            background: #f9f9f9;
            opacity: 0.7;
        }

        .completed-task td {
            text-decoration: line-through;
            color: #999;
        }

        .overdue-task {
            background: #fff0f0;
        }

        .overdue-task td {
            color: #dc3545;
            font-weight: 500;
        }

        .checkbox-col {
            width: 40px;
            text-align: center;
        }

        .checkbox-col input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .action-bar {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .complete-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
        }

        .complete-btn:hover {
            background: #218838;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 550px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            font-size: 18px;
            font-weight: 500;
            color: #333;
        }

        .modal-body {
            padding: 25px;
        }

        .task-detail {
            margin-bottom: 20px;
        }

        .detail-row {
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 15px;
            color: #333;
            word-break: break-word;
        }

        .detail-value.comment {
            background: #f9f9f9;
            padding: 12px;
            border-radius: 6px;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            flex-wrap: wrap;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .modal-btn.close {
            background: #f0f0f0;
            color: #333;
        }

        .modal-btn.update {
            background: #333;
            color: white;
        }

        .modal-btn.save {
            background: #28a745;
            color: white;
        }

        .modal-btn.reset {
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
        }

        .modal-btn.delete {
            background: #dc3545;
            color: white;
        }

        .modal-btn.delete:hover {
            background: #c82333;
        }

        .edit-form .form-group {
            margin-bottom: 16px;
        }

        .edit-form label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            color: #555;
            font-weight: 500;
        }

        .edit-form input,
        .edit-form select,
        .edit-form textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }

        .edit-form textarea {
            resize: vertical;
            min-height: 80px;
        }

        .edit-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .empty-row td {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        button[type="submit"] {
            cursor: pointer;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            th, td {
                padding: 10px 8px;
            }
            
            .action-bar {
                justify-content: stretch;
            }
            
            .complete-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>


<div class="container">
    <!-- Все сообщения об ошибках, предупреждениях или успехах -->
    <?php if (isset($_GET['completed'])): ?>
        <div class="success-message">
            Выбранные задачи успешно отмечены как выполненные
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="success-message">
            Задача успешно удалена
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            Ошибка при выполнении операции
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['no_selection'])): ?>
        <div class="warning-message">
            Не выбрано ни одной задачи. Отметьте задачи, которые нужно завершить.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="success-message">
            Задача успешно обновлена
        </div>
    <?php endif; ?>

    <div class="filters-section">
        <form method="GET" action=""> <!-- Форма фильтрации, а не отправки данных. Поэтому GET -->
            <div class="filter-row">
                <div class="filter-group">
                    <label>Показать задачи:</label>
                    <select name="filter_status">
                        <option value="current" <?= (($_GET['filter_status'] ?? 'current') == 'current') ? 'selected' : '' ?>>Текущие задачи</option>
                        <option value="overdue" <?= (($_GET['filter_status'] ?? '') == 'overdue') ? 'selected' : '' ?>>Просроченные задачи</option>
                        <option value="completed" <?= (($_GET['filter_status'] ?? '') == 'completed') ? 'selected' : '' ?>>Выполненные задачи</option>
                        <option value="by_date" <?= (($_GET['filter_status'] ?? '') == 'by_date') ? 'selected' : '' ?>>Задачи на конкретную дату</option>
                    </select>
                </div>

                <div class="filter-group date-field">
                    <label>Дата:</label>
                    <input type="text" name="specific_date" placeholder="ДД.ММ.ГГГГ" value="<?= htmlspecialchars($_GET['specific_date'] ?? '') ?>">
                </div>
            </div>

            <div class="date-buttons">
                <button type="submit" name="quick_date" value="allTask" class="date-btn">Все задачи</button>
                <button type="submit" name="quick_date" value="today" class="date-btn">Сегодня</button>
                <button type="submit" name="quick_date" value="tomorrow" class="date-btn">Завтра</button>
                <button type="submit" name="quick_date" value="this_week" class="date-btn">На эту неделю</button>
                <button type="submit" name="quick_date" value="next_week" class="date-btn">На следующую неделю</button>
                <button type="submit" name="quick_date" value="pull" class="date-btn">Применить</button>
            </div>
        </form>
    </div>
    <!-- Работа с таблицей -->
    <div class="tasks-table">
        <form method="POST" action="task.php">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-col"></th>
                        <th>Тип</th>
                        <th>Задача</th>
                        <th>Место</th>
                        <th>Время</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($demoTasks)): ?>
                        <tr class="empty-row">
                            <td colspan="5">Нет задач для отображения</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $types = Application::getTypes();
                        foreach ($demoTasks as $task): 
                        ?>
                            <tr class="<?= $task['completed'] ? 'completed-task' : ($task['overdue'] == 1 ? 'overdue-task' : '') ?>">
                                <td class="checkbox-col">
                                    <input type="checkbox" name="completed_ids[]" value="<?= $task['id'] ?>" <?= $task['completed'] ? 'checked disabled' : '' ?>>
                                </td>
                                <td><?= htmlspecialchars($types[$task['type']] ?? 'Неизвестно') ?></td>
                                <td>
                                    <a href="?view_task=<?= $task['id'] ?>" class="task-link">
                                        <?= htmlspecialchars($task['theme']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($task['location']) ?></td>
                                <td><?= htmlspecialchars($task['time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="action-bar">
                <button type="submit" name="complete_selected" class="complete-btn">Завершить выбранное</button>
            </div>
        </form>
    </div>
</div>

<?php if ($selectedTask !== null): ?>
<!-- Если задача выбрана (есть ID в URL и задача найдена в массиве), показываем модальное окно -->
<div class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <!-- Заголовок окна: "Редактирование задачи" или "Детали задачи" в зависимости от режима -->
            <?= $editMode ? 'Редактирование задачи' : 'Детали задачи' ?>
        </div>
        <div class="modal-body">
            
            <?php if (!$editMode): ?>
            <!-- ========== РЕЖИМ ПРОСМОТРА ========== -->
            <!-- Показываем детали задачи, если не в режиме редактирования -->
                <div class="task-detail">
                    <!-- Тема задачи -->
                    <div class="detail-row">
                        <div class="detail-label">Тема</div>
                        <div class="detail-value"><?= htmlspecialchars($selectedTask['theme']) ?></div>
                    </div>
                    
                    <!-- Тип задачи (преобразуем числовой ID в название через массив getTypes()) -->
                    <div class="detail-row">
                        <div class="detail-label">Тип</div>
                        <?php $types = Application::getTypes(); ?>
                        <div class="detail-value"><?= htmlspecialchars($types[$selectedTask['type']] ?? 'Неизвестно') ?></div>
                    </div>
                    
                    <!-- Место проведения -->
                    <div class="detail-row">
                        <div class="detail-label">Место</div>
                        <div class="detail-value"><?= htmlspecialchars($selectedTask['location']) ?></div>
                    </div>
                    
                    <!-- Дата (если не указана, выводим "Не указана") -->
                    <div class="detail-row">
                        <div class="detail-label">Дата</div>
                        <div class="detail-value"><?= htmlspecialchars($selectedTask['date'] ?? 'Не указана') ?></div>
                    </div>
                    
                    <!-- Время -->
                    <div class="detail-row">
                        <div class="detail-label">Время</div>
                        <div class="detail-value"><?= htmlspecialchars($selectedTask['time']) ?></div>
                    </div>
                    
                    <!-- Длительность в минутах -->
                    <div class="detail-row">
                        <div class="detail-label">Длительность</div>
                        <div class="detail-value"><?= htmlspecialchars($selectedTask['duration'] ?? 'Не указана') ?> минут</div>
                    </div>
                    
                    <!-- Комментарий (nl2br преобразует переносы строк в <br>) -->
                    <div class="detail-row">
                        <div class="detail-label">Комментарий</div>
                        <div class="detail-value comment"><?= nl2br(htmlspecialchars($selectedTask['comment'] ?? '')) ?></div>
                    </div>
                </div>
                
                <!-- Кнопки в режиме просмотра -->
                <div class="modal-buttons">
                    <!-- Закрыть: просто закрывает окно (добавляет close=1 в URL, чтобы окно не открылось снова) -->
                    <a href="task.php?close=1" class="modal-btn close">Закрыть</a>
                    
                    <!-- Обновить: переходит в режим редактирования (добавляет edit_task=1 в URL) -->
                    <a href="task.php?view_task=<?= $selectedTask['id'] ?>&edit_task=1" class="modal-btn update">Обновить</a>
                    
                    <!-- Удалить: отправляет POST запрос на удаление задачи -->
                    <form method="POST" action="task.php" style="display: inline;">
                        <input type="hidden" name="task_id" value="<?= $selectedTask['id'] ?>">
                        <button type="submit" name="delete_task" class="modal-btn delete" onclick="return confirm('Вы уверены, что хотите удалить эту задачу?')">Удалить</button>
                    </form>
                </div>
                
            <?php else: ?>
            <!-- ========== РЕЖИМ РЕДАКТИРОВАНИЯ ========== -->
            <!-- Показываем форму для редактирования задачи -->
                <form method="POST" action="task.php">
                    <!-- Скрытое поле с ID задачи, чтобы знать, какую задачу обновлять -->
                    <input type="hidden" name="task_id" value="<?= $selectedTask['id'] ?>">
                    
                    <div class="edit-form">
                        <!-- Поле редактирования темы -->
                        <div class="form-group">
                            <label>Тема</label>
                            <input type="text" name="theme" value="<?= htmlspecialchars($selectedTask['theme']) ?>" required>
                        </div>
                        
                        <!-- Выпадающий список для выбора типа -->
                        <div class="form-group">
                            <label>Тип</label>
                            <select name="type">
                                <?php $types = Application::getTypes(); ?>
                                <?php foreach ($types as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= ($selectedTask['type'] == $key) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($value) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        
                        <!-- Поле редактирования места -->
                        <div class="form-group">
                            <label>Место</label>
                            <input type="text" name="location" value="<?= htmlspecialchars($selectedTask['location']) ?>" required>
                        </div>
                        
                        <!-- Поле редактирования длительности (числовое, по умолчанию 30) -->
                        <div class="form-group">
                            <label>Длительность (минут)</label>
                            <input type="number" name="duration" value="<?= htmlspecialchars($selectedTask['duration'] ?? 30) ?>">
                        </div>
                        
                        <!-- Поле редактирования комментария (многострочное) -->
                        <div class="form-group">
                            <label>Комментарий</label>
                            <textarea name="comment"><?= htmlspecialchars($selectedTask['comment'] ?? '') ?></textarea>
                        </div>
                        
                        <!-- Кнопки в режиме редактирования -->
                        <div class="edit-actions">
                            <!-- Сохранить: отправляет POST запрос на обновление задачи -->
                            <button type="submit" name="save_updated_task" class="modal-btn save">Сохранить</button>
                            
                            <!-- Сбросить: возвращает в режим просмотра без сохранения -->
                            <a href="task.php?view_task=<?= $selectedTask['id'] ?>" class="modal-btn reset">Сбросить</a>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            
        </div>
    </div>
</div>
<?php endif; ?>

</body>
</html>