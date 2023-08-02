<?php 
session_start();

require 'classes/DB.php';
require 'classes/User.php';

define('BASE_URL', 'http://localhost/vchat/peer-to-peer/');

$userObj = new \MyApp\User;