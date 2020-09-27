--DROP PROCEDURE 'dba'.sp_rep_alu_carreras();
--SP para el sistema de Reportes (Actualiza la tabla rep_alumno_carreras en la cual se lleva un registro por cada alumnos y sus respectivas carreras)

CREATE PROCEDURE  "dba".sp_rep_alu_carreras()
RETURNING      
	DATETIME YEAR to FRACTION	--fecha en que se corre este SP  

DEFINE v_legajo LIKE sga_alumnos.legajo;
DEFINE v_carrera LIKE sga_carreras.carrera;
DEFINE v_fecha_actualizacion DATETIME YEAR to FRACTION;

BEGIN

DROP TABLE rep_alumno_carreras;
CREATE TABLE rep_alumno_carreras
(
	legajo VARCHAR(15),
	carrera_1 VARCHAR(5),
	carrera_2 VARCHAR(5),
	carrera_3 VARCHAR(5),
	carrera_4 VARCHAR(5),
	carrera_5 VARCHAR(5)
);

FOREACH SELECT DISTINCT legajo
        INTO v_legajo
	FROM sga_alumnos
		WHERE regular = 'S'
		AND calidad = 'A'
		AND carrera <> 290	--Descarto Cursos Extracurriculares
	
	INSERT INTO rep_alumno_carreras (legajo) VALUES (v_legajo);
	
	FOREACH SELECT DISTINCT carrera
			INTO v_carrera
		FROM sga_alumnos
			WHERE legajo = v_legajo
			AND regular = 'S'
			AND calidad = 'A'
			AND carrera <> 290	--Descarto Cursos Extracurriculares
		
		if ( (SELECT COUNT(carrera_1) FROM rep_alumno_carreras WHERE legajo = v_legajo) == 0) then
			UPDATE rep_alumno_carreras SET carrera_1 = v_carrera WHERE legajo = v_legajo;
		else 
			if ( (SELECT COUNT(carrera_2) FROM rep_alumno_carreras WHERE legajo = v_legajo) == 0) then
				UPDATE rep_alumno_carreras SET carrera_2 = v_carrera WHERE legajo = v_legajo;
			else
				if ( (SELECT COUNT(carrera_3) FROM rep_alumno_carreras WHERE legajo = v_legajo) == 0) then
					UPDATE rep_alumno_carreras SET carrera_3 = v_carrera WHERE legajo = v_legajo;
				else
					if ( (SELECT COUNT(carrera_4) FROM rep_alumno_carreras WHERE legajo = v_legajo) == 0) then
						UPDATE rep_alumno_carreras SET carrera_4 = v_carrera WHERE legajo = v_legajo;
					else
						if ( (SELECT COUNT(carrera_5) FROM rep_alumno_carreras WHERE legajo = v_legajo) == 0) then
							UPDATE rep_alumno_carreras SET carrera_5 = v_carrera WHERE legajo = v_legajo;
						end if;
					end if;
				end if;
			end if;
		end if;
	
	END FOREACH;
	
END FOREACH;

SELECT CURRENT 
	INTO v_fecha_actualizacion
	FROM systables WHERE tabid == 1;

INSERT INTO rep_fecha_ultima_actualiz VALUES ('rep_alumno_carreras', v_fecha_actualizacion);
RETURN v_fecha_actualizacion;

END;	

END PROCEDURE;

--EXECUTE PROCEDURE sp_rep_alu_carreras();
--SELECT * FROM rep_alumno_carreras;
--SELECT * FROM rep_fecha_ultima_actualiz;

/*
DROP TABLE rep_fecha_ultima_actualiz;
CREATE TABLE rep_fecha_ultima_actualiz
(
	tabname VARCHAR(50),
	fecha_actualiz DATETIME YEAR to FRACTION
);
*/
