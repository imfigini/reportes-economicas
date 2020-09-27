DROP PROCEDURE 'dba'.sp_rep_alumnos_con_cursadas_tit_interm();
--EXECUTE PROCEDURE  "dba".sp_rep_alumnos_con_cursadas_tit_interm();

CREATE PROCEDURE  "dba".sp_rep_alumnos_con_cursadas_tit_interm()

DEFINE v_legajo LIKE sga_alumnos.legajo;
DEFINE v_carrera LIKE sga_carreras.carrera;
DEFINE v_plan LIKE sga_planes.plan;  
DEFINE v_version LIKE sga_planes.version_actual;  
DEFINE i_cant_mat_aprobadas INTEGER;
DEFINE i_cursadas_aprobadas INTEGER;
DEFINE v_mail LIKE sga_datos_censales.e_mail;
DEFINE v_alumno varchar(100);
DEFINE v_nombre_carrera LIKE sga_carreras.nombre;

BEGIN
{
DROP TABLE rep_alumnos_con_cursadas_tit_interm;
CREATE TABLE rep_alumnos_con_cursadas_tit_interm
(
	legajo VARCHAR(15),
	alumno VARCHAR(100),
	e_mail VARCHAR(50),
	carrera VARCHAR(5),
	nombre_carrera VARCHAR(255),
	plan VARCHAR(5)
);
}
DELETE FROM rep_alumnos_con_cursadas_tit_interm;

--SET DEBUG FILE TO '/tmp/sp_rep_alumnos_con_cursadas_tit_interm.txt';
--TRACE ON;

--Cuanto las materias Normales Obligatorias para cada plan
SELECT DISTINCT sga_ciclos_orient.carrera, sga_ciclos_orient.plan, sga_ciclos_orient.version, COUNT(*) AS cant
			FROM sga_materias_ciclo,
				sga_ciclos_orient,
				sga_titulos_plan, 
				sga_titulos,
				sga_atrib_mat_plan				
			WHERE sga_titulos_plan.carrera IN (206,209)
			AND sga_titulos_plan.plan <> '1988'
			AND sga_titulos_plan. titulo = sga_titulos.titulo
			AND sga_titulos.nivel = "INTERMEDIO"
			AND sga_ciclos_orient.titulo = sga_titulos_plan. titulo
			AND sga_ciclos_orient.plan= sga_titulos_plan.plan
			AND sga_ciclos_orient.ciclo = sga_materias_ciclo.ciclo
			AND sga_materias_ciclo.materia = sga_atrib_mat_plan.materia
			AND sga_ciclos_orient.carrera= sga_atrib_mat_plan.carrera
			AND sga_ciclos_orient.plan= sga_atrib_mat_plan.plan
			AND sga_ciclos_orient.version= sga_atrib_mat_plan.version
			AND sga_atrib_mat_plan.tipo_materia IN ('N', 'G')
			AND sga_atrib_mat_plan.obligatoria = 'S'
GROUP BY 1,2,3
INTO TEMP cant_mat_intermedio WITH NO LOG;

--Selecciono las materias que corresponden a los titulos intermedios de LTA Y Sistemas
SELECT DISTINCT sga_materias_ciclo.materia, sga_ciclos_orient.carrera, sga_ciclos_orient.plan, sga_ciclos_orient.version
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
INTO TEMP materias_intermedio WITH NO LOG;

FOREACH
	--Selecciono los alumnos regulares y activos de las carreras LTA Y Sistemas que aun no tienen el titulo intermedio otorgado
	SELECT legajo, carrera
		INTO v_legajo, v_carrera
	FROM sga_alumnos 
	WHERE carrera IN (206, 209)
	AND plan <> '1988'
	AND regular = 'S'
	AND calidad = 'A'
	AND legajo NOT IN
		(SELECT legajo
			FROM sga_titulos_otorg	
			WHERE 	titulo IN (SELECT titulo FROM sga_titulos WHERE nivel = 'INTERMEDIO')
				AND carrera = sga_alumnos.carrera)

	EXECUTE PROCEDURE "dba".sp_plan_de_alumno('EXA', v_carrera, v_legajo, TODAY) INTO v_plan, v_version;

	--Selecciono las materias aprobadas para cada alumno, carrera, plan
	SELECT COUNT(*)
		INTO i_cant_mat_aprobadas
	FROM vw_hist_academica
		WHERE legajo = v_legajo
		AND carrera = v_carrera
		AND resultado IN ('A')
		AND materia IN (SELECT materia FROM materias_intermedio
					WHERE carrera = v_carrera
					AND plan = v_plan
					AND version = v_version);

	--Si todavía no aprobó todos los finales para el titulo intermedio
	IF ( i_cant_mat_aprobadas < (SELECT cant 
					FROM cant_mat_intermedio 
						WHERE carrera = v_carrera
						AND plan = v_plan
						AND version = v_version) ) THEN

		--Selecciono las cursadas / materias aprobadas para cada alumno, carrera, plan
		SELECT DISTINCT materia
		FROM sga_cursadas
			WHERE legajo = v_legajo
			AND carrera = v_carrera
			AND resultado IN ('A', 'P')
			AND materia IN (SELECT materia FROM materias_intermedio
						WHERE carrera = v_carrera
						AND plan = v_plan
						AND version = v_version)
		UNION 
		SELECT DISTINCT materia
		FROM vw_hist_academica
			WHERE legajo = v_legajo
			AND carrera = v_carrera
			AND resultado IN ('A')
			AND materia IN (SELECT materia FROM materias_intermedio
						WHERE carrera = v_carrera
						AND plan = v_plan
						AND version = v_version)
		INTO TEMP cursadas_aprobadas WITH NO LOG;	

		--Cuento la cantidad de materias que tiene con cursada aprobada
		SELECT DISTINCT COUNT(*) 
			INTO i_cursadas_aprobadas
		FROM cursadas_aprobadas;


		--Si tiene todas las cursadas aprobadas
		IF ( i_cursadas_aprobadas >= (SELECT cant 
					FROM cant_mat_intermedio 
						WHERE carrera = v_carrera
						AND plan = v_plan
						AND version = v_version) ) THEN

			SELECT e_mail
				INTO v_mail
			FROM gda_anun_conf_pers 
				WHERE nro_inscripcion = v_legajo;

			SELECT apellido || ', ' || nombres
				INTO v_alumno
			FROM sga_personas
				WHERE nro_inscripcion = v_legajo;

			SELECT nombre
				INTO v_nombre_carrera
			FROM sga_carreras 
				WHERE carrera = v_carrera;

			INSERT INTO rep_alumnos_con_cursadas_tit_interm VALUES 
					(v_legajo, v_alumno, v_mail, v_carrera, v_nombre_carrera, v_plan);
		END IF;

	    DROP TABLE cursadas_aprobadas;

	END IF;

END FOREACH;

DROP TABLE materias_intermedio;
DROP TABLE cant_mat_intermedio;

INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_alumnos_con_cursadas_tit_interm', CURRENT YEAR TO SECOND);

--TRACE OFF;

END;	

END PROCEDURE;
