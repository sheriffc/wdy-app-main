UPDATE person
SET date_of_birth = DATE_ADD(DATE_ADD(date_of_birth, INTERVAL 4 YEAR), INTERVAL 46 DAY)
;

UPDATE teacher
SET pin = pin + 173
;
