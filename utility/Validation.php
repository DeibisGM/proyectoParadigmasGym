<?php

class Validation
{
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function setError($field, $message)
    {
        self::start();
        $_SESSION['errors'][$field] = $message;
    }

    public static function getError($field)
    {
        self::start();
        if (isset($_SESSION['errors'][$field])) {
            $error = $_SESSION['errors'][$field];
            unset($_SESSION['errors'][$field]);
            return $error;
        }
        return null;
    }

    public static function hasErrors()
    {
        self::start();
        return !empty($_SESSION['errors']);
    }

    public static function setOldInput($input)
    {
        self::start();
        $_SESSION['old_input'] = $input;
    }

    public static function getOldInput($field, $default = '')
    {
        self::start();
        if (isset($_SESSION['old_input'][$field])) {
            return $_SESSION['old_input'][$field];
        }
        return $default;
    }

    public static function clear()
    {
        self::start();
        unset($_SESSION['errors']);
        unset($_SESSION['old_input']);
    }
}
?>
