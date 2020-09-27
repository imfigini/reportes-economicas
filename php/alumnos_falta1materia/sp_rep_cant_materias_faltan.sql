/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  imfigini
 * Created: 23/10/2018
 */

DROP FUNCTION 'dba'.sp_rep_cant_materias_faltan(varchar,varchar,varchar);

CREATE PROCEDURE "dba".sp_rep_cant_materias_faltan(p_unidad_academica VARCHAR(5), p_carrera VARCHAR(5), p_legajo VARCHAR(15)) 
RETURNING integer, integer, integer;

DEFINE i_total_materias         INTEGER;
DEFINE i_total_optativas		INTEGER;
DEFINE i_materias_aprobadas     INTEGER;
DEFINE f_aporte_optativas		FLOAT;
DEFINE f_acumulador				FLOAT;
DEFINE f_porcentaje             FLOAT;
DEFINE v_plan                   VARCHAR(5);
DEFINE v_version                VARCHAR(5);
DEFINE v_resultado              VARCHAR(255);
DEFINE vc_materia				VARCHAR(5);
DEFINE i_resultado			INTEGER;
DEFINE i_resultado_regular		INTEGER;
DEFINE i_resultado_opt		INTEGER;

BEGIN

    LET i_total_materias = 0;
    LET i_materias_aprobadas = 0;
    LET f_porcentaje = 0;
    LET f_aporte_optativas = 0;
    LET i_resultado_regular = 0;
    LET i_resultado_opt = 0;

--SET DEBUG FILE TO '/tmp/sp_rep_cant_materias_faltan.sql';
--TRACE ON;
		
    LET v_plan, v_version = "dba".sp_plan_de_alumno(p_unidad_academica, p_carrera, p_legajo) ;
	LET v_resultado = "";
	LET i_resultado = -1;
	
	-- Recupero las materias que corresponden:
	SELECT DISTINCT materia
                FROM sga_atrib_mat_plan
                WHERE carrera = p_carrera AND plan = v_plan AND version = v_version AND
                tipo_materia = "N" AND obligatoria = "S"
	INTO TEMP materias
	WITH NO LOG;

	--Recupero la cantidad de materias regulares (normales y obligatorias, incuida inglés)
	SELECT COUNT(*) 
		INTO i_total_materias
	FROM materias;

	-- Recupero la cantidad de materias optativas:
	SELECT NVL(cnt_optativas, 0)
		INTO i_total_optativas
	FROM sga_planes
        	WHERE carrera = p_carrera AND plan = v_plan AND version_actual = v_version;

	--Si la carrera es sistemas (no plan 1988), desnormalizo las optativas asumiendo son 8 las materias a realizar 
	--y 5 en el plan 2011 (3 son optativas obligatorias y están contabilizadas dentro de las regulares)
	IF (p_carrera = '206' AND v_plan != '1988') THEN 
		IF (v_plan = '2011') THEN
			LET i_total_optativas = 5;
		ELSE
			LET i_total_optativas = 8;
		END IF;
	END IF;

	--En el caso de TUDAI son 3 optativas
	IF (p_carrera = '213') THEN 
		LET i_total_optativas = 3;
	END IF;

	-- Cuento las materias aprobadas normales que corresponden al alumno:
	SELECT COUNT(DISTINCT vw_hist_academica.materia) 
		INTO i_materias_aprobadas
			FROM vw_hist_academica, sga_atrib_mat_plan
			WHERE 
			vw_hist_academica.materia = sga_atrib_mat_plan.materia
					AND vw_hist_academica.carrera = sga_atrib_mat_plan.carrera				
			--
			AND sga_atrib_mat_plan.carrera = p_carrera 
			AND sga_atrib_mat_plan.plan = v_plan 
			AND sga_atrib_mat_plan.version = v_version 
			AND sga_atrib_mat_plan.tipo_materia = "N" 
			AND sga_atrib_mat_plan.obligatoria = "S"
			-- Iris: Se controla que además de obligatoria salga en listado (por inglés)
			AND sga_atrib_mat_plan.sale_listado = "S"
			--
			AND vw_hist_academica.legajo = p_legajo
					AND vw_hist_academica.unidad_academica = p_unidad_academica
					AND vw_hist_academica.resultado = "A";

	-- Si el plan tiene optativas:
	IF i_total_optativas > 0 THEN
	
		FOREACH 
		SELECT 
				sga_mat_genericas.materia_optativa, 
				(sga_mat_genericas.valor_materia / (SELECT M2.puntaje_requerido 
				FROM sga_materias M2
				WHERE M2.tipo_materia = 'G' AND
					M2.materia = sga_opt_generica.materia_generica
				)) AS aporte_materia_optativa 
				INTO vc_materia, f_acumulador

		FROM sga_opt_generica, sga_materias, sga_mat_genericas, vw_hist_academica
		WHERE 
			sga_mat_genericas.materia_generica = sga_opt_generica.materia_generica AND
			sga_mat_genericas.materia_optativa = sga_opt_generica.materia_optativa AND
			sga_opt_generica.materia_optativa = sga_materias.materia AND
			vw_hist_academica.carrera	=  sga_opt_generica.carrera AND
			vw_hist_academica.legajo	=  p_legajo AND
			vw_hist_academica.plan		=  sga_opt_generica.plan AND
			vw_hist_academica.version	=  sga_opt_generica.version AND
			vw_hist_academica.materia	=  sga_mat_genericas.materia_optativa AND
			vw_hist_academica.resultado	= 'A' AND
			sga_opt_generica.carrera = p_carrera AND
			sga_opt_generica.plan = v_plan  
			
			-- Caso de optativa que cambio de valores:
			IF (p_carrera = '206' AND vc_materia = '1165'AND 
				p_legajo IN ('246940', '244321', '246983')) THEN
				LET f_acumulador = 0.125;
			END IF;
			
			IF (p_carrera = '206' AND v_plan != '1988') THEN 
				LET f_acumulador = f_acumulador * i_total_optativas;
			END IF;
		
			LET f_aporte_optativas = f_aporte_optativas + f_acumulador;
			
		END FOREACH;
	END IF;


    
	--LET v_resultado = "i_total_materias: " || i_total_materias || " i_total_optativas: " || i_total_optativas || " - i_materias_aprobadas:" || i_materias_aprobadas || " f_aporte_optativas: " || f_aporte_optativas ;

	LET i_resultado = (i_total_materias + i_total_optativas) - (i_materias_aprobadas + f_aporte_optativas);
	LET i_resultado_regular = i_total_materias - i_materias_aprobadas;
	LET i_resultado_opt = i_total_optativas - f_aporte_optativas;

	DROP TABLE materias;
		
	RETURN i_resultado, i_resultado_regular, i_resultado_opt;

--TRACE OFF;

END;
END PROCEDURE;