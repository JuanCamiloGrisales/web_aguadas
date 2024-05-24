# Inicializar Base de Datos

```sql
CREATE DATABASE aguadas;

USE aguadas;

CREATE TABLE login (
    usuario VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    PRIMARY KEY (usuario)
);
```
