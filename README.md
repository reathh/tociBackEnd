# TociBackEnd
The backend for the Toci Chat project

API API API API
# POST api/register

body parameters:
username: string
password: string
email: string
fullName: string

returns:
username: string
password: string
fullName: string
sessionKey: string

exceptions when:
Dublicated email
Dublicated username


# POST api/login

body parameters:
username: string
password: string

returns:
username: string
password: string
fullName: string
sessionKey: string

exceptions when:
Incorrect username or password: 500: "Incorrect login credentials"


# POST api/chat/addMessage

header parameters:
sessionKey: user's session key

body parameters:
toUserId: integer
content: string

exceptions when:
Incorrect or null sessionKey: 401: "You are not authorized"
Null or empty content: 400: "Content cannot be empty"
Null or empty toUserId: 400:Recipient's id cannot be empty