#
# Table structure for table 'tx_abavosearch_domain_model_indexer'
#
CREATE TABLE tx_abavosearch_domain_model_indexer (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	storagepid int(11) DEFAULT '0' NOT NULL,
	target varchar(255) DEFAULT '' NOT NULL,
	categories varchar(255) DEFAULT '' NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL,
	locale text NOT NULL,
	config text NOT NULL,
	priority int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

) ENGINE=MyISAM;

#
# Table structure for table 'tx_abavosearch_domain_model_index'
#
CREATE TABLE tx_abavosearch_domain_model_index (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	indexer int(11) DEFAULT '0' NOT NULL,
	refid VARCHAR(255) DEFAULT '' NOT NULL,
	title tinytext NOT NULL,
	content mediumtext NOT NULL,
	abstract text,
	params text,
	target VARCHAR(255) DEFAULT '' NOT NULL,
	fegroup VARCHAR(100) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	categories varchar(255) DEFAULT '' NOT NULL,
	ranking int(11) DEFAULT '0' NOT NULL,

	datetime datetime default NULL,

	FULLTEXT INDEX title (title),
	FULLTEXT INDEX fullcontent (content,abstract),

	PRIMARY KEY (uid),
	KEY parent (pid)

) ENGINE=MyISAM;

#
# Table structure for table 'tx_abavosearch_domain_model_term'
#
CREATE TABLE tx_abavosearch_domain_model_term (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	refid VARCHAR(255) DEFAULT '' NOT NULL,
	search tinytext NOT NULL,
	fegroup varchar(100) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,

	FULLTEXT INDEX terms (search),

	PRIMARY KEY (uid),
	KEY parent (pid)

) ENGINE=MyISAM;

#
# Table structure for table 'tx_abavosearch_domain_model_synonym'
#
CREATE TABLE tx_abavosearch_domain_model_synonym (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title tinytext NOT NULL,
	alt tinytext NOT NULL,
	synonym text NOT NULL,

	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)

) ENGINE=MyISAM;


#
# Table structure for table 'tx_abavosearch_domain_model_stat'
#
CREATE TABLE tx_abavosearch_domain_model_stat (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	refid VARCHAR(255) DEFAULT '' NOT NULL,
	`type` enum('expression','term','record') DEFAULT NULL,
	val VARCHAR(255) DEFAULT '' NOT NULL,
	hits int(11) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

) ENGINE=MyISAM;


#
# Table structure for table 'sys_file_metadata'
#
CREATE TABLE sys_file_metadata (

	tx_abavosearch_indexer int(11) DEFAULT NULL,
	tx_abavosearch_index_tstamp int(11) unsigned default NULL

);