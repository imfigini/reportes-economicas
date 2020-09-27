{ --Debe existir esta tabla:
CREATE TABLE rep_alumnos_regulares
(
	carrera VARCHAR(5),
	legajo VARCHAR(15),
	anio INTEGER
);
}

DROP FUNCTION 'dba'.sp_rep_alumnos_regulares(INTEGER);

CREATE PROCEDURE "dba".sp_rep_alumnos_regulares(pAnio INTEGER DEFAULT NULL)
RETURNING VARCHAR(5), VARCHAR(15), INTEGER;

DEFINE vc_carrera LIKE sga_carreras.nombre;
DEFINE i_anio	INTEGER;
DEFINE es_regular INTEGER;
DEFINE vc_unidad_academica LIKE sga_carreras.unidad_academica;
DEFINE vc_legajo LIKE sga_alumnos.legajo;

BEGIN	

	IF pAnio IS NULL THEN
		DELETE FROM rep_alumnos_regulares;
	ELSE
		DELETE FROM rep_alumnos_regulares WHERE anio = pAnio;
	END IF;

	FOREACH
		SELECT unidad_academica, carrera, legajo
			INTO vc_unidad_academica, vc_carrera, vc_legajo
			FROM sga_alumnos
			WHERE carrera NOT IN (211, 290)	-- Quedan excluidas la LEM y las Extracurriculares
		
		IF pAnio IS NULL THEN
			FOREACH 
				SELECT DISTINCT YEAR(fecha_alta) 
					INTO i_anio
					FROM rep_extensiones_cursada
				BEGIN
					LET es_regular = sp_ctrl_alu_regular(vc_unidad_academica, vc_carrera, i_anio, vc_legajo);
	
					IF es_regular = 1 THEN
						INSERT INTO rep_alumnos_regulares VALUES (vc_carrera, vc_legajo, i_anio);
					END IF;
				END
			END FOREACH; 
			
		ELSE
			LET es_regular = sp_ctrl_alu_regular(vc_unidad_academica, vc_carrera, pAnio, vc_legajo);
	
			IF es_regular = 1 THEN
				INSERT INTO rep_alumnos_regulares VALUES (vc_carrera, vc_legajo, pAnio);
			END IF;
		END IF;

	END FOREACH; 

	INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_alumnos_regulares', CURRENT YEAR TO SECOND);
	
	FOREACH
		SELECT carrera, legajo, anio
			INTO vc_carrera, vc_legajo, i_anio
			FROM rep_alumnos_regulares
		RETURN vc_carrera, vc_legajo, i_anio WITH RESUME; 
	END FOREACH;

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_alumnos_regulares(NULL);