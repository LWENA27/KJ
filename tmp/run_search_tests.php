<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/DoctorController.php';
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'doctor';
$_GET['q'] = 'hem';
$ctrl = new DoctorController();
$ctrl->search_tests();
