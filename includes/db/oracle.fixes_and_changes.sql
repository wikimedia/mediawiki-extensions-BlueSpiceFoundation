alter table /*$wgDBprefix*/RECENTCHANGES modify ("RC_CUR_ID" NUMBER NULL);
alter table /*$wgDBprefix*/INTERWIKI modify ("IW_API" BLOB NULL);
alter table /*$wgDBprefix*/ARCHIVE modify ("ar_user" NUMBER NULL);
/* just use on mediawiki before 1.17.1 /*
alter table /*$wgDBprefix*/CATEGORYLINKS modify ("CL_SORTKEY_PREFIX" VARCHAR2(255) NULL);

/*$mw$*/
CREATE OR REPLACE TRIGGER fa_id_inc
BEFORE INSERT ON /*$wgDBprefix*/filearchive
FOR EACH ROW
BEGIN
	SELECT filearchive_fa_id_seq.nextval INTO :NEW.fa_id FROM dual;
END;
/*$mw$*/