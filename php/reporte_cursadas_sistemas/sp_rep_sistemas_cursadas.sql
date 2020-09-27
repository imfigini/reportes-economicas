{
	DROP TABLE "dba".rep_sistemas_cursadas;
	CREATE TABLE "dba".rep_sistemas_cursadas
	(
		legajo VARCHAR(15),
		plan VARCHAR(5),
		fecha_ingreso DATE,
		nombre_materia VARCHAR(255),
		departamento VARCHAR(5),
		anio_de_cursada INTEGER,
		resultado VARCHAR(1),
		fecha_regularidad DATE,
		origen VARCHAR(1)
	);
}

DROP PROCEDURE 'dba'.sp_rep_sistemas_cursadas();

--Almacena en una tabla interedia para consultas eficientes, la información de los alumnos y sus cursadas, a partir que las regularizaron en 2004.
CREATE PROCEDURE  "dba".sp_rep_sistemas_cursadas()
BEGIN

DELETE FROM "dba".rep_sistemas_cursadas WHERE 1=1;

--Selecciono los alumnos regulares y activos
INSERT INTO "dba".rep_sistemas_cursadas 
SELECT 	A.legajo, 
	A.plan,
	A.fecha_ingreso,
	AMP.nombre_materia,
	M.departamento,
	AMP.anio_de_cursada,
	C.resultado,
	C.fecha_regularidad,
	C.origen

FROM 	sga_alumnos A
	JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion AND A.carrera = 206 AND A.plan <> '1988' AND A.regular = 'S' AND A.calidad = 'A')
	JOIN sga_cursadas C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera AND A.legajo = C.legajo AND A.plan = C.plan AND YEAR(C.fecha_regularidad) >= 2004)
	JOIN sga_materias M ON (M.unidad_academica = C.unidad_academica AND M.materia = C.materia)
	JOIN sga_atrib_mat_plan AMP ON (AMP.unidad_academica = C.unidad_academica AND AMP.carrera = C.carrera AND AMP.plan = C.plan AND AMP.version = C.version AND AMP.materia = C.materia AND AMP.plan = C.plan);

INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_sistemas_cursadas', CURRENT YEAR TO SECOND);

END;	

END PROCEDURE;

EXECUTE PROCEDURE  "dba".sp_rep_sistemas_cursadas();
SELECT * FROM rep_sistemas_cursadas;
