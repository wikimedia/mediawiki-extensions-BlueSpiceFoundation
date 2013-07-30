
CREATE FUNCTION /*$wgDBprefix*/save_settings_function(skey varchar, svalue varchar) RETURNS int AS $$
BEGIN
	IF EXISTS( SELECT * FROM /*$wgDBprefix*/settings WHERE key = skey ) THEN
		UPDATE /*$wgDBprefix*/bs_settings
		SET value = svalue WHERE key = skey;
	ELSE
		INSERT INTO /*$wgDBprefix*/bs_settings VALUES( skey, svalue );
	END IF;
	RETURN;
END;
$$ LANGUAGE plpgsql;
