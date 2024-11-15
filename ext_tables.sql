CREATE TABLE tx_news_tagsuggest_item (
		 uid int(11) NOT NULL auto_increment,
		 crdate int(11) DEFAULT '0' NOT NULL,
		 userid int(11) DEFAULT '0' NOT NULL,
		 title tinytext,

		 PRIMARY KEY (uid)
);
