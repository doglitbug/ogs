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
        //Removed FILTER_FLAG_STRIP_LOW so that \r\n are not turned into &#13;&#10;
        $string = filter_var($string, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH);
        return $this->connection->real_escape_string($string);
    }

    /** Generate pagination SQL
     * @return string LIMIT x, y
     */
    private function generate_pagination_sql(): string
    {
        list($page, $size) = get_page_and_size();
        $offset = ($page - 1) * $size;
        return "\nLIMIT {$offset}, {$size}";
    }

    /**
     * Perform a get query and return an assoc array of results
     * @param string $query Escaped query string
     * @return array Results
     */
    private
    function get_query(string $query): array
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
    private
    function insert_query(string $query): int
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
    private
    function update_query(string $query): void
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
    private
    function delete_query(string $query): void
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
    public
    function get_all_users(): array
    {
        $query = <<<SQL
        SELECT  user_id,
                username,
                name,
                email,
                user.description,
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

    public
    function get_user(string $user_id): array
    {
        $user_id = $this->escape($user_id);

        $query = <<<SQL
        SELECT  user_id,
                username,
                name,
                email,
                user.description,
                location_id,
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

        $result = $this->get_query($query);
        if ($result) {
            return $result[0];
        } else {
            return [];
        }
    }

    /** Get user by email address
     * @param string $email
     * @return array
     */
    public
    function get_user_by_email(string $email): array
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

    /** Update an existing user
     * @param array $user
     * @return void
     */
    public function update_user(array $user): void
    {
        $user_id = $this->escape($user['user_id']);
        $name = $this->escape($user['name']);
        $username = $this->escape($user['username']);
        $location_id = $this->escape($user['location_id']);
        $description = $this->escape($user['description']);

        $query = <<<SQL
        UPDATE user SET name = '$name',
                        username = '$username',
                        description = '$description',
                        location_id = '$location_id'
        WHERE user_id = '$user_id'
        LIMIT 1
        SQL;

        dump($query);
        $this->update_query($query);
    }

    /** Check to see if there is a user in the database with this email
     * @param string $email
     * @return bool
     */
    public
    function check_email_exists(string $email): bool
    {
        return $this->get_user_by_email($email) !== [];
    }

    /** Check to see if this user is Super Admin, Admin or ordinary User
     * @param string $user_id
     * @return string
     */
    public
    function get_access_level(string $user_id): string
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
        return $result[0]['access'];
    }

#endregion

