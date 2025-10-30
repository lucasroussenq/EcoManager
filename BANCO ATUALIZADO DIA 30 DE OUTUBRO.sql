
DROP DATABASE IF EXISTS ecomanager;


CREATE DATABASE ecomanager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE ecomanager;


CREATE TABLE usuario (
  id_usuario     BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome           VARCHAR(100) NOT NULL,
  email          VARCHAR(120) NOT NULL UNIQUE,
 
  senha_hash     VARCHAR(255) NOT NULL,
  telefone       VARCHAR(20),
  perfil         ENUM('ADMIN','USUARIO') NOT NULL DEFAULT 'USUARIO',
  ativo          TINYINT(1) NOT NULL DEFAULT 1,
  criado_em      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                         ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;


INSERT INTO usuario (nome, email, senha_hash, perfil)
VALUES ('Administrador', 'admin@ecomanager.local', '$2y$10$hash_fake_so_prateste1234567890', 'ADMIN');


CREATE TABLE familia (
  id_familia       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome             VARCHAR(80) NOT NULL,
  dono_id_usuario  BIGINT UNSIGNED NOT NULL,
  criado_em        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_familia_dono
    FOREIGN KEY (dono_id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;


INSERT INTO familia (nome, dono_id_usuario)
VALUES ('Minha família', 1);

CREATE TABLE membro_familia (
  id_familia  BIGINT UNSIGNED NOT NULL,
  id_usuario  BIGINT UNSIGNED NOT NULL,
  papel       ENUM('ADMIN','MEMBRO') NOT NULL DEFAULT 'MEMBRO',
  entrou_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_familia, id_usuario),
  CONSTRAINT fk_mf_familia
    FOREIGN KEY (id_familia)
    REFERENCES familia(id_familia)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_mf_usuario
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;


INSERT INTO membro_familia (id_familia, id_usuario, papel)
VALUES (1, 1, 'ADMIN');


CREATE TABLE familia_permissao (
  id_familia  BIGINT UNSIGNED NOT NULL,
  cod_perm    VARCHAR(30) NOT NULL,
  permitido   TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_familia, cod_perm),
  CONSTRAINT fk_fp_familia
    FOREIGN KEY (id_familia)
    REFERENCES familia(id_familia)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

INSERT INTO familia_permissao (id_familia, cod_perm, permitido)
VALUES (1, 'LANCAR', 1);


CREATE TABLE natureza (
  id_natureza SMALLINT UNSIGNED PRIMARY KEY,
  nome        VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

INSERT INTO natureza (id_natureza, nome)
VALUES
  (1, 'ENTRADA'),
  (2, 'SAIDA');


CREATE TABLE categoria (
  id_categoria BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_natureza  SMALLINT UNSIGNED NOT NULL,
  nome         VARCHAR(60) NOT NULL,
  CONSTRAINT fk_cat_nat
    FOREIGN KEY (id_natureza)
    REFERENCES natureza(id_natureza)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT uq_cat_natureza_nome
    UNIQUE (id_natureza, nome)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;


INSERT INTO categoria (id_natureza, nome) VALUES
  (2, 'Aluguel'),
  (2, 'Água'),
  (2, 'Energia'),
  (2, 'Internet'),
  (2, 'Mercado'),
  (2, 'Transporte'),
  (1, 'Salário'),
  (1, 'Freelance');


CREATE TABLE categoria_usuario (
  id_categoria_user BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_usuario        BIGINT UNSIGNED NOT NULL,
  id_natureza       SMALLINT UNSIGNED NOT NULL,
  nome              VARCHAR(60) NOT NULL,
  CONSTRAINT fk_cu_user
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_cu_nat
    FOREIGN KEY (id_natureza)
    REFERENCES natureza(id_natureza)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

CREATE TABLE lancamento (
  id_lancamento BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_usuario    BIGINT UNSIGNED NOT NULL,
  id_familia    BIGINT UNSIGNED NULL,
  id_categoria  BIGINT UNSIGNED NOT NULL,
  tipo          ENUM('RECEITA','DESPESA') NOT NULL,
  valor         DECIMAL(12,2) NOT NULL,
  data_mov      DATE NOT NULL,
  descricao     VARCHAR(200) NOT NULL,
  criado_em     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                         ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_lanc_user
    FOREIGN KEY (id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_lanc_cat
    FOREIGN KEY (id_categoria)
    REFERENCES categoria(id_categoria)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  
  CONSTRAINT fk_lanc_fam
    FOREIGN KEY (id_familia)
    REFERENCES familia(id_familia)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;


CREATE INDEX idx_lanc_user_data ON lancamento (id_usuario, data_mov);
CREATE INDEX idx_lanc_fam       ON lancamento (id_familia);
CREATE INDEX idx_lanc_cat       ON lancamento (id_categoria);
