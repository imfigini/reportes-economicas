/**
 * Author:  imfigini
 * Created: 18/09/2017
 */

DROP PROCEDURE 'dba'.sp_rep_alumnos_con_intermedio_sin_titulo();

CREATE PROCEDURE  "dba".sp_rep_alumnos_con_intermedio_sin_titulo()

DEFINE v_legajo LIKE sga_alumnos.legajo;
DEFINE v_carrera LIKE sga_carreras.carrera;
DEFINE v_plan LIKE sga_planes.plan;  
DEFINE v_regular LIKE sga_alumnos.regular;
DEFINE i_materias_sin_aprobar INTEGER;
DEFINE d_fecha_ingreso DATE;
DEFINE d_ult_final DATE;
DEFINE d_ult_cursada DATE;
DEFINE d_ultima_actividad DATE;
DEFINE v_mail LIKE sga_datos_censales.e_mail;
DEFINE v_alumno varchar(100);
DEFINE v_nombre_carrera LIKE sga_carreras.nombre;
DEFINE i_tiene_tesis INTEGER;

BEGIN
{
DROP TABLE rep_alumnos_con_intermedio_sin_titulo;
CREATE TABLE rep_alumnos_con_intermedio_sin_titulo
(
	legajo VARCHAR(15),
	alumno VARCHAR(100),
	e_mail VARCHAR(50),
	carrera VARCHAR(5),
	nombre_carrera VARCHAR(255),
	plan VARCHAR(5),
	regular VARCHAR(1),
	fecha_ingreso DATE,
	fecha_ultima_actividad DATE
);
}
DELETE FROM rep_alumnos_con_intermedio_sin_titulo;

--SET DEBUG FILE TO '/tmp/sp_rep_alumnos_con_intermedio_sin_titulo.txt';
--TRACE ON;

--Selecciono las materias que corresponden a los títulos intermedios de LTA Y Sistemas
SELECT DISTINCT sga_materias_ciclo.materia, sga_ciclos_orient.carrera, sga_ciclos_orient.plan
			FROM sga_materias_ciclo,
				sga_ciclos_orient,
				sga_titulos_plan, 
				sga_titulos				
			WHERE sga_titulos_plan.carrera IN (206,209)
			AND sga_titulos_plan.plan <> '1988'
			AND sga_titulos_plan. titulo = sga_titulos.titulo
			AND sga_titulos.nivel = "INTERMEDIO"
			AND sga_ciclos_orient.titulo = sga_titulos_plan. titulo
			AND sga_ciclos_orient.plan= sga_titulos_plan.plan
			AND sga_ciclos_orient.ciclo = sga_materias_ciclo.ciclo
INTO TEMP materias_intermedio;

FOREACH
--Selecciono los alumnos activos que de las carreras LTA Y Sistemas que aún no tienen el título intermedio otorgado
	SELECT legajo, carrera, plan, regular
		INTO v_legajo, v_carrera, v_plan, v_regular
	FROM sga_alumnos 
	WHERE carrera IN (206, 209)
	AND plan <> '1988'
	AND calidad = 'A'
	AND legajo||carrera NOT IN
		(SELECT legajo||carrera 
			FROM sga_titulos_otorg	
			WHERE titulo IN (SELECT titulo FROM sga_titulos WHERE nivel = 'INTERMEDIO'))

	--Selecciono las materias aprobadas para cada alumno, carrera, plan
	SELECT materia, carrera, plan 
	FROM vw_hist_academica 
		WHERE legajo = v_legajo
		AND carrera = v_carrera
		AND plan = v_plan
		AND resultado = 'A'
	INTO TEMP materias_aprobadas;

        -- Si ya dio la tesis se descarta
        -- Las materias de trabajo final son:
	-- 0205	Proyecto Final --> Sistemas
	-- 0210	Proyecto Final --> LTA
        SELECT COUNT(*) 
		INTO i_tiene_tesis
        FROM materias_aprobadas WHERE materia IN ('0205', '0210');

        IF (i_tiene_tesis = 0) THEN
        
		--Cuento las materias del titulo intermedio que aún no tiene aprobadas
            	SELECT COUNT(*) 
			INTO i_materias_sin_aprobar
		FROM materias_intermedio
			WHERE materia||carrera||plan NOT IN (SELECT materia||carrera||plan FROM materias_aprobadas)
			AND carrera = v_carrera
			AND plan = v_plan;

		--Si no le falta aprobar ninguna materia del titulo intermedio
		IF (i_materias_sin_aprobar = 0) THEN
		
			SELECT fecha_ingreso
				INTO d_fecha_ingreso
			FROM sga_alumnos
				WHERE legajo = v_legajo AND carrera = v_carrera;

			SELECT MAX(fecha)
				INTO d_ult_final
			FROM vw_hist_academica
                            WHERE legajo = v_legajo AND carrera = v_carrera;

			SELECT MAX(fecha_regularidad)
				INTO d_ult_cursada
			FROM sga_cursadas
				WHERE legajo = v_legajo AND carrera = v_carrera AND resultado <> 'U';

			IF d_ult_final > d_ult_cursada THEN
				LET d_ultima_actividad = d_ult_final;
			ELSE
				LET d_ultima_actividad = d_ult_cursada;
			END IF;

			SELECT e_mail
				INTO v_mail
			FROM vw_datos_censales_actuales 
				WHERE nro_inscripcion = v_legajo;

			SELECT apellido || ', ' || nombres
				INTO v_alumno
			FROM sga_personas
				WHERE nro_inscripcion = v_legajo;

			SELECT nombre
				INTO v_nombre_carrera
			FROM sga_carreras 
				WHERE carrera = v_carrera;

			INSERT INTO rep_alumnos_con_intermedio_sin_titulo VALUES 
					(v_legajo, v_alumno, v_mail, v_carrera, v_nombre_carrera, v_plan, v_regular, d_fecha_ingreso, d_ultima_actividad);
		END IF;

	END IF;
        DROP TABLE materias_aprobadas;
END FOREACH;

DROP TABLE materias_intermedio;

INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_alumnos_con_intermedio_sin_titulo', CURRENT YEAR TO SECOND);

--TRACE OFF;

END;	

END PROCEDURE;

--DROP TABLE materias_intermedio;
--EXECUTE PROCEDURE  "dba".sp_rep_alumnos_con_intermedio_sin_titulo();
--SELECT * FROM rep_alumnos_con_intermedio_sin_titulo;
