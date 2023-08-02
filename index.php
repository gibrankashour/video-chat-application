<?php
    include 'core/init.php';
    // var_dump(session_id());
    if($userObj->isLoggedIn()) {
        $userObj->redirect('home.php');
    }
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        
        if(isset($_POST)) {
            $email = trim(stripslashes(htmlentities($_POST["email"])));
            $password = $_POST["password"];
            if(!empty($email) && !empty($password)) {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "Invalid Email format";
                }else {
                    if($user = $userObj->emailExist($email)){
                        if(password_verify($password, $user->password)){
                            // regenerat session id to stop session hijacking and session fixation
                            session_regenerate_id();                            
                            $_SESSION["userID"] = $user->userID;
                            // var_dump($_SESSION["userID"]);
                            // var_dump(session_id());
                            $userObj->redirect("home.php");
                        }else {
                            $error = "Email or password is incorrect";
                        }
                    }
                }
            }else {
                $error = "Please enter your email and password to login";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!--[if lt IE 9]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
	<![endif]-->
</head>

<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
			<div class="account-center">
				<div class="account-box">
                    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" class="form-signin" method="POST">
						<div class="account-logo">
                            <a href=""><img src="assets/img/logo-dark.png" alt=""></a>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email"  autofocus="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <!-- <div class="form-group text-right">
                            <a href="forgot-password.html">Forgot your password?</a>
                        </div> -->
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary account-btn">Login</button>
                        </div>
                        <!-- <div class="text-center register-link">
                            Don’t have an account? <a href="register.html">Register Now</a>
                        </div> -->
                        <?php if(isset($error) && $error != null) { ?>
                            <p class="text-danger my-2"><?php echo $error ?></p>                            
                        <?php } ?>
                    </form>
                </div>
			</div>
        </div>
    </div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>