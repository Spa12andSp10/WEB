<?php

include 'sessios.php';

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
        $config = include 'config.php';
        if (!empty($config['data'])) {
            $this->dataDir = $config['data'];
        }
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
        if(!empty($this->errors)) {
            return false;
        }
    
        $date = new DateTime();
        $result = implode('|', $this->data);
        $result = $result . "|" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "|" . $date->format('Y-m-d H:i:s');
        $file = $this->dataDir . "data.txt";
    
    
        if (file_exists($file) && filesize($file) > 0) {
            $result = "\n" . $result;
            return file_put_contents($file, $result, FILE_APPEND | LOCK_EX);
        } else {
            return file_put_contents($file, $result, LOCK_EX);
        }
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
        $file = $this->dataDir . "data.txt";
        $lines = [];
        
        if (file_exists($file)) {
            $allLines = file($file, FILE_IGNORE_NEW_LINES);
            foreach ($allLines as $index => $line) {
                $sub_str = substr($line, -7);
                if ($sub_str != "deleted") {
                    $lines[$index] = $line;
                }
            }
        }
        
        return $lines;
    }

    public function deleteLines($indices)
    {
        $file = $this->dataDir . "data.txt";
        
        if (file_exists($file)) {
            $lines = file($file, FILE_IGNORE_NEW_LINES);
            $new_lines = [];
            
            foreach ($lines as $index => $line) {
                if (in_array($index, $indices)) {
                    $new_lines[] = $line . "|deleted";
                } else {
                    $new_lines[] = $line;
                }
            }
            
            return file_put_contents($file, implode("\n", $new_lines));
        }
        
        return false;
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