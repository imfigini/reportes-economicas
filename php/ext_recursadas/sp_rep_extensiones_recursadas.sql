DROP PROCEDURE "dba".sp_rep_extensiones_recursadas();
CREATE PROCEDURE "dba".sp_rep_extensiones_recursadas()
       RETURNING VARCHAR(5), VARCHAR(255), VARCHAR(15), INTEGER; 	

DEFINE vc_carrera VARCHAR(5);
DEFINE vc_materia VARCHAR(255);
DEFINE vc_legajo VARCHAR(15);
DEFINE i_recursadas INTEGER;

BEGIN	

	SELECT DISTINCT C.carrera, C.legajo, C.materia, C.comision
		FROM sga_prorrogas_regu P
		JOIN sga_cursadas C ON (C.unidad_academica = P.unidad_academica
			AND C.carrera = P.carrera
			AND C.legajo = P.legajo
			AND C.materia = P.materia
			AND C.resultado = 'A')
		JOIN sga_alumnos A ON (C.unidad_academica = A.unidad_academica 
			AND C.carrera = A.carrera
			AND C.legajo = A.legajo 
			AND A.regular = 'S' AND A.calidad = 'A')
	INTO TEMP aux_posibles_recursadas;


	SELECT DISTINCT R.carrera, M.nombre AS materia, R.legajo, COUNT(R.comision)-1 AS cant_recursadas
		FROM aux_posibles_recursadas R, sga_materias M
			WHERE R.materia = M.materia
	GROUP BY 1,2,3
	HAVING COUNT(R.comision) > 1
	INTO TEMP aux_recursadas;


	FOREACH
		SELECT carrera, materia, legajo, cant_recursadas
			INTO vc_carrera, vc_materia, vc_legajo, i_recursadas
		FROM aux_recursadas

		RETURN vc_carrera, vc_materia, vc_legajo, i_recursadas WITH RESUME;

	END FOREACH;

	DROP TABLE aux_posibles_recursadas;
	DROP TABLE aux_recursadas;

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_extensiones_recursadas();