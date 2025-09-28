ALTER TABLE llx_user_session ADD sessionid varchar(150) NOT NULL after nro_ip;
ALTER TABLE llx_user_session ADD ccode varchar(20) NULL after csession;
ALTER TABLE llx_user_session ADD dateu datetime NOT NULL after datec;