-- =================================================================
-- 1) Schema
-- =================================================================
CREATE DATABASE IF NOT EXISTS ecomanager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE ecomanager;

-- =================================================================
-- 2) Tabelas principais
-- =================================================================

-- Usuário
DROP TABLE IF EXISTS usuario;
CREATE TABLE usuario (
  id_usuario     BIGINT AUTO_INCREMENT PRIMARY KEY,
  nome           VARCHAR(100) NOT NULL,
  email          VARCHAR(120) NOT NULL UNIQUE,
  senha_hash     VARCHAR(255) NOT NULL,
  telefone       VARCHAR(20),
  perfil         ENUM('ADMIN','USUARIO') DEFAULT 'USUARIO',
  ativo          TINYINT(1) NOT NULL DEFAULT 1,
  criado_em      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Família
DROP TABLE IF EXISTS familia;
CREATE TABLE familia (
  id_familia      BIGINT AUTO_INCREMENT PRIMARY KEY,
  nome            VARCHAR(80)  NOT NULL,
  dono_id_usuario BIGINT       NOT NULL,
  criado_em       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_fam_dono
    FOREIGN KEY (dono_id_usuario) REFERENCES usuario(id_usuario)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Membros da família
DROP TABLE IF EXISTS membro_familia;
CREATE TABLE membro_familia (
  id_familia  BIGINT NOT NULL,
  id_usuario  BIGINT NOT NULL,
  papel       ENUM('ADMIN','MEMBRO') NOT NULL DEFAULT 'MEMBRO',
  entrou_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_familia, id_usuario),
  CONSTRAINT fk_mf_fam FOREIGN KEY (id_familia) REFERENCES familia(id_familia)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_mf_user FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Natureza (Entrada/Saída)
DROP TABLE IF EXISTS natureza;
CREATE TABLE natureza (
  id_natureza SMALLINT PRIMARY KEY,
  nome        VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categoria (vinculada à natureza)
DROP TABLE IF EXISTS categoria;
CREATE TABLE categoria (
  id_categoria BIGINT AUTO_INCREMENT PRIMARY KEY,
  id_natureza  SMALLINT NOT NULL,
  nome         VARCHAR(60) NOT NULL,
  CONSTRAINT fk_cat_nat FOREIGN KEY (id_natureza) REFERENCES natureza(id_natureza)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT uq_cat_nome UNIQUE (id_natureza, nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Lançamento
DROP TABLE IF EXISTS lancamento;
CREATE TABLE lancamento (
  id_lancamento BIGINT AUTO_INCREMENT PRIMARY KEY,
  id_usuario    BIGINT      NOT NULL,
  id_familia    BIGINT      NULL,
  id_categoria  BIGINT      NOT NULL,
  tipo          ENUM('RECEITA','DESPESA') NOT NULL,
  valor         DECIMAL(12,2) NOT NULL,
  data_mov      DATE         NOT NULL,
  descricao     VARCHAR(200) NOT NULL,
  criado_em     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_lanc_user FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_lanc_fam  FOREIGN KEY (id_familia)   REFERENCES familia(id_familia)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_lanc_cat  FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =================================================================
-- 3) Índices úteis
-- =================================================================
CREATE INDEX idx_lanc_user_data ON lancamento(id_usuario, data_mov);
CREATE INDEX idx_lanc_cat      ON lancamento(id_categoria);
CREATE INDEX idx_lanc_fam      ON lancamento(id_familia);

-- =================================================================
-- 4) Seeds (dados básicos)
-- =================================================================

-- Naturezas
INSERT INTO natureza (id_natureza, nome) VALUES
  (1, 'ENTRADA'),
  (2, 'SAIDA')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

-- Categorias exemplo
INSERT INTO categoria (id_categoria, id_natureza, nome) VALUES
  (1, 2, 'Mercado'),
  (2, 2, 'Aluguel'),
  (3, 2, 'Transporte'),
  (4, 2, 'Salário'),   -- natureza 2 = SAIDA (exemplo didático, ajuste se quiser)
  (5, 1, 'Freelance'),
  (6, 2, 'Energia'),
  (7, 2, 'Água'),
  (8, 2, 'Internet')
ON DUPLICATE KEY UPDATE id_natureza = VALUES(id_natureza), nome = VALUES(nome);

