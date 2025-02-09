-- Tabela de Especialidades Predefinidas
CREATE TABLE especialidade (
    id_especialidade INT AUTO_INCREMENT PRIMARY KEY,
    nome_especialidade VARCHAR(50) NOT NULL
);

-- Inserir Especialidades Predefinidas
INSERT INTO especialidade (nome_especialidade) VALUES
('Pediatria'),
('Ginecologia'),
('Cardiologia'),
('Ortopedia'),
('Dermatologia'),
('Neurologia'),
('Psiquiatria'),
('Oftalmologia'),
('Otorrinolaringologia'),
('Endocrinologia');

-- Tabela de Médicos com Relacionamento com Especialidades
CREATE TABLE medico (
    id_medico INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    nome_medico VARCHAR(100) NULL,
    crm_medico VARCHAR(10) NULL,
    PRIMARY KEY(id_medico)
);

-- Tabela de Relacionamento Médico-Especialidade
CREATE TABLE medico_especialidade (
    id_medico_especialidade INT AUTO_INCREMENT PRIMARY KEY,
    medico_id_medico INT UNSIGNED NOT NULL,
    especialidade_id_especialidade INT NOT NULL,
    FOREIGN KEY (medico_id_medico) REFERENCES medico(id_medico),
    FOREIGN KEY (especialidade_id_especialidade) REFERENCES especialidade(id_especialidade)
);

-- Tabela de Pacientes
CREATE TABLE paciente (
    id_paciente INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    nome_paciente VARCHAR(100) NULL,
    dt_nasc_paciente DATE NULL,
    email_paciente VARCHAR(100) NULL,
    endereco_paciente VARCHAR(100) NULL,
    fone_paciente VARCHAR(20) NULL,
    cpf_paciente VARCHAR(14) NULL,
    sexo_paciente CHAR(1) NULL,
    PRIMARY KEY(id_paciente)
);

-- Tabela de Consultas
CREATE TABLE consulta (
    id_consulta INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    paciente_id_paciente INTEGER UNSIGNED NOT NULL,
    medico_id_medico INTEGER UNSIGNED NOT NULL,
    data_consulta DATE NULL,
    hora_consulta TIME NULL,
    descricao_consulta TEXT NULL,
    PRIMARY KEY(id_consulta),
    FOREIGN KEY (paciente_id_paciente) REFERENCES paciente(id_paciente),
    FOREIGN KEY (medico_id_medico) REFERENCES medico(id_medico)
);

-- Tabela de Triagem
CREATE TABLE triagem (
    id_triagem INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paciente_id_paciente INT UNSIGNED NOT NULL,
    especialidade_id_especialidade INT NOT NULL,
    prioridade VARCHAR(10),
    temperatura DECIMAL(4,1),
    pressao VARCHAR(10),
    frequencia_cardiaca INT NOT NULL,
    observacoes TEXT,
    classificacao_gravidade ENUM('Vermelho', 'Amarelo', 'Verde', 'Azul') NOT NULL,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id_paciente) REFERENCES paciente(id_paciente),
    FOREIGN KEY (especialidade_id_especialidade) REFERENCES especialidade(id_especialidade)
);

-- Tabela de Recepção
CREATE TABLE recepcao (
    id_recepcao INT AUTO_INCREMENT PRIMARY KEY,  -- Chave primária auto-incrementada
    paciente_id_paciente INT UNSIGNED NOT NULL,  -- ID do paciente (não pode ser nulo)
    tipo_atendimento VARCHAR(50),  -- Tipo de atendimento
    status ENUM('Emergência', 'Aguardando Triagem', 'Aguardando Consulta', 'Aguardando Exames') NOT NULL,  -- Status com valores possíveis
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Data e hora do atendimento (padrão: hora atual)
    triagem_id INT UNSIGNED NULL,  -- ID de triagem (relacionamento com triagem)
    FOREIGN KEY (paciente_id_paciente) REFERENCES paciente(id_paciente),  -- Chave estrangeira para a tabela paciente
    FOREIGN KEY (triagem_id) REFERENCES triagem(id_triagem)  -- Chave estrangeira para a tabela triagem
);

