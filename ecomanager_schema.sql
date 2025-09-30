
-- EcoManager - Esquema relacional (usuario, natureza, categoria, familia, membro_familia)
-- MySQL 8+, InnoDB, utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS membro_familia;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS familia;
DROP TABLE IF EXISTS natureza;
DROP TABLE IF EXISTS usuario;

-- =======================
-- Tabela: usuario
-- =======================
CREATE TABLE usuario (
  id_usuario     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome           VARCHAR(100)    NOT NULL,
  email          VARCHAR(120)    NOT NULL,
  senha_hash     VARCHAR(255)    NOT NULL,
  telefone       VARCHAR(20)     NULL,
  perfil         ENUM('ADMIN','PADRAO') NOT NULL DEFAULT 'PADRAO',
  ativo          TINYINT(1)      NOT NULL DEFAULT 1,
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuario_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =======================
-- Tabela: natureza
-- =======================
CREATE TABLE natureza (
  id_natureza  SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome         VARCHAR(30)       NOT NULL,
  PRIMARY KEY (id_natureza),
  UNIQUE KEY uq_natureza_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =======================
-- Tabela: categoria
-- =======================
CREATE TABLE categoria (
  id_categoria BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
  id_natureza  SMALLINT UNSIGNED NOT NULL,
  nome         VARCHAR(60)       NOT NULL,
  PRIMARY KEY (id_categoria),
  KEY idx_categoria_natureza (id_natureza),
  UNIQUE KEY uq_categoria_natureza_nome (id_natureza, nome),
  CONSTRAINT fk_categoria_natureza
    FOREIGN KEY (id_natureza) REFERENCES natureza (id_natureza)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =======================
-- Tabela: familia
-- =======================
CREATE TABLE familia (
  id_familia       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome             VARCHAR(80)     NOT NULL,
  dono_id_usuario  BIGINT UNSIGNED NULL,
  criado_em        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_familia),
  KEY idx_familia_dono (dono_id_usuario),
  CONSTRAINT fk_familia_dono
    FOREIGN KEY (dono_id_usuario) REFERENCES usuario (id_usuario)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =======================
-- Tabela: membro_familia
-- =======================
CREATE TABLE membro_familia (
  id_familia   BIGINT UNSIGNED NOT NULL,
  id_usuario   BIGINT UNSIGNED NOT NULL,
  papel        ENUM('ADMIN','COLABORADOR','LEITOR') NOT NULL DEFAULT 'COLABORADOR',
  entrou_em    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_familia, id_usuario),
  KEY idx_mf_usuario (id_usuario),
  CONSTRAINT fk_mf_familia
    FOREIGN KEY (id_familia) REFERENCES familia (id_familia)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_mf_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- =======================
-- Dados básicos (opcional)
-- =======================
-- INSERT INTO natureza (nome) VALUES ('RECEITA'), ('DESPESA');
-- INSERT INTO usuario (nome,email,senha_hash,perfil) 
-- VALUES ('Admin','admin@exemplo.com','$2y$10$hash_bcrypt_aqui','ADMIN');
