{
	DROP TABLE "dba".rep_sistemas_finales;
	CREATE TABLE "dba".rep_sistemas_finales
	(
		legajo VARCHAR(15),
		plan VARCHAR(5),
		fecha_ingreso DATE,
		nombre_materia VARCHAR(255),
		anio_de_cursada INTEGER,
		resultado VARCHAR(1),
		fecha DATE,
		forma_aprobacion VARCHAR(25),
		departamento VARCHAR(5)
	);
}

DROP PROCEDURE 'dba'.sp_rep_sistemas_finales();

CREATE PROCEDURE  "dba".sp_rep_sistemas_finales()

BEGIN

DELETE FROM "dba".rep_sistemas_finales WHERE 1=1;

--Selecciono los alumnos regulares y activos
INSERT INTO "dba".rep_sistemas_finales 
SELECT 	A.legajo, 
	A.plan,
	A.fecha_ingreso,
	AMP.nombre_materia,
	AMP.anio_de_cursada,
	C.resultado,
	C.fecha,
	C.forma_aprobacion,
	M.departamento
FROM 	sga_alumnos A
	JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion AND A.carrera = 206 AND A.plan <> '1988' AND A.regular = 'S' AND A.calidad = 'A')
	JOIN vw_hist_academica C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera AND A.legajo = C.legajo AND A.carrera = 206 AND C.plan = A.plan AND YEAR(C.fecha) >= 2004)
	JOIN sga_materias M ON (M.unidad_academica = C.unidad_academica AND M.materia = C.materia)
	JOIN sga_atrib_mat_plan AMP ON (AMP.unidad_academica = C.unidad_academica AND AMP.carrera = C.carrera AND AMP.plan = C.plan AND AMP.version = C.version AND AMP.materia = C.materia AND AMP.plan = C.plan);
	
INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_sistemas_finales', CURRENT YEAR TO SECOND);

END;	

END PROCEDURE;

EXECUTE PROCEDURE "dba".sp_rep_sistemas_finales();

SELECT * FROM rep_fecha_actualiz_tablas;
SELECT * FROM rep_sistemas_finales;

