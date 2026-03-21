USE mixkit_records;

ALTER TABLE usuarios
  ADD COLUMN email VARCHAR(160) NULL,
  ADD COLUMN password VARCHAR(255) NULL;

-- Si ya tienes usuarios antiguos (solo telefono), puedes completar manualmente su email/password
-- antes de hacer estas columnas obligatorias.

ALTER TABLE usuarios
  ADD UNIQUE KEY uniq_usuarios_email (email);
