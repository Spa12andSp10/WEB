<?php

include 'sessios.php';
include 'database.php';

class Application
{

    protected $errors = [];
    protected $data = [];
    protected $session;

    protected $fillable = [
        'theme',
        'type',
        'location',
        'date',
        'time', 
        'duration',
        'comment'
    ];

    public function __construct()
    {
        $this->session = new Session();
    }

    public function fill(array $data)
    {
        foreach($data as $key => $value) {
            if(in_array($key, $this->fillable)) {
                $this->data[$key] = trim($value);
            }
        }
    }

    public function validate()
    {
        $this->errors = [];

        $patter_date = '/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[0-2])\.(19|20)\d{2}$/';
        $patter_time = '/^(0\d|1\d|2[0-3]):[0-5]\d$/';

        if (empty($this->data['theme'])) {
            $this->errors['theme'] = 'Поле с темой обязательно для заполнения! <br>';
        }
        if (empty($this->data['type'])) {
            $this->errors['type'] = 'Поле с типом обязательно для заполнения! <br>';
        }
        if (empty($this->data['location'])) {
            $this->errors['location'] = 'Поле с местом обязательно для заполнения! <br>';
        }
        if (empty($this->data['date'])) {
            $this->errors['date'] = 'Поле с датой обязательно для заполнения <br>';
        }
        elseif (!preg_match($patter_date, $this->data['date'])) {
            $this->errors['date'] = 'Ошибка в написании даты! Формат: ДД.ММ.ГГГГ <br>';
        }
        if (empty($this->data['time'])) {
            $this->errors['time'] = 'Поле с временем обязательно для заполнения <br>';
        }
        elseif (!preg_match($patter_time, $this->data['time'])) {
            $this->errors['time'] = 'Ошибка в написании времени! Формат: ЧЧ:ММ <br>';
        }
        if (empty($this->data['duration'])) {
            $this->errors['duration'] = 'Поле с длительностью обязательно к заполнению! <br>';
        } elseif (!is_numeric($this->data['duration']) || $this->data['duration'] <= 0) {
            $this->errors['duration'] = 'Длительность должна быть положительным числом! <br>';
        }
        
        if (empty($this->data['comment'])) {
            $this->data['comment'] = '';
        }
        
        return empty($this->errors);
    }

    public function save()
    {
        $dateParts = explode('.', $this->data['date']);
        $mysqlDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
        
        $mysqlTime = $this->data['time'] . ':00';

        $sqlQuery = 'INSERT INTO `tasks` (theme, type, location, date, time, duration, comment, completed, overdue, created_at) 
                        VALUES (:theme, :type, :location, :date, :time, :duration, :comment, NULL, NULL, NOW())';
        
        $data = [
            'theme' => $this->data['theme'],
            'type' => (int)$this->data['type'],
            'location' => $this->data['location'],
            'date' => $mysqlDate,
            'time' => $mysqlTime,
            'duration' => (int)$this->data['duration'],
            'comment' => $this->data['comment'] ?? '',
        ];

        return Database::exec($sqlQuery, $data);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getData()
    {
        return $this->data;
    }

    public static function getTypes()
    {
        return [
            1 => 'Встреча',
            2 => 'Звонок',
            3 => 'Совещание',
            4 => 'Дело'
        ];
    }

    public function loadAllTasks()
    {
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks';
        return Database::query($sqlQuery);
    }

    public function completeTasks($taskIds)
    {
        if (empty($taskIds)) {
            return false;
        }
    
        $success = true;
        foreach ($taskIds as $id) {
            $sqlQuery = "UPDATE tasks SET completed = 1, overdue = NULL WHERE id = ? AND (completed = 0 OR completed IS NULL)";
            $result = Database::exec($sqlQuery, [$id]);
            if ($result === false) {
                $success = false;
            }
        }
    
        return $success;
    }

    public function findTodayTask()
    {
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks WHERE date = CURDATE() AND (completed = 0 OR completed IS NULL)';
        return Database::query($sqlQuery);
    }

    public function findTomorrowTask()
    {
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks WHERE date = CURDATE() + INTERVAL 1 DAY AND (completed = 0 OR completed IS NULL)';
        return Database::query($sqlQuery);
    }

    public function findWeek()
    {
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks WHERE date BETWEEN CURDATE() AND CURDATE() + INTERVAL 7 DAY AND (completed = 0 OR completed IS NULL)';
        return Database::query($sqlQuery);
    }

    public function findNextWeek()
    {
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks WHERE date BETWEEN CURDATE() + INTERVAL 7 DAY AND CURDATE() + INTERVAL 14 DAY AND (completed = 0 OR completed IS NULL)';
        return Database::query($sqlQuery);
    }

    public function findOverdueTask()
    {
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks WHERE overdue = 1';
        return Database::query($sqlQuery);
    }

    public function findCompletedTask()
    {
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks WHERE completed = 1';
        return Database::query($sqlQuery);
    }

    public function findDateTask($date)
    {
        if (strpos($date, '.') !== false) {
            $dateParts = explode('.', $date);
            $mysqlDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
        } else {
            $mysqlDate = $date;
        }
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks WHERE date = ?';
        return Database::query($sqlQuery, [$mysqlDate]);
    }

    public function findCurrentTasks()
    {
        $sqlQuery = 'SELECT id, type, theme, location, date, time, comment, duration, completed, overdue FROM tasks WHERE (completed = 0 OR completed IS NULL) AND (overdue = 0 OR overdue IS NULL)';
        return Database::query($sqlQuery);
    }

    public function updateTask($id, $data)
    {
        $sqlQuery = 'UPDATE tasks SET 
                        theme = :theme, 
                        type = :type, 
                        location = :location, 
                        duration = :duration, 
                        comment = :comment 
                    WHERE id = :id';

        $params = [
            ':id' => (int)$id,
            ':theme' => $data['theme'],
            ':type' => (int)$data['type'],
            ':location' => $data['location'],
            ':duration' => (int)$data['duration'],
            ':comment' => $data['comment'] ?? '',
        ];

        return Database::exec($sqlQuery, $params);
    }

    public function deleteTask($id)
    {
        $sqlQuery = 'DELETE FROM tasks WHERE id = ?';
        return Database::exec($sqlQuery, [(int)$id]);
    }

}
?>