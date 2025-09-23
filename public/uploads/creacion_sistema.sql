-- Crear las tablas
CREATE TABLE action_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    active_ind           BINARY() NULL
);

CREATE TABLE attribute_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL
);

CREATE TABLE channel_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL
);

CREATE TABLE company
(
    id                   INTEGER NOT NULL,
    parent_company_id    INTEGER NULL,
    company_name         VARCHAR(20) NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    campany_dni          VARCHAR(20) NULL,
    contact_address      VARCHAR(20) NULL,
    contact_email        VARCHAR(20) NULL,
    contact_phone        CHAR(18) NULL,
    user_id              INTEGER NULL
);

CREATE TABLE company_status
(
    id                   INTEGER NOT NULL,
    start_date           DATE NULL,
    end_date             DATE NULL,
    observation_txt      VARCHAR(20) NULL,
    company_id           INTEGER NULL,
    status_type_id       INTEGER NULL,
    entity_user_id       INTEGER NULL
);

CREATE TABLE complaint
(
    id                   INTEGER NOT NULL,
    document_type_id     INTEGER NULL,
    description_txt      VARCHAR(20) NULL,
    name                 VARCHAR(20) NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    from_id              INTEGER NULL,
    company_id           INTEGER NULL,
    entity_user_id       INTEGER NULL,
    channel_id           INTEGER NULL
);

CREATE TABLE complaint_action
(
    id                   INTEGER NOT NULL,
    action_type_id       INTEGER NULL,
    complaint_id         INTEGER NULL,
    observation_txt      VARCHAR(20) NULL,
    active_ind           INTEGER NULL,
    start_date           DATE NULL,
    end_date             DATE NULL,
    entity_user_id       INTEGER NULL
);

CREATE TABLE complaint_auditor
(
    id                   INTEGER NOT NULL,
    complaint_id         INTEGER NULL,
    previous_user_id     INTEGER NULL,
    user_id              INTEGER NULL,
    observation_txt      VARCHAR(20) NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL
);

CREATE TABLE complaint_source
(
    id                   INTEGER NOT NULL,
    complaint_id         INTEGER NULL,
    name                 VARCHAR(20) NULL,
    email                VARCHAR(20) NULL,
    phone                VARCHAR(20) NULL,
    anonymous_ind        BINARY() NULL,
	tracking_code        VARCHAR(20) NULL,
	created_at           DATE NULL,
	updated_at           DATE NULL,
	entity_user_id       INTEGER NULL,
	active_ind           INTEGER NULL
);

CREATE TABLE complaint_status
(
    id                   INTEGER NOT NULL,
    start_date           DATE NULL,
    end_date             DATE NULL,
    observation_txt      VARCHAR(20) NULL,
    status_type_id       INTEGER NULL,
    complaint_id         INTEGER NULL,
    entity_user_id       INTEGER NULL
);

CREATE TABLE complaint_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    active_ind           BINARY() NULL
);

CREATE TABLE dictionary
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    diccionary_type_id   INTEGER NULL
);

CREATE TABLE dictionary_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL
);

CREATE TABLE document
(
    id                   INTEGER NOT NULL,
    document_type_id     INTEGER NULL,
    description_txt      VARCHAR(20) NULL,
    name                 VARCHAR(20) NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    file_id              INTEGER NULL,
    complaint_id         INTEGER NULL
);

CREATE TABLE document_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    active_ind           BINARY() NULL
);

CREATE TABLE field_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL
);

CREATE TABLE file
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    name_file            VARCHAR(20) NULL,
    size                 VARCHAR(20) NULL,
    type                 VARCHAR(20) NULL,
    root                 VARCHAR(20) NULL,
    file_type_id         INTEGER NULL,
    created_at           DATE NULL
);

CREATE TABLE file_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL
);

CREATE TABLE form
(
    id                   INTEGER NOT NULL,
    title_txt            VARCHAR(20) NULL,
    subtitle_txt         VARCHAR(20) NULL,
    label_name           VARCHAR(20) NULL,
    template_id          INTEGER NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL
);

CREATE TABLE form_status
(
    id                   INTEGER NOT NULL,
    start_date           DATE NULL,
    end_date             DATE NULL,
    observation_txt      VARCHAR(20) NULL,
    form_id              INTEGER NULL,
    status_type_id       INTEGER NULL,
    entity_user_id       INTEGER NULL
);

