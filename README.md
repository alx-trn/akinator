# Akinator
Projet jeu web Akinator simplifié avec 8 personnages de l'univers Zelda.

# Technologies
PHP, PHTML, SASS/CSS, MySQL

# Sécurité
Protection des identifiants BDD via `.env` + `.gitignore`, tokens CSRF sur les formulaires, rate limiting sur la connexion (5 tentatives max).

# Architecture
Structure MVC simple (assets / configs / controllers / repositories / services / views)