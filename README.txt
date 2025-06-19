TECNOLOGIE :
	- php (framework : symfony )
	- Vue ( tailwind, primevue )
	

Questa web app permette la registrazione l'accesso dell'utente il quale viene autenticato tramice access token e refresh token, 
memorizzato nei cockies in HTTP secure. 

L'utente può attuare la prenotazione della corsa e vederle all'interno della pagina USER


COMANDI UTILI :

docker composer up -d

docker exec -it php bash

php bin/console doctrine:schema:update --force

docker compose down