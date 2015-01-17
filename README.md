# TociBackEnd
The backend for the Toci Chat project

# API
POST api/register

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

