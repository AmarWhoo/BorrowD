<?php
require_once __DIR__ . '/exceptions.php';

/**
 * Small reusable validation helpers for services.
 * Every method throws a ValidationException (HTTP 400) with a readable message
 * and does nothing when the value is correct.
 */
class Validator
{
    /** Every field in $fields must exist and must not be empty. */
    public static function required($data, $fields)
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new ValidationException("Field '$field' is required.");
            }
        }
    }

    /** Text length between $min and $max characters. */
    public static function length($value, $field, $min, $max)
    {
        $length = mb_strlen(trim((string) $value));
        if ($length < $min || $length > $max) {
            throw new ValidationException("Field '$field' must be between $min and $max characters long.");
        }
    }

    /** A whole number, optionally inside a range. */
    public static function integer($value, $field, $min = null, $max = null)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new ValidationException("Field '$field' must be a whole number.");
        }
        if ($min !== null && $value < $min) {
            throw new ValidationException("Field '$field' must be at least $min.");
        }
        if ($max !== null && $value > $max) {
            throw new ValidationException("Field '$field' must be at most $max.");
        }
    }

    /** The value must be one of the allowed values. */
    public static function one_of($value, $field, $allowed)
    {
        if (!in_array($value, $allowed, true)) {
            throw new ValidationException("Field '$field' must be one of: " . implode(', ', $allowed) . ".");
        }
    }

    public static function email($value, $field = 'email')
    {
        if (filter_var(trim((string) $value), FILTER_VALIDATE_EMAIL) === false) {
            throw new ValidationException("Field '$field' must be a valid e-mail address.");
        }
    }

    /** A real calendar date in the format YYYY-MM-DD. */
    public static function date($value, $field)
    {
        $date = DateTime::createFromFormat('Y-m-d', (string) $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new ValidationException("Field '$field' must be a valid date (YYYY-MM-DD).");
        }
    }

    /** At least 8 characters, with at least one letter and one digit. */
    public static function password($value, $field = 'password')
    {
        $value = (string) $value;
        if (mb_strlen($value) < 8 || !preg_match('/[A-Za-z]/', $value) || !preg_match('/[0-9]/', $value)) {
            throw new ValidationException("Field '$field' must be at least 8 characters long and contain a letter and a digit.");
        }
    }
}