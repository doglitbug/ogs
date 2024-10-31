<?php

/**
 * A wrapper object around all database specific functions in case we need to change database later on
 */
class Database
{
    #region misc
    private mysqli $connection;

    public function connect(): void
    {
        try {
            $this->connection = mysqli_connect($_ENV["DATABASE_HOST"], $_ENV["DATABASE_USER"], $_ENV["DATABASE_PASS"], $_ENV["DATABASE_NAME"]);
        } catch (Exception) {
            error_database("Could not connect to database");
        }
    }

    public function disconnect(): void
    {
        if (isset($this->connnection)) {
            $this->connection->close();
        }
    }

    /**
     * Sanitize the provided string to prevent SQL injection
     * @param string $string input
     * @return string Cleaned string
     */
    public function escape(string $string): string
    {
        return $this->connection->real_escape_string($string);
    }

    /**
     * Perform a get query and return an assoc array of results
     * @param string $query Escaped query string
     * @return array Results
     */
    private function get_query(string $query): array
    {
        try {
            $result = $this->connection->query($query);
        } catch (Exception) {
            error_database("Error getting data");
        }
        if (!$result) {
            error_database("Error getting data");
        }
        $array = $result->fetch_all(MYSQLI_ASSOC);
        mysqli_free_result($result);
        return $array;
    }

    /**
     * Perform an insert query and return new id
     * @param string $query Escaped query string
     * @return int new ID
     */
    private function insert_query(string $query): int
    {
        try {
            $result = $this->connection->query($query);
        } catch (Exception) {
            error_database("Error inserting data");
        }
        if (!$result) {
            error_database("Error inserting data");
        }
        return $this->connection->insert_id;
    }

    /**
     * Perform an update query
     * @param string $query Escaped query string
     */
    private function update_query(string $query): void
    {
        try {
            $result = $this->connection->query($query);
        } catch (Exception) {
            error_database("Error updating data");
        }
        if (!$result) {
            error_database("Error updating data");
        }
    }

    /**
     * Perform a delete query
     * @param string $query Escaped query string
     */
    private function delete_query(string $query): void
    {
        try {
            $result = $this->connection->query($query);
        } catch (Exception) {
            error_database("Error deleting data");
        }
        if (!$result) {
            error_database("Error deleting data");
        }
    }

#endregion

#region user
    public function get_user(string $email): array
    {
        $email = $this->escape($email);

        $query = <<<SQL
        SELECT user_id,
               username,
               name,
               email,
               location.description as location,
               user.created_at,
               user.updated_at
        FROM user
        LEFT JOIN location using (location_id)
        WHERE email='$email'
        LIMIT 1
    SQL;

        $result = $this->get_query($query);
        if ($result) {
            return $result[0];
        } else {
            return [];
        }
    }

    public function check_email_exists(string $email): bool
    {
        return $this->get_user($email) !== [];
    }

    public function get_access_level($user_id):string{
        $user_id = $this->escape($user_id);

        $query = <<<SQL
        SELECT IFNULL(admin.description, 'User') as access
        FROM user
        LEFT JOIN user_admin using (user_id)
        LEFT JOIN admin using (admin_id)
        WHERE user_id='$user_id' 
        LIMIT 1
    SQL;

        $result = $this->get_query($query);
        if ($result) {
            return $result[0]['access'];
        } else {
            return "";
        }
    }
#endregion
}