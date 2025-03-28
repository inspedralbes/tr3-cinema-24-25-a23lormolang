# Documentació
Llistat d'alguns dels punts que han de quedar explicats en aquesta carpeta. Poden ser tots en aquest fitxer o en diversos fitxers enllaçats.

És obligatori modificar aquest document!!

## Documentació bàsica MÍNIMA
 * Objectius
 * Arquitectura bàsica
   * Tecnologies utilitzades
      * Nuxt
      * Laravel
      * MySql
      * Node
   * Interrelació entre els diversos components
 * Com crees l'entorn de desenvolupament
 * Com desplegues l'aplicació a producció
 * Llistat d'endpoints de l'API de backend
    * Rutes
   * Exemples de JSON de peticó
   * Exemples de JSON de resposta i els seus codis d'estat 200? 404?
 * Aplicació Android
 * Altres elements importants.
 * ...

## Llistat d'endpoints de l'API de backend
### POST /auth/login
• Descripció: Iniciar sessió de l'usuari.  
• Exemple de request:
```json
{
  "email": "exemple@usuari.com",
  "password": "contrasenya"
}
```
• Exemple de resposta (200):
```json
{
  "token": "TOKEN_D_AUTENTICACIO",
  "user": {
    "id": 1,
    "name": "Exemple Usuari",
    "email": "exemple@usuari.com"
  }
}
```
• Possibles estats: 200 (OK), 401 (Credencials incorrectes)

### GET /auth/check
• Descripció: Verifica si l'usuari està autenticat.  
• Exemple de resposta (200):
```json
{
  "user": {
    "id": 1,
    "name": "Exemple Usuari",
    "email": "exemple@usuari.com"
  },
  "valid": true
}
```
• Possibles estats: 200 (OK), 401 (No autoritzat)

### GET /stats/sales
• Descripció: Obté estadístiques de vendes (week, month, year).  
• Exemple de request:
```json
{
  "period": "week"
}
```
• Exemple de resposta (200):
```json
{
  "labels": ["01/10/2023", "02/10/2023"],
  "data": [150.00, 200.75],
  "average": 175.38
}
```
• Possibles estats: 200 (OK), 400 (Paràmetre no vàlid)

### POST /reservations
• Descripció: Crear una reserva.  
• Exemple de request:
```json
{
  "name": "Nom Client",
  "email": "client@exemple.com",
  "screening_id": 5,
  "seats": [10, 11]
}
```
• Exemple de resposta (201):
```json
{
  "id": 1004,
  "user_id": 2,
  "screening_id": 5,
  "tickets": [
    {
      "id": 200,
      "seat": {"id": 10, "row": "B", "number": 4, "type": "normal"}
    }
  ]
}
```
• Possibles estats: 201 (Creat), 400 (Error en la reserva)

### POST /auth/register
• Descripció: Registre un nou usuari.  
• Exemple de request:
```json
{
  "name": "Nom Usuari",
  "email": "usuari@exemple.com",
  "password": "contrasenya",
  "password_confirmation": "contrasenya"
}
```
• Exemple de resposta (201):
```json
{
  "message": "Usuario registrado exitosamente"
}
```
• Possibles estats: 201 (Creat), 400 (Error de validació)

### POST /auth/reset-password
• Descripció: Canvia la contrasenya actual de l’usuari autenticat.  
• Exemple de request:
```json
{
  "current_password": "contrasenya_actual",
  "new_password": "nova_contrasenya",
  "new_password_confirmation": "nova_contrasenya"
}
```
• Exemple de resposta (200):
```json
{
  "message": "Contraseña actualizada correctamente"
}
```
• Possibles estats: 200 (OK), 400 (Error de validació)

### POST /auth/logout
• Descripció: Tanca la sessió activa de l’usuari.  
• Exemple de resposta (200):
```json
{
  "message": "Sesión cerrada exitosamente"
}
```
• Possibles estats: 200 (OK)

### GET /admin/screenings
• Descripció: Retorna la llista de projeccions (administració).  
• Exemple de resposta (200):
```json
[
  {
    "id": 1,
    "date": "2023-10-01",
    "time": "20:00",
    "stats": {
      "occupied": 10,
      "revenue": 60.00
    }
  }
]
```
• Possibles estats: 200 (OK), 401 (No autoritzat)

