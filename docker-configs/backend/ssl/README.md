Command to generate TLS certificate:

```shell
openssl req -x509 -nodes -days 365 -newkey rsa:4096 -keyout privateKey.key -out certificate.crt -subj "/C=AU/ST=Some-State/O=Internet Widgits Pty Ltd/CN=localhost"
```
