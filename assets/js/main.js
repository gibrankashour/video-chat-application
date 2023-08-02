'use strict';


let pc; // webRTC peer connection
let sendTo;

let callBtn = document.getElementById('callBtn');
// المتغيرات الخاصة للبوب أب عند ورود مكالمة للمستخدم
let callBox     = document.getElementById('callBox'); // يمثل مربع الحوار الذي يظهر عند ورود المكالمة ويكون موجود في نهاية كل صفحة ضمن الموقع
let declineBtn  = document.getElementById('declineBtn');
let answerBtn   = document.getElementById('answerBtn');
let hangupBtn   = document.getElementById('hangupBtn');
// المتغيرات الخاصة بتشغيل او أيقاف الكاميرة والمايك اثناء المكالمة
let videoControl   = document.getElementById('video-control');
let micControl   = document.getElementById('mic-control');

// المتغيرات الخاصة بالبوب أب عند إغلاق المكالمة مثلا
let alertBox   = document.getElementById('alertBox');
if(callBtn) {
    sendTo = callBtn.dataset.user; // destination user id
}
let mediaDevices;
let localStream;

// vidoe elements
const localVideo  = document.getElementById("localVideo");
const remoteVideo = document.getElementById("remoteVideo");

// media info
const mediaConst = {
    video:true,
    audio:true,
}
// what expect to receive from the another client 
const options = {
    offerToReceiveVideo: 1,
    offerToReceiveAudio: 1,
}
// information about stun servers
const config = {
    iceServers:[
        {urls:"stun:stun1.l.google.com:19302"},
    ]
}
// get webRTC connection
getConn();
function getConn() {
    if(!pc) {
        pc = new RTCPeerConnection(config);
    }
}

//ask for media input

async function getCam() {
    // let mediaStrem;
    // try {
        if(!pc) { await getConn(); }
        // سؤال المستخدمم للحصول على صلاحية الوصول الى الكميرا و الصوت
        
        mediaDevices = await navigator.mediaDevices.getUserMedia(mediaConst)
		.then((stream) => {
			// Changing the source of video to current stream.
			localVideo.srcObject = stream;
			localVideo.addEventListener("loadedmetadata", () => {
                localVideo.play();
			});
            
            // اسناد محتوى الستريم الذي تم الحصول عليه الى 
            // webrtc connection 
            // حتى نستطيع إرساله لاحقا
            stream.getTracks().forEach( function (track) {
                pc.addTrack(track, stream);                
            }); 

            /* 
            MediaStreamTrack: enabled property
            When enabled, a track's data is output from the source to the destination;
            otherwise, empty frames are output.

            In the case of audio, a disabled track generates frames of silence
            (that is, frames in which every sample's value is 0).
            For video tracks, every frame is filled entirely with black pixels.
            */
            // ايقاف او تشغيل الكاميرا
            videoControl.addEventListener("click", function() {                        
                if(this.firstElementChild.dataset.status == 'mute') {            
                    document.getElementById('video-play').classList = 'hidden'; 
                    document.getElementById('video-mute').classList = ''; 
                    this.firstElementChild.dataset.status = 'play';
                    this.firstElementChild.setAttribute('title','Play Video');
                    // إيقاف تشغيل الكاميرا
                    stream.getVideoTracks()[0].enabled = false;
                }else if(this.firstElementChild.dataset.status == 'play') {            
                    document.getElementById('video-mute').classList = 'hidden'; 
                    document.getElementById('video-play').classList = ''; 
                    this.firstElementChild.dataset.status = 'mute';
                    this.firstElementChild.setAttribute('title','Mute Video');
                    // إعادة تشغيل الكاميرا
                    stream.getVideoTracks()[0].enabled = true;
                }
            });
            // ايقاف او تشغيل المايك
            micControl.addEventListener("click", function() {                        
                if(this.firstElementChild.dataset.status == 'mute') {            
                    document.getElementById('mic-play').classList = 'hidden'; 
                    document.getElementById('mic-mute').classList = ''; 
                    this.firstElementChild.dataset.status = 'play';
                    this.firstElementChild.setAttribute('title','Play Mic');
                    // إيقاف تشغيل المايك
                    stream.getAudioTracks()[0].enabled = false;
                }else if(this.firstElementChild.dataset.status == 'play') {            
                    document.getElementById('mic-mute').classList = 'hidden'; 
                    document.getElementById('mic-play').classList = ''; 
                    this.firstElementChild.dataset.status = 'mute';
                    this.firstElementChild.setAttribute('title','Mute Mic');
                    // إعادة تشغيل المايك
                    stream.getAudioTracks()[0].enabled = true;
                }
            });

		}) 
		.catch(error => {console.log(error)}); 
}


