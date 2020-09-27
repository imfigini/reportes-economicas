--DROP PROCEDURE "dba".sp_rep_extensiones_cursada();
CREATE PROCEDURE "dba".sp_rep_extensiones_cursada()
       RETURNING DATETIME YEAR TO SECOND; 	

DEFINE d_fecha_actualizacion DATETIME YEAR TO SECOND; 
BEGIN	

	LET d_fecha_actualizacion = NULL;

	DELETE FROM rep_extensiones_cursada;
	
	INSERT INTO rep_extensiones_cursada
	SELECT C.carrera, M.materia, P.legajo, P.f_venc_reg_ant, P.f_prorroga_hasta, P.fecha_alta, V.resultado, V.fecha
		FROM sga_carreras C, sga_materias M, sga_prorrogas_regu P
		LEFT JOIN vw_hist_academica V ON (V.unidad_academica = P.unidad_academica AND V.legajo = P.legajo AND V.carrera = P.carrera AND V.materia = P.materia)
		WHERE P.unidad_academica = C.unidad_Academica AND P.carrera = C.carrera
			AND P.unidad_academica = M.unidad_academica AND P.materia = M.materia;

	INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_extensiones_cursada', CURRENT YEAR TO SECOND);

	SELECT MAX(fecha_ultima_actualizacion) 
		INTO d_fecha_actualizacion
		FROM rep_fecha_actualiz_tablas
			WHERE tabla = 'rep_extensiones_cursada';

	RETURN d_fecha_actualizacion;

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_extensiones_cursada();