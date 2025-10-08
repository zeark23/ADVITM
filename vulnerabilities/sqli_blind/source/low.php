<?php

if ( isset( $_GET['Submit'] ) ) {
    // Get input and validate (must be integer)
    $id_raw = isset($_GET['id']) ? $_GET['id'] : null;
    $id = filter_var($id_raw, FILTER_VALIDATE_INT);
    $exists = false;

    if ($id === false || $id === null) {
        // invalid id -> treat as not found
        $exists = false;
    } else {
        switch ($_DVWA['SQLI_DB']) {
            case MYSQL:
                // Use prepared statement (mysqli procedural) to avoid SQL Injection
                $conn = $GLOBALS["___mysqli_ston"];

                $query = 'SELECT first_name, last_name FROM users WHERE user_id = ?';
                try {
                    $stmt = mysqli_prepare($conn, $query);
                    if ($stmt !== false) {
                        mysqli_stmt_bind_param($stmt, 'i', $id);
                        mysqli_stmt_execute($stmt);

                        // store result and check number of rows
                        mysqli_stmt_store_result($stmt);
                        $num_rows = mysqli_stmt_num_rows($stmt);
                        $exists = ($num_rows > 0);

                        mysqli_stmt_close($stmt);
                    } else {
                        // prepare failed
                        $exists = false;
                    }
                } catch (Exception $e) {
                    $exists = false;
                }

                // NOTE: original code closed the global connection here; avoid closing global DB connection unless intended.
                // If you must close (to match original behaviour), uncomment the next line:
                // ((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
                break;

            case SQLITE:
                // Use SQLite3 prepared statements
                global $sqlite_db_connection;

                $query = 'SELECT first_name, last_name FROM users WHERE user_id = :id';
                try {
                    $stmt = $sqlite_db_connection->prepare($query);
                    if ($stmt !== false) {
                        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                        $res = $stmt->execute();
                        if ($res !== false) {
                            $row = $res->fetchArray(SQLITE3_ASSOC);
                            $exists = ($row !== false);
                            $res->finalize();
                        } else {
                            $exists = false;
                        }
                        // Note: no need to explicitly close $stmt (SQLite3 frees it on script end),
                        // but you can unset($stmt) if you want to free immediately.
                    } else {
                        $exists = false;
                    }
                } catch (Exception $e) {
                    $exists = false;
                }
                break;
        }
    }

    if ($exists) {
        // Feedback for end user
        $html .= '<pre>User ID exists in the database.</pre>';
    } else {
        // User wasn't found, so the page wasn't!
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');

        // Feedback for end user
        $html .= '<pre>User ID is MISSING from the database.</pre>';
    }
}

?>
