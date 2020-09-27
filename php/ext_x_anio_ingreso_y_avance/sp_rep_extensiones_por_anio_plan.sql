--Alumnos que pidieron extensiones de materias de 1º/2º/3º etc año (por año de ingreso, y % avance)
DROP PROCEDURE "dba".sp_rep_extensiones_por_anio_plan();

CREATE PROCEDURE "dba".sp_rep_extensiones_por_anio_plan()
	RETURNING	INTEGER, 
			VARCHAR(255), 
			VARCHAR(15), 
			INTEGER, 
			INTEGER, 
			FLOAT(5);

DEFINE i_anio_de_cursada INTEGER;
DEFINE vc_nombre_carrera VARCHAR(255);
DEFINE i_cant_materias INTEGER;
DEFINE vc_legajo VARCHAR(15);
DEFINE i_anio_ingreso INTEGER;
DEFINE f_porcentaje FLOAT(5);

BEGIN	
	LET i_anio_de_cursada = 0;
	LET vc_nombre_carrera = NULL;
	LET i_cant_materias = 0;
	LET vc_legajo = NULL;
	LET i_anio_ingreso = 0;
	LET f_porcentaje = 0;

	CREATE CLUSTER INDEX idx_1 ON rep_porcentaje_carrera(carrera, legajo);
	CREATE CLUSTER INDEX idx_2 ON rep_extensiones_cursada(carrera, legajo);

	-----------Calculo a qué año pertenece cada materia--------------------------------------------------------------
	SELECT DISTINCT R.carrera, R.materia, NVL(P.anio_de_cursada, 99) AS anio_de_cursada
		FROM rep_extensiones_cursada R, sga_atrib_mat_plan P
		WHERE R.carrera = P.carrera
			AND R.materia = P.materia
	GROUP BY R.carrera, R.materia, P.anio_de_cursada
	INTO TEMP aux_materia_anio;
	-----------------------------------------------------------------------------------------------------------------
	
	--------------Me quedo con las extensiones que pidio cada alumno sin repetidos y recupero el año de ingreso------
	SELECT DISTINCT R.carrera, R.materia, R.legajo, P.anio_academico AS anio_ingreso
		FROM rep_extensiones_cursada R, sga_carrera_aspira C, sga_periodo_insc P
		WHERE 	R.carrera = C.carrera
			AND R.legajo = C.nro_inscripcion
			AND C.periodo_inscripcio = P.periodo_inscripcio
	GROUP BY R.carrera, R.materia, R.legajo, P.anio_academico
	INTO TEMP aux_extensiones_cursada;
	-----------------------------------------------------------------------------------------------------------------

	SELECT DISTINCT A.anio_de_cursada, R2.nombre_carrera, R.legajo, COUNT(R.materia) AS cant_materias, R.anio_ingreso, NVL(R2.porcentaje,'')::FLOAT(5) AS porcentaje
		FROM aux_materia_anio A, aux_extensiones_cursada R, rep_porcentaje_carrera R2
		WHERE R.carrera = A.carrera 
			AND R.materia = A.materia
			AND R.carrera = R2.carrera AND R.legajo = R2.legajo
	GROUP BY 1,2,3,5,6
	INTO TEMP aux1;

	CREATE CLUSTER INDEX idx_3 ON aux1(anio_de_cursada, nombre_carrera);

	FOREACH	
		SELECT anio_de_cursada, nombre_carrera, legajo, cant_materias, anio_ingreso, porcentaje
			INTO i_anio_de_cursada, vc_nombre_carrera, vc_legajo, i_cant_materias, i_anio_ingreso, f_porcentaje
		FROM aux1
	
		RETURN  i_anio_de_cursada, vc_nombre_carrera, vc_legajo, i_cant_materias, i_anio_ingreso, f_porcentaje WITH RESUME; 
	END FOREACH;

	DROP INDEX idx_1;
	DROP INDEX idx_2;
	DROP INDEX idx_3;
	DROP TABLE aux_materia_anio;
	DROP TABLE aux1;
	DROP TABLE aux_extensiones_cursada;
END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_extensiones_por_anio_plan();
