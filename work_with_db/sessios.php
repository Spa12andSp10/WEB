<?php

class Session
{
    public function __construct()
    {
        session_start();
    }

    public function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?: $default;
    }

    public function forget($key)
    {
        if(isset($_SESSION[$key]))
            {
                unset($_SESSION[$key]);
            }
    }
    public function pull($key)
    {
        $value = $this->get($key);
        if($value)
            {
                $this->forget($key);
            }
        return $value;
    }
}

?>