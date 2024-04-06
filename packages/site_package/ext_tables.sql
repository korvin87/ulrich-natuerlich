CREATE TABLE pages (
    tx_sitepackage_headlinealign varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_headerslider_height varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_headerslider_mapsmarker varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE tt_content (
    tx_sitepackage_headerstyle varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_textalign varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_fontsize varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_fontcolor varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_bgimage varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_bgimagesize varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_bgcolor varchar(255) DEFAULT '' NOT NULL,
    tx_sitepackage_bgcoloroverimage tinyint(4) DEFAULT '0' NOT NULL
);

CREATE TABLE sys_file_reference (
    tx_sitepackage_posterimage varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE tx_news_domain_model_news (
    tx_csseo int(11) unsigned NOT NULL DEFAULT '0'
);

CREATE TABLE sys_category (
  tx_csseo int(11) unsigned DEFAULT '0' NOT NULL
);
