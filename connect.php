<?php 
    include 'core/init.php';
    
    if(!$userObj->isLoggedIn()) {
        $userObj->redirect('index.php');
    }
    if(isset($_GET['username']) && $_GET['username'] != null) {
        $user = $userObj->userData();
        $profileData = $userObj->getUserByUserName($_GET['username']);
        if(!$profileData) {
            $userObj->redirect('home.php');
        }elseif($profileData->name == $user->name) {
            $userObj->redirect('home.php');
        }
    }else {
        $userObj->redirect('home.php');
    }
    
?>
 
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    
    <title><?php echo $profileData->username ?></title> 
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

        <div class="page-wrapper glass" >
            <div class="chat-main-row">
                <div class="chat-main-wrapper">
                    <div class="col-lg-9 message-view chat-view">
                        <div class="chat-window">

                            <div class="fixed-header">
								<div class="navbar">
                                    <div class="user-details mr-auto">
                                        <div class="float-left user-img m-r-10">
                                            <a href="#" title="<?php echo $profileData->username ?>"><img src="<?php echo BASE_URL. $profileData->profileImage ?>" alt="" class="w-40 rounded-circle"></a>
                                        </div>
                                        <div class="user-info float-left">
                                            <a href="#" title="<?php echo $profileData->username ?>"><span class="font-bold"><?php echo $profileData->username ?></span></a>
                                            
                                        </div>
                                    </div>                                    
								</div>
                            </div>

                            <div class="chat-contents">
                                <div class="chat-content-wrap">
                                    <div class="user-video">
                                        <!-- الفديو الخاص بالشخص الذي نقوم بمحادثته -->
                                        <img src="<?php echo BASE_URL . $profileData->profileImage ?>" alt="">
                                        <video id="remoteVideo" ></video>
                                    </div>
                                    <div class="my-video">
                                        <ul>
                                            <li>
                                                <!-- <img src="assets/img/user-02.jpg" class="img-fluid" alt=""> -->
                                                <!-- الفديو الخاص بي انا -->
                                                <video id="localVideo"></video>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>


                            <div class="chat-footer">
                                <div class="call-icons">
                                    <!-- START CALL BTN  -->
                                    <div id="start-call">
                                        <button id="callBtn" class="btn btn-success" data-user="<?php echo $profileData->userID ?>"><i class="fa fa-phone mr-2"></i>  Start Call</button>
                                    </div>
                                    <!-- END CALL BTN -->
                                    <div id="end-call">
                                        <span id="callTimer" class="call-duration"></span>
                                        <ul class="call-items">
                                            <li class="call-item" id="video-control">
                                                <a href="javascript:void(0)" title="Mute Video" data-placement="top"  data-status="mute">
                                                    <!-- <i class="fa fa-video-camera camera"></i> -->
                                                    <img src="assets/img/video.png" alt="" id="video-play">
                                                    <img src="assets/img/video-mute.png" alt="" id="video-mute" class="hidden">
                                                </a>
                                            </li>
                                            <li class="call-item" id="mic-control">
                                                <a href="javascript:void(0)" title="Mute Audio" data-placement="top" data-status="mute">
                                                    <!-- <i class="fa fa-microphone microphone"></i> -->
                                                    <img src="assets/img/mic.png" alt="" id="mic-play">
                                                    <img src="assets/img/mic-mute.png" alt="" id="mic-mute" class="hidden">
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="end-call">
                                        <button id="hangupBtn" class="btn btn-danger"><i class="fa fa-phone mr-2"></i>  End Call</button>
                                            <!-- <a href="">	End Call</a> -->
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-3 message-view chat-profile-view chat-sidebar" id="chat_sidebar">
                        <div class="chat-window video-window">
                            <div class="fixed-header">
                                <ul class="nav nav-tabs nav-tabs-bottom">
                                    <li class="nav-item"><a class="nav-link active" href="#profile_tab" data-toggle="tab">Profile</a></li>
                                </ul>
                            </div>
                            <div class="tab-content chat-contents">                                
                                <div class="content-full tab-pane  show active" id="profile_tab">
                                    <div class="display-table">
                                        <div class="table-row">
                                            <div class="table-body">
                                                <div class="table-content">
                                                    <div class="chat-profile-img">
                                                        <div class="edit-profile-img">
                                                            <img src="assets/img/user.jpg" alt="">
                                                            
                                                        </div>
                                                        <h3 class="user-name m-t-10 mb-0"><?php echo $profileData->username ?></h3>
                                                        <small class="text-muted">MBBS, MD</small>
                                                    </div>
                                                    <!-- user information -->
                                                    <div class="chat-profile-info">
                                                        <ul class="user-det-list">
                                                            <li>
                                                                <span>Username:</span>
                                                                <span class="float-right text-muted"><?php echo $profileData->name ?></span>
                                                            </li>
                                                            <li>
                                                                <span>DOB:</span>
                                                                <span class="float-right text-muted">1996/1/1</span>
                                                            </li>
                                                            <li>
                                                                <span>Email:</span>
                                                                <span class="float-right text-muted"><?php echo $profileData->email ?></span>
                                                            </li>
                                                            <li>
                                                                <span>Phone:</span>
                                                                <span class="float-right text-muted">9876543210</span>
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
                            <!-- popup -->
            <div class="popup hidden" id="alertBox">
              <div class="call-container">
                  <div>
                    <img src="assets/images/defaultImage.jpg" alt="" class="rounded" id="profileImage">
                    <section>
                        <h2 id="alertName">User name</h2>
                        <p id="alertMessage" class=" "></p>
                    </section>
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
    <script src="assets/js/timer.js"></script>

    <!-- <script src="https://webrtchacks.github.io/adapter/adapter-latest.js"></script> -->
    <script src="assets/js/main.js"></script>

</body>

</html>