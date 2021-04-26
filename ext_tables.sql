#
# Table structure for table 'sys_file_reference'
# which is one usage of a file with overloaded metadata
#
CREATE TABLE sys_file_reference
(
    `mute`           tinyint(4)   DEFAULT '0' NOT NULL,
    `showinfo`       tinyint(4)   DEFAULT '0' NOT NULL,
    `controls`       tinyint(4)   DEFAULT '0' NOT NULL,
    `loop`           tinyint(4)   DEFAULT '0' NOT NULL,

    `track_language` varchar(30)  DEFAULT ''  NOT NULL,
    `track_type`     varchar(30)  DEFAULT ''  NOT NULL,
    `track_label`    varchar(255) DEFAULT ''  NOT NULL,
    `track_default`  tinyint(4)   DEFAULT '0' NOT NULL
);
#
# Table structure for table 'sys_file_metadata'
#
CREATE TABLE sys_file_metadata
(
    tracks int(11) DEFAULT '0' NOT NULL
);