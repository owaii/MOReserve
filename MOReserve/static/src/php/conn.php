<?php
    $db = new mysqli("localhost","root","","more");

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }