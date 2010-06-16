CREATE TABLE IF NOT EXISTS user (
    id                      int(11) not null auto_increment, 
    tp_xid                  varchar(20),
    name                    mediumtext,
    session_id              varchar(20),
    session_sync_token      varchar(80),

    primary key (id),
    key (tp_xid),
    key (session_id),
    key (session_sync_token)
);

CREATE TABLE IF NOT EXISTS config (
    consumer_key            varchar(64),
    urls                    mediumtext
);

CREATE TABLE IF NOT EXISTS oauth_server_registry (
    osr_id                      int(11) not null auto_increment,
    osr_usa_id_ref              int(11),
    osr_consumer_key            varchar(64) binary not null,
    osr_consumer_secret         varchar(64) binary not null,
    osr_enabled                 tinyint(1) not null default '1',
    osr_status                  varchar(16) not null,
    osr_requester_name          varchar(64) not null,
    osr_requester_email         varchar(64) not null,
    osr_callback_uri            varchar(255) not null,
    osr_application_uri         varchar(255) not null,
    osr_application_title       varchar(80) not null,
    osr_application_descr       text not null,
    osr_application_notes       text not null,
    osr_application_type        varchar(20) not null,
    osr_application_commercial  tinyint(1) not null default '0',
    osr_issue_date              datetime not null,
    osr_timestamp               timestamp not null default current_timestamp,

    primary key (osr_id),
    unique key (osr_consumer_key),
    key (osr_usa_id_ref)
);

CREATE TABLE IF NOT EXISTS oauth_consumer_registry (
    ocr_id                  int(11) not null auto_increment,
    ocr_usa_id_ref          int(11),
    ocr_consumer_key        varchar(64) binary not null,
    ocr_consumer_secret     varchar(64) binary not null,
    ocr_signature_methods   varchar(255) not null default 'HMAC-SHA1,PLAINTEXT',
    ocr_server_uri          varchar(255) not null,
    ocr_server_uri_host     varchar(128) not null,
    ocr_server_uri_path     varchar(128) binary not null,

    ocr_request_token_uri   varchar(255) not null,
    ocr_authorize_uri       varchar(255) not null,
    ocr_access_token_uri    varchar(255) not null,
    ocr_timestamp           timestamp not null default current_timestamp,

    primary key (ocr_id),
    unique key (ocr_consumer_key, ocr_usa_id_ref),
    key (ocr_server_uri),
    key (ocr_server_uri_host, ocr_server_uri_path),
    key (ocr_usa_id_ref)
);

CREATE TABLE IF NOT EXISTS oauth_consumer_token (
    oct_id                  int(11) not null auto_increment,
    oct_ocr_id_ref          int(11) not null,
    oct_usa_id_ref          int(11) not null,
    oct_name                varchar(64) binary not null default '',
    oct_token               varchar(170) binary not null,
    oct_token_secret        varchar(64) binary not null,
    oct_token_type          enum('request','authorized','access'),
    oct_token_ttl           datetime not null default '9999-12-31',
    oct_timestamp           timestamp not null default current_timestamp,

    primary key (oct_id),
    unique key (oct_ocr_id_ref, oct_token),
    unique key (oct_usa_id_ref, oct_ocr_id_ref, oct_token_type, oct_name),
    key (oct_token_ttl),

    foreign key (oct_ocr_id_ref) references oauth_consumer_registry (ocr_id)
        on update cascade
        on delete cascade
);

CREATE TABLE IF NOT EXISTS oauth_log (
    olg_id                  int(11) not null auto_increment,
    olg_osr_consumer_key    varchar(64) binary,
    olg_ost_token           varchar(64) binary,
    olg_ocr_consumer_key    varchar(64) binary,
    olg_oct_token           varchar(64) binary,
    olg_usa_id_ref          int(11),
    olg_received            text not null,
    olg_sent                text not null,
    olg_base_string         text not null,
    olg_notes               text not null,
    olg_timestamp           timestamp not null default current_timestamp,
    olg_remote_ip           bigint not null,

    primary key (olg_id),
    key (olg_osr_consumer_key, olg_id),
    key (olg_ost_token, olg_id),
    key (olg_ocr_consumer_key, olg_id),
    key (olg_oct_token, olg_id),
    key (olg_usa_id_ref, olg_id)
);