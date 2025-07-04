# Web App Prenotazione Corse

Questa web app consente agli utenti di registrarsi, autenticarsi e prenotare corse. Il frontend è realizzato con Vue.js e TailwindCSS, mentre il backend è sviluppato in PHP utilizzando il framework Symfony. L'ambiente di sviluppo è gestito tramite Docker.

---

## Tecnologie Utilizzate

### Backend
- PHP (Symfony)
- Doctrine ORM

### Frontend
- Vue.js
- TailwindCSS
- PrimeVue

### DevOps
- Docker
- Docker Compose

---

## Autenticazione

Il sistema di autenticazione si basa su:
- Access Token e Refresh Token
- I token vengono salvati in cookie HTTP-only e Secure per garantire la massima sicurezza

---

## Funzionalità Utente

- Registrazione e login
- Prenotazione delle corse
- Visualizzazione delle corse prenotate

---

## Avvio del Progetto

Per avviare l'ambiente di sviluppo, eseguire i seguenti comandi:

```bash
# Avvia i container in background
docker compose up -d --build

# Ferma e rimuove i container
docker compose down -v


```

## Credenziali Accesso phpMyAdmin

```bash
username : root
password : default
```

url : localhost:5173


## Repository URL

[text](https://github.com/Crespi09/CrespiRoncalli-Daniele.git)