// عند الضغط على زر إجراء مكالمة فديو
if(callBtn) {
    callBtn.addEventListener("click", function() {
        if(conn.readyState === 1) {
            getCam();
            send("is-client-ready", null, sendTo);
        }else{
            alert("Ratchet server unreachable");
        }
    });
}


// عند الضغط على زر إغلاق مكالمة فديو
if(hangupBtn) {
    hangupBtn.addEventListener("click", function() {
        send("client-hangup", null, sendTo);
        pc.close();
        pc = null;
        location.reload(true);
    });
}
function dispalyAlert(username, profileImage, message) {
    document.getElementById('alertName').textContent = username;
    document.getElementById('alertMessage').textContent = message;
    document.getElementById('profileImage').setAttribute('src', profileImage);

    alertBox.classList.remove("hidden");
    var videos = document.querySelectorAll("video");
    for(let i=0; i < videos.length; i++) {
        videos[i].style.display = "none";
    }
    document.querySelector(".user-video img").style.display = "block";
    document.getElementById("end-call").style.display = "none";
    document.getElementById("start-call").style.display = "block";
}
// -------------------------------------------------------------------- //
// الفانكشنز الخاصة بالتواصل مع الويب سوكيت سيرفر
// المتفير conn يتم انشائه في بداية صفحة connection.php
// -------------------------------------------------------------------- //
// -------------------------------------------------------------------- //
// التابع التالي يعمل عند الأتصال مع الويب سوكيت سيرفر
conn.onopen = e => {
    console.log("connected to websocket server");
}
// التابع التالي يعمل عند استقبال رسالة من الويب سوكيت سيرفر
conn.onmessage = async e => {

    let message     = JSON.parse(e.data);
    let by          = message.by;
    let data        = message.data;
    let type        = message.type;
    let profileImage = message.profileImage;
    let username     = message.username;
    let name         = message.name;
    
    document.getElementById('username').textContent = username;    
    document.getElementById('profileImage').setAttribute('src', profileImage);
    
    switch(type) {
        case "is-client-ready":
            if(!pc) { await getConn(); }
            if(pc.iceConnectionState === "connected") {
                send("client-already-oncall", null, by);
            }else {
                
                //dispaly popup for incomming video call
                callBox.classList.remove("hidden");
                // التحقق فيما اذا كان مستقبل المكالمة فاتح صفحة منشئ المكالمة
                if(window.location.href.indexOf(name) > -1) {
                    
                    // عند الضغط على زر الموافقة على إجراء مكالمة فديو
                    answerBtn.addEventListener("click", function() {                        
                        callBox.classList.add("hidden");
                        send("client-is-ready", null, by);
                    });
                    

                }else {
                    // اذا كان في صفحة اخرى عندئذ يتم حفظ المتغيرات في سيشن وتحويله الى صفحة اخرى
                    // عند الضغط على زر الموافقة على إجراء مكالمة فديو
                    answerBtn.addEventListener("click", function() {
                        callBox.classList.add("hidden");
                        redirectToCall(name, by);
                    });
                }
                
                // عند الضغط على زر رفض إجراء مكالمة فديو
                declineBtn.addEventListener("click", function() {
                    send("client-rejected", null, by);
                    // تحديث صفحة مشتقبل المكالمة بعض رفض الأتصال
                    location.reload(true);
                });

            }    
        break;

        case "client-already-oncall":
            dispalyAlert(username, profileImage, "client is already on another call");
            setTimeout("window.location.reload(true)", 2000);
        break;

        case "client-rejected":
            dispalyAlert(username, profileImage, "client rejected the call");
            setTimeout("window.location.reload(true)", 2000);
        break;

        case "client-is-ready":
            // بعد ارسال طلب إجراء مكالمة فديو والموافقة على الطلب من 
            // المستقبل عندها يتم ارسال معلومات webRTC 
            // الى المستقبل
            createOffer(sendTo);
        break;

        case "client-offer":
            // الأن المستخدم الذي طلب إنشاء مكالمة الفديو وبعد موافقة المستخدم
            // الأخر وبعد ذلك قيام المستخدم المنشئ للمكالمة بإرسال معلومات
            // webRTC
            // إلى الطرف المستقبل عندها يتم تفعيل هذه الحالة
            createAnswer(by, data);
            $("#callTimer").timer({format: "%m:%s"});
        break;
        case "client-answer":
            // الأن المستخدم الذي طلب إنساء مكالمة الفديو وبعد موافقة المستخدم
            // الأخر وبعد ذلك قيام المستخدم المنشئ للمكالمة بإرسال معلومات
            // webRTC
            // إلى الطرف المستقبل الذي يقوم بدوره بإرسال معلوماته الى
            // الطرف الذي قام بطلب إنشاء مكالمة فديو
            // عندها يتم تفعيل هذه الرسالة
            if(pc.localDescription) {
                await pc.setRemoteDescription(data);
            }
            $("#callTimer").timer({format: "%m:%s"});
        break;

        case "client-candidate":
            // بعدنجاح الأتصال وإرسال الأوفر أو إرسال الأنسر 
            // عندها يتم إرسال ice candidate
            // والذي يحوي على عنوان ال IP  
            if(pc.localDescription) {
                await pc.addIceCandidate(new RTCIceCandidate(data));
            }
        break;

        case "client-hangup":
            dispalyAlert(username, profileImage, "client disconnected the call");
            setTimeout("window.location.reload(true)", 2000);
        break;
        // اذا كان المستخدم في الجهة المقابلة مسكر الموقع اي مانو مسجل دخول
        case "client-is-unreachable":
            dispalyAlert(username, profileImage, "client is unreachable");
            setTimeout("window.location.reload(true)", 2000);
        break;
    }
}

