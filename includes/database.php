<?php

$connect = mysqli_connect('mysql.railway.internal', 'root', 'llBhSnJhgoNktIPCqMYyNgPPAPJRdalt', 'railway');

if (mysqli_connect_errno()) {
    exit('Failed to connect to Mysql : ' . mysqli_connect_error());

}
