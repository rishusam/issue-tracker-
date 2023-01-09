<?php
if(!is_dir('./db'))
    mkdir('./db');
if(!defined('db_file')) define('db_file','./db/issue_tracker_db.db');
function my_udf_md5($string) {
    return md5($string);
}

Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
         $this->open(db_file);
         $this->createFunction('md5', 'my_udf_md5');

         $this->exec("CREATE TABLE IF NOT EXISTS issue_list (
            `issue_id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `title` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `department_id` INTEGER NOT NULL,
            `user_id` INTEGER NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 0,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `date_updated` TIMESTAMP NULL
        )");
        // status: 0 = Open, 1= closed
        $this->exec("CREATE TABLE IF NOT EXISTS comment_list (
            `comment_id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `issue_id` INTEGER NOT NULL,
            `comment` TEXT NOT NULL,
            `user_id` INTEGER NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->exec("CREATE TABLE IF NOT EXISTS user_list (
            `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
            `fullname` TEXT NOT NULL,
            `email` TEXT NOT NULL,
            `contact` TEXT NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `department_id` INTEGER,
            `type` INTEGER,
            `designation` TEXT NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
         $this->exec("CREATE TABLE IF NOT EXISTS department_list (
            `name` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
         $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime AFTER UPDATE on `issue_list`
        BEGIN
            UPDATE `issue_list` SET date_updated = CURRENT_TIMESTAMP where issue_id = issue_id;
        END
        ");
    }
    function __destruct(){
         $this->close();
    }
}

$conn = new DBConnection();