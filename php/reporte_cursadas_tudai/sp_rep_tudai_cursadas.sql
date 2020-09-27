{
	DROP TABLE "dba".rep_tudai_cursadas;
	CREATE TABLE "dba".rep_tudai_cursadas
	(
		legajo VARCHAR(15),
		fecha_ingreso DATE,
		nombre_materia VARCHAR(255),
		anio_de_cursada INTEGER,
		resultado VARCHAR(1),
		fecha_regularidad DATE,
		origen VARCHAR(1)
	);
}

DROP PROCEDURE 'dba'.sp_rep_tudai_cursadas();

CREATE PROCEDURE "dba".sp_rep_tudai_cursadas()

BEGIN

DELETE FROM "dba".rep_tudai_cursadas WHERE 1=1;

--Selecciono los alumnos regulares y activos
INSERT INTO "dba".rep_tudai_cursadas 
SELECT 	A.legajo, 
	A.fecha_ingreso,
	M.nombre_materia,
	M.anio_de_cursada,
	C.resultado,
	C.fecha_regularidad,
	C.origen
FROM 	sga_alumnos A
	JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion AND A.carrera = 213 AND A.regular = 'S' AND A.calidad = 'A')
	JOIN sga_cursadas C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera AND A.legajo = C.legajo AND A.carrera = 213)
	JOIN sga_atrib_mat_plan M ON (M.unidad_academica = C.unidad_academica AND M.carrera = C.carrera AND M.plan = C.plan AND M.version = C.version AND M.materia = C.materia)
	WHERE 	A.carrera = 213;

INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_tudai_cursadas', CURRENT YEAR TO SECOND);

END;	

END PROCEDURE;

EXECUTE PROCEDURE "dba".sp_rep_tudai_cursadas();

SELECT * FROM rep_fecha_actualiz_tablas;