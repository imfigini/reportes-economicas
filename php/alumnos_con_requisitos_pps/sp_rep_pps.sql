DROP PROCEDURE 'dba'.sp_rep_pps();

CREATE PROCEDURE "dba".sp_rep_pps() 
--Actualiza la tabla rep_pps
--Contiene legajo, cantidad de materias aprobadas de los alumnos que cumplen las siguientes condiciones:
--   - Estudiantes de Sistemas (carrera 206) Plan 2011
--   - No tienen cursada ni aprpbada las PPS
--   - Cantidad de materias cursadas (puede ser por cursada o final libre o equivalencia)

DEFINE i_total_mat	INTEGER;
DEFINE i_cant_mat 	FLOAT;

BEGIN
   -- 0211	Prácticas Profesionales Supervisadas

   DELETE FROM rep_alu_con_pps_206;

   --Inserto los que tienen el final de la PPS
   INSERT INTO rep_alu_con_pps_206
   SELECT A.legajo FROM sga_alumnos A, vw_hist_academica H 
   	WHERE A.carrera = 206 AND A.plan = '2011' AND A.calidad = 'A'
   	AND A.unidad_academica = H.unidad_academica AND A.carrera = H.carrera AND A.legajo = H.legajo AND A.plan = H.plan
   	AND H.materia = '0211' AND H.resultado = 'A';
   
   --Inserto los que tienen la cursada de la PPS
   INSERT INTO rep_alu_con_pps_206
   SELECT A.legajo FROM sga_alumnos A, sga_cursadas C
   	WHERE A.carrera = 206 AND A.plan = '2011' AND A.calidad = 'A'
   	AND A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera AND A.legajo = C.legajo AND A.plan = C.plan
   	AND C.materia = '0211' AND C.resultado = 'A';
   
   --Elimino los duplicados, quedan los alumnos con PPS aprobada
   SELECT * FROM rep_alu_con_pps_206 INTO TEMP auxi;
   DELETE FROM rep_alu_con_pps_206;
   INSERT INTO rep_alu_con_pps_206 SELECT DISTINCT * FROM auxi;
   DROP TABLE auxi;

   --Alumnos que aún no hiceron la PPS con sus respectivas materias aprobadas
   CREATE TABLE alu_sin_pps_aprob
   (	legajo VARCHAR(15),
	materia VARCHAR (5)
   );
   
   --Inserto los alumnos sin PPS con las materias aprobadas por final
   INSERT INTO alu_sin_pps_aprob
   SELECT A.legajo, H.materia FROM sga_alumnos A, vw_hist_academica H 
   	WHERE A.carrera = 206 AND A.plan = '2011' AND A.calidad = 'A'
   	AND A.legajo NOT IN (SELECT legajo FROM rep_alu_con_pps_206)
   	AND A.unidad_academica = H.unidad_academica AND A.carrera = H.carrera AND A.legajo = H.legajo AND A.plan = H.plan
   	AND H.resultado = 'A';
   
   --Inserto los alumnos sin PPS con las materias aprobadas por cursada
   INSERT INTO alu_sin_pps_aprob
   SELECT A.legajo, C.materia FROM sga_alumnos A, sga_cursadas C
   	WHERE A.carrera = 206 AND A.plan = '2011' AND A.calidad = 'A'
   	AND A.legajo NOT IN (SELECT legajo FROM rep_alu_con_pps_206)
   	AND A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera AND A.legajo = C.legajo AND A.plan = C.plan
   	AND C.resultado = 'A';
   
   --Elimino los duplicados, sólo quedan alumnos sin PPS con materias aprobadas (indistinto si es por final o cursada)
   SELECT * FROM alu_sin_pps_aprob INTO TEMP auxi;
   DELETE FROM alu_sin_pps_aprob;
   INSERT INTO alu_sin_pps_aprob SELECT DISTINCT * FROM auxi;
   DROP TABLE auxi;


   DELETE FROM rep_alu_sin_pps_206;
   
   --Inserto la cantidad de materias aprobadas 
   INSERT INTO rep_alu_sin_pps_206 (legajo, cant_mat)
   SELECT S.legajo, COUNT(S.materia)
   	FROM alu_sin_pps_aprob S, sga_atrib_mat_plan P
   	WHERE S.materia = P.materia
   	AND P.carrera = 206 AND plan = '2011'
   	--AND P.tipo_materia = "N" 
   	GROUP BY legajo;
   
   --Elimino tablas tmeporales usadas
   DROP TABLE alu_sin_pps_aprob;

END;

END PROCEDURE;