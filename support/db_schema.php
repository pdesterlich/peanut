<?php

	$description = "statistiche accesso";

	$migrations = array(
		"CREATE TABLE {table_prefix}stats_connections (
		 id                 INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
		 , unique_id        CHAR(32),
		 , connection_date  DATE,
		 , ip_address       VARCHAR(50),
		 , start_time       DATETIME,
		 , end_time         DATETIME,
		 , time_spent       TIME,
		 , browser_family   VARCHAR(50),
		 , browser_major    VARCHAR(50),
		 , browser_minor    VARCHAR(50),
		 , browser_full     VARCHAR(255),
		 , os_family        VARCHAR(50),
		 , os_major         VARCHAR(50),
		 , os_minor         VARCHAR(50),
		 , os_build         VARCHAR(50),
		 , os_full          VARCHAR(255),
		 , is_mobile        SMALLINT DEFAULT 0,
		 , is_mobile_device SMALLINT DEFAULT 0,
		 , is_tablet        SMALLINT DEFAULT 0,
		 , is_spider        SMALLINT DEFAULT 0,
		 , is_computer      SMALLINT DEFAULT 0
		)",
		"CREATE INDEX unique_id_idx ON {table_prefix}stats_connections(unique_id)",
		"CREATE INDEX connection_date_idx ON {table_prefix}stats_connections(connection_date)",

		"CREATE TABLE {table_prefix}stats_connections_details (
		id               INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
		, connection_id  INTEGER DEFAULT 0,
		, detail_date    DATETIME,
		, request        VARCHAR(255),
		, request_method VARCHAR(50),
		)",
		"CREATE INDEX connection_id_idx ON stats_connections_details(connection_id)",
		);

?>