#region garage
    /** Get all garages
     * @param array $options visible
     * @return array
     */
    public
    function get_all_garages(array $options = []): array
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
    public
    function get_garage(string $garage_id): array
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
     * @param array $options access Owner or Worker
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

    /** Get a list of owners, then workers for this garage
     * @param string $garage_id
     * @return array
     */
    public function get_garage_staff(string $garage_id): array
    {
        $garage_id = $this->escape($garage_id);

        $query = <<<SQL
        SELECT  user_id,
                username,
                access.description
        FROM user_garage_access
            LEFT JOIN user USING (user_id)
            LEFT JOIN access using (access_id)
        WHERE garage_id = '{$garage_id}'
        ORDER BY access.access_id
        SQL;

        return $this->get_query($query);
    }

    /** Create a new garage
     * @param array $garage
     * @return int New ID
     * @note This does not set ownership or access at all!
     */
    public
    function insert_garage(array $garage): int
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

    /** Update an existing garage
     * @param array $garage
     * @return void
     */
    public
    function update_garage(array $garage): void
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
    public
    function delete_garage(array $garage): void
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
    /** Get items, usually from an individual garage with primary image
     * @param array $options garage_id: Filter to particular garage
     *                       search: Filter to search
     *                       visible: Hide hidden items (required for pagination to work)
     *                       paginate: Use pagination to return only a subset
     * @return array
     */
    public function get_items(array $options = []): array
    {
        if (isset($options['garage_id'])) {
            $extra_queries[] = "garage_id='" . $this->escape($options['garage_id']) . "'";
        }

        if (isset($options['search'])) {
            $extra_queries[] = "MATCH(item.name, item.description) AGAINST('" . $this->escape($options['search']) . "')";
        }

        //TODO Hide if garage is hidden as well?
        if (isset($options['visible'])) {
            $extra_queries[] = "visible='" . $this->escape($options['visible']) . "'";
        }

        $query = <<<SQL
        SELECT item.item_id,
               garage_id,
               name,
               description,
               visible,
               item.updated_at,
               item.created_at,
               image.image_id,
               image.width,
               image.height,
               CONCAT(path, '/', filename) as source
        FROM item
                 LEFT JOIN LATERAL (SELECT *
                                    FROM item_image
                                    WHERE item.item_id = item_image.item_id
                                    ORDER BY main DESC
                                    LIMIT 1) as iii
            using (item_id)
                 LEFT JOIN (SELECT *
                            FROM image) as image using (image_id)
        SQL;

        if (isset($extra_queries)) {
            //First query needs to add WHERE
            $query .= "\nWHERE " . $extra_queries[0];
            //Subsequent queries need AND
            for ($i = 1; $i < sizeof($extra_queries); $i++) {
                $query .= "\nAND " . $extra_queries[$i];
            }
        }

        if (isset($options['paginate'])) {
            $query .= $this->generate_pagination_sql();
        }

        return $this->get_query($query);
    }

    /** Get an individual item, usually for show/edit item
     * @param string $item_id
     * @param array $options public: garage hidden will override visibility
     * @return array
     */
    public
    function get_item(string $item_id, array $options = []): array
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
    }

    /** Insert a new item
     * @param array $item
     * @return int
     */
    public
    function insert_item(array $item): int
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

    public
    function update_item(array $item): void
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

    /** Delete an item, assumes item_image links have been removed
     * @param array $item
     * @return void
     */
    public
    function delete_item(array $item): void
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
    public
    function get_user_access(string $user_id, string $garage_id): string
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
#region image
    /** Get a single image from the database
     * @param string $image_id
     * @return array
     */
    public
    function get_image(string $image_id): array
    {
        $image_id = $this->escape($image_id);

        $query = <<<SQL
        SELECT  image_id,
                width,
                height,
                CONCAT(path, '/', filename) as source
        FROM image
        WHERE image_id = '$image_id'
        SQL;

        $result = $this->get_query($query);
        if ($result) {
            return $result[0];
        } else {
            return [];
        }
    }

    /** Get the images for an item
     * @param string $item_id
     * @return array
     */
    public
    function get_item_images(string $item_id): array
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

        return $this->get_query($query);
    }

    /** Insert a new image into the database, assumes it has been moved to the correct location
     * @param array $image
     * @return int image_id
     */
    public
    function insert_image(array $image): int
    {
        $width = $this->escape($image['width']);
        $height = $this->escape($image['height']);
        $path = $this->escape($image['path']);
        $filename = $this->escape($image['filename']);

        $query = <<<SQL
            INSERT INTO image
            (width, height, path, filename)
            VALUES ('$width',
                    '$height',
                    '$path',
                    '$filename'
                    )
        SQL;

        return $this->insert_query($query);
    }

    /** Link an image to a garage
     * @param string $garage_id
     * @param string $image_id
     * @param string $main is this the main image?
     * @return void
     */
    public
    function insert_garage_image(string $garage_id, string $image_id, string $main = "0"): void
    {
        $garage_id = $this->escape($garage_id);
        $image_id = $this->escape($image_id);

        $query = <<<SQL
            INSERT IGNORE INTO garage_image
            (garage_id, image_id, main)
            VALUES ('$garage_id',
                    '$image_id',
                    '$main'
                    )
        SQL;

        $this->insert_query($query);
    }

    /** Link an image to an item
     * @param string $item_id
     * @param string $image_id
     * @param string $main is this the main image?
     * @return void
     */
    public
    function insert_item_image(string $item_id, string $image_id, string $main = "0"): void
    {
        $item_id = $this->escape($item_id);
        $image_id = $this->escape($image_id);

        $query = <<<SQL
            INSERT IGNORE INTO item_image
            (item_id, image_id, main)
            VALUES ('$item_id',
                    '$image_id',
                    '$main'
                    )
        SQL;

        $this->insert_query($query);
    }

    /** Remove image from database, assumes that the file has already been removed
     * This should also remove item/garage_item links
     * @param array $image
     * @return void
     */
    public
    function delete_image(array $image): void
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