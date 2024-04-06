#
# Table structure for table 'tx_ulrichproducts_domain_model_product'
#
CREATE TABLE tx_ulrichproducts_domain_model_product (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	slug varchar(255) DEFAULT '' NOT NULL,
	description text,
	accordiontext_plant text,
	accordiontext_plant_media int(11) unsigned DEFAULT '0' NOT NULL,
	accordiontext_origin text,
	accordiontext_origin_media int(11) unsigned DEFAULT '0' NOT NULL,
	accordiontext_production text,
	accordiontext_production_media int(11) unsigned DEFAULT '0' NOT NULL,
	accordiontext_application text,
	accordiontext_application_media int(11) unsigned DEFAULT '0' NOT NULL,
	accordiontext_facts text,
	accordiontext_facts_media int(11) unsigned DEFAULT '0' NOT NULL,
	appearance varchar(255) DEFAULT '' NOT NULL,
	cas_number varchar(255) DEFAULT '' NOT NULL,
	eg_number varchar(255) DEFAULT '' NOT NULL,
	granulation varchar(255) DEFAULT '' NOT NULL,
	bestbefor varchar(255) DEFAULT '' NOT NULL,
	qualities varchar(255) DEFAULT '' NOT NULL,
	spec varchar(255) DEFAULT '' NOT NULL,
	physical_state varchar(255) DEFAULT '' NOT NULL,
	chemical_properties varchar(255) DEFAULT '' NOT NULL,
	molecular_formula varchar(255) DEFAULT '' NOT NULL,
	chemical_name varchar(255) DEFAULT '' NOT NULL,
	registration varchar(255) DEFAULT '' NOT NULL,
	e_number varchar(255) DEFAULT '' NOT NULL,
	grass_state varchar(255) DEFAULT '' NOT NULL,
	container varchar(255) DEFAULT '' NOT NULL,
	inci varchar(255) DEFAULT '' NOT NULL,
	einecs varchar(255) DEFAULT '' NOT NULL,
	melting_point varchar(255) DEFAULT '' NOT NULL,
	durability varchar(255) DEFAULT '' NOT NULL,
	storage varchar(255) DEFAULT '' NOT NULL,
	media int(11) unsigned DEFAULT '0' NOT NULL,
	origin_countries int(11) unsigned DEFAULT '0' NOT NULL,
	related_products int(11) unsigned DEFAULT '0' NOT NULL,
	contacts int(11) unsigned DEFAULT '0',
	categories int(11) unsigned DEFAULT '0' NOT NULL,
	branch int(11) unsigned DEFAULT '0' NOT NULL,
	nextday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	tx_csseo int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state smallint(6) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,
	l10n_state text,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_ulrichproducts_domain_model_contact'
#
CREATE TABLE tx_ulrichproducts_domain_model_contact (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	position varchar(255) DEFAULT '' NOT NULL,
	phone varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	media int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state smallint(6) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)

);

#
# Table structure for table 'tx_ulrichproducts_product_product_mm'
#
CREATE TABLE tx_ulrichproducts_product_product_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_ulrichproducts_product_static_countries_mm'
#
CREATE TABLE tx_ulrichproducts_product_static_countries_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_ulrichproducts_product_contact_mm'
#
CREATE TABLE tx_ulrichproducts_product_contact_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_ulrichproducts_product_pages_mm'
#
CREATE TABLE tx_ulrichproducts_product_pages_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid_local,uid_foreign),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
