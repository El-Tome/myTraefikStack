use mkcert
mark as always trust

générate certificat
```bash
mkcert -cert-file certs/dev.localhost.cert \
       -key-file  certs/dev.localhost.key \
       "*.dev.localhost" "dev.localhost" localhost 127.0.0.1 ::1
```
