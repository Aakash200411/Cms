<?php

$connect = mysqli_connect('localhost', 'cms', 'secret@cms', 'cms');

if (mysqli_connect_errno()) {
    exit('Failed to connect to Mysql : ' . mysqli_connect_error());

}
