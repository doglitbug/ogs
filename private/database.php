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
    /** Get first 10 user used for debugging at this time
     * @return array
     */
    public function get_all_users(): array
    {
        $query = <<<SQL
        SELECT user_id,
               username,
               name,
               email,
               location.description as location,
               IFNULL(admin.description, 'User') as access,
               user.created_at,
               user.updated_at
        FROM user
        LEFT JOIN location using (location_id)
        LEFT JOIN user_admin using (user_id)
        LEFT JOIN admin using (admin_id)
        LIMIT 10
        SQL;

        return $this->get_query($query);
    }

    public function get_user(string $user_id): array
    {
        $user_id = $this->escape($user_id);

        $query = <<<SQL
        SELECT user_id,
               username,
               name,
               email,
               location.description as location,
               IFNULL(admin.description, 'User') as access,
               user.created_at,
               user.updated_at
        FROM user
        LEFT JOIN location using (location_id)
        LEFT JOIN user_admin using (user_id)
        LEFT JOIN admin using (admin_id)
        WHERE user_id = '$user_id'
        LIMIT 1
        SQL;

        return $this->get_query($query);
    }

    /** Get user by email address
     * @param string $email
     * @return array
     */
    public function get_user_by_email(string $email): array
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

    /** Check to see if there is a user in the database with this email
     * @param string $email
     * @return bool
     */
    public function check_email_exists(string $email): bool
    {
        return $this->get_user_by_email($email) !== [];
    }

    /**
     * @param string $user_id
     * @return string
     */
    public function get_access_level(string $user_id): string
    {
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

    #region garage
    /** Get all garage
     * @return array
     */
    public function get_all_garages(): array
    {
        $query = <<<SQL
            SELECT  garage_id,
                    name,
                    garage.description,
                    location.description as location,
                    visible,
                    garage.updated_at,
                    garage.created_at
            FROM garage
            LEFT JOIN location using (location_id)
        SQL;

        return $this->get_query($query);
    }

    /** Get an individual garage
     * @param string $garage_id
     * @return array
     */
    public function get_garage(string $garage_id): array
    {
        $garage_id = $this->escape($garage_id);

        $query = <<<SQL
            SELECT  garage_id,
                    name,
                    garage.description,
                    location.description as location,
                    visible,
                    garage.updated_at,
                    garage.created_at
            FROM garage
            LEFT JOIN location using (location_id)
            WHERE garage_id='$garage_id'
            LIMIT 1
        SQL;

        $result = $this->get_query($query);

        if ($result) {
            return $result[0];
        } else {
            return [];
        }
    }

    /** Get all garage that this user has access to
     * @param string $user_id
     * @param array $options Owner or Worker
     * @return array
     * TODO Do this as an option in get_garages?
     */
    public function get_user_garages(string $user_id, array $options = []): array
    {
        $user_id = $this->escape($user_id);
        $access_query = isset($options['access']) ? "AND access.description='" . $this->escape($options['access']) . "'" : '';
        $query = <<<SQL
        SELECT  user_id,
            garage_id,
            garage.name,
            garage.description as description,
            access.description as access,
            location.description as location,
            visible
        FROM user_garage_access
            LEFT JOIN access using (access_id)
            LEFT JOIN garage using (garage_id)
            LEFT JOIN location using (location_id)
        WHERE user_id = '$user_id'
            {$access_query}
        ORDER BY access 
        SQL;

        return $this->get_query($query);
    }
    #endregion

    #region item
    public function get_all_items(array $options = []): array
    {
        //TODO Change to AND When WHERE quesy added in?
        $garage_query = isset($options['garage_id']) ? "WHERE garage_id='" . $this->escape($options['garage_id']) . "'" : '';
        $query = <<<SQL
        SELECT  item_id,
                garage_id,
                name,
                description,
                visible,
                updated_at,
                created_at
        FROM item
        {$garage_query}
        SQL;

        return $this->get_query($query);
    }
    #endregion
}