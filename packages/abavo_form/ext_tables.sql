#
# Table structure for table 'tx_abavoform_domain_model_form'
#
CREATE TABLE tx_abavoform_domain_model_form (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	unique_id varchar(255) DEFAULT '' NOT NULL,

	salutation varchar(255) DEFAULT '' NOT NULL,
	firstname varchar(255) DEFAULT '' NOT NULL,
	lastname varchar(255) DEFAULT '' NOT NULL,
	phone varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	company varchar(255) DEFAULT '' NOT NULL,
	address varchar(255) DEFAULT '' NOT NULL,
	zip varchar(255) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	country int(11) DEFAULT '0' NOT NULL,
	country_zone int(11) DEFAULT '0' NOT NULL,
	description text NOT NULL,
	media blob,
	privacyhint tinyint(4) unsigned DEFAULT '0' NOT NULL,

	datetime datetime default NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	UNIQUE KEY unique_id (unique_id)

) ENGINE=MyISAM;