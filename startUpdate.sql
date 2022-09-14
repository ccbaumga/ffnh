DROP PROCEDURE IF EXISTS StartUpdate;
DELIMITER $$
CREATE PROCEDURE StartUpdate()
BEGIN
DECLARE rowcountt INTEGER DEFAULT 0;
update globals set lastscoreupdate = CURRENT_TIMESTAMP(), updateinprocess = true 
where updateinprocess = false and (lastscoreupdate is null or ADDTIME(lastscoreupdate, updatetimedelta) < CURRENT_TIMESTAMP());
select ROW_COUNT() INTO rowcountt;
IF rowcountt = 0 THEN
	select false;
END IF;
IF rowcountt = 1 THEN
	select true;
END IF;
END$$
DELIMITER ;