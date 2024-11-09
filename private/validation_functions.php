<?php
#region generic
/**
 * Check to see if the provided value is blank/empty
 * @param string $value
 * @return bool
 */
function is_blank(string $value): bool
{
    return (!isset($value) || trim($value) === '');
}

/**
 * Check to see if the provided value has content
 * @param string $value
 * @return bool
 */
function has_data(string $value): bool
{
    return !is_blank($value);
}

function has_length_greater_than(string $value, int $min): bool
{
    $length = strlen($value);
    return $length > $min;
}

function has_length_less_than(string $value, int $max): bool
{
    $length = strlen($value);
    return $length < $max;
}

function has_length_exactly(string $value, int $exact): bool
{
    $length = strlen($value);
    return $length == $value;
}

/**
 * Validates string length by combining greater_than, less_than and exactly
 * @param string $value
 * @param array $options min, max, exact
 * @return bool
 */
function has_length(string $value, array $options): bool
{
    if (isset($options['min']) && !has_length_greater_than($value, $options['min'] - 1)) {
        return false;
    }

    if (isset($options['max']) && !has_length_less_than($value, $options['max'] + 1)) {
        return false;
    }

    if (isset($options['exact']) && !has_length_exactly($value, $options['exact'])) {
        return false;
    }

    return true;
}

/** Validate inclusion in a set
 * @param mixed $value
 * @param array $set
 * @return bool
 * @example 5, [1,3,5,7,9]
 */
function has_inclusion_of(mixed $value, array $set): bool
{
    return in_array($value, $set);
}

/** Validate exclusion from a set
 * @param mixed $value
 * @param array $set
 * @return bool
 * @example 2, [1,3,5,7,9]
 */
function has_exclusion_of(mixed $value, array $set): bool
{
    return !in_array($value, $set);
}

/**
 * Check for inclusion of characters
 * @param string $value
 * @param string $required_string
 * @return bool
 * @example 'nobody@nowhere.com', 'com'
 */
function has_string(string $value, string $required_string): bool
{
    return str_contains($value, $required_string);
}

/**
 * Check email is 'valid'
 * Format: [chars]@[chars].[2+ letters]
 * @param string $value
 * @return bool
 */
function has_valid_email(string $value): bool
{
    $email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\Z/i';
    return preg_match($email_regex, $value) === 1;
}

#endregion

#region validation
function validate_garage(array $garage): array
{
    global $db;
    $errors = [];

    #name
    if (is_blank($garage['name'])) {
        $errors['name'] = "Name cannot be blank";
    } else if (!has_length($garage['name'], ['min' => 2, 'max' => 255])) {
        $errors['name'] = "Name must be between 2 and 255 characters";
    }

    #description
    if (is_blank($garage['description'])) {
        $errors['description'] = "Description cannot be blank";
    } else if (!has_length($garage['description'], ['min' => 2, 'max' => 255])) {
        $errors['description'] = "Description must be between 2 and 255 characters";
    }

    #location
    $location_str = (string)$garage['location_id'];
    $locations = array_column($db->get_all_locations(), 'location_id');

    if (!has_inclusion_of($location_str, $locations)) {
        $errors['location'] = "Location must be one of the provided options";
    }

    #visible
    $visible_str = (string)$garage['visible'];
    if (!has_inclusion_of($visible_str, ["0", "1"])) {
        $errors['visible'] = "Visible must be true or false";
    }

    return $errors;
}

function validate_item(array $item): array
{
    global $db;
    $errors = [];

    #name
    if (is_blank($item['name'])) {
        $errors['name'] = "Name cannot be blank";
    } else if (!has_length($item['name'], ['min' => 2, 'max' => 255])) {
        $errors['name'] = "Name must be between 2 and 255 characters";
    }

    #description
    if (is_blank($item['description'])) {
        $errors['description'] = "Description cannot be blank";
    } else if (!has_length($item['description'], ['min' => 2, 'max' => 255])) {
        $errors['description'] = "Description must be between 2 and 255 characters";
    }

    #visible
    $visible_str = (string)$item['visible'];
    if (!has_inclusion_of($visible_str, ["0", "1"])) {
        $errors['visible'] = "Visible must be true or false";
    }

    return $errors;
}

#endregion
?>