<?php 
    include 'core/init.php';
    // عند التاكد من أن كلمة المرور وأسم المستخدم صحيحين
    // عندئذ يتم تحديث السيشين أي دي ثم تتم عملية تسجيل الدخول وتوجيه المستخدم إلى هذه الصفحة
    // لذلك يتم تحديث قيمة السيشن في جدول قاعدة البيانات في بداية هذه الصفحة 
    $userObj->updateSession();
    if(!$userObj->isLoggedIn()) {
        $userObj->redirect('index.php');
    }
    $user = $userObj->userData();
    
?>  
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    
    <title>Home</title> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <!-- نقوم في هذه الصفحة بأنشاء ويب سوكيت كونكشن من السيرفر الذي قمنا بإنشائه في بداية المشروع -->
    <!-- Ratchet server -->
    <!-- ونرسل له التوكن الخاص بالمستخدم المسجل حاليا وهو عبارة عن السيشين أي دي الخاص به -->
    <script type="text/javascript">
        const conn = new WebSocket("ws://localhost:8080?token=<?php echo $user->sessionID ?>")
    </script>
</head>

<body>
    <div class="main-wrapper">
        <!-- start navbar  -->
        <div class="header">
            <div class="header-left">
                <a href="home.php" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>Video Chat</span>
                </a>
            </div>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="<?php echo BASE_URL . $user->profileImage ?>" width="40" alt="Admin">
							<!-- <span class="status online"></span> -->
                        </span>
                        <span><?php echo $user->username ?></span>
                    </a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="logout.php">Logout</a>
					</div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
        <!-- end navbar  -->

        <!-- start sidebar  -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div class="sidebar-menu">
                    <ul>
                        <li>
                            <a href="logout.php"><i class="fa fa-home back-icon"></i> <span>Logout</span></a>
                        </li>
                        <li>
                            <br>
                        </li>

                        <?php foreach($userObj->getUsers() as $userInfo) { ?>
                            <li>
                                <a href="<?php echo BASE_URL . $userInfo->name ?>">
                                    <span class="chat-avatar-sm user-img">
                                        <img src="<?php echo BASE_URL . $userInfo->profileImage ?>" alt="" class="rounded-circle">
                                        <!-- <span class="status online"></span> -->
                                    </span>    
                                    <?php echo $userInfo->username ?>
                                </a>
                            </li>  
                        <?php } ?>
                        
                        
                    </ul>
                </div>
            </div>
        </div>
        <!-- end sidebar  -->

        <div class="page-wrapper" >
        <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title">My Profile</h4>
                    </div>

                    
                </div>
                <div class="card-box profile-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-view">
                                <div class="profile-img-wrap">
                                    <div class="profile-img">
                                        <a href="#"><img class="avatar" src="<?php echo BASE_URL . $user->profileImage ?>" alt=""></a>
                                        <!-- <a href="#"><img class="avatar" src="assets/img/doctor-03.jpg" alt=""></a> -->
                                    </div>
                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="profile-info-left">
                                                <h3 class="user-name m-t-0 mb-0"><?php echo $user->username ?></h3>
                                                <small class="text-muted">Gynecologist</small>
                                                <div class="staff-id">Employee ID : DR-0001</div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="personal-info">
                                                <li>
                                                    <span class="title">Phone:</span>
                                                    <span class="text"><a href="">xxx-xxx-xxxx</a></span>
                                                </li>
                                                <li>
                                                    <span class="title">Email:</span>
                                                    <span class="text"><?php echo $user->email ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Birthday:</span>
                                                    <span class="text">1996/1/1</span>
                                                </li>
                                                <li>
                                                    <span class="title">Address:</span>
                                                    <span class="text">Tartus, Syria</span>
                                                </li>
                                                <li>
                                                    <span class="title">Gender:</span>
                                                    <span class="text">Male</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div>
				
            </div>
                            <!-- call answer or decline -->
            <div class="callBox hidden" id="callBox">
              <div class="call-container">
                  <div>
                    <img src="assets/images/defaultImage.jpg" alt="" class="rounded" id="profileImage">
                    <h2 id="username">User name</h2>
                  </div>
                  <div>
                      <button id="declineBtn">
                            <i class="fa fa-close text-danger"></i>
                      </button>
                      <button id="answerBtn">
                            <i class="fa fa-phone text-success"></i>  
                      </button>
                      
                  </div>
              </div>
            </div>

        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/app.js"></script>

    <script src="assets/js/main.js"></script>

</body>

</html>