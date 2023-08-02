<?php 
namespace MyApp;

use PDO;

class User {
    public $db, $userID, $sessionID;

    public function __construct() {
        $db = new DB();
        $this->db = $db->connect();
        $this->userID = $this->ID();
        $this->sessionID = session_id();
    }

    // Check if email exist
    public function emailExist($email) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->bindParam('email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        if(!empty($user)) {
            return $user;
        }else {
            return false;
        }
    }
    // Generate hash for the password
    public function hash($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function redirect($location) {
        header('Location:' . BASE_URL . $location);
        exit();
    }
    // get loggedIn user id
    public function ID() {
        return $this->isLoggedIn()?$_SESSION['userID']:false;
    }
    // get user information depending in his id
    public function userData($userID = null) {
        $userID = $userID != null ? $userID : $this->userID;
        $stmt = $this->db->prepare('SELECT * FROM users WHERE userID = :userID');
        $stmt->bindParam('userID', $userID, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    // check if user login
    public function isLoggedIn() {
        return isset($_SESSION['userID']) && $_SESSION['userID'] != null ? true : false;
    } 

    public function loggout() {
        // $_SESSION = array();
        session_unset();
        session_destroy();
        session_regenerate_id();
        return $this->redirect('index.php');
    }
    // get all users
    public function getUsers() {
        $userID = $this->userID;
        $stmt = $this->db->prepare('SELECT * FROM users WHERE userID != :userID');
        $stmt->bindParam('userID', $userID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    // get user by username
    public function getUserByUserName($name) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE name = :name');
        $stmt->bindParam('name', $name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);       
    }
    // set the session value to sessionID parameter
    // public function setSession() {
    //     return session_id();
    // }
    // update session id every time user login
    public function updateSession() {
        $stmt = $this->db->prepare('UPDATE users SET sessionID = :sessionID WHERE userID = :userID');
        $stmt->bindParam('sessionID', $this->sessionID, PDO::PARAM_STR);
        $stmt->bindParam('userID', $this->userID, PDO::PARAM_INT);
        $stmt->execute();
    }
    // get user info by his sessionID
    public function getUserBySession($sessionID) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE sessionID = :sessionID');
        $stmt->bindParam('sessionID', $sessionID, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);       
    }
    // update user connection ID
    public function updateConnection($connectionId, $userID) {
        $stmt = $this->db->prepare('UPDATE users SET connectionId = :connectionId WHERE userID = :userID');
        $stmt->bindParam('connectionId', $connectionId, PDO::PARAM_STR);
        $stmt->bindParam('userID', $userID, PDO::PARAM_INT);
        $stmt->execute();
    }
}