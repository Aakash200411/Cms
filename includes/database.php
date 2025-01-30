<?php

$connect = mysqli_connect('mysql.railway.app', 'root', 'YTHbOgINtFZqRZcABqbrRxiPOtnJcqQE', 'railway');

if (mysqli_connect_errno()) {
    exit('Failed to connect to Mysql : ' . mysqli_connect_error());

}
