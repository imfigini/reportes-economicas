/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  imfigini
 * Created: 23/10/2018
 */

DROP FUNCTION 'dba'.sp_rep_cant_materias_faltan();

--SP para el sistema de Reportes 

CREATE PROCEDURE  "dba".sp_rep_cant_materias_faltan()
RETURNING      
	varchar(15),	--legajo                              
	varchar(100), 	--alumno (apellido y nombres)
	varchar(255),	--nombre carrera
	varchar(5), 	--plan del alumno en esa carrera
	integer		--cantidad materias le faltan para egresar

DEFINE v_legajo LIKE sga_alumnos.legajo;
DEFINE v_alumno varchar(100);
DEFINE v_carrera LIKE sga_carreras.carrera;
DEFINE v_nombre_carrera LIKE sga_carreras.nombre;
DEFINE v_plan LIKE sga_planes.plan;  
DEFINE v_unidad_academica LIKE sga_carreras.unidad_academica; 
DEFINE i_cant_materias_faltan INTEGER;
DEFINE d_fecha_ingreso DATE;
DEFINE d_ultima_actividad DATE;
DEFINE d_ult_final DATE;
DEFINE d_ult_cursada DATE;
DEFINE i_cant_materias_regu_faltan		INTEGER;
DEFINE i_cant_materias_opt_faltan		INTEGER;

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
DROP TABLE rep_cant_materias_faltan;
CREATE TABLE rep_cant_materias_faltan
(
	legajo VARCHAR(15),
	alumno VARCHAR(100),
	carrera VARCHAR(5),
	nombre_carrera VARCHAR(255),
	plan VARCHAR(5),
	cant_materias_faltan INTEGER,
	cant_materias_regu_faltan INTEGER,
	cant_materias_opt_faltan INTEGER,
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
	
	EXECUTE PROCEDURE sp_rep_cant_materias_faltan(v_unidad_academica, v_carrera, v_legajo)
		INTO i_cant_materias_faltan, i_cant_materias_regu_faltan, i_cant_materias_opt_faltan;
	
	INSERT INTO rep_cant_materias_faltan VALUES (
		v_legajo, v_alumno, v_carrera, v_nombre_carrera, v_plan, i_cant_materias_faltan, i_cant_materias_regu_faltan, i_cant_materias_opt_faltan, d_fecha_ingreso, d_ultima_actividad);
END FOREACH;

DROP TABLE alu_en_carrera;

INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_cant_materias_faltan', CURRENT YEAR TO SECOND);

END;	

END PROCEDURE;

{
EXECUTE PROCEDURE  "dba".sp_rep_cant_materias_faltan();
SELECT * FROM rep_cant_materias_faltan;
}