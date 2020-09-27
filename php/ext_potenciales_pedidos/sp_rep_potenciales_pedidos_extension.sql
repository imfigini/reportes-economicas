DROP PROCEDURE "dba".sp_rep_potenciales_pedidos_extension(INTEGER);
CREATE PROCEDURE "dba".sp_rep_potenciales_pedidos_extension(pCuatrimestre LIKE sga_periodos_lect.periodo_lectivo)
       RETURNING VARCHAR(255), INTEGER; 	

DEFINE vc_materia VARCHAR(255);
DEFINE i_cantidad_potenciales_pedidos INTEGER;

DEFINE d_desde DATE;
DEFINE d_hasta DATE;
BEGIN	


	IF (pCuatrimestre = '1° cuatrimestre') THEN
		LET d_desde = MDY(2,1,YEAR(TODAY));
		LET d_hasta = MDY(6,30,YEAR(TODAY));
	ELSE 
		LET d_desde = MDY(7,1,YEAR(TODAY));
		LET d_hasta = MDY(12,31,YEAR(TODAY));
	END IF;

	SELECT * FROM sga_cursadas
		WHERE 	fin_vigencia_regul BETWEEN d_desde AND d_hasta
			AND resultado = 'A'
	INTO TEMP aux_posibles_pedidos_extension;

	SELECT DISTINCT M.nombre AS materia, COUNT(C.legajo) AS cant
		FROM aux_posibles_pedidos_extension C, sga_alumnos A, sga_materias M
		WHERE C.carrera||C.legajo||C.materia NOT IN (
			SELECT carrera||legajo||materia
				FROM sga_prorrogas_regu
			)
			AND C.unidad_academica = A.unidad_academica
			AND C.carrera = A.carrera
			AND C.legajo = A.legajo
			AND A.regular = 'S' AND A.calidad = 'A'
			AND C.unidad_academica = M.unidad_academica
			AND C.materia = M.materia
	GROUP BY 1
	ORDER BY 2 DESC
	INTO TEMP aux_potenciales_pedidos_extension;

	FOREACH
		SELECT materia, cant 
			INTO vc_materia, i_cantidad_potenciales_pedidos
		FROM aux_potenciales_pedidos_extension

		RETURN vc_materia, i_cantidad_potenciales_pedidos WITH RESUME;

	END FOREACH;

	DROP TABLE aux_posibles_pedidos_extension;
	DROP TABLE aux_potenciales_pedidos_extension;

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_potenciales_pedidos_extension('1° cuatrimestre');