CREATE TABLE login_attempt
(
    id                   INTEGER NOT NULL,
    ip_address           VARCHAR(20) NULL,
    success_ind          BINARY() NULL,
	created_at           DATE NULL,
	user_id              INTEGER NULL
);

CREATE TABLE multiple_choice
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    template_detail_id   INTEGER NULL,
    diccionary_id        INTEGER NULL
);

CREATE TABLE response
(
    id                   INTEGER NOT NULL,
    title_txt            VARCHAR(20) NULL,
    description_txt      VARCHAR(20) NULL,
    observation_txt      VARCHAR(20) NULL,
    order_num            INTEGER NULL,
    form_id              INTEGER NULL,
    segment_type_id      INTEGER NULL,
    response_val         VARCHAR(20) NULL,
    attribute_type_id    INTEGER NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    file_id              INTEGER NULL
);

CREATE TABLE role
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    text_name            VARCHAR(20) NULL
);

CREATE TABLE section
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    company_id           INTEGER NULL,
    template_company_id  INTEGER NULL
);

CREATE TABLE section_user
(
    section_id           INTEGER NOT NULL,
    user_id              INTEGER NOT NULL,
    id                   INTEGER NOT NULL,
    active_ind           BINARY() NULL,
	created_at           DATE NULL,
	updated_at           DATE NULL,
	entity_user_id       INTEGER NULL
);

CREATE TABLE segment_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL
);

CREATE TABLE status_type
(
    id                   INTEGER NOT NULL,
    name                 VARCHAR(20) NULL,
    status_cd            INTEGER NULL
);

CREATE TABLE template
(
    id                   INTEGER NOT NULL,
    title_txt            VARCHAR(20) NULL,
    subtitle_txt         VARCHAR(20) NULL,
    logo_img             VARCHAR(20) NULL,
    label_name           VARCHAR(20) NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    is_template          BINARY() NULL,
	complaint_type_id    INTEGER NULL
);

CREATE TABLE template_alert
(
    id                   INTEGER NOT NULL,
    template_id          INTEGER NULL,
    days_num             DATE NULL,
    notification_txt     INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    name                 VARCHAR(20) NULL,
    email_notification   VARCHAR(20) NULL
);

CREATE TABLE template_company
(
    id                   INTEGER NOT NULL,
    template_id          INTEGER NULL,
    active_ind           BINARY() NULL,
	created_at           DATE NULL,
	updated_at           DATE NULL,
	entity_user_id       INTEGER NULL,
	company_id           INTEGER NULL
);

CREATE TABLE template_detail
(
    id                   INTEGER NOT NULL,
    title_txt            VARCHAR(20) NULL,
    descripcion_txt      VARCHAR(20) NULL,
    order_num            INTEGER NULL,
    template_id          INTEGER NULL,
    attribute_type_id    INTEGER NULL,
    field_type_id        INTEGER NULL,
    segment_type_id      INTEGER NULL,
    active_ind           INTEGER NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    observation_ind      BINARY() NULL
);

CREATE TABLE user
(
    id                   INTEGER NOT NULL,
    password             VARCHAR(20) NULL,
    avatar               VARCHAR(20) NULL,
    email                VARCHAR(20) NULL,
    name                 CHAR(18) NULL,
    failed_num           INTEGER NULL,
    last_login_date      DATE NULL,
    created_at           DATE NULL,
    updated_at           DATE NULL,
    entity_user_id       INTEGER NULL,
    token                VARCHAR(20) NULL,
    expires_at           DATE NULL
);

CREATE TABLE user_role
(
    role_id              INTEGER NOT NULL,
    user_id              INTEGER NOT NULL
);

-- Ahora agregar las relaciones (claves foráneas)
ALTER TABLE company
    ADD FOREIGN KEY (parent_company_id) REFERENCES company (id);

ALTER TABLE company
    ADD FOREIGN KEY (user_id) REFERENCES user (id);

ALTER TABLE company_status
    ADD FOREIGN KEY (company_id) REFERENCES company (id);

ALTER TABLE company_status
    ADD FOREIGN KEY (status_type_id) REFERENCES status_type (id);

ALTER TABLE complaint
    ADD FOREIGN KEY (document_type_id) REFERENCES complaint_type (id);

