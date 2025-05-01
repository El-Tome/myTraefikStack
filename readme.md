1. Pré-requis
   •	Docker et Docker Compose installés sur votre machine hôte
   •	mkcert installé localement (cf. https://github.com/FiloSottile/mkcert)

---

2. Installation et confiance de la CA mkcert

2.1 Installation de mkcert
```shell
brew install mkcert
brew install nss     # si vous utilisez Firefox
```

2.2 Marquer la CA comme « toujours approuvée »

Sur macOS, mkcert installe automatiquement sa CA dans le trousseau système :
1.	Ouvrez Trousseau d’accès.
2.	Recherchez mkcert development CA.
3.	Effectuez un clic droit → Lire les informations.
4.	Déployez la section Confiance et, pour Utiliser ce certificat, choisissez Toujours approuver.

⸻

3. Génération des certificats de développement

Dans le répertoire où seront stockés vos certificats (./certs), exécutez :

```shell
mkcert \
  -cert-file certs/dev.localhost.cert \
  -key-file  certs/dev.localhost.key \
  "*.dev.localhost" dev.localhost localhost 127.0.0.1 ::1
```
- -cert-file : chemin et nom du fichier PEM contenant le certificat
- -key-file : chemin et nom du fichier PEM contenant la clé privée
- Les DNS/hosts passés en argument (ici : *.dev.localhost, dev.localhost, localhost, 127.0.0.1, ::1) seront couverts par le certificat.

4. Comment l'utiliser

1) Apache

```yaml
services:
  app:
    image: php:8.1-apache
    volumes:
      - ./app:/var/www/html:ro
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.app.rule=Host(`apache.dev.localhost`)"
      - "traefik.http.routers.app.entrypoints=websecure"
      - "traefik.http.routers.app.tls=true"
      - "traefik.http.services.app.loadbalancer.server.port=80"
    networks:
      - web

networks:
  web:
    external: true
```

2) NGINX 


