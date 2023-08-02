<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;// -- get user connectiom

class Chat implements MessageComponentInterface {
    protected $clients;
    public $userObj, $data;

    public function __construct() {
        // -- store the clients whoes are connected to our application
        $this->clients = new \SplObjectStorage;
        $this->userObj = new User;
    }

    public function onOpen(ConnectionInterface $conn) {
        // --get the user how is connected to our application 
        // Store the new connection to send messages to later

        /* 
        في كل مرة يقوم المستخدم بعمل تحديث للصفحة فأنه ينشئ إتصال جديد مع 
        $conn->resourceId الويب سوكيت سيرفر لذلك يجب حفظ رقم هذا الاتصال والذي يمثله المتغير
        في قاعدة البيانات حيث نجلب السيشين أي دي الذي يحصل عليه الويب سوكيت سيرفر من الرابط التالي
        ws://localhost:8080?token=9a3du2bjh915vs63lcot4dhf3c
        ثم نجد المستخدم الذي يمثله هذا التوكين ثم نقوم بعملية التحديث 

       */
        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $query);
        if($data = $this->userObj->getUserBySession($query["token"])){
            $this->data = $data;
            // إضافة معلومات المستخدم المتصل إلى المتغير $conn
            $conn->data = $data; 
            $this->clients->attach($conn);
            //تحديث بيانات المستخدم المتصل بالويب سوكيت سيرفر في قاعدة البيانات
            $this->userObj->updateConnection($conn->resourceId, $data->userID);
            echo "New connection! ({$this->data->username})-({$conn->resourceId})\n";
            // echo "New connection! ({$conn->resourceId})\n";
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // -- when user send a message use this function
        // -- $from > THe user is sending the message
        // -- $msg > The message
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        //Connection 93 sending message "{"sendTo":"2","type":"is-client-ready","data":null}" to 1 other connection
        $data = json_decode($msg, true);
        $sendTo = $this->userObj->userData($data["sendTo"]);
        
        $send["sendTo"]         = $sendTo->userID;
        $send["by"]             = $from->data->userID;
        $send["profileImage"]   = $from->data->profileImage;
        $send["username"]       = $from->data->username;
        $send["name"]           = $from->data->name;
        $send["type"]           = $data["type"];
        $send["data"]           = $data["data"];
        // $send["test"]           = $from->data;
        // var_dump($send) ;
        $test = 0;
        foreach ($this->clients as $client) {
            // -- loop throw the cliets connected to our application 
            if ($from !== $client) {
                echo $client->resourceId;
                // The sender is not the receiver, send to each client connected
                if($client->resourceId == $sendTo->connectionID){
                    $client->send(json_encode($send));
                    $test = 1;
                }
            }
        }
        // اذا اتصل مع مستخدم غير فاتح حاليا للموقع
        if($test === 0) {
            $send["sendTo"]         = $from->data->userID;
            $send["name"]         = $from->data->name;
            $send["by"]             = $sendTo->userID;
            $send["profileImage"]   = $sendTo->profileImage;
            $send["username"]       = $sendTo->username;
            $send["data"]           = null;
            $send["type"] = "client-is-unreachable";
            $from->send(json_encode($send));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}