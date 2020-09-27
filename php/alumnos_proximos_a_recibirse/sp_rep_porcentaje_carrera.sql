--DROP FUNCTION 'dba'.sp_alumnos_prox_recibirse();

DROP PROCEDURE 'dba'.sp_rep_porcentaje_carrera();
--SP para el sistema de Reportes (Actualiza la tabla rep_porcentaje_carrera con el porcentaje de avance de carrera de los alumnos)

CREATE PROCEDURE  "dba".sp_rep_porcentaje_carrera()
RETURNING      
	varchar(15),	--legajo                              
	varchar(100), 	--alumno (apellido y nombres)
	varchar(255),	--nombre carrera
	varchar(5), 	--plan del alumno en esa carrera
	float		--porcentaje aprobado de la carrera

DEFINE v_legajo LIKE sga_alumnos.legajo;
DEFINE v_alumno varchar(100);
DEFINE v_carrera LIKE sga_carreras.carrera;
DEFINE v_nombre_carrera LIKE sga_carreras.nombre;
DEFINE v_plan LIKE sga_planes.plan;  
DEFINE v_porcentaje FLOAT;
DEFINE v_unidad_academica LIKE sga_carreras.unidad_academica; 
DEFINE i_solo_falta_tesis INTEGER;
DEFINE d_fecha_ingreso DATE;
DEFINE d_ultima_actividad DATE;
DEFINE d_ult_final DATE;
DEFINE d_ult_cursada DATE;

BEGIN

--DROP TABLE alu_en_carrera;
CREATE TABLE alu_en_carrera
(
	unidad_academica VARCHAR(5),
	legajo VARCHAR(15),
	alumno VARCHAR(100),
	carrera VARCHAR(5),
	nombre_carrera VARCHAR(255),
	plan VARCHAR(5)
);
CREATE INDEX idx_alu_en_carrera ON alu_en_carrera(alumno);

--Selecciono los alumnos regulares y activos
INSERT INTO alu_en_carrera 
SELECT DISTINCT A.unidad_academica, A.legajo, P.apellido || ', ' || P.nombres AS alumno, A.carrera, C.nombre_reducido AS nombre_carrera, A.plan
	FROM sga_alumnos A 
	JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
	JOIN sga_carreras C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
	WHERE A.calidad = 'A' AND A.regular = 'S'
	AND A.carrera <> 290;	--Cursos Extracurriculares

--Ordeno la tabla auxiliar según el apellido y nombres del alumno
ALTER INDEX idx_alu_en_carrera TO CLUSTER;

--Para cada alumno consulto el porcentaje de avance en la carrera y lo inserta en la tabla rep_porcentaje_carrera
DROP TABLE rep_porcentaje_carrera;
CREATE TABLE rep_porcentaje_carrera
(
	legajo VARCHAR(15),
	alumno VARCHAR(100),
	carrera VARCHAR(5),
	nombre_carrera VARCHAR(255),
	plan VARCHAR(5),
	porcentaje FLOAT,
	solo_falta_tesis INTEGER,
	fecha_ingreso DATE,
	fecha_ultima_actividad DATE
);

FOREACH SELECT unidad_academica, legajo, alumno, carrera, nombre_carrera, plan
        INTO v_unidad_academica, v_legajo, v_alumno, v_carrera, v_nombre_carrera, v_plan
	FROM alu_en_carrera
	
	SELECT fecha_ingreso
		INTO d_fecha_ingreso
	FROM sga_alumnos
		WHERE legajo = v_legajo AND unidad_academica = v_unidad_academica AND carrera = v_carrera;
		
	SELECT MAX(fecha)
	INTO d_ult_final
	FROM vw_hist_academica
		WHERE legajo = v_legajo AND unidad_academica = v_unidad_academica AND carrera = v_carrera;
	
	SELECT MAX(fecha_regularidad)
	INTO d_ult_cursada
	FROM sga_cursadas
		WHERE legajo = v_legajo AND unidad_academica = v_unidad_academica AND carrera = v_carrera AND resultado <> 'U';
		
	IF d_ult_final > d_ult_cursada THEN
		LET d_ultima_actividad = d_ult_final;
	ELSE
		LET d_ultima_actividad = d_ult_cursada;
	END IF;
	
	LET v_porcentaje = sp_porc_exa(v_unidad_academica, v_carrera, v_legajo);
	LET i_solo_falta_tesis = sp_solo_falta_tesis(v_unidad_academica, v_carrera, v_legajo);
	
	INSERT INTO rep_porcentaje_carrera VALUES (
		v_legajo, v_alumno, v_carrera, v_nombre_carrera, v_plan, v_porcentaje, i_solo_falta_tesis, d_fecha_ingreso, d_ultima_actividad);
END FOREACH;

DROP TABLE alu_en_carrera;

INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_porcentaje_carrera', CURRENT YEAR TO SECOND);

END;	

END PROCEDURE;


--EXECUTE PROCEDURE "dba".sp_rep_porcentaje_carrera();