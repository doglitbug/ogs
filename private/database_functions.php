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
        $string = filter_var($string, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
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
    /** Get first 10 users, used for debugging at this time
     * @return array
     */
    public function get_all_users(): array
    {
        $query = <<<SQL
        SELECT  user_id,
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
        SELECT  user_id,
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
        SELECT  user_id,
                username,
                name,
                email,
                location_id,
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

    /** Check to see if t5his user is Super Admin, Admin or ordinary User
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
    /** Get all garages
     * @param array $options
     * @return array
     */
    public function get_all_garages(array $options = []): array
    {
        $visible_query = isset($options['visible']) ? "WHERE visible = '" . $this->escape($options['visible']) . "'" : "";

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
        {$visible_query}
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
                location.location_id,
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

    /** Get all garages that this user has access to
     * @param string $user_id
     * @param array $options Owner or Worker
     * @return array
     * TODO Do this as an option/filter in get_all_garages?
     */
    public function get_garages_by_user(string $user_id, array $options = []): array
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

    /** Create a new garage
     * @param array $garage
     * @return int New ID
     * @note This does not set ownership or access at all!
     */
    public function insert_garage(array $garage): int
    {
        $name = $this->escape($garage['name']);
        $description = $this->escape($garage['description']);
        $location_id = $this->escape($garage['location_id']);
        $visible = $this->escape($garage['visible']);

        $query = <<<SQL
            INSERT INTO garage
            (name, description, location_id, visible)
            VALUES ('$name',
                    '$description',
                    '$location_id',
                    '$visible'
                    )
        SQL;

        return $this->insert_query($query);
    }

    /** Update and existing garage
     * @param array $garage
     * @return void
     */
    public function update_garage(array $garage): void
    {
        $garage_id = $this->escape($garage['garage_id']);
        $name = $this->escape($garage['name']);
        $description = $this->escape($garage['description']);
        $location_id = $this->escape($garage['location_id']);
        $visible = $this->escape($garage['visible']);

        $query = <<<SQL
        UPDATE garage SET   name = '$name',
                            description = '$description',
                            location_id = '$location_id',
                            visible = '$visible'
        WHERE garage_id = '$garage_id'
        LIMIT 1
        SQL;

        $this->update_query($query);
    }

    /** Delete garage, this assumes that this action has only been called by user with authority etc
     * user_garage_access rows will be deleted automatically!
     * @param array $garage
     * @return void
     */
    public function delete_garage(array $garage): void
    {
        $garage_id = $this->escape($garage['garage_id']);

        $query = <<<SQL
        DELETE FROM garage
        WHERE garage_id = '$garage_id'
        LIMIT 1;
        SQL;

        $this->delete_query($query);
    }

#endregion

#region item
    /** Get items, usually from an individual garage
     * @param array $options garage_id
     * @return array
     * @todo rename this to get_garage_items($garage_id) if we never use it without the option
     */
    public function get_all_items(array $options = []): array
    {
        //TODO Change to AND when WHERE query added in?
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

    /** Get an individual item, usually for show/edit item
     * @param string $item_id
     * @param array $options public: garage hidden will override visibility
     * @return array
     */
    public function get_item(string $item_id, array $options = []): array
    {
        $item_id = $this->escape($item_id);

        $visible_query = isset($options['public']) ? "IF(item.visible AND garage.visible, true, false) as visible" : "item.visible";

        $query = <<<SQL
        SELECT  item.item_id,
                item.garage_id,
                item.name,
                item.description,
                $visible_query
        FROM item
        LEFT JOIN garage USING (garage_id)
        WHERE item_id = '$item_id'
        LIMIT 1
        SQL;

        $result = $this->get_query($query);
        if ($result) {
            return $result[0];
        } else {
            return [];
        }

        return $this->get_query($query);
    }

    /** Insert a new item
     * @param array $item
     * @return int
     */
    public function insert_item(array $item): int
    {
        $garage_id = $this->escape($item['garage_id']);
        $name = $this->escape($item['name']);
        $description = $this->escape($item['description']);
        $visible = $this->escape($item['visible']);

        $query = <<<SQL
            INSERT INTO item
            (garage_id, name, description, visible)
            VALUES  ('$garage_id',
                    '$name',
                    '$description',
                    '$visible'
                    )
        SQL;

        return $this->insert_query($query);
    }

    public function update_item(array $item): void
    {
        $item_id = $this->escape($item['item_id']);
        $garage_id = $this->escape($item['item_id']);
        $name = $this->escape($item['name']);
        $description = $this->escape($item['description']);
        $visible = $this->escape($item['visible']);

        $query = <<<SQL
        UPDATE item SET name = '$name',
                        description = '$description',
                        visible = '$visible'
        WHERE item_id = '$item_id'
        LIMIT 1
        SQL;

        $this->update_query($query);
    }

    /** Delete an item
     * @param array $item
     * @return void
     * @todo Remove all images as well?
     */
    public function delete_item(array $item): void
    {
        $item_id = $this->escape($item['item_id']);

        $query = <<<SQL
        DELETE FROM item
        WHERE item_id = '$item_id'
        LIMIT 1;
        SQL;

        $this->delete_query($query);
    }


#endregion

#region location
    /** Get all locations
     * @return array
     */
    public
    function get_all_locations(): array
    {
        $query = <<<SQL
        SELECT  location_id,
                description
        FROM location
        ORDER BY description
        SQL;

        return $this->get_query($query);
    }

#endregion

#region user_garage_access
    /** Set access for user to garage
     * @param string $user_id
     * @param string $garage_id
     * @param string $access
     * @return void
     * @todo Change access to int instead of string, check to see if access already exists?
     */
    public
    function set_user_garage_access(string $user_id, string $garage_id, string $access): void
    {
        $user_id = $this->escape($user_id);
        $garage_id = $this->escape($garage_id);
        $access = $this->escape($access);

        $query = <<<SQL
        INSERT INTO user_garage_access
            (user_id, garage_id, access_id)
            VALUES ('$user_id',
                    '$garage_id',
                    (SELECT access_id FROM access WHERE description = '$access'))
        SQL;

        $this->insert_query($query);
    }

    /** Find this users access level for this garage
     * @param string $user_id
     * @param string $garage_id
     * @return string Owner|Worker|User
     */
    public function get_user_access(string $user_id, string $garage_id): string
    {
        $user_id = $this->escape($user_id);
        $garage_id = $this->escape($garage_id);

        $query = <<<SQL
        SELECT IFNULL((
            SELECT description
            FROM user_garage_access
            LEFT JOIN access using (access_id)
            WHERE user_id='$user_id'
            AND garage_id='$garage_id'
            LIMIT 1),
        "User") access;
        SQL;

        return $this->get_query($query)[0]['access'];
    }
#endregion
#region images
    /** Get the images for an item
     * @param string $item_id
     * @param array $options
     * @return array
     */
    public function get_item_images(string $item_id, array $options = []): array
    {
        $item_id = $this->escape($item_id);

        $query = <<<SQL
        SELECT  item_id,
                image_id,
                main,
                width,
                height,
                CONCAT(path, '/', filename) as source
        FROM item_image
        JOIN image using (image_id) 
        WHERE item_id = '$item_id'
        ORDER BY main DESC
        SQL;
        //TODO Then order by date created/updated?

        return $this->get_query($query);
    }

    /** Remove image from database, assumes that the file has already been removed
     * This should also remove item/garage_item links
     * @param array $image
     * @return void
     */
    public function delete_image(array $image): void
    {
        $image_id = $this->escape($image['image_id']);

        $query = <<<SQL
        DELETE FROM image
        WHERE image_id = '$image_id'
        LIMIT 1;
        SQL;

        $this->delete_query($query);
    }

#endregion
}