<?php

include 'sessios.php';
include 'database.php';

class Application
{

    protected $dataDir;
    protected $errors = [];
    protected $data = [];
    protected $session;

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'phone',
        'topic', 
        'payment',
        'agreed'
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

        $patter_name = '/^[a-zA-ZА-Яа-яёЁ\s\-]+$/u';
        $patter_lastname = '/^[a-zA-ZА-Яа-яёЁ\s\-]+$/u';
        $patter_phone = "/^8[0-9]{10}$/";
        $patter_email = "/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/";

        if (empty($this->data['name'])) {
            $this->errors['name'] = 'Поле с именем обязательно к заполнению! <br>';
        }
        elseif (!preg_match($patter_name, $this->data['name'])) {
            $this->errors['name'] = 'Проверьте наличие лишних символов в имени! <br>';
        }
        if (empty($this->data['lastname'])) {
            $this->errors['lastname'] = 'Поле с фамилией обязательно к заполнению! <br>';
        }
        elseif (!preg_match($patter_lastname, $this->data['lastname'])) {
            $this->errors['lastname'] = 'Проверьте наличие лишних символов в фамилии! <br>';
        }
        if (empty($this->data['email'])) {
            $this->errors['email'] = 'Поле с адресом электронной почты обязательно к заполнению! <br>';
        }
        elseif (!preg_match($patter_email, $this->data['email'])) {
            $this->errors['email'] = 'Ошибка в написании адреса электронной почты! <br>';
        }
        if (empty($this->data['phone'])) {
            $this->errors['phone'] = 'Поле с телефоном обязательно к заполнению! <br>';
        }
        elseif (!preg_match($patter_phone, $this->data['phone'])) {
            $this->errors['phone'] = 'Ошибка в написании номера телефона! <br>';
        }
        if (empty($this->data['topic'])) {
            $this->errors['topic'] = 'Поле с темой обязательно к заполнению! <br>';
        }
        if (empty($this->data['payment'])) {
            $this->errors['payment'] = 'Поле с методом оплаты обязательно к заполнению! <br>';
        }
        
        if (empty($this->data['agreed'])) {
            $this->data['agreed'] = "no";
        }

        return empty($this->errors);
    }

    public function save()
    {
        $sqlQuery = 'INSERT INTO `participants` (`name`, `lastname`, `email`, `phone`, `subject`, `payment`, `mailing`, `created_at`) 
                     VALUES (:name, :lastname, :email, :phone, :subject, :payment, :mailing, NOW())';
        
        $data = [
            'name' => $this->data['name'],
            'lastname' => $this->data['lastname'],
            'email' => $this->data['email'],
            'phone' => $this->data['phone'],
            'subject' => (int)$this->data['topic'],
            'payment' => (int)$this->data['payment'],
            'mailing' => $this->data['agreed'] === 'yes' ? 1 : 0,
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

    public static function getTopics()
    {
        return [
            1 => 'Бизнес',
            2 => 'Технологии',
            3 => 'Реклама и Маркетинг'
        ];
    }
    
    public static function getPayments()
    {
        return [
            1 => 'WebMoney',
            2 => 'Яндекс.Деньги',
            3 => 'PayPal',
            4 => 'кредитная карта'
        ];
    }

    public function getAllLines()
    {
        $sql = Database::query('SELECT * FROM participants WHERE deleted_at IS NULL ORDER BY id');
        $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
        $lines = [];
    
        $topics = self::getTopics();
        $payments = self::getPayments();
    
        foreach ($rows as $row) {
            $lines[$row['id']] = implode(' | ', [
                $row['name'],
                $row['lastname'],
                $row['email'],
                $row['phone'],
                $topics[$row['subject']] ?? 'Неизвестно',
                $payments[$row['payment']] ?? 'Неизвестно',
                $row['mailing'] ? 'Да' : 'Нет',
                $row['created_at']
            ]);
        }
    
        return $lines;
    }

    public function deleteLines($indices)
    {
        if (empty($indices)) {
            return false;
        }
    
        $placeholders = trim(str_repeat('?,', count($indices)), ',');
        $sql = "UPDATE participants SET deleted_at = NOW() WHERE id IN ($placeholders) AND deleted_at IS NULL";
    
        return Database::exec($sql, $indices);
    }
    public function setSuccess($message)
    {
        $this->session->put('flash_success', $message);
    }

    public function getSuccess()
    {
        return $this->session->pull('flash_success');
    }
}
?>