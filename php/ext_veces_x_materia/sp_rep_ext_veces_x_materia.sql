--DROP PROCEDURE "dba".sp_rep_ext_veces_x_materia(int);
CREATE PROCEDURE "dba".sp_rep_ext_veces_x_materia(pCarrera LIKE sga_carreras.carrera)
       RETURNING VARCHAR(255), INTEGER, INTEGER;

DEFINE vc_materia VARCHAR(255);
DEFINE i_anio INTEGER;
DEFINE i_cant_pedidos INTEGER;

BEGIN	

	--Descarto los duplicados por alumno, me quedo solo con las materias que pidió cada alumno		
	IF (pCarrera is NULL) THEN 
		SELECT materia, legajo, MAX(fecha_alta) AS fecha_alta
			FROM rep_extensiones_cursada 
		GROUP BY materia, legajo
		INTO TEMP aux_pedidos;
	ELSE
		SELECT materia, legajo, MAX(fecha_alta) AS fecha_alta
			FROM rep_extensiones_cursada 
			WHERE carrera = pCarrera
		GROUP BY materia, legajo
		INTO TEMP aux_pedidos;
	END IF;

	FOREACH 
		SELECT M.nombre AS materia, YEAR(fecha_alta), COUNT(P.legajo)
		INTO vc_materia, i_anio, i_cant_pedidos
			FROM aux_pedidos P, sga_materias M
			WHERE P.materia = M.materia
		GROUP BY 1,2
	
		RETURN vc_materia, i_anio, i_cant_pedidos WITH RESUME;

	END FOREACH;
	
	DROP TABLE aux_pedidos;

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_ext_veces_x_materia(206);

