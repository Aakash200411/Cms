<?php

$connect = mysqli_connect('mysql.db.mdbgo.com', 'aakash200411_cmsdb', 'Secret@cms1', 'aakash200411_cmsdb');

if (mysqli_connect_errno()) {
    exit('Failed to connect to Mysql : ' . mysqli_connect_error());

}
