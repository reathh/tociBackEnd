# TociBackEnd
The backend for the Toci Chat project

API API API API
# PAGING

there are two URL parameters for the paging: pageSize & pageNumber. Methods which
implement paging return the number of pages the query returns.

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


# POST api/chat/message

header parameters:
sessionKey: string, user's session key

body parameters:
toUserId: Can be just an userId (/34) or multiple id's (in case of group messages) separated by ',' (/34,35)
content: string

exceptions when:
Incorrect or null sessionKey: 401: "You are not authorized"
Null or empty content: 400: "Content cannot be empty"
Null or empty toUserId: 400:Recipient's id cannot be empty


# GET api/chat/user/messages/:to
IMPLEMENTS PAGING

header parameters:
sessionKey: string, user's session key (acts as "from")

url parameters:
to: acts as "to" for the query. Can be just an userId (/34) or multiple id's (in case of group messages) separated by ',' (/34,35)

exceptions when:
Incorrect or null sessionKey: 401: "You are not authorized"
Null or empty to: 400: "To cannot be empty"


# GET api/chat/user/chronology
IMPLEMENTS PAGING

returns to who has the current user wrote to sorted descending by last message's date (The person to which the user send a message first
is returned first, etc..)

header parameters:
sessionKey: string, user's session key