### POST /admin/screenings
• Descripció: Crea una nova projecció.  
• Exemple de request:
```json
{
  "movie_id": 3,
  "room_id": 2,
  "date": "2023-10-10",
  "time": "20:00",
  "is_special": false
}
```
• Exemple de resposta (201):
```json
{
  "id": 8,
  "date": "2023-10-10",
  "time": "20:00"
}
```
• Possibles estats: 201 (Creat), 400 (Error de validació)

### PUT /admin/screenings/{screening}
• Descripció: Actualitza la projecció especificada.  
• Exemple de request:
```json
{
  "time": "18:30"
}
```
• Exemple de resposta (200):
```json
{
  "id": 8,
  "date": "2023-10-10",
  "time": "18:30"
}
```
• Possibles estats: 200 (OK), 400 (Error de validació), 404 (No trobat)

### DELETE /admin/screenings/{screening}
• Descripció: Elimina la projecció indicant la ID.  
• Exemple de resposta (200):
```json
{
  "message": "Proyección eliminada"
}
```
• Possibles estats: 200 (OK), 400 (No es pot eliminar)

### POST /movies
• Descripció: Afegeix una pel·lícula a la base de dades a partir del seu ID d’IMDB.  
• Exemple de request:
```json
{
  "imdb_id": "tt1234567"
}
```
• Exemple de resposta (201):
```json
{
  "id": 10,
  "title": "Títol Pel·lícula",
  "imdb_id": "tt1234567"
}
```
• Possibles estats: 201 (Creat), 404 (Pel·lícula no trobada)

### GET /omdb/search
• Descripció: Cerca pel·lícules a OMDB segons un terme de recerca.  
• Exemple de request:
```json
{
  "query": "Matrix"
}
```
• Exemple de resposta (200):
```json
[
  {
    "Title": "The Matrix",
    "Year": "1999"
  }
]
```
• Possibles estats: 200 (OK), 400 (Falta paràmetre obligatori)

### GET /movies/{movie}
• Descripció: Retorna la informació detallada de la pel·lícula amb les projeccions futures.  
• Exemple de resposta (200):
```json
{
  "id": 1,
  "title": "Exemple",
  "screenings": [
    { "id": 10, "date": "2023-12-01", "time": "21:00" }
  ]
}
```
• Possibles estats: 200 (OK), 404 (No trobat)

### GET /screenings
• Descripció: Retorna projeccions disponibles per als clients entre dates.  
• Exemple de request:
```json
{
  "start_date": "2023-10-01",
  "end_date": "2023-10-31"
}
```
• Exemple de resposta (200):
```json
[
  {
    "id": 1,
    "date": "2023-10-02",
    "time": "20:00",
    "movie": {
      "id": 3,
      "title": "Peli"
    }
  }
]
```
• Possibles estats: 200 (OK), 400 (Error de validació)

### GET /screenings/movies
• Descripció: Retorna llista de pel·lícules que tenen projeccions en el període indicat.  
• Exemple de resposta (200):
```json
[
  {
    "id": 3,
    "title": "Peli"
  }
]
```
• Possibles estats: 200 (OK)

### GET /screenings/{screening}
• Descripció: Retorna la informació d’una projecció concreta (pel·lícula, sala, seients).  
• Exemple de resposta (200):
```json
{
  "movie": {
    "id": 3,
    "title": "Peli"
  },
  "screening": {
    "id": 1,
    "date": "2023-10-02",
    "time": "20:00"
  },
  "room": {
    "id": 2,
    "name": "Sala 1"
  },
  "seats": [
    { "id": 10, "row": "A", "number": 1, "is_occupied": true }
  ]
}
```
• Possibles estats: 200 (OK), 404 (No trobat)

### POST /reservations/access-link
• Descripció: Genera un enllaç d’accés a les reserves associades a un correu.  
• Exemple de request:
```json
{
  "email": "client@exemple.com"
}
```
• Exemple de resposta (200):
```json
{
  "message": "Enlace de acceso enviado a tu correo"
}
```
• Possibles estats: 200 (OK)

### GET /reservations/purchases/{token}
• Descripció: Retorna les reserves disponibles per a l’usuari associat al token indicat.  
• Exemple de resposta (200):
```json
[
  {
    "id": 1,
    "user_id": 2,
    "screening_id": 5
  }
]
```
• Possibles estats: 200 (OK), 404 (Token no vàlid)
