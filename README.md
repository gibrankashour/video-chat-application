# Video chat application

Peer to peer video chat application using ratchet WebSocket server and WebRTC.

## Features

- User can make peer to peer video chat
- User can accept or reject incoming calls
- If receiver rejects the call then popup will shows in the call creator that the receiver rejected the call 
- If receiver is on anther call then popup will shows that user is busy   
- Users can mute and unmute mic and camera during the call 

## Notes

- There are three default accounts and they all have the same password `123` but all passwords are encrypted by `password_hash()` function so in database they show in a different format.

## Run Locally


Start the Ratchet server

```bash
  php bin/server.php
```


## 🔗 Links

[![linkedin](https://img.shields.io/badge/linkedin-0A66C2?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/gibran-kashour-a073471b2/)


