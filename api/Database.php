<?php

class Database extends PDO {
    public function __construct($ini = "config.ini") {
        // Parse the configuration file.
        if (!$parse = parse_ini_file($ini, true)) {
        	throw new exception("Could not read configuration file.");
        }

        // Store the necessary variables.
        $driver = $parse["db_driver"];
        $dsn = "${driver}:";
        $user = $parse["db_user"];
        $password = $parse["db_password"];
        $options = $parse["db_options"];
        $attributes = $parse["db_attributes"];

        // Parse DSN information.
        foreach ($parse["dsn"] as $k => $v) {
            $dsn .= "${k}=${v};";
        }

        // Call the parent constructor.
        parent::__construct($dsn, $user, $password, $options);

        // Set any additional attributes for the connection.
        foreach ($attributes as $k => $v) {
            parent::setAttribute(constant("PDO::{$k}"), constant("PDO::{$v}"));
        }
    }
}