function send(type, data, sendTo) {
    conn.send(JSON.stringify({
        sendTo : sendTo,
        type : type,
        data : data
    }));
}
// -------------------------------------------------------------------- //
// -------------------------------------------------------------------- //
async function createOffer(sendTo) {    
    await pc.createOffer(options);
    await pc.setLocalDescription(pc.localDescription);
    send("client-offer", pc.localDescription, sendTo); // send session description protocol information
    sendIceCandidate(sendTo); // send ip addess information
    // لازم يكون متصل بالانترنت حتى يعرف عنوان الاي بي حتى و كان محلي
}

async function createAnswer(sendTo, data) {
    if(!pc) { await getConn() }
    // تشغيل الكميرا عند المستقبل 
    if(!localStream) { await getCam() }
    
    await pc.setRemoteDescription(data);
    await pc.createAnswer();
    await pc.setLocalDescription(pc.localDescription);
    send("client-answer", pc.localDescription, sendTo);
    sendIceCandidate(sendTo);
}

function sendIceCandidate(sendTo) {
    pc.onicecandidate = e => {
        // send("client-candidate", e.candidate, sendTo);
        if(e.candidate !== null ) {
            // send icecandidate ti other client
            send("client-candidate", e.candidate, sendTo);
        }
    }

}

// هذا الحدث يتم تفعيله عند استقبال بيانات من خلال قناة 
// peerconnection (webRTC)

pc.ontrack = e => {        

    // عرض الفديو القادم من الطرف الأخر
    remoteVideo.srcObject = e.streams[0];

    remoteVideo.addEventListener("loadedmetadata", () => {
        console.log("receive remote video");
        remoteVideo.play();
    });

    var videos = document.querySelectorAll("video");
    for(let i=0; i < videos.length; i++) {
        videos[i].style.display = "block";
    }
    document.querySelector(".user-video img").style.display = "none";
    document.getElementById("end-call").style.display = "block";
    document.getElementById("start-call").style.display = "none";

}
// -------------------------------------------
// إذا استقبل المستخدم أتصال من مستخدم أخر وكان فاتح صفحة
// أخرى غير صفحة المستخدم منشئ المكالمة
function redirectToCall(username, sendTo) {
    if(window.location.href.indexOf(username) == -1) {
        sessionStorage.setItem("redirect", true);
        sessionStorage.setItem("sendTo", sendTo);
        window.location.href = "/vchat/" + username; 
    }
}
// اذا تم تحويل المستخدم الى صفحة منشئ المكالمة
if(sessionStorage.getItem("redirect")) {
    sendTo = sessionStorage.getItem("sendTo");
    // التحقق من اتصال المستخدم متلقي المكالمة مع الويب سوكيت سيرفر
    let waitForWs = setInterval(() => {
        if(conn.readyState === 1) {
            send("client-is-ready", null, sendTo);
            clearInterval(waitForWs);
        }
    }, 500);
    sessionStorage.removeItem("redirect");
    sessionStorage.removeItem("sendTo");
}