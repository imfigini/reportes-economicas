DROP PROCEDURE 'dba'.sp_alumnos_cohorte();

--Almacena en rep_alumnos_cohorte todos los alumnos de cohorte que ingresaron a partir del año 2004.
--EXECUTE PROCEDURE "dba".sp_alumnos_cohorte();

CREATE PROCEDURE  "dba".sp_alumnos_cohorte()

DEFINE v_legajo LIKE sga_alumnos.legajo;
DEFINE v_alumno varchar(100);
DEFINE v_carrera LIKE sga_carreras.carrera;
DEFINE v_nombre_carrera LIKE sga_carreras.nombre;
DEFINE v_plan LIKE sga_planes.plan;  
DEFINE i_es_cohorte INTEGER;
DEFINE i_anio_ingreso INTEGER;

BEGIN

--Selecciono los alumnos, sus carreras y año de ingreso
SELECT DISTINCT A.legajo, P.apellido || ', ' || P.nombres AS alumno, I.anio_academico AS anio_ingreso, A.carrera, C.nombre_reducido AS nombre_carrera, A.plan
	FROM sga_alumnos A 
	JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
	JOIN sga_carreras C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
	JOIN sga_carrera_aspira ASP ON (A.unidad_academica = ASP.unidad_academica AND A.nro_inscripcion = ASP.nro_inscripcion AND A.carrera = ASP.carrera)
	JOIN sga_periodo_insc I ON (ASP.periodo_inscripcio = I.periodo_inscripcio)
	WHERE A.carrera <> 290	--Cursos Extracurriculares
		AND I.anio_academico >= 2010
INTO TEMP alu_en_carrera WITH NO LOG;

--Para cada alumno consulto si es de cohorte en esa carrera, y lo inserta en la tabla rep_alumnos_cohorte

DELETE FROM rep_alumnos_cohorte;
	
FOREACH SELECT legajo, alumno, carrera, nombre_carrera, plan, anio_ingreso
        INTO v_legajo, v_alumno, v_carrera, v_nombre_carrera, v_plan, i_anio_ingreso
	FROM alu_en_carrera
	
	LET i_es_cohorte = sp_alu_cohorte(v_legajo, v_carrera);
	
	IF (i_es_cohorte == 1) THEN
		INSERT INTO rep_alumnos_cohorte VALUES (
				v_legajo, v_alumno, v_carrera, v_nombre_carrera, v_plan, i_anio_ingreso);
	END IF;
END FOREACH;

INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_alumnos_cohorte', CURRENT YEAR TO SECOND);

DROP TABLE alu_en_carrera;

END;	

END PROCEDURE;


{
--Tabla que debe existir antes:
CREATE TABLE rep_alumnos_cohorte
(
	legajo VARCHAR(15),
	alumno VARCHAR(100),
	carrera VARCHAR(5),
	nombre_carrera VARCHAR(255),
	plan VARCHAR(5),
	anio_ingreso INTEGER
);
}