ALTER TABLE complaint
    ADD FOREIGN KEY (from_id) REFERENCES form (id);

ALTER TABLE complaint
    ADD FOREIGN KEY (company_id) REFERENCES company (id);

ALTER TABLE complaint
    ADD FOREIGN KEY (channel_id) REFERENCES channel_type (id);

ALTER TABLE complaint_action
    ADD FOREIGN KEY (action_type_id) REFERENCES action_type (id);

ALTER TABLE complaint_action
    ADD FOREIGN KEY (complaint_id) REFERENCES complaint (id);

ALTER TABLE complaint_auditor
    ADD FOREIGN KEY (complaint_id) REFERENCES complaint (id);

ALTER TABLE complaint_auditor
    ADD FOREIGN KEY (previous_user_id) REFERENCES user (id);

ALTER TABLE complaint_auditor
    ADD FOREIGN KEY (user_id) REFERENCES user (id);

ALTER TABLE complaint_source
    ADD FOREIGN KEY (complaint_id) REFERENCES complaint (id);

ALTER TABLE complaint_status
    ADD FOREIGN KEY (status_type_id) REFERENCES status_type (id);

ALTER TABLE complaint_status
    ADD FOREIGN KEY (complaint_id) REFERENCES complaint (id);

ALTER TABLE dictionary
    ADD FOREIGN KEY (diccionary_type_id) REFERENCES dictionary_type (id);

ALTER TABLE document
    ADD FOREIGN KEY (document_type_id) REFERENCES document_type (id);

ALTER TABLE document
    ADD FOREIGN KEY (complaint_id) REFERENCES complaint (id);

ALTER TABLE file
    ADD FOREIGN KEY (file_type_id) REFERENCES file_type (id);

ALTER TABLE form
    ADD FOREIGN KEY (template_id) REFERENCES template (id);

ALTER TABLE form_status
    ADD FOREIGN KEY (form_id) REFERENCES form (id);

ALTER TABLE form_status
    ADD FOREIGN KEY (status_type_id) REFERENCES status_type (id);

ALTER TABLE login_attempt
    ADD FOREIGN KEY (user_id) REFERENCES user (id);

ALTER TABLE multiple_choice
    ADD FOREIGN KEY (template_detail_id) REFERENCES template_detail (id);

ALTER TABLE multiple_choice
    ADD FOREIGN KEY (diccionary_id) REFERENCES dictionary (id);

ALTER TABLE response
    ADD FOREIGN KEY (form_id) REFERENCES form (id);

ALTER TABLE response
    ADD FOREIGN KEY (segment_type_id) REFERENCES segment_type (id);

ALTER TABLE response
    ADD FOREIGN KEY (attribute_type_id) REFERENCES attribute_type (id);

ALTER TABLE response
    ADD FOREIGN KEY (file_id) REFERENCES file (id);

ALTER TABLE section
    ADD FOREIGN KEY (company_id) REFERENCES company (id);

ALTER TABLE section
    ADD FOREIGN KEY (template_company_id) REFERENCES template_company (id);

ALTER TABLE section_user
    ADD FOREIGN KEY (section_id) REFERENCES section (id);

ALTER TABLE section_user
    ADD FOREIGN KEY (user_id) REFERENCES user (id);

ALTER TABLE template
    ADD FOREIGN KEY (complaint_type_id) REFERENCES complaint_type (id);

ALTER TABLE template_alert
    ADD FOREIGN KEY (template_id) REFERENCES template (id);

ALTER TABLE template_company
    ADD FOREIGN KEY (template_id) REFERENCES template (id);

ALTER TABLE template_company
    ADD FOREIGN KEY (company_id) REFERENCES company (id);

ALTER TABLE template_detail
    ADD FOREIGN KEY (template_id) REFERENCES template (id);

ALTER TABLE template_detail
    ADD FOREIGN KEY (attribute_type_id) REFERENCES attribute_type (id);

ALTER TABLE template_detail
    ADD FOREIGN KEY (field_type_id) REFERENCES field_type (id);

ALTER TABLE template_detail
    ADD FOREIGN KEY (segment_type_id) REFERENCES segment_type (id);

ALTER TABLE user_role
    ADD FOREIGN KEY (role_id) REFERENCES role (id);

ALTER TABLE user_role
    ADD FOREIGN KEY (user_id) REFERENCES user (id);
