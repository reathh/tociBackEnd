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
Incorrect login credentials
